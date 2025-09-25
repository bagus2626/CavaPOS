<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Models\Owner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class OwnerAuthController extends Controller
{
    public function create()
    {
        return view('pages.owner.auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:owners,email'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('owners', 'public');
        }

        $owner = Owner::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'password'     => $data['password'], // casts -> hashed
            'role'         => 'owner',
            'image'        => $imagePath,
            'is_active'    => true,
        ]);

        // login-kan supaya bisa akses notice & resend
        Auth::guard('owner')->login($owner);

        // kirim email verifikasi
        $owner->sendEmailVerificationNotification();

        // arahkan ke halaman "cek email"
        return redirect()->route('owner.verification.notice')
            ->with('status', 'Link verifikasi telah dikirim ke email Anda.');
    }


    public function login()
    {
        return view('pages.owner.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        // Tambahkan constraint is_active=1 agar sekalian tervalidasi di query
        $ok = Auth::guard('owner')->attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => 1],
            $request->boolean('remember')
        );

        if (!$ok) {
            // bisa karena email/password salah atau is_active = 0
            throw ValidationException::withMessages(['email' => 'Email atau password salah atau akun tidak aktif.']);
        }

        $request->session()->regenerate();

        /** @var \App\Models\Owner $owner */
        $owner = Auth::guard('owner')->user();

        // Jika belum verifikasi, arahkan ke notice (kirim ulang link)
        if (!$owner->hasVerifiedEmail()) {
            $owner->sendEmailVerificationNotification();
            return redirect()
                ->route('owner.verification.notice')
                ->with('status', 'Silakan verifikasi email Anda. Tautan verifikasi telah dikirim.');
        }

        return redirect()->intended(route('owner.user-owner.dashboard'))
            ->with('success', 'Berhasil login.');
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();   // ← bukan Auth::logout() umum
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('owner.login');
    }

    public function redirectToProvider(Request $request, $partner_slug, $table_code, $provider)
    {
        // simpan ke session dulu
        session([
            'oauth.partner_slug' => $partner_slug,
            'oauth.table_code'   => $table_code,
        ]);

        // return Socialite::driver($provider)->redirect();
        return Socialite::driver($provider)
            ->with(['state' => json_encode([
                'partner_slug' => $partner_slug,
                'table_code'   => $table_code,
            ])])
            ->redirect();
    }


    public function handleProviderCallback() // <- hanya $provider
    {
        // dd(session()->all());
        $state = json_decode(request()->get('state'), true);
        $partner_slug = $state['partner_slug'];
        $table_code   = $state['table_code'];

        // Ambil profil dari Google (stateful dulu; fallback stateless bila perlu)
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            $socialUser = Socialite::driver('google')->stateless()->user();
        }

        // Provision / link akun customer
        $customer = Customer::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'     => $socialUser->getName() ?: 'Customer',
                'password' => Hash::make(Str::random(32)),
            ]
        );

        Auth::guard('customer')->login($customer, remember: true);

        // Bersihkan konteks
        session()->forget(['oauth.partner_slug', 'oauth.table_code']);

        // Redirect balik ke menu meja
        return redirect()->route('customer.menu.index', [
            'partner_slug' => $partner_slug,
            'table_code'   => $table_code,
        ]);
    }

    public function redirect()
    {
        $state = [
            'role'     => 'owner',
            // sesuaikan intended ke dashboard owner-mu
            'intended' => route('owner.user-owner.dashboard'),
        ];

        // (opsional) backup ke session agar tetap aman bila param 'state' di-strip browser
        session([
            'oauth.intended' => $state['intended'],
        ]);

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->with([
                'prompt' => 'select_account',
                'state'  => base64_encode(json_encode($state)),
            ])
            ->redirect();
    }

    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Jika URL yang diminta berada di bawah prefix /owner,
            // arahkan ke halaman login owner.
            if ($request->is('owner/*')) {
                return route('owner.login');
            }

            // fallback default (jika kamu punya login umum)
            return route('login');
        }

        return null;
    }
}
