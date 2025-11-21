<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedWithRole
{
    public function handle(Request $request, Closure $next)
    {
        // Jika sudah login sebagai admin
        if (Auth::guard('web')->check() && Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        // Jika sudah login sebagai owner
        if (Auth::guard('owner')->check()) {
            return redirect()->route('owner.user-owner.dashboard');
        }

        // Jika sudah login sebagai partner
        if (Auth::guard('partner')->check()) {
            return redirect()->route('partner.dashboard');
        }

        // Jika sudah login sebagai employee
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.cashier.dashboard');
        }

        return $next($request);
    }
}
