<?php

namespace App\Http\Controllers\PaymentGateway\Xendit;

use App\Http\Controllers\Controller;
use App\Models\Xendit\XenditPayout;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayoutController extends Controller
{
    protected $xendit;
    protected $ilumaBaseUrl;
    protected $ilumaApiKey;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
        $this->ilumaBaseUrl = rtrim(config('services.xendit.iluma_base_url'), '/');
        $this->ilumaApiKey = config('services.xendit.iluma_api_key');
    }

    public function getPayoutChannels()
    {
        try {
            $response = $this->xendit->getPayoutChannels();

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'data'  => null,
                    'message' => 'Gagal mengambil payout channels',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Data payout channels berhasil diambil',
                'data'    => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'   => null,
                'message' => 'Terjadi kesalahan internal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function validateBankAccount(Request $request)
    {
        $validated = $request->validate([
            'bank_code' => 'required|string',
            'account_number' => 'required|string',
            'reference_id' => 'nullable|string',
        ]);

        $bankCode = $validated['bank_code'];
        $accountNumber = $validated['account_number'];
        $referenceId = $validated['reference_id'] ?? 'ref_' . Str::random(10);

        $now = now()->toISOString();

        if (app()->environment('local')) {
            return response()->json([
                'status' => 'COMPLETED',
                'bank_account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'created' => $now,
                'updated' => $now,
                'id' => 'bknv_' . Str::random(24),
                'reference_id' => $referenceId,
                'result' => [
                    'is_found' => true,
                    'account_holder_name' => 'FIRA DIYANKA',
                    'is_virtual_account' => false,
                ],
            ]);
        }

        try {
            $response = Http::withBasicAuth($this->ilumaApiKey, '')
                ->acceptJson()
                ->post("{$this->ilumaBaseUrl}/bank_account_data_requests", [
                    'bank_account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                    'reference_id' => $referenceId,
                ]);

            if ($response->failed()) {
                Log::error('Xendit bank validation failed', [
                    'bank_code' => $bankCode,
                    'account_number' => $accountNumber,
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'status' => 'FAILED',
                    'bank_account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                    'reference_id' => $referenceId,
                    'result' => [
                        'is_found' => false,
                        'account_holder_name' => null,
                        'is_virtual_account' => false,
                    ],
                    'error' => $response->json(),
                ], $response->status());
            }

            $data = $response->json();

            return response()->json([
                'status' => data_get($data, 'status', 'COMPLETED'),
                'bank_account_number' => data_get($data, 'bank_account_number', $accountNumber),
                'bank_code' => data_get($data, 'bank_code', $bankCode),
                'created' => data_get($data, 'created', $now),
                'updated' => data_get($data, 'updated', $now),
                'id' => data_get($data, 'id', 'bknv_' . Str::random(24)),
                'reference_id' => $referenceId,
                'result' => [
                    'is_found' => data_get($data, 'result.is_found', true),
                    'account_holder_name' => data_get($data, 'result.account_holder_name', 'UNKNOWN'),
                    'is_virtual_account' => data_get($data, 'result.is_virtual_account', false),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Xendit bank validation exception', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'FAILED',
                'bank_account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'reference_id' => $referenceId,
                'result' => [
                    'is_found' => false,
                ],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createPayout(string $forUserId, string $idEmpotencyKey = null, array $payload)
    {
        try {
            $response = $this->xendit->createPayout($forUserId, $idEmpotencyKey, $payload);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payout gagal dibuat',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            $payoutData = $response->json();

            $channelProperties = $payoutData['channel_properties'] ?? [];
            $receiptNotification = $payoutData['receipt_notification'] ?? [];

            XenditPayout::create([
                'payout_id'            => $payoutData['id'] ?? null,
                'reference_id'         => $payoutData['reference_id'] ?? null,
                'business_id'          => $payoutData['business_id'] ?? null,
                'amount'               => $payoutData['amount'] ?? null,
                'currency'             => $payoutData['currency'] ?? null,
                'channel_code'         => $payoutData['channel_code'] ?? null,
                'status'               => $payoutData['status'] ?? null,
                'description'          => $payoutData['description'] ?? null,
                'failure_code'         => $payoutData['failure_code'] ?? null,
                'account_holder_name'  => $channelProperties['account_holder_name'] ?? null,
                'account_number'       => $channelProperties['account_number'] ?? null,
                'account_type'         => $channelProperties['account_type'] ?? null,
                'email_to'             => $receiptNotification['email_to'] ?? null,
                'email_cc'             => $receiptNotification['email_cc'] ?? null,
                'email_bcc'            => $receiptNotification['email_bcc'] ?? null,
                'metadata'             => $payoutData['metadata'] ?? null,
                'estimated_arrival_time' => $payoutData['estimated_arrival_time'] ?? null,
                'created_xendit'       => $payoutData['created'] ?? null,
                'updated_xendit'       => $payoutData['updated'] ?? null,
                'raw_response'         => $payoutData,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payout berhasil dibuat',
                'data'    => $payoutData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Gagal membuat payout',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPayoutById(string $forUserId, string $payoutId = null)
    {
        try {
            $response = $this->xendit->getPayoutById($forUserId, $payoutId);;

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'data'  => null,
                    'message' => 'Gagal mengambil data payout',
                    'errors'  => $response->json(),
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'message' => 'Data payout berhasil diambil',
                'data'    => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'   => null,
                'message' => 'Terjadi kesalahan internal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
