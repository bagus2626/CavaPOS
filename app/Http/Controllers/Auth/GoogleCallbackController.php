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

    public function handle() // callback tunggal skema A: /oauth/google/callback
    {
        // 1) Ambil state dari query (baru), atau dari session (lama)
        $state = $this->parseState(request('state')) ?? [
            'role'         => 'customer', // default jika tak ada
            'partner_slug' => session('oauth.partner_slug'),
            'table_code'   => session('oauth.table_code'),
            'intended'     => session('oauth.intended'),
        ];

        // 2) Ambil profil Google (stateful → fallback stateless bila perlu)
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            $googleUser = Socialite::driver('google')->stateless()->user();
        }

        // 3) Provision / link akun sesuai role
        $email = $googleUser->getEmail();
        $name  = $googleUser->getName() ?: 'User';

        if (($state['role'] ?? 'customer') === 'owner') {
            $owner = \App\Models\Owner::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make(Str::random(32))]
            );
            Auth::guard('owner')->login($owner, remember: true);
            $redirect = $state['intended'] ?? route('owner.dashboard');
        } else {
            // default: customer
            $customer = \App\Models\Customer::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make(Str::random(32))]
            );
            Auth::guard('customer')->login($customer, remember: true);
            $redirect = $state['intended']
                ?? route('customer.menu.index', [
                    'partner_slug' => $state['partner_slug'] ?? 'default-partner',
                    'table_code'   => $state['table_code'] ?? 'default-table',
                ]);
        }

        // 4) Bersihkan konteks lama
        session()->forget(['oauth.partner_slug', 'oauth.table_code', 'oauth.intended']);

        return redirect($redirect);
    }
}
