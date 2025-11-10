<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsPartner;
use App\Http\Middleware\IsEmployee;
use App\Http\Middleware\IsOwner;
use App\Http\Middleware\RedirectIfAuthenticatedWithRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            require base_path('routes/xendit-webhook.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'is_partner' => IsPartner::class,
            'is_employee' => IsEmployee::class,
            'is_owner' => IsOwner::class,
            'redirect.auth.role' => \App\Http\Middleware\RedirectIfAuthenticatedWithRole::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'owner.verification.access' => \App\Http\Middleware\OwnerVerification::class,

        ]);

        $middleware->appendToGroup('web', \Illuminate\Session\Middleware\StartSession::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withBroadcasting(__DIR__ . '/../routes/channels.php')
    ->create();
