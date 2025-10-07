<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Exports\SalesReportExport;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SalesReportController extends Controller
{
    /**
     * Display sales report dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        $baseQuery = $this->buildFilteredQuery($filters);

        $data = [
            'totalRevenue'       => $this->calculateTotalRevenue(clone $baseQuery),
            'totalOrders'        => $this->calculateTotalItemsSold(clone $baseQuery),
            'totalBookingOrders' => $this->calculateTotalDiscreetOrders(clone $baseQuery),
            'revenueChartData'   => $this->getRevenueChartData(clone $baseQuery, $filters['period']),
            'categoryChartData'  => $this->getCategoryChartData(clone $baseQuery),
            'topProducts'        => $this->getTopProducts(clone $baseQuery),
            'recentTransactions' => $this->getRecentTransactions(clone $baseQuery),
            'indicatorText'      => $this->getIndicatorText($filters),
            'filters'            => $filters,
        ];

        return view('pages.owner.reports.sales', $data);
    }

    /**
     * Export sales report to Excel
     */
    public function export(Request $request)
    {
        $filters = $this->getFilters($request);
        $fileName = $this->generateFileName($filters);

        return Excel::download(new SalesReportExport($filters), $fileName);
    }

    /**
     * Get order details by ID (for modal)
     */
    public function getOrderDetails(Request $request, $id)
    {
        $orderDetails = DB::table('order_details')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->where('booking_order_id', $id)
            ->select(
                'order_details.id as order_detail_id',
                'partner_products.name',
                'order_details.quantity',
                'order_details.base_price',
                'order_details.options_price'
            )
            ->get();

        foreach ($orderDetails as $detail) {
            $options = DB::table('order_detail_options')
                ->where('order_detail_id', $detail->order_detail_id)
                ->join('partner_product_options', 'order_detail_options.option_id', '=', 'partner_product_options.id')
                ->select('partner_product_options.name', 'order_detail_options.price')
                ->get();

            $detail->options = $options;
        }

        return response()->json($orderDetails);
    }

    // ====== PRIVATE HELPER METHODS ======

    /**
     * Get and validate filters from request
     */
    private function getFilters(Request $request): array
    {
        $period = $request->input('period', 'daily');
        $filters = ['period' => $period];

        switch ($period) {
            case 'yearly':
                $filters['year_from'] = $request->input('year_from', date('Y'));
                $filters['year_to'] = $request->input('year_to', date('Y'));
                break;

            case 'monthly':
                $filters['month_year'] = $request->input('month_year', date('Y'));
                break;

            default: // daily
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

        // Ensure from and to dates are always set
        if (!isset($filters['from'])) {
            $filters['from'] = now()->toDateString();
        }
        if (!isset($filters['to'])) {
            $filters['to'] = now()->toDateString();
        }

        return $filters;
    }

    /**
     * Build base query with filters
     */
    private function buildFilteredQuery(array $filters): Builder
    {
        $query = DB::table('booking_orders')
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED']);

        switch ($filters['period']) {
            case 'yearly':
                $query->whereYear('booking_orders.created_at', '>=', $filters['year_from'])
                    ->whereYear('booking_orders.created_at', '<=', $filters['year_to']);
                break;

            case 'monthly':
                $query->whereYear('booking_orders.created_at', $filters['month_year']);
                break;

            default: // daily
                $query->whereDate('booking_orders.created_at', '>=', $filters['from'])
                    ->whereDate('booking_orders.created_at', '<=', $filters['to']);
                break;
        }

        return $query;
    }

    /**
     * Calculate total revenue from orders
     */
    private function calculateTotalRevenue(Builder $query): float
    {
        return $query->sum('total_order_value') ?? 0;
    }

    /**
     * Calculate total number of discrete orders
     */
    private function calculateTotalDiscreetOrders(Builder $query): int
    {
        return $query->count();
    }

    /**
     * Calculate total items/menu sold
     */
    private function calculateTotalItemsSold(Builder $query): int
    {
        $orderIds = $query->pluck('id');

        if ($orderIds->isEmpty()) {
            return 0;
        }

        return DB::table('order_details')
            ->whereIn('booking_order_id', $orderIds)
            ->sum('quantity') ?? 0;
    }

    /**
     * Get revenue data for chart based on period
     */
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

    /**
     * Get revenue data by category for pie chart
     */
    private function getCategoryChartData(Builder $query): array
    {
        $result = $query
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->join('categories', 'partner_products.category_id', '=', 'categories.id')
            ->select(
                'categories.category_name as label',
                DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total')
            )
            ->groupBy('categories.category_name')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total')
        ];
    }

    /**
     * Get top selling products
     */
    private function getTopProducts(Builder $query, ?int $paginate = null)
    {
        $query = $query
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->select(
                'partner_products.name',
                DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total_sales'),
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->groupBy('partner_products.name')
            ->orderBy('total_quantity', 'desc');

        if ($paginate) {
            return $query->paginate($paginate)->withQueryString();
        }

        return $query->get();
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions(Builder $query): \Illuminate\Support\Collection
    {
        return $query
            ->select('id', 'booking_order_code', 'total_order_value', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get indicator text for UI based on filter period
     */
    private function getIndicatorText(array $filters): string
    {
        return match ($filters['period']) {
            'yearly'  => "Tampilan Tahunan ({$filters['year_from']} - {$filters['year_to']})",
            'monthly' => "Tampilan Bulanan ({$filters['month_year']})",
            default   => 'Tampilan Harian (' . Carbon::parse($filters['from'])->format('d M') . ' - ' . Carbon::parse($filters['to'])->format('d M, Y') . ')',
        };
    }

    /**
     * Generate filename for Excel export based on filters
     */
    private function generateFileName(array $filters): string
    {
        $baseName = 'laporan-penjualan';

        switch ($filters['period']) {
            case 'yearly':
                if ($filters['year_from'] == $filters['year_to']) {
                    return "{$baseName}-tahunan-{$filters['year_from']}.xlsx";
                }
                return "{$baseName}-tahunan-{$filters['year_from']}-{$filters['year_to']}.xlsx";

            case 'monthly':
                return "{$baseName}-bulanan-{$filters['month_year']}.xlsx";

            default: // daily
                $from = Carbon::parse($filters['from'])->format('d-m-Y');
                $to = Carbon::parse($filters['to'])->format('d-m-Y');

                if ($filters['from'] == $filters['to']) {
                    return "{$baseName}-harian-{$from}.xlsx";
                }
                return "{$baseName}-harian-{$from}_sampai_{$to}.xlsx";
        }
    }
}
