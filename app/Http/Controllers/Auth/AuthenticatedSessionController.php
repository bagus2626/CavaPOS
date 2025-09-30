<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // dd($request->all());
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        $intended = session()->get('url.intended');

        if ($user->role === 'admin') {
            if ($intended && str_starts_with($intended, url('/admin'))) {
                return redirect()->intended();
            }

            return redirect('/admin/dashboard');
        } elseif ($user->role === 'partner') {
            if ($intended && str_starts_with($intended, url('/partner'))) {
                return redirect()->intended();
            }

            return redirect('/partner');
        }

        return redirect()->intended(route('dashboard'));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/partner/login');
    }
}
