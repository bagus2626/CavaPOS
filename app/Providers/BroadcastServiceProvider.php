<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Endpoint default: /broadcasting/auth
        Broadcast::routes([
            'middleware' => ['web', 'auth:employee'], // pakai guard employee
        ]);

        // Pastikan resolver user mengambil dari guard employee
        Broadcast::resolveAuthenticatedUserUsing(function (Request $request) {
            return $request->user('employee');
        });

        require base_path('routes/channels.php');
    }
}
