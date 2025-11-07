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

    public function createAccount(Request $request)
    {
        try {
            $partnerId = $request->input('partner_id');
            $partnerEmail = $request->input('partner_email');

            if (!$partnerId || !$partnerEmail) {
                return redirect()->back()
                    ->with('error', 'Partner ID and email are required.');
            }

            if (XenditSubAccount::where('partner_id', $partnerId)->exists()) {
                return redirect()->back()
                    ->with('warning', 'This partner is already registered with Xendit.');
            }

            $payload = [
                'email' => $partnerEmail,
                'type' => $request->input('account_type', 'OWNED'),
                'public_profile' => [
                    'business_name' => $request->input('business_name', 'PT Partner'),
                ],
            ];

            $response = $this->xendit->createAccount($payload);

            if (!$response->successful()) {
                return redirect()->back()
                    ->with('error', 'Failed to create Xendit account: ' . ($response->json()['message'] ?? 'Unknown error'));
            }

            $data = $response->json();

            XenditSubAccount::create([
                'partner_id'     => $partnerId,
                'xendit_user_id' => $data['id'] ?? null,
                'business_name'  => $data['public_profile']['business_name'] ?? $request->input('business_name'),
                'email'          => $data['email'] ?? $request->input('xendit_user_email'),
                'type'           => $data['type'] ?? $request->input('account_type'),
                'status'         => $data['status'] ?? null,
                'country'        => $data['country'] ?? null,
                'raw_response'   => json_encode($data),
            ]);

            $owner = Owner::findOrFail($partnerId);
            if ($owner) {
                $owner->update([
                    'xendit_registration_status' => $data['status'],
                    'xendit_registered_at' => Carbon::parse($data['created']),
                    ]);
            }

            return redirect()->back()
                ->with('success', 'Partner Xendit account created successfully!');
        } catch (\Exception $e) {
            Log::error('createAccount error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getSubAccounts($filters = [])
    {
        try {
            $response = $this->xendit->getSubAccounts($filters);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data sub account',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Data sub account berhasil diambil',
                'data'    => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal',
                'error'   => $e->getMessage(),
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
                    'message' => 'Gagal mengambil sub account',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Sub account berhasil diambil',
                'data'    => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
