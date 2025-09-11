<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        return view('pages.partner.dashboard.index');
    }
}
