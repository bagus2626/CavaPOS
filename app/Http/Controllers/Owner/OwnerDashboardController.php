<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Transaction\BookingOrder;
use App\Models\Product\MasterProduct;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        // dd($owner);

        $outlets = User::where('owner_id', $owner->id)
            ->where('role', 'partner');

        $total_products = MasterProduct::where('owner_id', $owner->id)->count();

        $total_outlets = $outlets->count();
        $outlet_ids = $outlets->pluck('id')->toArray();
        $total_employees = Employee::whereIn('partner_id', $outlet_ids)->count();
        $total_accounts = $total_outlets + $total_employees + 1;
        $orders = BookingOrder::whereIn('partner_id', $outlet_ids)
            ->whereYear('created_at', now()->year);

        $orders_gross_income = $orders->sum('total_order_value');
        $total_orders = $orders->count();

        $last_orders = $orders->latest()->take(10)->get();
        // dd($last_orders);

        $data = [
            'total_outlets'      => $total_outlets,
            'total_employees'    => $total_employees,
            'total_accounts'     => $total_accounts,
            'orders_gross_income'=> $orders_gross_income,
            'total_orders'       => $total_orders,
            'total_products'     => $total_products,
            'last_orders'        => $last_orders,
        ];

        // dd($data);

        return view('pages.owner.dashboard.index', compact('data'));
    }
}
