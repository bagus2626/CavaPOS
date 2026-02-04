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
        // dd($request->all());
        $employee = Employee::findOrFail(Auth::id());
        $partner  = $employee->partner;


        $payment = $request->string('payment')->toString(); // 'CASH' | 'QRIS' | ''
        $status  = $request->input('status', '');  // 'PAID' | 'UNPAID' | ''
        $q       = $request->string('q')->toString();


        $from = $request->date('from');
        $to   = $request->date('to');


        $needPaymentOrder = null;


        $unpaidOrder = BookingOrder::where('booking_order_code', $q)
            ->whereIn('order_status', ['UNPAID', 'EXPIRED'])
            ->first();
        if ($unpaidOrder) {
            $needPaymentOrder = $unpaidOrder;
        }
        // dd($needPaymentOrder);


        if (!$from && !$to) {
            $from = $to = Carbon::today();
        } elseif ($from && !$to) {
            $to = Carbon::today();
        } elseif (!$from && $to) {
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
            ->whereNotIn('order_status', ['PAYMENT'])
            ->where('partner_id', $partner->id)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            ->when($status !== null && $status !== '', function ($q2) use ($status) {
                if ($status === '0' || $status === '1') {
                    $q2->where('payment_flag', (int) $status);
                } elseif ($status === 'PROCESSED') {
                    $q2->whereIn('order_status', ['PROCESSED', 'PAID']);
                } else {
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


        $ordersToday = (clone $base)->latest()->get();
        // dd($ordersToday);
        $tabCounts = [
            'pembayaran' => $ordersToday
                ->whereIn('payment_method', ['CASH', 'QRIS'])
                ->whereIn('order_status', ['UNPAID', 'EXPIRED'])
                ->where('payment_flag', 0)
                ->count(),
            'proses' => $ordersToday
                ->whereIn('order_status', ['PROCESSED', 'PAID'])
                ->count(),
            'selesai' => $ordersToday
                ->where('order_status', 'SERVED')
                ->count(),
        ];
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
            'tabCounts',
            'metrics',
            'periodLabel',
            'needPaymentOrder'
        ));
    }


    public function show(Request $request, string $tab)
    {
        $partnerId = auth('employee')->user()->partner_id;
        $employeeId = auth('employee')->id();


        $payment = $request->string('payment')->toString();
        $status  = $request->string('status')->toString();
        $from = $request->date('from');
        $to   = $request->date('to');
        $q       = $request->string('q')->toString();


        $base = BookingOrder::query()
            ->with('table')
            ->where('partner_id', $partnerId)
            ->whereNotIn('order_status', ['PAYMENT'])
            ->when($from || $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [
                    Carbon::parse($from ?? $to)->startOfDay(),
                    Carbon::parse($to ?? $from)->endOfDay(),
                ]);
            })
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            ->when($status !== null && $status !== '', function ($q2) use ($status) {
                if ($status === '0' || $status === '1') {
                    $q2->where('payment_flag', (int) $status);
                } elseif ($status === 'PROCESSED') {
                    $q2->whereIn('order_status', ['PROCESSED', 'PAID']);
                } else {
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
                $items = (clone $base)->whereIn('payment_method', ['CASH', 'QRIS', 'manual_tf', 'manual_ewallet', 'manual_qris'])->whereIn('order_status', ['UNPAID', 'EXPIRED', 'PAYMENT REQUEST'])->latest()->get();
                return view('pages.employee.cashier.dashboard.tabs.pembayaran', compact('items'));
            case 'proses':
                $items = (clone $base)
                    ->whereIn('order_status', ['PROCESSED', 'PAID'])
                    ->where(function ($query) use ($employeeId) {
                        $query->where('cashier_process_id', $employeeId)
                            ->orWhereNull('cashier_process_id');
                    })
                    ->latest()
                    ->get();
                return view('pages.employee.cashier.dashboard.tabs.proses', compact('items', 'employeeId'));
            case 'selesai':
                $items = (clone $base)->where('order_status', 'SERVED')->whereDate('updated_at', Carbon::today())->latest()->get();
                return view('pages.employee.cashier.dashboard.tabs.selesai', compact('items'));
            default:
                abort(404);
        }
    }


    public function metrics(Request $request)
    {
        $ordersToday = $this->getOrdersTodayWithFilters($request);


        $unpaidCash = $ordersToday->where('payment_flag', 0)->count();
        $onProcess  = $ordersToday->whereIn('order_status', ['PROCESSED', 'PAID'])->count();
        $served     = $ordersToday->where('order_status', 'SERVED')->count();


        return response()->json([
            'total_order'       => $ordersToday->count(),
            'unpaid_cash'       => $unpaidCash,
            'paid_cash'         => $ordersToday->where('payment_method', 'CASH')->where('payment_flag', 1)->count(),
            'qris_paid'         => $ordersToday->where('payment_method', 'QRIS')->where('payment_flag', 1)->count(),
            'on_process'        => $onProcess,
            'revenue'           => $ordersToday->where('payment_flag', 1)->sum('total_order_value'),


            // 🔹 tambahan untuk badge & panel
            'badge_pembayaran'  => $unpaidCash,
            'badge_proses'      => $onProcess,
            'badge_selesai'     => $served,
            'pending_cash_count' => $unpaidCash, // atau logika lain kalau perlu beda
        ]);
    }




    protected function getOrdersTodayWithFilters(Request $request)
    {
        // ambil partner dari employee yang login
        $employee = Employee::findOrFail(Auth::id());
        $partner  = $employee->partner;


        // Ambil filter dari query string
        $payment = $request->string('payment')->toString(); // 'CASH' | 'QRIS' | ''
        $status  = $request->string('status')->toString();  // 'PAID' | 'UNPAID' | '' (bundle dengan index())
        $q       = $request->string('q')->toString();


        // Ambil tanggal dari request
        $from = $request->date('from');
        $to   = $request->date('to');


        // Atur default tanggal (sama seperti index)
        if (!$from && !$to) {
            $from = $to = Carbon::today();
        } elseif ($from && !$to) {
            $to = Carbon::today();
        } elseif (!$from && $to) {
            $from = Carbon::parse($to)->startOfMonth();
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


        // kembalikan collection order yang sudah difilter
        return $base->latest()->get();
    }


    public function activity(Request $request)
    {
        $employee = Employee::findOrFail(Auth::id());
        $partner  = $employee->partner;


        // Gunakan filter yang sama dengan index()
        $payment = $request->string('payment')->toString();
        $status  = $request->input('status', '');
        $q       = $request->string('q')->toString();
        $tab     = $request->input('tab', 'pembayaran'); // Ambil tab dari request


        $from = $request->date('from');
        $to   = $request->date('to');


        // Default tanggal (sama seperti index)
        if (!$from && !$to) {
            $from = $to = Carbon::today();
        } elseif ($from && !$to) {
            $to = Carbon::today();
        } elseif (!$from && $to) {
            $from = Carbon::parse($to)->startOfMonth();
        }


        // Base query yang SAMA dengan index()
        $baseQuery = BookingOrder::query()
            ->with('table')
            ->whereNotIn('order_status', ['PAYMENT'])
            ->where('partner_id', $partner->id)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            ->when($status !== null && $status !== '', function ($q2) use ($status) {
                if ($status === '0' || $status === '1') {
                    $q2->where('payment_flag', (int) $status);
                } elseif ($status === 'PROCESSED') {
                    $q2->whereIn('order_status', ['PROCESSED', 'PAID']);
                } else {
                    $q2->where('order_status', $status);
                }
            })
            ->when($q, function ($q2) use ($q) {
                $q2->where(function ($qq) use ($q) {
                    $qq->where('booking_order_code', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhereHas('table', fn($t) => $t->where('table_no', 'like', "%{$q}%"));
                });
            });


        // ⭐ FILTER BERDASARKAN TAB DI QUERY (SEBELUM PAGINATION)
        $baseQuery->when($tab, function ($q2) use ($tab) {
            switch ($tab) {
                case 'pembayaran':
                    $q2->whereIn('payment_method', ['CASH', 'QRIS'])
                        ->whereIn('order_status', ['UNPAID', 'EXPIRED'])
                        ->where('payment_flag', 0);
                    break;
                case 'proses':
                    $q2->whereIn('order_status', ['PROCESSED', 'PAID']);
                    break;
                case 'selesai':
                    $q2->where('order_status', 'SERVED');
                    break;
            }
        });


        $baseQuery->orderBy('created_at', 'DESC');


        // Clone untuk metrics (tanpa pagination, tanpa filter tab)
        $allOrdersQuery = BookingOrder::query()
            ->with('table')
            ->whereNotIn('order_status', ['PAYMENT'])
            ->where('partner_id', $partner->id)
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->when($payment, fn($q2) => $q2->where('payment_method', $payment))
            ->when($status !== null && $status !== '', function ($q2) use ($status) {
                if ($status === '0' || $status === '1') {
                    $q2->where('payment_flag', (int) $status);
                } elseif ($status === 'PROCESSED') {
                    $q2->whereIn('order_status', ['PROCESSED', 'PAID']);
                } else {
                    $q2->where('order_status', $status);
                }
            })
            ->when($q, function ($q2) use ($q) {
                $q2->where(function ($qq) use ($q) {
                    $qq->where('booking_order_code', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhereHas('table', fn($t) => $t->where('table_no', 'like', "%{$q}%"));
                });
            });


        $allOrders = $allOrdersQuery->get();


        // Paginate untuk tampilan tabel (10 data per halaman) - SUDAH TERFILTER BERDASARKAN TAB
        $ordersToday = $baseQuery->paginate(10)->appends($request->except('page'));


        // Hitung metrics dari semua data (tidak di-paginate, tidak filter tab)
        $metrics = [
            'total_order'   => $allOrders->count(),
            'unpaid'        => $allOrders->whereIn('order_status', ['UNPAID', 'EXPIRED'])->count(),
            'paid_cash'     => $allOrders->where('payment_method', 'CASH')->where('payment_flag', 1)->count(),
            'qris_paid'     => $allOrders->where('payment_method', 'QRIS')->where('payment_flag', 1)->count(),
            'processed'     => $allOrders->whereIn('order_status', ['PROCESSED', 'PAID'])->count(),
            'served'        => $allOrders->where('order_status', 'SERVED')->count(),
            'revenue'       => $allOrders->where('payment_flag', 1)->sum('total_order_value'),
        ];


        // Hitung tab counts dari semua data
        $tabCounts = [
            'pembayaran' => $allOrders
                ->whereIn('payment_method', ['CASH', 'QRIS'])
                ->whereIn('order_status', ['UNPAID', 'EXPIRED'])
                ->where('payment_flag', 0)
                ->count(),
            'proses' => $allOrders
                ->whereIn('order_status', ['PROCESSED', 'PAID'])
                ->count(),
            'selesai' => $allOrders
                ->where('order_status', 'SERVED')
                ->count(),
        ];


        return view('pages.employee.cashier.dashboard.tabs.activity', compact(
            'partner',
            'ordersToday',
            'metrics',
            'tabCounts',
            'from',
            'to'
        ));
    }
    public function openOrder($id)
    {
        $booking_order = BookingOrder::findOrFail($id);
        $tab = 'proses';


        // if (!in_array($booking_order->order_status, ['PROCESSED', 'PAID'])) {
        //     abort(404);
        // }


        if ($booking_order->order_status === 'PAID') {
            $tab = 'proses';
        } else {
            $tab = 'pembayaran';
        }


        return redirect()->route('employee.cashier.dashboard', [
            'open_order' => $id,
            'tab' => $tab
        ]);
    }
}