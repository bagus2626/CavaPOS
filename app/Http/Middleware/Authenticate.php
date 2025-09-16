<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Kalau akses employee route
            if ($request->is('employee/*')) {
                return route('employee.login');
            } else if ($request->is('owner/*')) {
                return route('owner.login');
            }

            // Default login
            return route('login');
        }

        return null;
    }
}
