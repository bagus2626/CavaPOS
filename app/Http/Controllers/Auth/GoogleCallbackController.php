<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GoogleCallbackController extends Controller
{
    // GoogleCallbackController
    private function parseState(?string $raw)
    {
        if (!$raw) return null;

        // Coba decode base64 → JSON
        $decoded = base64_decode($raw, true);
        if ($decoded !== false) {
            $arr = json_decode($decoded, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
                return $arr;
            }
        }

        // Fallback: JSON polos
        $arr = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
            return $arr;
        }

        return null;
    }

    public function handle()
    {
        $state = $this->parseState(request('state')) ?? [
            'role'         => 'customer',
            'partner_slug' => session('oauth.partner_slug'),
            'table_code'   => session('oauth.table_code'),
            'intended'     => session('oauth.intended'),
        ];

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            $googleUser = Socialite::driver('google')->stateless()->user();
        }

        $email = $googleUser->getEmail();
        $name  = $googleUser->getName() ?: 'User';

        // <-- ambil status verifikasi email dari Google
        $isVerified = (bool) (
            data_get($googleUser->user, 'email_verified') ??
            data_get($googleUser->user, 'verified_email') ??
            false
        );

        if (($state['role'] ?? 'customer') === 'owner') {
            // buat / temukan owner
            $owner = \App\Models\Owner::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32))]
            );

            // jika email Google verified dan Owner belum verified -> set email_verified_at
            if ($isVerified && (! method_exists($owner, 'hasVerifiedEmail') || ! $owner->hasVerifiedEmail())) {
                $owner->forceFill(['email_verified_at' => now()])->save();
            }

            \Illuminate\Support\Facades\Auth::guard('owner')->login($owner, remember: true);
            $redirect = $state['intended'] ?? route('owner.user-owner.dashboard'); // pastikan ini route yang valid di app-mu
        } else {
            $customer = \App\Models\Customer::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32))]
            );

            // (opsional) lakukan hal yang sama untuk customer
            if ($isVerified && property_exists($customer, 'email_verified_at') && method_exists($customer, 'hasVerifiedEmail') && ! $customer->hasVerifiedEmail()) {
                $customer->forceFill(['email_verified_at' => now()])->save();
            }

            \Illuminate\Support\Facades\Auth::guard('customer')->login($customer, remember: true);
            $redirect = $state['intended']
                ?? route('customer.menu.index', [
                    'partner_slug' => $state['partner_slug'] ?? 'default-partner',
                    'table_code'   => $state['table_code'] ?? 'default-table',
                ]);
        }

        session()->forget(['oauth.partner_slug', 'oauth.table_code', 'oauth.intended']);

        return redirect($redirect);
    }


    // public function handle() // callback tunggal skema A: /oauth/google/callback
    // {
    //     // 1) Ambil state dari query (baru), atau dari session (lama)
    //     $state = $this->parseState(request('state')) ?? [
    //         'role'         => 'customer', // default jika tak ada
    //         'partner_slug' => session('oauth.partner_slug'),
    //         'table_code'   => session('oauth.table_code'),
    //         'intended'     => session('oauth.intended'),
    //     ];

    //     // 2) Ambil profil Google (stateful → fallback stateless bila perlu)
    //     try {
    //         $googleUser = Socialite::driver('google')->user();
    //     } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
    //         $googleUser = Socialite::driver('google')->stateless()->user();
    //     }

    //     // 3) Provision / link akun sesuai role
    //     $email = $googleUser->getEmail();
    //     $name  = $googleUser->getName() ?: 'User';

    //     if (($state['role'] ?? 'customer') === 'owner') {
    //         $owner = \App\Models\Owner::firstOrCreate(
    //             ['email' => $email],
    //             ['name' => $name, 'password' => Hash::make(Str::random(32))],
    //         );
    //         Auth::guard('owner')->login($owner, remember: true);
    //         $redirect = $state['intended'] ?? route('owner.dashboard');
    //     } else {
    //         // default: customer
    //         $customer = \App\Models\Customer::firstOrCreate(
    //             ['email' => $email],
    //             ['name' => $name, 'password' => Hash::make(Str::random(32))]
    //         );
    //         Auth::guard('customer')->login($customer, remember: true);
    //         $redirect = $state['intended']
    //             ?? route('customer.menu.index', [
    //                 'partner_slug' => $state['partner_slug'] ?? 'default-partner',
    //                 'table_code'   => $state['table_code'] ?? 'default-table',
    //             ]);
    //     }

    //     // 4) Bersihkan konteks lama
    //     session()->forget(['oauth.partner_slug', 'oauth.table_code', 'oauth.intended']);

    //     return redirect($redirect);
    // }
}
