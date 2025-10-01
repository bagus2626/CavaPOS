<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;


class CustomerAuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegisterForm($partner_slug, $table_code)
    {
        return view('pages.customer.auth.register', compact('partner_slug', 'table_code'));
    }

    public function register(Request $request, $partner_slug, $table_code)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Login agar bisa akses notice & resend
            Auth::guard('customer')->login($customer);

            // Simpan tujuan setelah verifikasi
            session([
                'customer.intended'      => route('customer.menu.index', compact('partner_slug', 'table_code')),
                'customer.partner_slug'  => $partner_slug,
                'customer.table_code'    => $table_code,
            ]);

            // Kirim email verifikasi
            $customer->sendEmailVerificationNotification();

            session()->flash('status', 'verification-link-sent');

            // Arahkan ke halaman "cek email"
            return redirect()->route('customer.verification.notice', [
                'partner_slug' => $partner_slug,
                'table_code'   => $table_code,
            ])->with('status', 'Link verifikasi telah dikirim ke email Anda.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Show login form
     */
    public function showLoginForm($partner_slug, $table_code)
    {
        return view('pages.customer.auth.login', compact('partner_slug', 'table_code'));
    }


    public function login(Request $request, $partner_slug, $table_code)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var \App\Models\Customer $customer */
            $customer = Auth::guard('customer')->user();

            if (!$customer->hasVerifiedEmail()) {
                session([
                    'customer.partner_slug' => $partner_slug,
                    'customer.table_code'   => $table_code,
                ]);
                // simpan intended agar balik ke menu setelah verif
                $customer->sendEmailVerificationNotification();

                session()->flash('status', 'verification-link-sent');
                session(['customer.intended' => route('customer.menu.index', compact('partner_slug', 'table_code'))]);

                // Arahkan ke halaman "cek email"
                return redirect()->route('customer.verification.notice', [
                    'partner_slug' => $partner_slug,
                    'table_code'   => $table_code,
                ])->with('status', 'Anda belum Verifikasi Email. Link verifikasi telah dikirim ke email Anda.');
            }

            return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'))
                ->with('success', 'Login berhasil, selamat datang!');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    // public function login(Request $request, $partner_slug, $table_code)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
    //         $request->session()->regenerate();
    //         return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'))
    //             ->with('success', 'Login berhasil, selamat datang!');
    //     }

    //     throw ValidationException::withMessages([
    //         'email' => __('auth.failed'),
    //     ]);
    // }

    /**
     * Handle logout
     */
    public function logout(Request $request, $partner_slug, $table_code)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login', compact('partner_slug', 'table_code'));
    }

    public function logoutSimple(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // aman diarahkan ke home (atau halaman login customer umum jika ada)
        return redirect()->route('home');
    }

    public function guestLogout($partner_slug, $table_code)
    {
        session()->forget('guest_customer');
        return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'))
            ->with('success', 'Registrasi berhasil, selamat datang!');
    }


    public function guestLogin(Request $request, $partner_slug, $table_code)
    {
        $guestCustomer = [
            'id' => 'guest_' . uniqid(),
            'name' => 'Guest',
        ];

        session(['guest_customer' => (object)$guestCustomer]);

        return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'));
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

    public function redirect($partner_slug, $table_code)
    {
        $state = [
            'role'         => 'customer',
            'partner_slug' => $partner_slug,
            'table_code'   => $table_code,
            'intended'     => route('customer.menu.index', compact('partner_slug', 'table_code')),
        ];

        // (opsional) backup ke session
        session([
            'oauth.partner_slug' => $partner_slug,
            'oauth.table_code'   => $table_code,
            'oauth.intended'     => $state['intended'],
        ]);

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->with([
                'prompt' => 'select_account',
                'state'  => base64_encode(json_encode($state)), // ← ganti .state(...) dengan .with(['state'=>...])
            ])
            ->redirect();
    }
}
