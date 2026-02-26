<?php

namespace App\Http\Controllers\Employee\Staff\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesReportExcelService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffSalesReportController extends Controller
{
    /**
     * Mendapatkan Data Konteks (Employee, Partner, Owner)
     */
    private function getContext()
    {
        $employee = Auth::guard('employee')->user();
        $partner = User::findOrFail($employee->partner_id);

        return (object)[
            'employee' => $employee,
            'partner_id' => $partner->id,
            'owner_id' => $partner->owner_id,
            'role_prefix' => strtolower($employee->role)
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getContext();
        $filters = $this->getFilters($request, $context->partner_id);

        $baseQuery = $this->buildFilteredQuery($filters);

        $data = [
            // Variabel Partner/Outlet dihilangkan karena staff hanya punya 1
            'totalRevenue'       => $this->calculateTotalRevenue(clone $baseQuery),
            'totalOrders'        => $this->calculateTotalItemsSold(clone $baseQuery),
            'totalBookingOrders' => $this->calculateTotalDiscreetOrders(clone $baseQuery),
            'revenueChartData'   => $this->getRevenueChartData(clone $baseQuery, $filters['period']),
            'categoryChartData'  => $this->getCategoryChartData(clone $baseQuery),
            'topProducts'        => $this->getTopProducts(clone $baseQuery, $filters, 10),
            'topProductsChart'   => $this->getTopProductsChartData(clone $baseQuery),
            'paymentMethodChart' => $this->getPaymentMethodChartData(clone $baseQuery),
            'paymentRevenueChart' => $this->getPaymentRevenueChartData(clone $baseQuery),
            'indicatorText'      => $this->getIndicatorText($filters),
            'filters'            => $filters,
            'routePrefix'        => $context->role_prefix,
        ];

        return view('pages.employee.staff.report.sales-report.index', $data);
    }

    public function getTopProductsAjax(Request $request)
    {
        $context = $this->getContext();
        $filters = $this->getFilters($request, $context->partner_id);
        $baseQuery = $this->buildFilteredQuery($filters);

        $products = $this->getTopProducts(clone $baseQuery, $filters);

        return response()->json($products);
    }

    public function export(Request $request)
    {
        try {
            $context = $this->getContext();
            $filters = $this->getFilters($request, $context->partner_id);
            
            // Service Excel ini butuh menyesuaikan logic view dan data jika di dalamnya ada hardcode
            $excelService = new SalesReportExcelService();

            return $excelService->export($filters, $this);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }


    private function calculateTotalRevenue(Builder $query): float
    {
        return $query->sum('total_order_value') ?? 0;
    }

    private function calculateTotalDiscreetOrders(Builder $query): int
    {
        return $query->count();
    }

    private function calculateTotalItemsSold(Builder $query): int
    {
        $subQuery = $query->toSql();
        $bindings = $query->getBindings();

        return DB::table(DB::raw("({$subQuery}) as filtered_orders"))
            ->mergeBindings($query)
            ->join('order_details', 'filtered_orders.id', '=', 'order_details.booking_order_id')
            ->sum('order_details.quantity') ?? 0;
    }

    private function getRevenueChartData(Builder $query, string $period): array
    {
        $groupBy = match ($period) {
            'yearly'  => DB::raw('YEAR(booking_orders.created_at) as label'),
            'monthly' => DB::raw("DATE_FORMAT(booking_orders.created_at, '%b %Y') as label"),
            default   => DB::raw("DATE_FORMAT(booking_orders.created_at, '%d %b') as label"),
        };

        $result = $query
            ->select($groupBy, DB::raw('SUM(total_order_value) as total'))
            ->groupBy('label')
            ->orderBy(DB::raw('MIN(booking_orders.created_at)'))
            ->get();

        return [
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total')
        ];
    }

    private function getCategoryChartData(Builder $query): array
    {
        $result = $query
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->join('categories', 'partner_products.category_id', '=', 'categories.id')
            ->select(
                'categories.category_name as label',
                DB::raw('SUM(order_details.quantity) as total')
            )
            ->groupBy('categories.category_name')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total')
        ];
    }

    private function getTopProductsChartData(Builder $query): array
    {
        $result = $query
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->select(
                'partner_products.name as label',
                DB::raw('SUM(order_details.quantity) as total')
            )
            ->groupBy('partner_products.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return [
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total')
        ];
    }

    private function getPaymentMethodChartData(Builder $query): array
    {
        $result = $query
            ->select(
                DB::raw("CASE 
                    WHEN payment_method = 'cash' THEN 'Cash'
                    WHEN payment_method = 'qris' THEN 'QRIS'
                    ELSE 'Lainnya'
                END as label"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('label')
            ->get();

        return [
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total')
        ];
    }

    private function getPaymentRevenueChartData(Builder $query): array
    {
        $result = $query
            ->select(
                DB::raw("CASE 
                    WHEN payment_method = 'cash' THEN 'Cash'
                    WHEN payment_method = 'qris' THEN 'QRIS'
                    ELSE 'Lainnya'
                END as label"),
                DB::raw('SUM(total_order_value) as total')
            )
            ->groupBy('label')
            ->get();

        return [
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total')
        ];
    }

    private function getTopProducts(Builder $query, array $filters, ?int $paginate = null)
    {
        $sortDirection = $filters['sort_products'] === 'asc' ? 'asc' : 'desc';

        $query = $query
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id');

        // Untuk Staff, kita selalu menggunakan query yang sifatnya spesifik per outlet (bukan global)
        $query->select(
            'partner_products.id',
            'partner_products.name',
            'partner_products.pictures',
            'partner_products.is_hot_product',
            DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total_sales'),
            DB::raw('SUM(order_details.quantity) as total_quantity')
        )->groupBy(
            'partner_products.id',
            'partner_products.name',
            'partner_products.pictures',
            'partner_products.is_hot_product'
        );

        $query->orderBy('total_quantity', $sortDirection);

        if ($paginate) {
            return $query->paginate($paginate)->withQueryString();
        }

        return $query->get();
    }

    public function buildFilteredQuery(array $filters): Builder
    {
        $query = DB::table('booking_orders')
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED']);

        // Paksa filter menggunakan partner_id dari staff yang sedang login
        $query->where('booking_orders.partner_id', $filters['partner_id']);

        switch ($filters['period']) {
            case 'yearly':
                $query->whereYear('booking_orders.created_at', '>=', $filters['year_from'])
                    ->whereYear('booking_orders.created_at', '<=', $filters['year_to']);
                break;

            case 'monthly':
                $startDate = Carbon::createFromDate(
                    $filters['month_from_year'],
                    $filters['month_from_month'],
                    1
                )->startOfMonth();

                $endDate = Carbon::createFromDate(
                    $filters['month_to_year'],
                    $filters['month_to_month'],
                    1
                )->endOfMonth();

                $query->whereBetween('booking_orders.created_at', [$startDate, $endDate]);
                break;

            default:
                $query->whereDate('booking_orders.created_at', '>=', $filters['from'])
                    ->whereDate('booking_orders.created_at', '<=', $filters['to']);
                break;
        }

        return $query;
    }

    public function buildRawQueryForExport(array $filters)
    {
        $baseQuery = $this->buildFilteredQuery($filters);

        return DB::table(DB::raw("({$baseQuery->toSql()}) as filtered_orders"))
            ->mergeBindings($baseQuery)
            ->join('order_details', 'filtered_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->join('categories', 'partner_products.category_id', '=', 'categories.id')
            ->select(
                'filtered_orders.created_at as tanggal',
                'filtered_orders.booking_order_code as booking_order',
                'partner_products.name as menu',
                'categories.category_name as kategori',
                'order_details.quantity as jumlah',
                DB::raw('order_details.base_price + order_details.options_price as harga_satuan'),
                'filtered_orders.payment_method as pembayaran'
            )
            ->orderBy('filtered_orders.created_at', 'asc');
    }


    private function getFilters(Request $request, $partnerId): array
    {
        $period = $request->input('period', 'daily');
        $filters = ['period' => $period];

        // Pastikan partner_id terkunci ke staf
        $filters['partner_id'] = $partnerId;

        switch ($period) {
            case 'yearly':
                $filters['year_from'] = $request->input('year_from', date('Y'));
                $filters['year_to'] = $request->input('year_to', date('Y'));
                break;

            case 'monthly':
                $monthFrom = $request->input('month_from', date('Y-m'));
                $monthTo = $request->input('month_to', date('Y-m'));

                $fromParts = explode('-', $monthFrom);
                $toParts = explode('-', $monthTo);

                $filters['month_from'] = $monthFrom;
                $filters['month_to'] = $monthTo;

                $filters['month_from_year'] = $fromParts[0] ?? date('Y');
                $filters['month_from_month'] = $fromParts[1] ?? '01';
                $filters['month_to_year'] = $toParts[0] ?? date('Y');
                $filters['month_to_month'] = $toParts[1] ?? '12';
                break;

            default:
                $from = $request->input('from');
                $to = $request->input('to');

                if (empty($from) && empty($to)) {
                    $from = $to = now()->toDateString();
                } elseif (!empty($from) && empty($to)) {
                    $to = now()->toDateString();
                } elseif (empty($from) && !empty($to)) {
                    $from = $to;
                }

                $filters['from'] = $from;
                $filters['to'] = $to;
                break;
        }

        if (!isset($filters['from'])) {
            $filters['from'] = now()->toDateString();
        }
        if (!isset($filters['to'])) {
            $filters['to'] = now()->toDateString();
        }

        $filters['sort_products'] = $request->input('sort_products', 'desc');

        return $filters;
    }

    private function getIndicatorText(array $filters): string
    {
        $periodText = match ($filters['period']) {
            'yearly'  => "Tampilan Tahunan ({$filters['year_from']} - {$filters['year_to']})",
            'monthly' => $this->getMonthlyIndicatorText($filters),
            default   => 'Tampilan Harian (' . Carbon::parse($filters['from'])->format('d M') . ' - ' . Carbon::parse($filters['to'])->format('d M, Y') . ')',
        };

        // Menghapus penambahan nama partner di sini karena untuk Staf ini tidak relevan lagi
        // (Semua data adalah milik outlet dia sendiri)

        return $periodText;
    }

    private function getMonthlyIndicatorText(array $filters): string
    {
        $monthNames = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $fromParts = explode('-', $filters['month_from']);
        $toParts = explode('-', $filters['month_to']);

        $yearFrom = $fromParts[0] ?? date('Y');
        $monthFrom = $monthNames[$fromParts[1]] ?? 'Januari';

        $yearTo = $toParts[0] ?? date('Y');
        $monthTo = $monthNames[$toParts[1]] ?? 'Desember';

        if ($filters['month_from'] === $filters['month_to']) {
            return "Tampilan Bulanan ({$monthFrom} {$yearFrom})";
        }

        if ($yearFrom === $yearTo) {
            return "Tampilan Bulanan ({$monthFrom} - {$monthTo} {$yearFrom})";
        }

        return "Tampilan Bulanan ({$monthFrom} {$yearFrom} - {$monthTo} {$yearTo})";
    }
}