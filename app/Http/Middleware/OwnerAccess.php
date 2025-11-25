<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login sebagai owner
        if (Auth::guard('owner')->check()) {
            $owner = Auth::guard('owner')->user();

            // Cek apakah akun owner tidak aktif
            if (!$owner->is_active) {
                // Simpan data deactivation ke session untuk digunakan di view
                session([
                    'owner_deactivation_data' => [
                        'reason' => $owner->deactivation_reason,
                        'deactivated_at' => $owner->deactivated_at
                    ]
                ]);

                // Jika request bukan ke halaman inactive-owners, redirect ke sana
                if (!$request->routeIs('owner.user-owner.inactive-owners')) {
                    return redirect()->route('owner.user-owner.inactive-owners');
                }
            } else {
                // Hapus session data jika akun sudah aktif
                session()->forget('owner_deactivation_data');

                // Jika akun aktif tapi mencoba akses halaman inactive-owners, redirect ke dashboard
                if ($request->routeIs('owner.user-owner.inactive-owners')) {
                    return redirect()->route('owner.user-owner.dashboard');
                }
            }
        }

        return $next($request);
    }
}
