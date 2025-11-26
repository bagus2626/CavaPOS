<?php

namespace App\Http\Controllers\Employee\Dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use App\Models\Transaction\OrderDetailOption;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use App\Models\User;
use App\Models\Store\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CashierDashboardController extends Controller
{
    public function index(Request $request)
    {
        $employee = Employee::findOrFail(Auth::id());
        $partner  = $employee->partner;
        
        

        // Ambil filter dari query string
        $payment = $request->string('payment')->toString(); // 'CASH' | 'QRIS' | ''
        $status  = $request->string('status')->toString();  // 'PAID' | 'UNPAID' | ''
        $q       = $request->string('q')->toString();

        // Ambil tanggal dari request
        $from = $request->date('from');
        $to   = $request->date('to');

        // Atur default tanggal
        if (!$from && !$to) {
            // Default: hari ini
            $from = $to = Carbon::today();
        } elseif ($from && !$to) {
            // Kalau hanya "dari" → sampai hari ini
            $to = Carbon::today();
        } elseif (!$from && $to) {
            // Kalau hanya "sampai" → dari awal bulan (bisa kamu ubah sesuai kebutuhan)
            $from = Carbon::parse($to)->startOfMonth();
        }

        // Label periode untuk ditampilkan
        if ($from->equalTo($to)) {
            $periodLabel = $from->translatedFormat('d F Y');
        } else {
            $periodLabel = $from->translatedFormat('d F Y') . " - " . $to->translatedFormat('d F Y');
        }

        // Base query: partner + rentang tanggal (inklusif)
        $base = BookingOrder::query()
            ->with('table')
            ->where('partner_id', $partner->id)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            ->when($status,  fn($q2) => $q2->where('order_status', $status))
            ->when($q, function ($q2) use ($q) {
                $q2->where(function ($qq) use ($q) {
                    $qq->where('booking_order_code', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhereHas('table', fn($t) => $t->where('table_no', 'like', "%{$q}%"));
                });
            })
            ->orderBy('created_at', 'ASC');

        $ordersToday = (clone $base)->latest()->get();
        $pendingCashOrders = (clone $base)
            ->where('payment_method', 'CASH')
            ->where('payment_flag', 0)
            ->latest()->get();

        $metrics = [
            'qris_paid'     => (clone $base)->where('payment_method', 'QRIS')->where('order_status', 'PAID')->count(),
            'revenue_today' => (clone $base)->where('order_status', 'PAID')->sum('total_order_value'),
        ];

        return view('pages.employee.cashier.dashboard.index', compact(
            'employee',
            'partner',
            'pendingCashOrders',
            'ordersToday',
            'metrics',
            'periodLabel'
        ));
    }


    public function show(Request $request, string $tab)
    {
        $partnerId = auth('employee')->user()->partner_id;
        $employeeId = auth('employee')->id();

        $payment = $request->string('payment')->toString();
        $status  = $request->string('order_status')->toString();
        $from    = $request->date('from') ?: Carbon::today();
        $to      = $request->date('to')   ?: Carbon::today();
        $q       = $request->string('q')->toString();

        $base = BookingOrder::query()
            ->with('table')
            ->where('partner_id', $partnerId)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            // ->when($status,  fn($q2) => $q2->where('order_status', $status))
            ->when($status !== null && $status !== '', function ($q2) use ($status) {
                if (in_array($status, [0, 1], true)) {
                    // kalau status berupa flag 0/1
                    $q2->where('payment_flag', $status);
                } else {
                    // kalau status berupa string (PAID/UNPAID)
                    $q2->where('order_status', $status);
                }
            })
            ->when($q, function ($q2) use ($q) {
                $q2->where(function ($qq) use ($q) {
                    $qq->where('booking_order_code', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhereHas('table', fn($t) => $t->where('table_no', 'like', "%{$q}%"));
                });
            })

            ->orderBy('created_at', 'ASC');

        switch ($tab) {
            case 'pembelian':

                $partner = User::findOrFail($partnerId);
                $partner_products = PartnerProduct::with([
                    'category',
                    'promotion' => function ($q) {
                        $q->activeToday();
                    },
                    'stock',
                    'parent_options.options.stock',
                ])
                    ->where('partner_id', $partner->id)
                    ->where('is_active', 1)
                    ->get();

                $categories = Category::whereIn('id', $partner_products->pluck('category_id'))->get();
                $tables = Table::where('partner_id', $partner->id)->orderBy('table_no', 'ASC')->get();

                return view('pages.employee.cashier.dashboard.tabs.pembelian', compact('partner', 'partner_products', 'categories', 'tables'));
            case 'pembayaran':
                $items = (clone $base)->where('payment_method', 'CASH')->where('order_status', 'UNPAID')->latest()->get();
                return view('pages.employee.cashier.dashboard.tabs.pembayaran', compact('items'));
            case 'proses':
                $items = (clone $base)
                ->whereIn('order_status', ['PROCESSED', 'PAID'])
                ->where(function($query) use ($employeeId) {
                    $query->where('cashier_process_id', $employeeId)
                          ->orWhereNull('cashier_process_id'); 
                })
                ->latest()
                ->get();
                return view('pages.employee.cashier.dashboard.tabs.proses', compact('items', 'employeeId'));
            case 'selesai':
                $items = (clone $base)->where('order_status', 'SERVED')->latest()->get();
                return view('pages.employee.cashier.dashboard.tabs.selesai', compact('items'));
            default:
                abort(404);
        }
    }

}
