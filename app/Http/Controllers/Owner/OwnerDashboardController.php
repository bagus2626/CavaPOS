<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        return view('pages.owner.dashboard.index');
    }
}
