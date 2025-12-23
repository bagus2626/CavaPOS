<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PartnerAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah sedang di halaman suspended
        if ($request->routeIs('partner.account.suspended')) {
            $suspendedData = session('partner_suspended_data');

            // Jika tidak ada data suspended, redirect ke home
            if (!$suspendedData) {
                if (Auth::guard('web')->check()) {
                    $partner = Auth::guard('web')->user();
                    $owner = $partner->owner ?? null;

                    if ($owner && $owner->is_active && $partner->is_active_admin && $partner->is_active) {
                        return redirect()->route('partner.dashboard');
                    }
                }

                return redirect('/');
            }

            // Cek apakah akun sudah aktif kembali
            if (Auth::guard('web')->check()) {
                $partner = Auth::guard('web')->user();
                $owner = $partner->owner ?? null;

                // Jika owner dan partner sudah aktif, redirect ke dashboard
                if ($owner && $owner->is_active && $partner->is_active_admin && $partner->is_active) {
                    session()->forget('partner_suspended_data');
                    return redirect()->route('partner.dashboard');
                }
            }

            return $next($request);
        }

        // Cek status akun untuk partner yang login
        if (Auth::guard('web')->check()) {
            $partner = Auth::guard('web')->user();
            $owner = $partner->owner ?? null;

            // Cek status owner
            if ($owner && !$owner->is_active) {
                session([
                    'partner_suspended_data' => [
                        'user_type' => 'partner',
                        'suspended_by' => 'admin',
                        'owner_name' => $owner->name,
                        'partner_name' => $partner->name
                    ]
                ]);
                return redirect()->route('partner.account.suspended');
            }

            // Cek status partner (suspended oleh admin)
            if (!$partner->is_active_admin) {
                session([
                    'partner_suspended_data' => [
                        'user_type' => 'partner',
                        'suspended_by' => 'admin',
                        'owner_name' => $owner ? $owner->name : null,
                        'partner_name' => $partner->name,
                        'deactivation_reason' => $partner->deactivation_reason
                    ]
                ]);
                return redirect()->route('partner.account.suspended');
            }

            // Cek status partner (suspended oleh owner)
            if (!$partner->is_active) {
                session([
                    'partner_suspended_data' => [
                        'user_type' => 'partner',
                        'suspended_by' => 'owner',
                        'partner_name' => $partner->name,
                        'owner_name' => $owner ? $owner->name : null
                    ]
                ]);
                return redirect()->route('partner.account.suspended');
            }
        }

        return $next($request);
    }
}
