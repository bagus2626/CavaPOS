<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Transaction\BookingOrder;
use App\Models\Product\OutletProduct;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use App\Models\MessageNotification\MessageAttachment;
use App\Models\Partner\Products\PartnerProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        $partner = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();

        // Total karyawan di outlet ini
        $total_employees = Employee::where('partner_id', $partner->id)->count();

        // Total karyawan aktif
        $total_employees_active = Employee::where('partner_id', $partner->id)
            ->where('is_active', 1)
            ->count();

        // Total produk aktif di outlet ini
        $total_products = PartnerProduct::where('partner_id', $partner->id)->count();

        // Total akun (partner + karyawan)
        $total_accounts = $total_employees;

        // Orders untuk outlet ini
        $ordersQuery = BookingOrder::where('partner_id', $partner->id)
            ->whereYear('created_at', now()->year)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED']);

        $total_sales = (clone $ordersQuery)->sum('total_order_value');
        $total_orders = (clone $ordersQuery)->count();

        // === STATS HARI INI ===

        // Total penjualan hari ini
        $today_sales = BookingOrder::where('partner_id', $partner->id)
            ->whereDate('created_at', $today)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->sum('total_order_value');

        // Total pesanan hari ini yang sudah PAID
        $today_orders_paid = BookingOrder::where('partner_id', $partner->id)
            ->whereDate('created_at', $today)
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->count();

        // 10 pesanan terakhir
        $last_orders = (clone $ordersQuery)->latest()->take(10)->get();

        // === CHART DATA: 7 HARI TERAKHIR ===
        $last7Days = [];
        $salesLast7Days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = $date->format('D, d M'); // Format: Sen, 01 Jan

            $sales = BookingOrder::where('partner_id', $partner->id)
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
            ->where('booking_orders.partner_id', $partner->id)
            ->whereMonth('booking_orders.created_at', now()->month)
            ->whereYear('booking_orders.created_at', now()->year)
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->groupBy('order_details.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'product_name' => $product->product_name,
                    'total_quantity' => (int) $product->total_quantity,
                    'total_revenue' => (float) $product->total_revenue
                ];
            });

        // === PERFORMA PER KATEGORI PRODUK (BULAN INI) - 
        $categoryPerformance = DB::table('order_details')
            ->join('booking_orders', 'order_details.booking_order_id', '=', 'booking_orders.id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->join('categories', 'partner_products.category_id', '=', 'categories.id')
            ->select(
                'categories.category_name',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->where('booking_orders.partner_id', $partner->id)
            ->whereMonth('booking_orders.created_at', now()->month)
            ->whereYear('booking_orders.created_at', now()->year)
            ->whereIn('booking_orders.order_status', ['PAID', 'PROCESSED', 'SERVED'])
            ->groupBy('categories.id', 'categories.category_name')
            ->orderByDesc('total_quantity')
            ->get()
            ->map(function ($category) {
                return [
                    'category_name' => $category->category_name,
                    'total_quantity' => (int) $category->total_quantity
                ];
            });

        // Popup messages untuk partner
        $popups = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where('message_type', 'popup')
                    ->where(function ($qq) use ($partner) {
                        $qq->where(function ($qx) use ($partner) {
                            $qx->where('recipient_id', $partner->id)
                                ->where('recipient_type', 'outlet')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'outlet',
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

        // Timeline messages untuk partner
        $messages = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($partner) {
                        $qq->where(function ($qx) use ($partner) {
                            $qx->where('recipient_id', $partner->id)
                                ->where('recipient_type', 'outlet')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'outlet',
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
            ->paginate(5);

        $data = [
            'total_employees'        => $total_employees,
            'total_employees_active' => $total_employees_active,
            'total_accounts'         => $total_accounts,
            'total_sales'            => $total_sales,
            'total_orders'           => $total_orders,
            'total_products'         => $total_products,
            'last_orders'            => $last_orders,
            'messages'               => $messages,
            'popups'                 => $popups,

            // Stats hari ini
            'today_sales'            => $today_sales,
            'today_orders_paid'      => $today_orders_paid,

            // Chart data
            'last7Days'              => $last7Days,
            'salesLast7Days'         => $salesLast7Days,
            'topProducts'            => $topProducts,
            'categoryPerformance'    => $categoryPerformance, // [BARU DITAMBAHKAN]
        ];

        return view('pages.partner.dashboard.index', compact('data'));
    }

    public function timelineMessages(Request $request)
    {
        $partner = Auth::user();
        $now = Carbon::now();
        $page = $request->get('page', 1);

        $messages = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($partner) {
                        $qq->where(function ($qx) use ($partner) {
                            $qx->where('recipient_id', $partner->id)
                                ->where('recipient_type', 'outlet')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'outlet',
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
            ->paginate(5, ['*'], 'page', $page);

        return view('pages.partner.dashboard.partials.timeline-items', compact('messages'));
    }
}
