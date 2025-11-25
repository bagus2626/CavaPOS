<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // cek apakah sedang di halaman suspended
        if ($request->routeIs('employee.account.suspended')) {
            $suspendedData = session('employee_suspended_data');

            // Jika tidak ada data suspended, redirect ke home
            if (!$suspendedData) {
                if (Auth::guard('employee')->check()) {
                    $employee = Auth::guard('employee')->user();
                    $partner = $employee->partner ?? null;
                    $owner = $partner->owner ?? null;

                    // Jika semua status aktif → arahkan ke dashboard sesuai role
                    if (
                        $owner && $owner->is_active &&
                        $partner && $partner->is_active_admin && $partner->is_active &&
                        $employee->is_active_admin && $employee->is_active
                    ) {
                        // Arahkan sesuai role employee
                        if ($employee->role === 'CASHIER') {
                            return redirect()->route('employee.cashier.dashboard');
                        } elseif ($employee->role === 'KITCHEN') {
                            return redirect()->route('employee.kitchen.dashboard');
                        }
                    }
                }
                return redirect('/');
            }

            // Cek apakah akun sudah aktif kembali
            if (Auth::guard('employee')->check()) {
                $employee = Auth::guard('employee')->user();
                $partner = $employee->partner ?? null;
                $owner = $partner->owner ?? null;

                // Jika semua sudah aktif, redirect ke dashboard employee
                if (
                    $owner && $owner->is_active &&
                    $partner && $partner->is_active_admin && $partner->is_active &&
                    $employee->is_active_admin && $employee->is_active
                ) {
                    session()->forget('employee_suspended_data');

                    // Redirect sesuai role employee
                    if ($employee->role === 'CASHIER') {
                        return redirect()->route('employee.cashier.dashboard');
                    } elseif ($employee->role === 'KITCHEN') {
                        return redirect()->route('employee.kitchen.dashboard');
                    }
                }
            }

            return $next($request);
        }

        // cek status akun untuk employee yang login
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $partner = $employee->partner ?? null;
            $owner = $partner->owner ?? null;

            // Cek status owner
            if ($owner && !$owner->is_active) {
                session([
                    'employee_suspended_data' => [
                        'user_type' => 'employee',
                        'suspended_by' => 'admin',
                        'owner_name' => $owner->name,
                        'partner_name' => $partner ? $partner->name : null,
                        'employee_name' => $employee->name
                    ]
                ]);
                return redirect()->route('employee.account.suspended');
            }

            // Cek status partner (suspended oleh admin)
            if ($partner && !$partner->is_active_admin) {
                session([
                    'employee_suspended_data' => [
                        'user_type' => 'employee',
                        'suspended_by' => 'admin',
                        'partner_name' => $partner->name,
                        'owner_name' => $owner ? $owner->name : null,
                        'employee_name' => $employee->name
                    ]
                ]);
                return redirect()->route('employee.account.suspended');
            }

            // Cek status partner (suspended oleh owner)
            if ($partner && !$partner->is_active) {
                session([
                    'employee_suspended_data' => [
                        'user_type' => 'employee',
                        'suspended_by' => 'owner',
                        'partner_name' => $partner->name,
                        'owner_name' => $owner ? $owner->name : null,
                        'employee_name' => $employee->name
                    ]
                ]);
                return redirect()->route('employee.account.suspended');
            }

            // Cek status employee (suspended oleh admin)
            if (!$employee->is_active_admin) {
                session([
                    'employee_suspended_data' => [
                        'user_type' => 'employee',
                        'suspended_by' => 'admin',
                        'employee_name' => $employee->name,
                        'partner_name' => $partner ? $partner->name : null,
                        'owner_name' => $owner ? $owner->name : null,
                        'deactivation_reason' => $employee->deactivation_reason
                    ]
                ]);
                return redirect()->route('employee.account.suspended');
            }

            // Cek status employee (suspended oleh partner)
            if (!$employee->is_active) {
                session([
                    'employee_suspended_data' => [
                        'user_type' => 'employee',
                        'suspended_by' => 'owner',
                        'employee_name' => $employee->name,
                        'partner_name' => $partner ? $partner->name : null,
                        'owner_name' => $owner ? $owner->name : null
                    ]
                ]);
                return redirect()->route('employee.account.suspended');
            }
        }

        return $next($request);
    }
}
