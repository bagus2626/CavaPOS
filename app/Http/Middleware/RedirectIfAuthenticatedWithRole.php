<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedWithRole
{
    public function handle(Request $request, Closure $next)
    {
        // Tentukan guard berdasarkan route
        $routeName = $request->route()->getName();
        $path = $request->path();

        // Untuk route ADMIN - hanya cek guard 'web'
        if (str_starts_with($path, 'admin/')) {
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();

                if ($user->role === 'admin') {
                    return redirect('/admin/dashboard');
                }

                if ($user->role === 'partner') {
                    return redirect()->route('partner.dashboard');
                }
            }
            return $next($request);
        }

        // Untuk route PARTNER - hanya cek guard 'web'
        if (str_starts_with($path, 'partner/')) {
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();

                if ($user->role === 'admin') {
                    return redirect('/admin/dashboard');
                }

                if ($user->role === 'partner') {
                    return redirect()->route('partner.dashboard');
                }
            }
            return $next($request);
        }

        // Untuk route OWNER - hanya cek guard 'owner'
        if (str_starts_with($path, 'owner/')) {
            if (Auth::guard('owner')->check()) {
                return redirect()->route('owner.user-owner.dashboard');
            }
            return $next($request);
        }

        // Untuk route EMPLOYEE - hanya cek guard 'employee'
        if (str_starts_with($path, 'employee/')) {
            if (Auth::guard('employee')->check()) {
                return redirect()->route('employee.cashier.dashboard');
            }
            return $next($request);
        }

        // Untuk route CUSTOMER - hanya cek guard 'customer'
        if (str_starts_with($path, 'customer/')) {
            if (Auth::guard('customer')->check()) {
                // redirect ke halaman customer (sesuaikan)
                return redirect()->route('customer.menu.index', [
                    'partner_slug' => session('customer.partner_slug', 'default'),
                    'table_code' => session('customer.table_code', 'default')
                ]);
            }
            return $next($request);
        }

        // Untuk route umum lainnya - cek guard 'web'
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($user->role === 'partner') {
                return redirect()->route('partner.dashboard');
            }
        }

        return $next($request);
    }
}