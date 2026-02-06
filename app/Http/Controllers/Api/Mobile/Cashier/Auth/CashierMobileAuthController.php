<?php

namespace App\Http\Controllers\Api\Mobile\Cashier\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner\HumanResource\Employee;

class CashierMobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'user_name' => ['required', 'string'],
            'password'  => ['required', 'string'],
        ]);

        $employee = Employee::query()
            ->where('user_name', $data['user_name'])
            ->where('role', 'CASHIER')
            // kalau kamu punya kolom status/active, sesuaikan:
            // ->where('status', 'ACTIVE')
            ->first();

        if (! $employee || ! Hash::check($data['password'], $employee->password)) {
            return response()->json([
                'message' => 'Kredensial tidak valid atau akun nonaktif.',
            ], 401);
        }

        // optional: hapus token lama biar 1 device = 1 token
        $employee->tokens()->delete();

        $token = $employee->createToken('cashier-mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'        => $employee->id,
                'name'      => $employee->name ?? ($employee->full_name ?? '-'),
                'user_name' => $employee->user_name,
                'role'      => $employee->role,
                'partner_id' => $employee->partner_id ?? null,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}
