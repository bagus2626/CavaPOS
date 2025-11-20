<?php

namespace App\Http\Controllers\Admin\SendPayment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\BalanceController;
use App\Http\Controllers\PaymentGateway\Xendit\PayoutController as XenditPayoutController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use App\Models\Xendit\XenditPayout;
use App\Services\XenditService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayoutController extends Controller
{
    protected $xendit;
    protected $xenditPayout;
    protected $xenditSubAccount;
    protected $xenditBalance;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
        $this->xenditPayout = new XenditPayoutController($xendit);
        $this->xenditSubAccount = new SubAccountController($xendit);
        $this->xenditBalance = new BalanceController($xendit);
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'need-validation');
        $payoutChannelsResponse = $this->xenditPayout->getPayoutChannels();
        $payoutChannelsData = $payoutChannelsResponse->getData(true);
        $payoutChannels = collect($payoutChannelsData['data'])
            ->where('currency', 'IDR');

        $subAccountResponse = $this->xenditSubAccount->getSubAccounts();
        $subAccounts = $subAccountResponse->getData(true);
        $accounts = $subAccounts['data']['data'] ?? [];
        foreach ($accounts as &$account) {
            $balanceResponse = $this->xenditBalance->getBalance($account['id'], $request);
            $balanceData = $balanceResponse->getData(true);

            $account['balance'] = $balanceData['success']
                ? $balanceData['data']['balance']
                : null;
        }

        return view('pages.admin.send-payment.disbursement.index',
            [
                'tab' => $tab,
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
            'recipient_email' => 'nullable|email',
            'metadata' => 'nullable|array',
            'metadata.my_custom_id' => 'nullable|string|max:255',
            'metadata.my_custom_order_id' => 'nullable|string|max:255',
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
                    "email_to" => [$validated['recipient_email'] ?? 'ardhinata46@gmail.com'],
                    "email_cc" => [],
                    "email_bcc" => [],
                ],
                "metadata" => [
                    "my_custom_id" => $validated['metadata']['my_custom_id'] ?? 'merchant-' . uniqid(),
                    "my_custom_order_id" => $validated['metadata']['my_custom_order_id'] ?? 'order-' . uniqid(),
                ],
            ];

            $payoutResponse = $this->xenditPayout->createPayout($forUserId, $referenceId, $payload);
            $payoutData = $payoutResponse->getData(true);
            $success = $payoutData['success'] ?? null;

            if ($success) {
                $message = "Disbursement berhasil dibuat! Reference ID: " . $referenceId;
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'reference_id' => $referenceId,
                ], 200);
            }

            $errorMessage = $payoutData['failure_reason'] ?? $payoutData['message'] ?? 'Gagal memproses disbursement. Status API: ' . ($status ?? 'Unknown');
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage,
            ], 400);

        } catch (Exception $e) {
            Log::error('Payout creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error: Payout gagal dibuat. Silakan coba lagi. Detail: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = XenditPayout::with('subAccount')
            ->orderByDesc('id');

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

        return view('pages.admin.send-payment.disbursement.display', ['data' => $data])->render();
    }

    public function getPayout($businessId, $payoutId)
    {
        $payoutResponse = $this->xenditPayout->getPayoutById($businessId, $payoutId);
        $payoutData = $payoutResponse->getData(true);
        $payout = $payoutData['data'] ?? $payoutData;

        if (!is_array($payout)) {
            $payout = (array) $payout;
        }

        $connectorReference = XenditPayout::where('payout_id', $payoutId)->first();
        $payout['connector_reference'] = $connectorReference->connector_reference ?? null;

        return view('pages.admin.send-payment.disbursement.detail.index', ['data' => $payout]);
    }
}