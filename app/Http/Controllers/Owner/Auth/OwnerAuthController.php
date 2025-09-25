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
        // Validasi
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:owners,email'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Upload image (opsional)
        $imagePath = null;
        if ($request->hasFile('image')) {
            // pastikan sudah: php artisan storage:link
            $imagePath = $request->file('image')->store('owners', 'public');
        }

        // Simpan ke DB
        $owner = Owner::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'password'     => $data['password'], // otomatis di-hash jika model pakai casts 'password' => 'hashed'
            'role'         => 'owner',           // tetap role owner saat register
            'image'        => $imagePath,        // simpan path relatif (storage/public/owners/xxx)
            'is_active'    => true,
        ]);

        // Redirect ke halaman yang kamu mau
        return redirect()
            ->route('owner.login')
            ->with('success', 'Registrasi berhasil. Silakan login untuk melanjutkan.');
    }

    public function login()
    {
        return view('pages.owner.auth.login');
    }

    public function authenticate(Request $request)
    {
        // dd($request->all());
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        // Cari owner berdasarkan email
        $owner = Owner::where('email', $credentials['email'])->first();

        if (!$owner || !Hash::check($credentials['password'], $owner->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        if (!$owner->is_active) {
            return back()
                ->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi admin.'])
                ->onlyInput('email');
        }
        // dd($owner);

        // Login-kan owner pada guard default (web)
        Auth::guard('owner')->login($owner, (bool) ($credentials['remember'] ?? false));

        // Arahkan ke halaman tujuan khusus owner
        return redirect()->intended('/owner/user-owner')->with('success', 'Berhasil login.');
    }

    /** Logout owner (opsional) */
    public function logout(Request $request)
    {
        Auth::logout();
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
}
