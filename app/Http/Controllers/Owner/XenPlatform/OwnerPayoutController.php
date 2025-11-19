<?php

namespace App\Http\Controllers\Owner\XenPlatform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\BalanceController;
use App\Http\Controllers\PaymentGateway\Xendit\PayoutController as XenditPayoutController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use App\Models\Owner;
use App\Models\Xendit\XenditPayout;
use App\Services\XenditService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OwnerPayoutController extends Controller
{
    protected $xendit;
    protected $xenditPayout;
    protected $xenditSubAccount;
    protected $xenditBalance;
    protected $xenditAccountId;
    protected $userAuth;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
        $this->xenditPayout = new XenditPayoutController($xendit);
        $this->xenditSubAccount = new SubAccountController($xendit);
        $this->xenditBalance = new BalanceController($xendit);
        $this->userAuth = Auth::guard('owner')->user();

        $this->xenditAccountId = Owner::with('xenditSubAccount')
            ->where('id', $this->userAuth->id)
            ->first()
            ->xenditSubAccount->xendit_user_id ?? null;

        if (is_null($this->xenditAccountId)) {
            abort(404, 'Xendit account not found.');
        }
    }

    public function index(Request $request)
    {
        $payoutChannelsResponse = $this->xenditPayout->getPayoutChannels();
        $payoutChannelsData = $payoutChannelsResponse->getData(true);
        $payoutChannels = collect($payoutChannelsData['data'])
            ->where('currency', 'IDR');

        $subAccountResponse = $this->xenditSubAccount->getSubAccountById($this->xenditAccountId);
        $subAccounts = $subAccountResponse->getData(true);
        $accounts = $subAccounts['data'] ?? [];
        $balanceResponse = $this->xenditBalance->getBalance($accounts['id'], $request);
        $balanceData = $balanceResponse->getData(true);

        $accounts['balance'] = $balanceData['success']
            ? $balanceData['data']['balance']
            : null;

        return view('pages.owner.xen_platform.payout.index',
            [
                'accounts' => $accounts,
                'payoutChannels' => $payoutChannels
            ]);
    }

    public function validateBankAccount(Request $request)
    {
        $validateBankResponse = $this->xenditPayout->validateBankAccount($request);
        $validateBankData = $validateBankResponse->getData(true);

        return $validateBankData;
    }

    public function createPayout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_id' => 'required|string|max:255',
            'channel_code' => 'required|string',
            'channel_properties.account_number' => 'required|string',
            'channel_properties.account_holder_name' => 'required|string',
            'channel_properties.account_type' => 'nullable|string',
            'amount' => 'required|numeric|min:10000',
            'currency' => ['nullable', 'string', Rule::in(['IDR', 'USD'])],
            'description' => 'nullable|string|max:255',
            'for_user_id' => 'nullable|string',
            'recipient_email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $referenceId = $validated['reference_id'];
            $forUserId = $validated['for_user_id'] ?? null;

            $emailTo = [];
            if (!empty($validated['recipient_email'])) {
                $emails = array_map('trim', explode(',', $validated['recipient_email']));
                $emailTo = array_filter($emails, function($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
                });
            }

            if (empty($emailTo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada email yang valid. Harap masukkan minimal satu alamat email yang valid.',
                    'errors' => [
                        'recipient_email' => ['Format email tidak valid. Gunakan format: email1@example.com, email2@example.com']
                    ],
                ], 422);
            }

            $payload = [
                "reference_id" => $referenceId,
                "channel_code" => $validated['channel_code'],
                "channel_properties" => [
                    "account_number" => $validated['channel_properties']['account_number'],
                    "account_holder_name" => $validated['channel_properties']['account_holder_name'],
                ],
                "amount" => (int)$validated['amount'],
                "currency" => $validated['currency'] ?? "IDR",
                "description" => $validated['description'] ?? "Disbursement from CavaPOS",
                "receipt_notification" => [
                    "email_to" => $emailTo,
                    "email_cc" => [],
                    "email_bcc" => [],
                ],
                "metadata" => [
                    "disbursement_type" => 'single_payout',
                    "initiated_at" => now()->toISOString(),
                    "system_source" => 'cavapos_owner_panel',
                    ],
            ];

            $payoutResponse = $this->xenditPayout->createPayout($forUserId, $referenceId, $payload);
            $payoutData = $payoutResponse->getData(true);

            $success = $payoutData['success'] ?? false;
            $status = $payoutData['status'] ?? null;

            if ($success || $status === 'COMPLETED' || $status === 'PENDING') {
                $message = "Disbursement berhasil dibuat! Reference ID: " . $referenceId;

                $emailInfo = '';
                if (count($emailTo) > 1) {
                    $emailInfo = ' Receipt akan dikirim ke ' . count($emailTo) . ' alamat email.';
                } elseif (count($emailTo) === 1) {
                    $emailInfo = ' Receipt akan dikirim ke ' . $emailTo[0];
                }

                return response()->json([
                    'success' => true,
                    'message' => $message . $emailInfo,
                    'reference_id' => $referenceId,
                ], 200);
            }

            $errorMessage = $payoutData['failure_reason'] ??
                $payoutData['message'] ??
                'Gagal memproses disbursement. Status: ' . ($status ?? 'Unknown');

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 400);

        } catch (Exception $e) {
            Log::error('Payout creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'reference_id' => $request->reference_id ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error: Payout gagal dibuat. Silakan coba lagi.',
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $accountId = $this->xenditAccountId;

        $query = XenditPayout::with('subAccount')
            ->orderByDesc('id');

        $query->whereHas('subAccount', function ($q) use ($accountId) {
            $q->where('business_id', $accountId);
        });

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $dateFromCarbon = Carbon::parse($dateFrom)->startOfDay();
            $query->where('created_xendit', '>=', $dateFromCarbon);
        }

        if ($dateTo) {
            $dateToCarbon = Carbon::parse($dateTo)->endOfDay();
            $query->where('created_xendit', '<=', $dateToCarbon);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $searchTerm = '%' . $search . '%';

                $q->where('reference_id', 'like', $searchTerm)
                    ->orWhere('amount', 'like', $searchTerm)
                    ->orWhere('currency', 'like', $searchTerm)
                    ->orWhere('channel_code', 'like', $searchTerm)
                    ->orWhere('status', 'like', $searchTerm)
                    ->orWhere('account_holder_name', 'like', $searchTerm)
                    ->orWhere('account_number', 'like', $searchTerm);

                $q->orWhereHas('subAccount', function ($sq) use ($searchTerm) {
                    $sq->where('business_name', 'like', $searchTerm);
                });
            });
        }

        $data = $query->paginate(10);

        return view('pages.owner.xen_platform.payout.display', ['data' => $data])->render();
    }

    public function getPayout($payoutId)
    {
        $businessId = $this->xenditAccountId;
        $payoutResponse = $this->xenditPayout->getPayoutById($businessId, $payoutId);
        $payoutData = $payoutResponse->getData(true);
        $payout = $payoutData['data'] ?? $payoutData;

        if (!is_array($payout)) {
            $payout = (array) $payout;
        }

        $connectorReference = XenditPayout::where('payout_id', $payoutId)->first();
        $payout['connector_reference'] = $connectorReference->connector_reference ?? null;

        return view('pages.owner.xen_platform.payout.detail.index', ['data' => $payout]);
    }
}