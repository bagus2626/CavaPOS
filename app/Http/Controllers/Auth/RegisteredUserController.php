<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // dd($request->all());
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'province' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'district' => ['required', 'string', 'max:255'],
                'village' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string'],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'province' => $request->province_name,
                'province_id' => $request->province,
                'city' => $request->city_name,
                'city_id' => $request->city,
                'subdistrict' => $request->district_name,
                'subdistrict_id' => $request->district,
                'urban_village' => $request->village_name,
                'urban_village_id' => $request->village,
                'address' => $request->address,
            ]);

            event(new Registered($user));
            Auth::login($user);

            return redirect()
                ->route('dashboard')
                ->with('success', 'Akun berhasil dibuat! Selamat datang, '.$user->name.' dari '.$user->city);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

}
