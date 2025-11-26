<?php

namespace App\Http\Controllers\Admin\XenPlatform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\TransactionsController as XenditTransactionsController;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    protected $xenditSubAccount;
    protected $accountMasterId;
    protected $xenditTransactions;

    public function __construct(XenditService $xendit)
    {
        $this->xenditTransactions = new XenditTransactionsController($xendit);
        $this->accountMasterId = config('services.xendit.account_master_id');
    }

    public function index()
    {
        return view('pages.admin.xen_platform.transactions.index');
    }

    public function getData(Request $request)
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
            $transactionsResponse = $this->xenditTransactions->getTransactions(null, $apiParams);
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

        $summaryView  = view('pages.admin.xen_platform.transactions.summary', ['summary' => $summary])->render();
        $transactionsView = view('pages.admin.xen_platform.transactions.table',
            ['transactions' => $transactions,
            'summary' => $summary,
            'meta' => $paginationMeta,
            'filters' => $apiParams,
            'errorMessage' => $errorMessage,])->render();

        return response()->json([
            'transactionsTable' => $transactionsView,
            'summary' => $summaryView,
        ]);
    }

    public function getTransactionById($transactionId)
    {
        $transactionsResponse = $this->xenditTransactions->getTransactionById(null, $transactionId);
        $transactionData = $transactionsResponse->getData(true);
        $transactions = $transactionData['data'] ?? [];

        return view('pages.admin.xen_platform.transactions.detail.index', ['transaction' => $transactions]);
    }
}