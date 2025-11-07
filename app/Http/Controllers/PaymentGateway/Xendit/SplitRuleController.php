<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Services\XenditService;
use Illuminate\Http\JsonResponse;

class SplitRuleController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function createSplitRule(array $payload): JsonResponse
    {
        try {
            $response = $this->xendit->createSplitRule($payload);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat split rule (Xendit)',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'message' => 'Split rule berhasil dibuat',
                'data'    => $data,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan server saat membuat split rule',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
