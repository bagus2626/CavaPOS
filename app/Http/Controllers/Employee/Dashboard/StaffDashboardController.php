<?php

namespace App\Http\Controllers\Employee\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffDashboardController extends Controller
{
    public function index()
    {
        return view('pages.employee.staff.index');
    }
}
