<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Services\XenditService;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function getBalance(string $subAccountId, Request $request)
    {
        try {
            $params = [
                'account_type' => $request->query('account_type', 'CASH'),
                'currency' => $request->query('currency', 'IDR'),
                'at_timestamp' => $request->query('at_timestamp'),
            ];

            $response = $this->xendit->getBalance($subAccountId, $params);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to get balance data',
                    'errors' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Balance data retrieved successfully',
                'data' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}