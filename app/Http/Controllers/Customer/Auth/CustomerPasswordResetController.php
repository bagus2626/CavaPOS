<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CustomerPasswordResetController extends Controller
{
    // Form minta link reset (bawa konteks)
    public function requestForm(string $partner_slug, string $table_code)
    {
        // simpan konteks agar bisa dipakai saat generate URL email
        session([
            'customer.partner_slug' => $partner_slug,
            'customer.table_code'   => $table_code,
        ]);

        return view('pages.customer.auth.forgot-password', compact('partner_slug', 'table_code'));
    }

    // Kirim email berisi link reset
    public function sendLink(Request $request, string $partner_slug, string $table_code)
    {
        $request->validate(['email' => ['required', 'email']]);

        // pastikan konteks tetap ada di session
        session([
            'customer.partner_slug' => $partner_slug,
            'customer.table_code'   => $table_code,
        ]);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Form reset password (dibuka dari email). partner_slug & table_code akan dikirim via query
    public function resetForm(Request $request, string $token)
    {
        return view('pages.customer.auth.reset-password', [
            'token'        => $token,
            'email'        => $request->query('email'),
            'partner_slug' => $request->query('partner_slug'),
            'table_code'   => $request->query('table_code'),
        ]);
    }

    // Proses update password
    public function update(Request $request)
    {
        $request->validate([
            'token'        => ['required'],
            'email'        => ['required', 'email'],
            'password'     => ['required', 'confirmed', 'min:8'],
            'partner_slug' => ['nullable', 'string'],
            'table_code'   => ['nullable', 'string'],
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                // jika model Customer pakai casts "password" => "hashed", ini cukup
                $customer->forceFill([
                    'password'       => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($customer));

                // login-kan langsung setelah reset
                Auth::guard('customer')->login($customer);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)]);
        }

        // redirect tujuan: kembali ke menu jika konteks ada, kalau tidak ke dashboard atau home
        $ps = $request->input('partner_slug') ?: session('customer.partner_slug');
        $tc = $request->input('table_code')   ?: session('customer.table_code');

        // bersihkan konteks
        session()->forget(['customer.partner_slug', 'customer.table_code']);

        if ($ps && $tc) {
            return redirect()->route('customer.menu.index', [
                'partner_slug' => $ps,
                'table_code'   => $tc,
            ])->with('success', 'Password berhasil direset. Anda sudah login.');
        }

        // fallback kalau konteks tidak ada
        return redirect()->route('customer.dashboard')->with('success', 'Password berhasil direset. Anda sudah login.');
    }
}
