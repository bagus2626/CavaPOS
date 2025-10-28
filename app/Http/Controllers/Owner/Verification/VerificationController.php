<?php

namespace App\Http\Controllers\Owner\Verification;

use App\Http\Controllers\Controller;
use App\Models\Owner\BusinessCategory;
use App\Models\Owner\OwnerVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerificationController extends Controller
{

    public function index()
    {
        $owner = auth()->guard('owner')->user();

        $businessCategories = BusinessCategory::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Ambil data verifikasi terakhir yang ditolak
        $latestVerification = null;
        if ($owner->verification_status === 'rejected') {
            $latestVerification = OwnerVerification::where('owner_id', $owner->id)
                ->where('status', 'rejected')
                ->latest()
                ->first();

            // Decrypt KTP number jika ada
            if ($latestVerification) {
                try {
                    $latestVerification->ktp_number_decrypted = Crypt::decryptString($latestVerification->ktp_number);
                } catch (\Exception $e) {
                    $latestVerification->ktp_number_decrypted = '';
                    Log::warning('Failed to decrypt KTP number: ' . $e->getMessage());
                }
            }
        }

        return view('pages.owner.verification.index', compact('businessCategories', 'owner', 'latestVerification'));
    }

    public function store(Request $request)
    {
        $owner = $request->user('owner');

        // Cek apakah ini resubmit (rejected) atau submit pertama kali
        $isResubmit = $owner->verification_status === 'rejected';

        // Ambil verifikasi terakhir jika resubmit
        $latestVerification = null;
        if ($isResubmit) {
            $latestVerification = OwnerVerification::where('owner_id', $owner->id)
                ->latest()
                ->first();
        }

        // KTP photo dan business logo optional jika resubmit dan ada foto lama
        $ktpPhotoRule = ($isResubmit && $latestVerification && $latestVerification->ktp_photo_path)
            ? 'nullable|image|mimes:jpeg,jpg,png|max:1024'
            : 'required|image|mimes:jpeg,jpg,png|max:1024';

        $validatedData = $request->validate([
            'owner_name' => 'required|string|min:3|max:255',
            'owner_phone' => ['required', 'string', 'min:10', 'max:15', 'regex:/^(08|62)\d{8,12}$/'],
            // owner_email dihapus dari validasi
            'ktp_number' => 'required|string|digits:16',
            'ktp_photo' => $ktpPhotoRule,
            'business_name' => 'required|string|min:3|max:255',
            'business_category_id' => 'required|exists:business_categories,id',
            'business_address' => 'required|string|min:10',
            'business_phone' => ['required', 'string', 'min:10', 'max:15', 'regex:/^(08|62)\d{8,12}$/'],
            'business_email' => 'nullable|email|max:255',
            'business_logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'terms' => 'accepted',
        ]);

        DB::beginTransaction();

        try {
            if (in_array($owner->verification_status, ['pending', 'approved'])) {
                return redirect()->route('owner.user-owner.dashboard')
                    ->with('warning', 'Anda tidak dapat mengirimkan ulang data verifikasi.');
            }

            // Handle KTP Photo
            $ktpPhotoPath = null;
            if ($request->hasFile('ktp_photo')) {
                // Upload foto baru
                $ktpPhotoPath = $request->file('ktp_photo')->store('ktp_verifications', 'local');
            } else if ($latestVerification && $latestVerification->ktp_photo_path) {
                // Gunakan foto lama jika tidak upload baru
                $ktpPhotoPath = $latestVerification->ktp_photo_path;
            }

            // Handle Business Logo
            $businessLogoPath = null;
            if ($request->hasFile('business_logo')) {
                // Upload logo baru
                $businessLogoPath = $request->file('business_logo')->store('business_logos', 'public');
            } else if ($latestVerification && $latestVerification->business_logo_path) {
                // Gunakan logo lama jika tidak upload baru
                $businessLogoPath = $latestVerification->business_logo_path;
            }

            $encryptedKtpNumber = Crypt::encryptString($validatedData['ktp_number']);

            OwnerVerification::create([
                'owner_id' => $owner->id,
                'owner_name' => $validatedData['owner_name'],
                'owner_phone' => $validatedData['owner_phone'],
                'owner_email' => $owner->email, // Gunakan email dari akun owner
                'ktp_number' => $encryptedKtpNumber,
                'ktp_photo_path' => $ktpPhotoPath,
                'business_name' => $validatedData['business_name'],
                'business_category_id' => $validatedData['business_category_id'],
                'business_address' => $validatedData['business_address'],
                'business_phone' => $validatedData['business_phone'],
                'business_email' => $validatedData['business_email'],
                'business_logo_path' => $businessLogoPath,
                'status' => 'pending',
            ]);

            $owner->verification_status = 'pending';
            $owner->save();

            DB::commit();

            return redirect()->route('owner.user-owner.verification.status')
                ->with('success', 'Data verifikasi berhasil dikirim! Mohon tunggu proses review.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Verification Submission Failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim data. Mohon coba lagi.')
                ->withInput();
        }
    }

    public function status(Request $request)
    {
        $owner = auth()->guard('owner')->user();

        $verification = OwnerVerification::with('businessCategory')
            ->where('owner_id', $owner->id)
            ->latest()
            ->first();

        if (!$verification) {
            return redirect()->route('owner.user-owner.verification.index')
                ->with('info', 'Silakan lengkapi data verifikasi terlebih dahulu.');
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => $verification->status,
                'reviewed_at' => $verification->reviewed_at
            ]);
        }

        try {
            $verification->ktp_number_decrypted = Crypt::decryptString($verification->ktp_number);
        } catch (\Exception $e) {
            $verification->ktp_number_decrypted = '****************';
            Log::warning('Failed to decrypt KTP number for owner ' . $owner->id . ': ' . $e->getMessage());
        }

        return view('pages.owner.verification.verification-status', compact('verification'));
    }

    public function showKtpImage(): StreamedResponse
    {
        $owner = auth()->guard('owner')->user();

        $verification = OwnerVerification::where('owner_id', $owner->id)
            ->latest()
            ->firstOrFail();

        if (!Storage::disk('local')->exists($verification->ktp_photo_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($verification->ktp_photo_path);
    }
}
