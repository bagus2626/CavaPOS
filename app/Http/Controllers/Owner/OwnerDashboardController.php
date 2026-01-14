<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Transaction\BookingOrder;
use App\Models\Product\MasterProduct;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use App\Models\MessageNotification\MessageAttachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();

        // Get outlets
        $outlets = User::where('owner_id', $owner->id)
            ->where('role', 'partner');

        $total_products = MasterProduct::where('owner_id', $owner->id)->count();

        // Total outlets aktif
        $total_outlets_active = (clone $outlets)
            ->where('is_active', 1)
            ->count();

        $total_outlets = $outlets->count();
        $outlet_ids = $outlets->pluck('id')->toArray();
        $total_employees = Employee::whereIn('partner_id', $outlet_ids)->count();
        $total_accounts = $total_outlets + $total_employees;

        // Orders Query untuk tahun ini
        $ordersQuery = BookingOrder::whereIn('partner_id', $outlet_ids)
            ->whereYear('created_at', now()->year)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED']);

        $orders_gross_income = (clone $ordersQuery)->sum('total_order_value');
        $total_orders = (clone $ordersQuery)->count();

        // === STATS HARI INI ===

        // Total penjualan hari ini
        $today_sales = BookingOrder::whereIn('partner_id', $outlet_ids)
            ->whereDate('created_at', $today)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->sum('total_order_value');

        // Total pesanan hari ini yang sudah PAID
        $today_orders_paid = BookingOrder::whereIn('partner_id', $outlet_ids)
            ->whereDate('created_at', $today)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->count();

        $last_orders = (clone $ordersQuery)->latest()->take(10)->get();

        // === CHART DATA: 7 HARI TERAKHIR ===
        $last7Days = [];
        $salesLast7Days = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = $date->format('D, d M'); // Format: Sen, 01 Jan
            
            $sales = BookingOrder::whereIn('partner_id', $outlet_ids)
                ->whereDate('created_at', $date)
                ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
                ->sum('total_order_value');
            
            $salesLast7Days[] = (float) $sales;
        }

        // === TOP 5 PRODUCTS BULAN INI ===
        $topProducts = DB::table('order_details')
            ->join('booking_orders', 'order_details.booking_order_id', '=', 'booking_orders.id')
            ->select(
                'order_details.product_name',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total_revenue')
            )
            ->whereIn('booking_orders.partner_id', $outlet_ids)
            ->whereMonth('booking_orders.created_at', now()->month)
            ->whereYear('booking_orders.created_at', now()->year)
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->groupBy('order_details.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'product_name' => $product->product_name,
                    'total_quantity' => (int) $product->total_quantity,
                    'total_revenue' => (float) $product->total_revenue
                ];
            });

        // === ALL OUTLETS PERFORMANCE (for filtering) ===
        $outletPerformance = BookingOrder::select(
                'partner_id',
                'partner_name',
                DB::raw('SUM(total_order_value) as total_sales'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->whereIn('partner_id', $outlet_ids)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->groupBy('partner_id', 'partner_name')
            ->orderByDesc('total_sales')
            ->get()
            ->map(function($outlet) {
                return [
                    'partner_id' => $outlet->partner_id,
                    'partner_name' => $outlet->partner_name,
                    'total_sales' => (float) $outlet->total_sales,
                    'total_orders' => $outlet->total_orders
                ];
            });

        // Messages and Popups (existing code)
        $popups = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($owner) {
                $q->where('message_type', 'popup')
                    ->where(function ($qq) use ($owner) {
                        $qq->where(function ($qx) use ($owner) {
                            $qx->where('recipient_id', $owner->id)
                                ->where('recipient_type', 'owner')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'owner',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->get();

        $data = [
            'total_outlets'        => $total_outlets,
            'total_outlets_active' => $total_outlets_active,
            'total_employees'      => $total_employees,
            'total_accounts'       => $total_accounts,
            'orders_gross_income'  => $orders_gross_income,
            'total_orders'         => $total_orders,
            'total_products'       => $total_products,
            'last_orders'          => $last_orders,
            'popups'               => $popups,

            // Stats hari ini
            'today_sales'          => $today_sales,
            'today_orders_paid'    => $today_orders_paid,

            // Chart data
            'last7Days'            => $last7Days,
            'salesLast7Days'       => $salesLast7Days,
            'topProducts'          => $topProducts,
            'outletPerformance'    => $outletPerformance,
        ];

        return view('pages.owner.dashboard.index', compact('data'));
    }
}