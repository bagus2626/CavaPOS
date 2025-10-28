<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerVerification
{

    public function handle(Request $request, Closure $next): Response
    {
        $owner = Auth::guard('owner')->user();

        if (!$owner) {
            return redirect()->route('owner.login');
        }

        $status = $owner->verification_status;
        $currentRoute = $request->route()->getName();

        // Routes yang selalu diizinkan (logout, dll)
        $alwaysAllowedRoutes = [
            'owner.logout',
            'owner.user-owner.verification.ktp-image',
        ];

        if (in_array($currentRoute, $alwaysAllowedRoutes)) {
            return $next($request);
        }

        // Definisi route groups
        $verificationFormRoutes = [
            'owner.user-owner.verification.index',
            'owner.user-owner.verification.store',
        ];

        $verificationStatusRoutes = [
            'owner.user-owner.verification.status',
        ];

        $allVerificationRoutes = array_merge($verificationFormRoutes, $verificationStatusRoutes);

        $dashboardAndOtherRoutes = !in_array($currentRoute, $allVerificationRoutes);

        // Logic berdasarkan status
        switch ($status) {
            case 'unverified':
                // Hanya bisa akses form verifikasi
                if ($dashboardAndOtherRoutes || in_array($currentRoute, $verificationStatusRoutes)) {
                    return redirect()
                        ->route('owner.user-owner.verification.index')
                        ->with('warning', 'Silakan lengkapi data verifikasi terlebih dahulu.');
                }
                break;

            case 'pending':
                // Hanya bisa akses halaman status
                if ($dashboardAndOtherRoutes || in_array($currentRoute, $verificationFormRoutes)) {
                    return redirect()
                        ->route('owner.user-owner.verification.status')
                        ->with('info', 'Data verifikasi Anda sedang dalam proses review. Mohon tunggu konfirmasi.');
                }
                break;

            case 'rejected':
                // Bisa akses form dan status verifikasi saja
                if ($dashboardAndOtherRoutes) {
                    return redirect()
                        ->route('owner.user-owner.verification.status')
                        ->with('warning', 'Verifikasi Anda ditolak. Silakan submit ulang dengan data yang benar.');
                }
                break;

            case 'approved':
                // Bisa akses semua halaman kecuali halaman verifikasi
                if (in_array($currentRoute, $allVerificationRoutes)) {
                    return redirect()
                        ->route('owner.user-owner.dashboard')
                        ->with('info', 'Akun Anda sudah terverifikasi.');
                }
                break;

            default:
                // Status tidak dikenal, redirect ke form verifikasi
                return redirect()
                    ->route('owner.user-owner.verification.index')
                    ->with('error', 'Status verifikasi tidak valid.');
        }

        return $next($request);
    }
}
