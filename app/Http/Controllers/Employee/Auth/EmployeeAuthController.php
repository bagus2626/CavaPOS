<?php

namespace App\Http\Controllers\Employee\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;

class EmployeeAuthController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login, lempar ke dashboard role-nya
        if (Auth::guard('employee')->check()) {
            return redirect()->to($this->dashboardFor(Auth::guard('employee')->user()->role));
        }
        return view('pages.employee.auth.login');
    }

    public function login(Request $request)
    {
        // dd($request->all());
        $cred = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // pastikan hanya employee aktif yang bisa login
        $attempt = Auth::guard('employee')->attempt([
            'email'     => $cred['email'],
            'password'  => $cred['password'],
            'is_active' => 1,
        ], $request->boolean('remember'));

        if (!$attempt) {
            return back()->withInput()->withErrors(['email' => 'Kredensial tidak valid atau akun nonaktif.']);
        }

        $request->session()->regenerate();
        $role = Auth::guard('employee')->user()->role;

        return redirect()->to($this->dashboardFor($role));
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('employee.login');
    }

    private function dashboardFor(string $role): string
    {
        return match ($role) {
            'CASHIER' => route('employee.cashier.dashboard'),
            'KITCHEN' => route('employee.kitchen.dashboard'),
            'WAITER'  => route('employee.waiter.dashboard'),
            default   => route('employee.login'),
        };
    }
}
