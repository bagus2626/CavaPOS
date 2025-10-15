<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesReportExcelService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{

    public function index(Request $request)
    {
        $filters = $this->getFilters($request);

        $ownerId = auth('owner')->id();
        $partners = User::where('owner_id', $ownerId)
            ->where('role', 'partner')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'partner_code', 'city', 'province']);

        $selectedPartner = null;
        if (!empty($filters['partner_id'])) {
            $selectedPartner = User::where('id', $filters['partner_id'])
                ->where('owner_id', $ownerId)
                ->first();
        }

        $baseQuery = $this->buildFilteredQuery($filters);

        $data = [
            'partners'           => $partners,
            'selectedPartner'    => $selectedPartner,
            'totalRevenue'       => $this->calculateTotalRevenue(clone $baseQuery),
            'totalOrders'        => $this->calculateTotalItemsSold(clone $baseQuery),
            'totalBookingOrders' => $this->calculateTotalDiscreetOrders(clone $baseQuery),
            'revenueChartData'   => $this->getRevenueChartData(clone $baseQuery, $filters['period']),
            'categoryChartData'  => $this->getCategoryChartData(clone $baseQuery),
            'topProducts'        => $this->getTopProducts(clone $baseQuery, $filters),
            'recentTransactions' => $this->getRecentTransactions(clone $baseQuery),
            'indicatorText'      => $this->getIndicatorText($filters),
            'filters'            => $filters,
        ];

        return view('pages.owner.reports.sales', $data);
    }

    public function getTopProductsAjax(Request $request)
    {
        $filters = $this->getFilters($request);
        $baseQuery = $this->buildFilteredQuery($filters);

        $products = $this->getTopProducts(clone $baseQuery, $filters);

        return response()->json($products);
    }

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

    public function export(Request $request)
    {
        try {
            $filters = $this->getFilters($request);
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

    private function getTopProducts(Builder $query, array $filters, ?int $paginate = null)
    {
        $sortDirection = $filters['sort_products'] === 'asc' ? 'asc' : 'desc';

        $query = $query
            ->join('order_details', 'booking_orders.id', '=', 'order_details.booking_order_id')
            ->join('partner_products', 'order_details.partner_product_id', '=', 'partner_products.id')
            ->select(
                'partner_products.name',
                DB::raw('SUM((order_details.base_price + order_details.options_price) * order_details.quantity) as total_sales'),
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->groupBy('partner_products.name')
            ->orderBy('total_quantity', $sortDirection);

        if ($paginate) {
            return $query->paginate($paginate)->withQueryString();
        }

        return $query->limit(7)->get();
    }

    private function getRecentTransactions(Builder $query)
    {
        return $query
            ->select('id', 'booking_order_code', 'total_order_value', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }


    public function buildFilteredQuery(array $filters): Builder
    {
        $ownerId = auth('owner')->id();

        $query = DB::table('booking_orders')
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED']);

        if (!empty($filters['partner_id'])) {
            $query->where('booking_orders.partner_id', $filters['partner_id']);
        } else {
            $partnerIds = User::where('owner_id', $ownerId)
                ->where('role', 'partner')
                ->pluck('id');

            $query->whereIn('booking_orders.partner_id', $partnerIds);
        }

        switch ($filters['period']) {
            case 'yearly':
                $query->whereYear('booking_orders.created_at', '>=', $filters['year_from'])
                    ->whereYear('booking_orders.created_at', '<=', $filters['year_to']);
                break;

            case 'monthly':
                $year = $filters['month_year'];
                $monthFrom = $filters['month_from'];
                $monthTo = $filters['month_to'];

                $startDate = Carbon::createFromDate($year, $monthFrom, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $monthTo, 1)->endOfMonth();

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

 
    private function getFilters(Request $request): array
    {
        $period = $request->input('period', 'daily');
        $filters = ['period' => $period];

        $filters['partner_id'] = $request->input('partner_id', '');

        switch ($period) {
            case 'yearly':
                $filters['year_from'] = $request->input('year_from', date('Y'));
                $filters['year_to'] = $request->input('year_to', date('Y'));
                break;

            case 'monthly':
                $filters['month_year'] = $request->input('month_year', date('Y'));
                $filters['month_from'] = $request->input('month_from', 1);
                $filters['month_to'] = $request->input('month_to', date('n'));
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

        if (!empty($filters['partner_id'])) {
            $partner = User::find($filters['partner_id']);
            if ($partner) {
                $periodText .= " - {$partner->name}";
            }
        }

        return $periodText;
    }

    private function getMonthlyIndicatorText(array $filters): string
    {
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $year = $filters['month_year'];
        $monthFrom = $monthNames[$filters['month_from']] ?? 'Januari';
        $monthTo = $monthNames[$filters['month_to']] ?? 'Desember';

        if ($filters['month_from'] == $filters['month_to']) {
            return "Tampilan Bulanan ({$monthFrom} {$year})";
        }

        return "Tampilan Bulanan ({$monthFrom} - {$monthTo} {$year})";
    }
}
