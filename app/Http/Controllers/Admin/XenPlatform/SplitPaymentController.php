<?php

namespace App\Http\Controllers\Admin\XenPlatform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\SplitRuleController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use App\Models\Owner;
use App\Models\Xendit\SplitRule;
use App\Models\Xendit\XenditSplitTransaction;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SplitPaymentController extends Controller
{
    protected $xenditSubAccount;
    protected $xenditSpliRules;
    protected $accountMasterId;

    public function __construct(XenditService $xendit)
    {
        $this->xenditSubAccount = new SubAccountController($xendit);
        $this->xenditSpliRules = new SplitRuleController($xendit);
        $this->accountMasterId = config('services.xendit.account_master_id');
    }

    public function index()
    {
        $accounts = XenditSubAccount::all()->map(function ($subAccount){
            return [
                'xendit_sub_account_id' => $subAccount->id,
                'xendit_user_id' => $subAccount->xendit_user_id,
                'business_name' => $subAccount->business_name,
                'type' => $subAccount->type,
                'status' => $subAccount->status,
                'country' => $subAccount->country,
            ];
        });

        return view('pages.admin.xen_platform.split-payments.index', compact('accounts'));
    }

    public function getSplitRules(Request $request)
    {
        $query = SplitRule::with('owner.business')->orderByDesc('id');

        if ($request->filled('rules_id_or_name')) {
            $value = $request->input('rules_id_or_name');
            $query->where(function ($q) use ($value) {
                $q->where('name', 'like', '%' . $value . '%')
                    ->orWhere('split_rule_id', 'like', '%' . $value . '%');
            });
        }

        if ($request->filled('business_name')) {
            $query->whereHas('owner.business', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('business_name') . '%');
            });
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        $perPage = $request->input('limit', 5);
        $perPage = max(1, (int)$perPage);

        $splitRules = $query->paginate($perPage)->withQueryString();

        $splitRules->getCollection()->transform(function ($rule) {
            $routes = $rule->routes;
            if (is_string($routes)) {
                $routes = json_decode($routes, true) ?? [];
            }

            return [
                'split_rule_id' => $rule->split_rule_id,
                'name' => $rule->name,
                'business_name' => $rule->owner->business->name ?? 'unknown',
                'description' => $rule->description,
                'created_at' => $rule->created_at?->timezone('Asia/Jakarta')->format('d M Y H:i:s'),
                'routes' => collect($routes)->map(function ($route) {
                    $subAccount = XenditSubAccount::where('xendit_user_id', $route['destination_account_id'] ?? null)->first();

                    return [
                        'flet_amount'     => $route['flat_amount'] ?? null,
                        'percent_amount'     => $route['percent_amount'] ?? null,
                        'reference'  => $route['reference_id'] ?? null,
                        'destination_account_id' => $route['destination_account_id'] ?? null,
                        'destination_account_name' => $route['destination_account_id'] == '68d0bee82b64e1b03b55ef44' ? 'PT VASTU CIPTA PERSADA' : $subAccount->account_name ?? 'unknow',
                    ];
                })->toArray(),
            ];
        });

        return view('pages.admin.xen_platform.split-payments.rules.table', compact('splitRules'));
    }

    public function getSplitPayments(Request $request)
    {
        $query = XenditSplitTransaction::with([
            'invoice',
            'splitRule',
            'sourceAccount',
            'destinationAccount',
            'splitRule.owner.xenditSubAccount'
        ])->orderByDesc('id');

        if ($request->filled('reference_id')) {
            $query->where('payment_reference_id', 'like', '%' . $request->input('reference_id') . '%');
        }

        if ($request->filled('transaction_status')) {
            $query->where('status', $request->input('transaction_status'));
        }

        if ($request->filled('business_name')) {
            $query->whereHas( 'sourceAccount', function ($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->input('business_name') . '%');
            });
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->input('date_end'));
        }

        if ($request->filled('min_split')) {
            $query->where('amount', '>=', $request->input('min_split'));
        }

        if ($request->filled('max_split')) {
            $query->where('amount', '<=', $request->input('max_split'));
        }

        $perPage = $request->input('limit', 10);
        $perPage = max(1, (int)$perPage);

        $splitTransactionsPaginator = $query->paginate($perPage)->withQueryString();

        $splitTransactionsPaginator->getCollection()->transform(function ($spliTransaction) {
            return [
                'xendit_split_payment_id'  => $spliTransaction->xendit_split_payment_id ?? 'unknown',
                'reference_id'             => $spliTransaction->payment_reference_id,
                'date_created'             => $spliTransaction->created_at,
                'source_account_id'        => $spliTransaction->source_account_id ?? 'unknown',
                'source_account_name'      => $spliTransaction->sourceAccount?->business_name ?? 'unknown',
                'transaction_amount'       => $spliTransaction->invoice?->amount ?? 0,
                'destination_account_id'   => $spliTransaction->destination_account_id ?? 'unknown',
                'destination_account_name' => $spliTransaction->destinationAccount?->business_name ?? 'PT VASTU CIPTA PERSADA',
                'total_split'              => $spliTransaction->amount,
                'date_settled'             => $spliTransaction->created_at,
                'status'                   => $spliTransaction->status,
            ];
        });

        return view('pages.admin.xen_platform.split-payments.table', [
            'splitTransactions' => $splitTransactionsPaginator,
            'oldRequest' => $request->all()
        ])->render();
    }

    public function generateReferenceId(string $prefix = 'reference'): string
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
    }

    public function createSplitRule(Request $request)
    {
        try {
            $validated = $request->validate([
                'split_rule_name' => 'required|string|max:255',
                'partner_account_id' => 'required|string',
                'split_type_option' => 'required|in:FLAT,PERCENT',
                'description' => 'nullable|string|max:500'
            ], [
                'split_rule_name.required' => 'Split rule name is required',
                'split_rule_name.string' => 'Split rule name must be a string',
                'split_rule_name.max' => 'Split rule name may not be greater than 255 characters',
                'partner_account_id.required' => 'Owner account is required',
                'split_type_option.required' => 'Split type is required',
                'split_type_option.in' => 'Split type must be FLAT or PERCENT',
                'description.string' => 'Description must be a string',
                'description.max' => 'Description may not be greater than 500 characters'
            ]);

            $splitType = $validated['split_type_option'];

            if ($splitType === 'FLAT') {
                $flatValidation = $request->validate([
                    'flat_amount' => 'required|numeric|min:1'
                ], [
                    'flat_amount.required' => 'Flat amount is required when split type is FLAT',
                    'flat_amount.numeric' => 'Flat amount must be a number',
                    'flat_amount.min' => 'Flat amount must be at least 1'
                ]);
                $validated['flat_amount'] = $flatValidation['flat_amount'];
            } else {
                $percentValidation = $request->validate([
                    'percent_amount' => 'required|numeric|min:0.01|max:100'
                ], [
                    'percent_amount.required' => 'Percent amount is required when split type is PERCENT',
                    'percent_amount.numeric' => 'Percent amount must be a number',
                    'percent_amount.min' => 'Percent amount must be at least 0.01',
                    'percent_amount.max' => 'Percent amount may not be greater than 100'
                ]);
                $validated['percent_amount'] = $percentValidation['percent_amount'];
            }

            $splitTypeData = $splitType === 'FLAT'
                ? ['flat_amount' => (int)$validated['flat_amount']]
                : ['percent_amount' => (float)$validated['percent_amount']];

            $xenditUserId = $validated['partner_account_id'];

            $payload = [
                "name" => $validated['split_rule_name'],
                "description" => $validated['description'] ?? null,
                "routes" => [
                    array_merge($splitTypeData, [
                        'destination_account_id' => $this->accountMasterId,
                        "currency" => "IDR",
                        "reference_id" => $this->generateReferenceId('split')
                    ])
                ]
            ];

            $splitRuleResponse = $this->xenditSpliRules->createSplitRule($payload);
            $splitRuleData = $splitRuleResponse->getData(true);

            if (!$splitRuleData['success'] || !isset($splitRuleData['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => $splitRuleData['message'] ?? 'Unknown error'
                ], 400);
            }

            $splitRule = $splitRuleData['data'];

            $subAccount = XenditSubAccount::where('xendit_user_id', $xenditUserId)->first();

            if (!$subAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner account not found'
                ], 404);
            }

            SplitRule::create([
                'partner_id' => $subAccount->partner_id,
                'split_rule_id' => $splitRule['id'],
                'name' => $splitRule['name'],
                'description' => $splitRule['description'],
                'routes' => json_encode($splitRule['routes']),
                'raw_response' => json_encode($splitRule),
            ]);

            $owner = Owner::find($subAccount->partner_id);
            if ($owner) {
                $owner->update([
                    'xendit_split_rule_status' => 'created',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Split Rule berhasil dibuat',
                'data' => [
                    'split_rule_id' => $splitRule['id'],
                    'name' => $splitRule['name'],
                    'partner_id' => $subAccount->partner_id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Create split rule failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create split rule: ' . $e->getMessage()
            ], 500);
        }
    }
}