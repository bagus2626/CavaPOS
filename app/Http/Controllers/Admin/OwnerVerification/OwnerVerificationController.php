<?php

namespace App\Http\Controllers\Admin\OwnerVerification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGateway\Xendit\SubAccountController;
use App\Jobs\SendEmailVerification;
use App\Models\Owner;
use App\Models\Owner\Businesses;
use App\Models\Owner\OwnerProfile;
use App\Models\Xendit\XenditSubAccount;
use App\Services\XenditService;
use Illuminate\Http\Request;
use App\Models\Owner\OwnerVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OwnerVerificationController extends Controller
{
    protected $xendit;
    protected $xenditSubAccount;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
        $this->xenditSubAccount = new SubAccountController($xendit);
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            $status = 'pending';
        }

        $pendingCount = OwnerVerification::where('status', 'pending')->count();
        $approvedCount = OwnerVerification::where('status', 'approved')->count();
        $rejectedCount = OwnerVerification::where('status', 'rejected')->count();
        $totalCount = OwnerVerification::count();

        $verifications = OwnerVerification::with(['owner.xenditSubAccount', 'owner.latestSplitRule'])
            ->where('status', $status)
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->appends(['status' => $status]);

        return view('pages.admin.owner.owner-verification', compact(
            'verifications',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount'
        ));
    }

    public function show($id)
    {
        $verification = OwnerVerification::findOrFail($id);

        try {
            $verification->ktp_number_decrypted = Crypt::decryptString($verification->ktp_number);
        } catch (\Exception $e) {
            $verification->ktp_number_decrypted = '****************';
            Log::warning('Failed to decrypt KTP number for owner ' . $id . ': ' . $e->getMessage());
        }

        return view('pages.admin.owner.owner-verification-detail', compact('verification'));
    }

public function approve($id)
    {

        try {
            DB::beginTransaction();

            $verification = OwnerVerification::findOrFail($id);


            if ($verification->status !== 'pending') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Verification already processed.');
            }

            $verification->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $ownerProfile = OwnerProfile::updateOrCreate(
                ['owner_id' => $verification->owner_id],
                [
                    'ktp_number' => $verification->ktp_number,
                    'ktp_photo_path' => $verification->ktp_photo_path,
                ]
            );

            $business = Businesses::create([
                'owner_id' => $verification->owner_id,
                'business_category_id' => $verification->business_category_id,
                'name' => $verification->business_name,
                'address' => $verification->business_address,
                'phone' => $verification->business_phone,
                'email' => $verification->business_email,
                'logo_path' => $verification->business_logo_path,
                'is_active' => true,
            ]);

            $owner = Owner::findOrFail($verification->owner_id);
            $owner->update([
                'verification_status' => 'approved',
                'approved_at' => now(),
                'name' => $verification->owner_name,
                'phone_number' => $verification->owner_phone,
            ]);

            DB::commit();

            SendEmailVerification::dispatch($owner, $verification)->onQueue('email');

            return redirect()->route('admin.owner-verification.show', $id)
                ->with('success', 'Verification approved successfully! Business has been created.');
        } catch (\Exception $e) {
            DB::rollBack();


            return redirect()->back()->with('error', 'Failed to approve verification: '. $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $verification = OwnerVerification::findOrFail($id);

            // Validasi apakah sudah diproses
            if ($verification->status !== 'pending') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Verification already processed.');
            }

            // Update status verification
            $verification->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            // Update Owner status
            $owner = Owner::findOrFail($verification->owner_id);
            $owner->update([
                'verification_status' => 'rejected',
                'approved_at' => null,
            ]);

            DB::commit();

            SendEmailVerification::dispatch($owner, $verification)->onQueue('email');

            return redirect()->route('admin.owner-verification.show', $id)
                ->with('success', 'Verification rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== REJECTION FAILED ===', [
                'verification_id' => $id,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
            ]);

            return redirect()->back()->with('error', 'Failed to reject verification: ' . $e->getMessage());
        }
    }

    public function showKtpImage($id)
    {
        $verification = OwnerVerification::findOrFail($id);

        if (!$verification->ktp_photo_path || !Storage::disk('local')->exists($verification->ktp_photo_path)) {
            abort(404, 'File KTP tidak ditemukan.');
        }

        return Storage::disk('local')->response($verification->ktp_photo_path);
    }

    public function registerXenditAccount(Request $request)
    {
        try {
            $validated = $request->validate([
                'partner_id' => 'required|integer|exists:owners,id',
                'partner_email' => 'required|email',
                'business_name' => 'required|string|max:255',
                'account_type' => 'required|in:OWNED,MANAGED'
            ], [
                'partner_id.required' => 'Partner ID is required',
                'partner_id.integer' => 'Partner ID must be a valid integer',
                'partner_id.exists' => 'Partner not found',
                'partner_email.required' => 'Partner email is required',
                'partner_email.email' => 'Partner email must be a valid email address',
                'business_name.required' => 'Business name is required',
                'business_name.string' => 'Business name must be a string',
                'business_name.max' => 'Business name may not be greater than 255 characters',
                'account_type.required' => 'Account type is required',
                'account_type.in' => 'Account type must be either OWNED or MANAGED'
            ]);

            $existingAccount = XenditSubAccount::where('partner_id', $validated['partner_id'])->first();

            if ($existingAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partner already has a registered Xendit account'
                ], 422);
            }

            $partnerId = $validated['partner_id'];
            $payload = [
                'email' => $validated['partner_email'],
                'type' => $validated['account_type'],
                'public_profile' => [
                    'business_name' => $validated['business_name'],
                ],
            ];

            $subAccountResponse = $this->xenditSubAccount->createAccount($payload, $partnerId);
            $subAccounts = $subAccountResponse->getData(true);

            $success = $subAccounts['success'] ?? null;
            if (!$success) {
                $message = $subAccounts['errors']['message'] ?? 'Unknown error from Xendit API.';
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);

            }

            return response()->json([
                'success' => true,
                'message' => 'Xendit account registration initiated successfully',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Xendit account registration failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to register Xendit account: ' . $e->getMessage()
            ], 500);
        }
    }
}
