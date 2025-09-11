<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegisterForm($partner_slug, $table_code)
    {
        return view('pages.customer.auth.register', compact('partner_slug', 'table_code'));
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request, $partner_slug, $table_code)
    {
        try {
            // Validasi request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Simpan customer baru
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Login otomatis setelah register
            Auth::guard('customer')->login($customer);

            return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'))
                ->with('success', 'Registrasi berhasil, selamat datang!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Kalau validasi gagal
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Kalau error lain (misalnya DB error)
            \Log::error('Register error: ' . $e->getMessage());
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


    /**
     * Handle customer login
     */
    public function login(Request $request, $partner_slug, $table_code)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'))
                ->with('success', 'Login berhasil, selamat datang!');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

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

    public function guestLogout($partner_slug, $table_code)
    {
        session()->forget('guest_customer');
        return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'))
                ->with('success', 'Registrasi berhasil, selamat datang!');
    }


    public function guestLogin(Request $request, $partner_slug, $table_code)
    {
        $guestCustomer = [
            'id' => 'guest_'.uniqid(),
            'name' => 'Guest',
        ];

        session(['guest_customer' => (object)$guestCustomer]);

        return redirect()->route('customer.menu.index', compact('partner_slug', 'table_code'));
    }

    public function redirectToProvider(Request $request, $partner_slug, $table_code, $provider)
    {
        return Socialite::driver($provider)
            ->with([
                'state' => json_encode([
                    'partner_slug' => $partner_slug,
                    'table_code'   => $table_code,
                ]),
            ])
            ->redirect();
    }

    public function handleProviderCallback(Request $request, $partner_slug, $table_code, $provider)
    {
        // Ambil user dari Google
        $socialUser = Socialite::driver($provider)->stateless()->user();

        // Decode state untuk ambil partner_slug & table_code
        $state = json_decode($request->input('state'), true);
        $partner_slug = $state['partner_slug'] ?? $partner_slug;
        $table_code   = $state['table_code'] ?? $table_code;

        // Lanjutkan proses login/register customer...
        $customer = Customer::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'password' => Hash::make(Str::random(16)),
            ]
        );

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.menu.index', [
            'partner_slug' => $partner_slug,
            'table_code'   => $table_code,
        ]);
    }


}
