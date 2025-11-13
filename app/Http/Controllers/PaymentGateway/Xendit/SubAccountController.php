<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubAccountController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function createAccount($payload, $partnerId)
    {
        try {
            $response = $this->xendit->createAccount($payload);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create Xendit account',
                    'errors' => $response->json(),
                ], $response->status());
            }

            $data = $response->json();

            XenditSubAccount::create([
                'partner_id' => $partnerId,
                'xendit_user_id' => $data['id'],
                'business_name' => $data['public_profile']['business_name'],
                'email' => $data['email'],
                'type' => $data['type'],
                'status' => $data['status'],
                'country' => $data['country'],
                'created_xendit' => Carbon::parse($data['created']),
                'updated_xendit' => Carbon::parse($data['updated']),
                'raw_response' => json_encode($data),
            ]);

            $owner = Owner::findOrFail($partnerId);
            if ($owner) {
                $owner->update([
                    'xendit_registration_status' => $data['status'],
                    'xendit_registered_at' => Carbon::parse($data['created']),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Partner Xendit account created successfully!',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('createAccount error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSubAccounts($filters = [])
    {
        try {
            $response = $this->xendit->getSubAccounts($filters);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch sub account data',
                    'errors' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Sub account data retrieved successfully',
                'data' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSubAccountById(string $id, Request $request = null): JsonResponse
    {
        try {
            $response = $this->xendit->getSubAccountById($id);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch sub account',
                    'errors' => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Sub account retrieved successfully',
                'data' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}