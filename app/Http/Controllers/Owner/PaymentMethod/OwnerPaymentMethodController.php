<?php

namespace App\Http\Controllers\Owner\PaymentMethod;

use App\Http\Controllers\Controller;
use App\Models\Owner\OwnerManualPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OwnerPaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $ownerId = Auth::guard('owner')->id();
        $search = $request->query('search');

        $paymentMethods = OwnerManualPayment::where('owner_id', $ownerId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('payment_type', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%")
                    ->orWhere('provider_account_name', 'like', "%{$search}%")
                    ->orWhere('additional_info', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // ⭐ penting biar pagination inget search

        return view('pages.owner.payment-method.index', compact('paymentMethods', 'search'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.owner.payment-method.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();

        $validated = $request->validate([
            'payment_type' => 'required|in:manual_tf,manual_ewallet,manual_qris',
            'provider_name' => 'required|string|max:255',
            'provider_account_name' => 'required|string|max:255',
            'is_active' => 'required|boolean',

            'provider_account_no' => 'required_if:payment_type,manual_tf,manual_ewallet|nullable|string|max:255',
            'images' => 'required_if:payment_type,manual_qris|nullable|image|mimes:jpg,jpeg,png,webp',

            'additional_info' => 'nullable|string',
        ]);

        $qrisPath = null;

        if ($validated['payment_type'] === 'manual_qris' && $request->hasFile('images')) {

            $file = $request->file('images');

            $manager = new ImageManager(['driver' => 'gd']); // ✅ GD
            $image = $manager->make($file->getRealPath());

            // Resize aman (maks 1200px, tidak upsize)
            $image->resize(1200, 1200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $targetBytes = 100 * 1024; // 100KB
            $quality = 85;
            $minQuality = 40;

            do {
                $encoded = $image->encode('jpg', $quality); // ✅ JPEG
                $binary = (string) $encoded;

                if (strlen($binary) <= $targetBytes) {
                    break;
                }

                $quality -= 5;
            } while ($quality >= $minQuality);

            $filename = Str::uuid() . '.jpg';
            $relativePath = 'payment_method/qris/' . $filename;

            Storage::disk('public')->put($relativePath, $binary);

            $qrisPath = $relativePath;
        }

        OwnerManualPayment::create([
            'owner_id'             => $ownerId,
            'payment_type'         => $validated['payment_type'],
            'provider_name'        => $validated['provider_name'],
            'provider_account_name'=> $validated['provider_account_name'],
            'provider_account_no'  => $validated['provider_account_no'] ?? null,
            'qris_image_url'       => $qrisPath,
            'additional_info'      => $validated['additional_info'] ?? null,
            'is_active'            => $validated['is_active'],
        ]);

        return redirect()
            ->route('owner.user-owner.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OwnerManualPayment $paymentMethod)
    {
        $this->authorizeOwner($paymentMethod);

        return view('pages.owner.payment-method.show', compact('paymentMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OwnerManualPayment $paymentMethod)
    {
        // Pastikan owner hanya bisa edit miliknya sendiri
        $ownerId = Auth::guard('owner')->id();
        abort_if($paymentMethod->owner_id !== $ownerId, 403);

        return view('pages.owner.payment-method.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OwnerManualPayment $paymentMethod)
    {
        $ownerId = Auth::guard('owner')->id();
        abort_if($paymentMethod->owner_id !== $ownerId, 403);

        $validated = $request->validate([
            'payment_type' => 'required|in:manual_tf,manual_ewallet,manual_qris',
            'provider_name' => 'required|string|max:255',
            'provider_account_name' => 'required|string|max:255',
            'is_active' => 'required|boolean',

            'provider_account_no' => 'required_if:payment_type,manual_tf,manual_ewallet|nullable|string|max:255',
            'images' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'additional_info' => 'nullable|string',

            // flag dari hidden input (kalau user klik remove)
            'remove_qris' => 'nullable|in:0,1',
        ]);

        $isQris = $validated['payment_type'] === 'manual_qris';

        // apakah upload gambar baru?
        $hasNewImage = $request->hasFile('images');

        // apakah gambar lama masih dipertahankan?
        $hasExistingImage =
            !empty($paymentMethod->qris_image_url)
            && $request->input('remove_qris') !== '1';

        // FINAL: apakah setelah update QRIS punya gambar?
        $willHaveQrisImage = $hasNewImage || $hasExistingImage;        

        if ($isQris && !$willHaveQrisImage) {
            return back()
                ->withErrors(['images' => 'Gambar QRIS wajib diupload.'])
                ->withInput();
        }

        // Default: pakai path lama
        $qrisPath = $paymentMethod->qris_image_url;

        // Jika payment_type bukan QRIS, kita buang gambar QRIS (optional sesuai kebutuhan)
        // Kalau kamu ingin tetap simpan gambar meskipun ganti type, hapus blok ini.
        if ($validated['payment_type'] !== 'manual_qris') {
            if ($qrisPath && Storage::disk('public')->exists($qrisPath)) {
                Storage::disk('public')->delete($qrisPath);
            }
            $qrisPath = null;
        }

        // Jika QRIS: handle remove manual (klik tombol remove)
        if ($validated['payment_type'] === 'manual_qris' && ($request->input('remove_qris') === '1')) {
            if ($qrisPath && Storage::disk('public')->exists($qrisPath)) {
                Storage::disk('public')->delete($qrisPath);
            }
            $qrisPath = null;
        }

        // Jika QRIS dan ada file baru -> compress + replace
        if ($validated['payment_type'] === 'manual_qris' && $request->hasFile('images')) {

            // hapus file lama dulu (kalau ada)
            if ($qrisPath && Storage::disk('public')->exists($qrisPath)) {
                Storage::disk('public')->delete($qrisPath);
            }

            $file = $request->file('images');

            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($file->getRealPath());

            // Resize aman (maks 1200px, tidak upsize)
            $image->resize(1200, 1200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $targetBytes = 100 * 1024; // 100KB
            $quality = 85;
            $minQuality = 40;

            do {
                $encoded = $image->encode('jpg', $quality); // JPEG via GD
                $binary = (string) $encoded;

                if (strlen($binary) <= $targetBytes) break;

                $quality -= 5;
            } while ($quality >= $minQuality);

            $filename = (string) Str::uuid() . '.jpg';
            $relativePath = 'payment_method/qris/' . $filename;

            Storage::disk('public')->put($relativePath, $binary);

            $qrisPath = $relativePath;
        }

        // Jika QRIS tapi tidak upload baru, dan tidak remove -> keep $qrisPath

        $paymentMethod->update([
            'payment_type'          => $validated['payment_type'],
            'provider_name'         => $validated['provider_name'],
            'provider_account_name' => $validated['provider_account_name'],
            'provider_account_no'   => $validated['payment_type'] !== 'manual_qris' ? $validated['provider_account_no'] ?? null : null,
            'qris_image_url'        => $qrisPath,
            'additional_info'       => $validated['additional_info'] ?? null,
            'is_active'             => $validated['is_active'],
        ]);

        return redirect()
            ->route('owner.user-owner.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OwnerManualPayment $paymentMethod)
    {
        $ownerId = Auth::guard('owner')->id();

        abort_if($paymentMethod->owner_id !== $ownerId, 403);

        if (
            $paymentMethod->qris_image_url &&
            Storage::disk('public')->exists($paymentMethod->qris_image_url)
        ) {
            Storage::disk('public')->delete($paymentMethod->qris_image_url);
        }

        $paymentMethod->delete();

        return redirect()
            ->route('owner.user-owner.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }


    /**
     * Pastikan data milik owner yang login
     */
    protected function authorizeOwner(OwnerManualPayment $paymentMethod): void
    {
        if ($paymentMethod->owner_id !== Auth::guard('owner')->id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
