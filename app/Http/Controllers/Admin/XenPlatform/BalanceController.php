<?php

namespace App\Http\Controllers\Admin\XenPlatform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\TransactionsController as XenditTransactionsController;
use App\Http\Controllers\PaymentGateway\Xendit\BalanceController as XenditBalanceController;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BalanceController extends Controller
{
    protected $xenditSubAccount;
    protected $accountMasterId;
    protected $xenditTransactions;
    protected $xenditBalance;

    public function __construct(XenditService $xendit)
    {
        $this->xenditTransactions = new XenditTransactionsController($xendit);
        $this->xenditBalance = new XenditBalanceController($xendit);
        $this->accountMasterId = config('services.xendit.account_master_id');
    }

    public function index()
    {
        return view('pages.admin.xen_platform.balance.index');
    }

    public function getData(Request $request)
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
            $transactionsResponse = $this->xenditTransactions->getTransactions(null, $apiParams);
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
                        null,
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

        } catch (\Exception $e) {
            Log::error('Xendit Partner Balance Activity Error: ' . $e->getMessage(), [
                'request_params' => $apiParams,
            ]);
            $errorMessage = 'Gagal mengambil data riwayat saldo dari Xendit. Silakan coba lagi. (' . $e->getMessage() . ')';
        }

        $balanceView = view('pages.admin.xen_platform.balance.table',
            [
                'balances' => $tempResult,
                'meta' => $paginationMeta,
                'filters' => $apiParams,
                'errorMessage' => $errorMessage])->render();

        return response()->json([
            'balanceTable' => $balanceView,
        ]);
    }
}