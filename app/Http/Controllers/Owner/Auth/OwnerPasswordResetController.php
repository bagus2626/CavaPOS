<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class OwnerPasswordResetController extends Controller
{
    // Form minta link reset
    public function requestForm()
    {
        return view('pages.owner.auth.forgot-password');
    }

    // Kirim email berisi link reset
    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::broker('owners')->sendResetLink($request->only('email'));

        // Selalu tampilkan pesan sukses (tanpa membocorkan exist/doesn't exist)
        return back()->with('status', __('passwords.sent'));
    }


    // Form reset password (dari link email)
    public function resetForm(Request $request, string $token)
    {
        return view('pages.owner.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // Proses update password
    public function update(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // broker 'owners'
        $status = Password::broker('owners')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($owner, $password) {
                // Model Owner kamu sudah casts 'password' => 'hashed', jadi bisa assign langsung
                $owner->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($owner));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('owner.login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
