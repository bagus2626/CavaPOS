<?php

namespace App\Http\Controllers\Employee\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction\BookingOrder;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\MessageNotification\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $employee   = Auth::guard('employee')->user();
        $employeeId = $employee->id;
        $partnerId  = $employee->partner_id;
        $now        = Carbon::now();
        $today      = Carbon::today();

        $total_employees_active = Employee::where('partner_id', $partnerId)
            ->where('is_active', 1)
            ->count();

        $total_products = PartnerProduct::where('partner_id', $partnerId)->count();

        $today_sales = BookingOrder::where('partner_id', $partnerId)
            ->whereDate('created_at', $today)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->sum('total_order_value');

        $today_orders_paid = BookingOrder::where('partner_id', $partnerId)
            ->whereDate('created_at', $today)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->count();

        $last7Days      = [];
        $salesLast7Days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date        = Carbon::today()->subDays($i);
            $last7Days[] = $date->format('D, d M');

            $sales = BookingOrder::where('partner_id', $partnerId)
                ->whereDate('created_at', $date)
                ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
                ->sum('total_order_value');

            $salesLast7Days[] = (float) $sales;
        }

        $topProducts = DB::table('order_details')
            ->join('booking_orders', 'order_details.booking_order_id', '=', 'booking_orders.id')
            ->select(
                'order_details.product_name',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total_revenue')
            )
            ->where('booking_orders.partner_id', $partnerId)
            ->whereMonth('booking_orders.created_at', now()->month)
            ->whereYear('booking_orders.created_at', now()->year)
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->groupBy('order_details.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'product_name'   => $p->product_name,
                'total_quantity' => (int) $p->total_quantity,
                'total_revenue'  => (float) $p->total_revenue,
            ]);

        $categoryPerformance = DB::table('order_details')
            ->join('booking_orders', 'order_details.booking_order_id', '=', 'booking_orders.id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->join('categories', 'partner_products.category_id', '=', 'categories.id')
            ->select(
                'categories.category_name',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->where('booking_orders.partner_id', $partnerId)
            ->whereMonth('booking_orders.created_at', now()->month)
            ->whereYear('booking_orders.created_at', now()->year)
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->groupBy('categories.id', 'categories.category_name')
            ->orderByDesc('total_quantity')
            ->get()
            ->map(fn($c) => [
                'category_name'  => $c->category_name,
                'total_quantity' => (int) $c->total_quantity,
            ]);

        $popups = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($employeeId) {
                $q->where('message_type', 'popup')
                    ->where(function ($qq) use ($employeeId) {
                        $qq->where(function ($qx) use ($employeeId) {
                            $qx->where('recipient_id', $employeeId)
                                ->where('recipient_type', 'employee')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', ['employee', 'all']);
                            });
                    });
            })
            ->where(function ($q) use ($now) {
                $q->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
                })->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->get();

        $data = [
            'total_employees_active' => $total_employees_active,
            'total_products'         => $total_products,
            'today_sales'            => $today_sales,
            'today_orders_paid'      => $today_orders_paid,
            'last7Days'              => $last7Days,
            'salesLast7Days'         => $salesLast7Days,
            'topProducts'            => $topProducts,
            'categoryPerformance'    => $categoryPerformance,
            'popups'                 => $popups,
        ];

        return view('pages.employee.staff.dashboard.index', compact('data'));
    }
}