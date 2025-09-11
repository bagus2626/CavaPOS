<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('employee')->user();
        if (!$user) {
            // Belum login sebagai employee
            abort(401);
            // atau redirect ke login employee kalau rutenya ada:
            // return redirect()->route('employee.login');
        }

        // Optional: blokir akun non-aktif
        if (property_exists($user, 'is_active') && !$user->is_active) {
            abort(403, 'Employee account is inactive.');
        }

        // Jika middleware dipanggil tanpa parameter role, cukup loloskan.
        if (!empty($roles)) {
            $userRole   = strtoupper((string) $user->role);
            $allowRoles = array_map('strtoupper', $roles);

            if (!in_array($userRole, $allowRoles, true)) {
                abort(403); // role tidak sesuai
            }
        }

        return $next($request);
    }
}
