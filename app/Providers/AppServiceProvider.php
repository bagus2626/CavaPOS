<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;
use App\Media\CustomMediaUrlGenerator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\Owner;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // ✅ tunda sampai service 'view' sudah siap
        $this->callAfterResolving('view', function () {
            View::composer('*', function ($view) {
                if (request()->route()) {
                    $view->with('partner_slug', request()->route('partner_slug'));
                    $view->with('table_code', request()->route('table_code'));
                }
            });
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            // Kalau yang diverifikasi adalah Owner → pakai route owner.*
            if ($notifiable instanceof Owner) {
                return URL::temporarySignedRoute(
                    'owner.verification.verify', // pastikan route ini ada
                    now()->addMinutes(config('auth.verification.expire', 60)),
                    [
                        'id'   => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ]
                );
            }

            // Fallback untuk model lain (mis. User default) jika ada
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id'   => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            if ($notifiable instanceof Owner) {
                // generate URL reset khusus OWNER
                return URL::route('owner.password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]);
            }

            // fallback untuk model lain (jika ada)
            return URL::route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}
