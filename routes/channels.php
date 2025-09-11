<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;

// paksa pakai guard employee
Broadcast::routes(['middleware' => ['web', 'auth:employee']]);

Broadcast::resolveAuthenticatedUserUsing(function (Request $request) {
    return $request->user('employee');
});

Broadcast::channel('partner.{partnerId}.orders', function ($user, $partnerId) {
    return (int) $user->partner_id === (int) $partnerId;
});
