<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStaffPermission
{
    public function handle(Request $request, Closure $next, $menuKey)
    {
        $employee = Auth::guard('employee')->user();

        if (!$employee) {
            return redirect()->route('employee.login');
        }

        if (!$employee->hasMenuAccess($menuKey)) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
