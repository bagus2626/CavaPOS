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
            if ($isVerified && is_null($customer->email_verified_at)) {
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
}
