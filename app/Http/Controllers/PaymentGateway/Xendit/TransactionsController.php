<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Services\XenditService;

class TransactionsController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function getTransactions(string $subAccountId = null, $filters = [])
    {
        try {
            $response = $this->xendit->getTransactions($subAccountId, $filters);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data transaksi dari Xendit',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diambil',
                'data'    => $response->json(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getTransactionById(string $subAccountId = null, string $transactionId = null)
    {
        try {
            $response = $this->xendit->getTransactionById($subAccountId, $transactionId);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data transaksi dari Xendit',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diambil',
                'data'    => $response->json(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
