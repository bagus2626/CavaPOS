<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CustomerAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $partnerSlug = $request->route('partner_slug');

        // cek apakah sedang di halaman suspended
        if ($request->routeIs('customer.account.suspended')) {
            $suspendedData = session('customer_suspended_data');

            // Jika tidak ada data suspended, redirect ke home
            if (!$suspendedData) {
                return redirect('/');
            }

            $partnerSlug = $suspendedData['partner_slug'] ?? null;
            $tableCode = $suspendedData['table_code'] ?? null;

            if ($partnerSlug && $tableCode) {
                $partner = User::where('slug', $partnerSlug)
                    ->orWhere('username', $partnerSlug)
                    ->first();

                if ($partner) {
                    $owner = $partner->owner ?? null;

                    // Jika owner, partner (admin), dan partner (owner) sudah aktif, redirect ke menu
                    if ($owner && $owner->is_active && $partner->is_active_admin && $partner->is_active) {
                        session()->forget('customer_suspended_data');
                        return redirect()->route('customer.menu.index', [
                            'partner_slug' => $partnerSlug,
                            'table_code' => $tableCode
                        ]);
                    }
                }
            }

            return $next($request);
        }

        // cek status akun untuk customer (akses via slug)
        if ($partnerSlug) {
            $partner = User::where('slug', $partnerSlug)
                ->orWhere('username', $partnerSlug)
                ->first();

            // Jika partner tidak ditemukan, lanjutkan 
            if (!$partner) {
                return $next($request);
            }

            $owner = $partner->owner ?? null;
            $tableCode = $request->route('table_code');

            // Cek status owner (admin suspend owner)
            if ($owner && !$owner->is_active) {
                session([
                    'customer_suspended_data' => [
                        'user_type' => 'customer',
                        'suspended_by' => 'admin',
                        'owner_name' => $owner->name,
                        'partner_name' => $partner->name,
                        'partner_slug' => $partnerSlug,
                        'table_code' => $tableCode
                    ]
                ]);
                return redirect()->route('customer.account.suspended');
            }

            // Cek status partner oleh admin (is_active_admin)
            if (!$partner->is_active_admin) {
                session([
                    'customer_suspended_data' => [
                        'user_type' => 'customer',
                        'suspended_by' => 'admin',
                        'partner_name' => $partner->name,
                        'owner_name' => $owner ? $owner->name : null,
                        'partner_slug' => $partnerSlug,
                        'table_code' => $tableCode
                    ]
                ]);
                return redirect()->route('customer.account.suspended');
            }

            // Cek status partner/outlet oleh owner (is_active)
            if (!$partner->is_active) {
                session([
                    'customer_suspended_data' => [
                        'user_type' => 'customer',
                        'suspended_by' => 'owner',
                        'partner_name' => $partner->name,
                        'owner_name' => $owner ? $owner->name : null,
                        'partner_slug' => $partnerSlug,
                        'table_code' => $tableCode
                    ]
                ]);
                return redirect()->route('customer.account.suspended');
            }
        }

        return $next($request);
    }
}
