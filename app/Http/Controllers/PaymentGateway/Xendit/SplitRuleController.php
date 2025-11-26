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
                    'message' => 'Failed to create split rule (Xendit)',
                    'errors' => $response->json(),
                ], $response->status());
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'message' => 'Split rule created successfully',
                'data' => $data,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error while creating split rule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}