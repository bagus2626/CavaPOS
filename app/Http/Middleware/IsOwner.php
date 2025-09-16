<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsOwner
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('owner')->user();
        if (!$user) {
            // Belum login sebagai owner
            abort(401);
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
