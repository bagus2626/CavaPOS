<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Urutan prioritas: prefix URL -> query ?lang= -> session -> default
        $locale = $request->route('locale')
            ?? $request->query('lang')
            ?? Session::get('app_locale')
            ?? config('app.locale');

        if (! in_array($locale, ['en', 'id'], true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        // Simpan ke session (agar halaman berikutnya tetap sama)
        Session::put('app_locale', $locale);

        return $next($request);
    }
}
