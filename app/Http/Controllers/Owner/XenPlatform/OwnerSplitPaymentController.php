<?php

namespace App\Http\Controllers\Owner\XenPlatform;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\SplitRuleController;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use App\Models\Owner;
use App\Models\Xendit\XenditSplitTransaction;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OwnerSplitPaymentController extends Controller
{
    protected $xenditSubAccount;
    protected $xenditSpliRules;
    protected $accountMasterId;
    protected $xenditAccountId;
    protected $userAuth;

    public function __construct(XenditService $xendit)
    {
        $this->xenditSubAccount = new SubAccountController($xendit);
        $this->xenditSpliRules = new SplitRuleController($xendit);
        $this->accountMasterId = config('services.xendit.account_master_id');

        $this->userAuth = Auth::guard('owner')->user();

        $this->xenditAccountId = Owner::with('xenditSubAccount')
            ->where('id', $this->userAuth->id)
            ->first()
            ->xenditSubAccount->xendit_user_id ?? null;

        if (is_null($this->xenditAccountId)) {
            abort(404, 'Xendit account not found.');
        }
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

        return view('pages.owner.xen_platform.split-payments.index', compact('accounts'));
    }

    public function getSplitPayments(Request $request)
    {
        $accountId = $this->xenditAccountId;
        $query = XenditSplitTransaction::with([
            'invoice',
            'splitRule',
            'sourceAccount',
            'destinationAccount',
            'splitRule.owner.xenditSubAccount'
        ])->orderByDesc('id');

        $query->whereHas('sourceAccount', function ($q) use ($accountId) {
            $q->where('source_account_id', $accountId);
        });

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

        return view('pages.owner.xen_platform.split-payments.table', [
            'splitTransactions' => $splitTransactionsPaginator,
            'oldRequest' => $request->all()
        ])->render();
    }
}