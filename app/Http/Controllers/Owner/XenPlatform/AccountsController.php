<?php

namespace App\Http\Controllers\Owner\XenPlatform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\BalanceController;
use App\Http\Controllers\PaymentGateway\Xendit\InvoiceController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use App\Http\Controllers\PaymentGateway\Xendit\TransactionsController;
use App\Models\Owner;
use App\Models\Xendit\XenditPayout;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountsController extends Controller
{
    protected $xendit;
    protected $xenditSubAccount;
    protected $xenditBalance;
    protected $xenditTransactions;
    protected $xenditInvoices;
    protected $xenditAccountId;
    protected $userAuth;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
        $this->xenditSubAccount = new SubAccountController($xendit);
        $this->xenditBalance = new BalanceController($xendit);
        $this->xenditTransactions = new TransactionsController($xendit);
        $this->xenditInvoices = new InvoiceController($xendit);
        $this->userAuth = Auth::guard('owner')->user();

        $this->xenditAccountId = Owner::with('xenditSubAccount')
            ->where('id', $this->userAuth->id)
            ->first()
            ->xenditSubAccount->xendit_user_id ?? null;

        if (is_null($this->xenditAccountId)) {
            abort(404, 'Xendit account not found.');
        }
    }

    public function showAccountInfo(Request $request)
    {
        $tab = $request->query('tab', 'profile');
        $accountId = $this->xenditAccountId;
        $account = $this->getProfile($accountId, $request);

        $data = [];

        if ($tab === 'activity') {
            $data = $this->getActivity($accountId, $request);
        } elseif ($tab === 'invoices') {
            $data = $this->getAllInvoices($accountId, $request);
        } elseif ($tab === 'balance') {
            $data = $this->getTransactionsWithBalance($accountId, $request);
        } elseif ($tab === 'profile') {
            $data = $account;
        }
        return view('pages.owner.xen_platform.accounts.information', [
            'tab' => $tab,
            'data' => $data,
            'account' => $account,
            'accountId' => $accountId,
        ]);
    }

    public function getActivity($accountId, Request $request)
    {
        $page = (int) $request->input('page', 1);
        $apiParams = [
            'limit'               => $request->input('limit', 10),
            'statuses'            => $request->input('statuses'),
            'settlement_statuses' => $request->input('settlement_statuses'),
            'types'               => $request->input('types'),
            'channel_categories'  => $request->input('channel_categories'),
            'currency'            => $request->input('currency'),
        ];

        $searchKeys = ['reference_id', 'account_identifier', 'amount', 'product_id'];
        foreach ($searchKeys as $key) {
            if ($request->has($key)) {
                $apiParams[$key] = $request->input($key);
                break;
            }
        }

        if ($request->has('after_id')) {
            $apiParams['after_id'] = $request->after_id;
        } elseif ($request->has('before_id')) {
            $apiParams['before_id'] = $request->before_id;
        }

        $createdGte = $request->input('created_gte');
        $createdLte = $request->input('created_lte');

        try {
            if (!empty($createdGte) && !empty($createdLte)) {
                $startDate = Carbon::createFromFormat('Y-m-d', trim($createdGte))->startOfDay();
                $endDate   = Carbon::createFromFormat('Y-m-d', trim($createdLte))->endOfDay();
            } else {
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate   = Carbon::now()->endOfMonth()->endOfDay();
            }

            $apiParams['created[gte]'] = $startDate->toIso8601String();
            $apiParams['created[lte]'] = $endDate->toIso8601String();

        } catch (\Exception $e) {
            $apiParams['created[gte]'] = Carbon::now()->startOfMonth()->startOfDay()->toIso8601String();
            $apiParams['created[lte]'] = Carbon::now()->endOfMonth()->endOfDay()->toIso8601String();
        }

        $apiParams = array_filter($apiParams, fn($value) => !is_null($value) && $value !== '');

        $transactions = [];
        $paginationMeta = [
            'has_more' => false,
            'before_id' => null,
            'after_id' => null,
            'limit' => $apiParams['limit'] ?? 10,
        ];
        $summary = [
            'incoming_amount' => 0,
            'incoming_count'  => 0,
            'outgoing_amount' => 0,
            'outgoing_count'  => 0,
        ];
        $errorMessage = null;

        try {
            $transactionsResponse = $this->xenditTransactions->getTransactions($accountId, $apiParams);
            $transactionData = $transactionsResponse->getData(true);

            if (!($transactionData['success'] ?? false)) {
                $apiMessage = $transactionData['message'] ?? 'Unknown error from Xendit API.';
                throw new \Exception("Gagal mengambil data transaksi. Pesan API: " . $apiMessage);
            }

            $transactions = $transactionData['data']['data'] ?? [];
            $hasMore = $transactionData['data']['has_more'] ?? false;

            $firstTransaction = reset($transactions);
            $lastTransaction = end($transactions);

            $nextPageCursor = ($lastTransaction) ? $lastTransaction['id'] : null;
            $prevPageCursor = ($firstTransaction) ? $firstTransaction['id'] : null;

            $isFirstPage = $page <= 1;

            $paginationMeta = [
                'has_more' => $isFirstPage ? true : $hasMore,
                'before_id' => $isFirstPage ? null : $prevPageCursor,
                'after_id' => $nextPageCursor,
                'limit' => $apiParams['limit'] ?? 10,
            ];

            foreach ($transactions as $trx) {
                $amount = $trx['amount'] ?? 0;
                $cashflow = $trx['cashflow'] ?? '';
                if ($cashflow === 'MONEY_IN') {
                    $summary['incoming_amount'] += $amount;
                    $summary['incoming_count']++;
                } elseif ($cashflow === 'MONEY_OUT') {
                    $summary['outgoing_amount'] += $amount;
                    $summary['outgoing_count']++;
                }
            }

        } catch (\Exception $e) {
            Log::error('Xendit Partner Transaction Error: ' . $e->getMessage(), [
                'request_params' => $apiParams,
            ]);
            $errorMessage = 'Gagal mengambil data transaksi dari Xendit. Silakan coba lagi. (' . $e->getMessage() . ')';
        }

        return [
            'transactions' => $transactions,
            'summary' => $summary,
            'meta' => $paginationMeta,
            'filters' => $apiParams,
            'errorMessage' => $errorMessage,
        ];
    }

    public function getProfile($accountId, Request $request)
    {
        $subAccountResponse = $this->xenditSubAccount->getSubAccountById($accountId);
        $subAccountData = $subAccountResponse->getData(true);
        $accounts = $subAccountData['data'];

        $balanceResponse = $this->xenditBalance->getBalance($accounts['id'], $request);
        $balanceData = $balanceResponse->getData(true);
        $accounts['balance'] = $balanceData['success']
            ? $balanceData['data']['balance']
            : null;

        return $accounts;
    }

    public function getAllInvoices($accountId, Request $request)
    {
        $page = (int) $request->input('page', 1);
        $apiParams = [
            'limit'             => $request->input('limit', 10),
            'statuses'          => $request->input('statuses'),
            'client_types'      => $request->input('client_types'),
            'payment_channels'  => $request->input('payment_channels'),
        ];

        $checkboxFilters = ['statuses', 'client_types', 'payment_channels'];
        foreach ($checkboxFilters as $key) {
            $values = $request->input($key);
            if (!empty($values)) {
                $apiParams[$key] = json_encode(explode(',', $values));

            }
        }

        $searchKeys = ['external_id', 'on_demand_link', 'recurring_payment_id'];
        foreach ($searchKeys as $key) {
            if ($request->has($key)) {
                $apiParams[$key] = $request->input($key);
                break;
            }
        }

        if ($request->has('before_id')) {
            $apiParams['last_invoice_id'] = $request->before_id;

        } elseif ($request->has('after_id')) {
            $apiParams['last_invoice_id'] = $request->after_id;
        }

        $dateKeys = ['created', 'paid', 'expired'];
        $dateFilterApplied = false;

        foreach ($dateKeys as $key) {
            $afterKey = "{$key}_after";
            $beforeKey = "{$key}_before";

            if ($request->has($afterKey) && $request->has($beforeKey)) {
                $apiParams[$afterKey] = $request->input($afterKey);
                $apiParams[$beforeKey] = $request->input($beforeKey);
                $dateFilterApplied = true;
                break;
            }
        }

        if (!$dateFilterApplied) {
            try {
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                $apiParams['created_after'] = $startDate->toIso8601String();
                $apiParams['created_before'] = $endDate->toIso8601String();
            } catch (\Exception $e) {
                $apiParams['created_after'] = Carbon::now()->startOfMonth()->startOfDay()->toIso8601String();
                $apiParams['created_before'] = Carbon::now()->endOfMonth()->endOfDay()->toIso8601String();
            }

        }

        $apiParams = array_filter($apiParams, function ($value) {
            return !is_null($value) && $value !== '' && (!is_array($value) || count($value) > 0);
        });

        $invoices = [];
        $paginationMeta = [
            'after_id' => null,
            'limit' => $apiParams['limit'] ?? 10,
        ];
        $errorMessage = null;

        try {
            $invoicesResponse = $this->xenditInvoices->getAllInvoices($accountId, $apiParams);
            $invoicesData = $invoicesResponse->getData(true);

            if (!($invoicesData['success'] ?? false)) {
                $apiMessage = $invoicesData['message'] ?? 'Unknown error from Xendit API.';
                throw new \Exception("Gagal mengambil data invoice. Pesan API: " . $apiMessage);
            }

            $invoices = $invoicesData['data'] ?? [];

            $lastInvoice = end($invoices);
            $count = count($invoices);

            $nextPageCursor = ($lastInvoice && $count == ($apiParams['limit'])) ? $lastInvoice['id'] : null;

            $paginationMeta = [
                'after_id' => $nextPageCursor,
                'limit' => $apiParams['limit'] ?? 10,
            ];

        } catch (\Exception $e) {
            Log::error('Xendit Invoice Error: ' . $e->getMessage(), [
                'request_params' => $apiParams,
            ]);
            $errorMessage = 'Gagal mengambil data invoice dari Xendit. Silakan coba lagi. (' . $e->getMessage() . ')';
        }

        return [
            'invoices' => $invoices,
            'summary' => [],
            'meta' => $paginationMeta,
            'filters' => $apiParams,
            'errorMessage' => $errorMessage,
        ];
    }

    public function getTabData(Request $request, $tab)
    {
        $accountId = $this->xenditAccountId;
        $account = $this->getProfile($accountId, $request);
        if ($tab === 'activity') {
            $data = $this->getActivity($accountId, $request);
            return view('pages.owner.xen_platform.accounts.tab-panel.transaction.index', ['data' => $data, 'account' => $account]);
        } elseif ($tab === 'invoices') {
            $data = $this->getAllInvoices($accountId, $request);
            return view('pages.owner.xen_platform.accounts.tab-panel.invoice.index', ['data' => $data, 'account' => $account]);
        } elseif ($tab === 'balance') {
            $data = $this->getTransactionsWithBalance($accountId, $request);
            return view('pages.owner.xen_platform.accounts.tab-panel.balance.index', ['data' => $data, 'account' => $account]);
        } elseif ($tab === 'profile') {
            $data = $this->getProfile($accountId, $request);
            return view('pages.owner.xen_platform.accounts.tab-panel.profile.index', ['data' => $data]);
        }

        abort(404);
    }

    public function filter(Request $request, $tab)
    {
        $accountId = $this->xenditAccountId;
        $account = $this->getProfile($accountId, $request);

        switch ($tab) {
            case 'activity':
                $data = $this->getActivity($accountId, $request);
                $activityView = view('pages.owner.xen_platform.accounts.tab-panel.transaction.table', ['data' => $data, 'account' => $account])->render();

                return response()->json([
                    'activityTable' => $activityView,
                ]);
            case 'invoices':
                $data = $this->getAllInvoices($accountId, $request);

                $invoiceView = view('pages.owner.xen_platform.accounts.tab-panel.invoice.table', ['data' => $data, 'account' => $account])->render();
                $invoiceData = $data ?? [];

                return response()->json([
                    'invoiceTable' => $invoiceView,
                    'invoiceData' => $invoiceData,
                ]);
            case 'balance':
                $data = $this->getTransactionsWithBalance($accountId, $request);
                $balanceView = view('pages.owner.xen_platform.accounts.tab-panel.balance.table', ['data' => $data, 'account' => $account])->render();

                return response()->json([
                    'balanceTable' => $balanceView,
                ]);
            default:
                abort(404);
        }
    }

    public function getTransactionsWithBalance($accountId, Request $request)
    {
        $page = (int) $request->input('page', 1);

        $apiParams = [
            'limit'                 => $request->input('limit', 10),
            'types'               => $request->input('types'),
            'channel_categories'    => $request->input('channel_categories'),
            'currency'              => $request->input('currency', 'IDR'),
        ];

        $searchKeys = ['reference_id', 'account_identifier', 'amount', 'product_id'];
        foreach ($searchKeys as $key) {
            if ($request->has($key)) {
                $apiParams[$key] = $request->input($key);
                break;
            }
        }

        if ($request->has('after_id')) {
            $apiParams['after_id'] = $request->after_id;
        } elseif ($request->has('before_id')) {
            $apiParams['before_id'] = $request->before_id;
        }

        $dateKeys = ['created', 'paid', 'expired'];
        $dateFilterApplied = false;

        foreach ($dateKeys as $key) {
            $dateGte = $request->input("{$key}_gte");
            $dateLte = $request->input("{$key}_lte");

            if (!empty($dateGte) && !empty($dateLte)) {
                $dateFilterApplied = true;
                try {
                    $startDate = Carbon::createFromFormat('Y-m-d', trim($dateGte))->startOfDay();
                    $endDate   = Carbon::createFromFormat('Y-m-d', trim($dateLte))->endOfDay();

                    $apiParams["{$key}[gte]"] = $startDate->toIso8601String();
                    $apiParams["{$key}[lte]"] = $endDate->toIso8601String();

                } catch (\Exception $e) {
                    Log::warning("Invalid date format for {$key}: " . $e->getMessage());
                }
                break;
            }
        }

        if (!$dateFilterApplied) {
            $apiParams['created[gte]'] = Carbon::now()->startOfMonth()->startOfDay()->toIso8601String();
            $apiParams['created[lte]'] = Carbon::now()->endOfMonth()->endOfDay()->toIso8601String();
        }

        $apiParams = array_filter($apiParams, fn($value) => !is_null($value) && $value !== '');

        $transactions = [];
        $errorMessage = null;
        $paginationMeta = [];

        try {
            $transactionsResponse = $this->xenditTransactions->getTransactions($accountId, $apiParams);
            $transactionData = $transactionsResponse->getData(true);

            if (!($transactionData['success'] ?? false) || !isset($transactionData['data']['data'])) {
                $apiMessage = $transactionData['message'] ?? 'Unknown error from Xendit API.';
                throw new \Exception("Gagal mengambil data transaksi (Balance). Pesan API: " . $apiMessage);
            }

            $rawTransactions = $transactionData['data']['data'];
            $transactionsCollection = collect($rawTransactions);

            $hasMore = $transactionData['data']['has_more'] ?? false;

            $firstTransaction = reset($rawTransactions);
            $lastTransaction = end($rawTransactions);

            $nextPageCursor = ($lastTransaction) ? $lastTransaction['id'] : null;
            $prevPageCursor = ($firstTransaction) ? $firstTransaction['id'] : null;

            $isFirstPage = $page <= 1;

            $paginationMeta = [
                'has_more' => $isFirstPage ? true : $hasMore,
                'before_id' => $isFirstPage ? null : $prevPageCursor,
                'after_id' => $nextPageCursor,
                'limit' => $apiParams['limit'] ?? 10,
            ];

            $tempResult = [];

            foreach ($transactionsCollection as $tx) {
                $dateCreatedField = $tx['estimated_settlement_time'] ?? $tx['created'] ?? now()->toIso8601String();

                $amount = $tx['amount'] ?? 0;
                $cashflow = $tx['cashflow'] ?? '';

                try {
                    $balanceTimestamp = Carbon::parse($dateCreatedField)->addSecond()->toIso8601String();
                    $balanceResponse = $this->xenditBalance->getBalance(
                        $accountId,
                        new Request(['at_timestamp' => $balanceTimestamp])
                    );
                    $balanceData = $balanceResponse->getData(true);
                    $currentBalance = $balanceData['data']['balance'] ?? 0;
                } catch (\Exception $e) {
                    Log::error('Xendit Balance API Error (N+1) at ' . $balanceTimestamp . ': ' . $e->getMessage());
                    $currentBalance = 0;
                }

                $xenditFee = abs($tx['fee']['xendit_fee'] ?? 0);
                $vatFee = abs($tx['fee']['value_added_tax'] ?? 0);
                $totalFees = $xenditFee + $vatFee;
                $statusFee = $tx['fee']['status'];

                $isXenPlatform = ($tx['channel_category'] ?? '') === 'XENPLATFORM';
                $isSplit = ($tx['channel_code'] ?? '') === 'SPLIT';
                $isPayment = ($tx['type'] ?? '') === 'PAYMENT';

                $internalSort = 0;
                if ($isXenPlatform || $isSplit) {
                    $internalSort = 1;
                } elseif ($isPayment) {
                    $internalSort = 4;
                } else {
                    $internalSort = 4;
                }

                $tempResult[] = [
                    'created' => $tx['created'],
                    'internal_sort' => $internalSort,
                    'transaction_type' => $isXenPlatform ? $tx['channel_category'] : $tx['type'],
                    'channel_code' => $tx['channel_code'] ?? '-',
                    'reference_id' => $tx['reference_id'] ?? '-',
                    'amount' => $amount,
                    'balance' => $currentBalance,
                    'cashflow' => $cashflow ?? 'N/A',
                    'settlement_status' => $tx['settlement_status'] ?? '-',
                    'fee_details' => [
                        'xendit_fee' => $xenditFee,
                        'vat_fee' => $vatFee,
                        'total_fees' => $totalFees,
                        'status' => $statusFee,
                    ]
                ];
            }

//            $finalTransactions = collect($tempResult)
//                ->sortByDesc('created_iso')
//                ->sortBy(function($item) {
//                    return $item['internal_sort'];
//                })
//                ->values()
//                ->map(fn($item) => Arr::except($item, ['created_iso', 'internal_sort']))
//                ->toArray();
//
//            $transactions = $finalTransactions;

        } catch (\Exception $e) {
            Log::error('Xendit Partner Balance Activity Error: ' . $e->getMessage(), [
                'request_params' => $apiParams,
            ]);
            $errorMessage = 'Gagal mengambil data riwayat saldo dari Xendit. Silakan coba lagi. (' . $e->getMessage() . ')';
        }

        return [
            'transactions' => $tempResult,
            'meta' => $paginationMeta,
            'filters' => $apiParams,
            'errorMessage' => $errorMessage,
            'summary' => [
                'incoming_amount' => 0,
                'outgoing_amount' => 0,
                'current_balance' => $currentBalance ?? 0,
            ]
        ];
    }

    public function getTransactionById($transactionId)
    {
        $accountId = $this->xenditAccountId;
        $transactionsResponse = $this->xenditTransactions->getTransactionById($accountId, $transactionId);
        $transactionData = $transactionsResponse->getData(true);
        $transactions = $transactionData['data'] ?? [];

        return view('pages.owner.xen_platform.accounts.tab-panel.transaction.detail.index', ['transaction' => $transactions]);
    }

    public function getInvoiceById($invoiceId)
    {
        $accountId = $this->xenditAccountId;
        $invoiceResponse = $this->xenditInvoices->getInvoiceById($accountId, $invoiceId);
        $invoiceData = $invoiceResponse->getData(true);
        $invoice = $invoiceData['data'] ?? [];

        return view('pages.owner.xen_platform.accounts.tab-panel.invoice.detail.index', ['invoice' => $invoice]);
    }

}