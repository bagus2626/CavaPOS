<?php

namespace App\Http\Controllers\Owner\Report;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // Siapkan semua filter yang diperlukan
        $filters = $this->getFilters($request);

        // Buat query dasar yang sudah difilter berdasarkan tanggal
        $baseQuery = $this->buildFilteredQuery($filters);

        // Hitung semua metrik yang dibutuhkan
        $totalRevenue = $this->calculateTotalRevenue(clone $baseQuery);
        $totalOrders = $this->calculateTotalOrders(clone $baseQuery); // <-- TAMBAHKAN BARIS INI

        // Kirim data yang sudah dihitung ke view
        return view('pages.owner.reports.sales', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders, // <-- TAMBAHKAN BARIS INI
            'filters' => $filters,
        ]);
    }

    // Fungsi ini hanya untuk mengambil dan menentukan nilai filter dari request.
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
            case 'weekly':
                $filters['week_month'] = $request->input('week_month', date('Y-m'));
                break;
            default: // daily
                $filters['from'] = $request->input('from', now()->toDateString());
                $filters['to'] = $request->input('to', now()->toDateString());
                break;
        }
        return $filters;
    }


    // Fungsi ini untuk membangun query dasar berdasarkan filter.
    private function buildFilteredQuery(array $filters): Builder
    {
        $query = DB::table('booking_orders')
            ->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED']);

        switch ($filters['period']) {
            case 'yearly':
                $query->whereYear('created_at', '>=', $filters['year_from'])
                    ->whereYear('created_at', '<=', $filters['year_to']);
                break;
            case 'monthly':
                $query->whereYear('created_at', $filters['month_year']);
                break;
            case 'weekly':
                $date = Carbon::parse($filters['week_month'] . '-01');
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
                break;
            default: // daily
                $query->whereDate('created_at', '>=', $filters['from'])
                    ->whereDate('created_at', '<=', $filters['to']);
                break;
        }
        return $query;
    }

    // Fungsi untuk menjumlahkan total revenue
    private function calculateTotalRevenue(Builder $query): float
    {
        return $query->sum('total_order_value');
    }


    // Fungsi untuk menjumlahkan total order
    private function calculateTotalOrders(Builder $query): int
    {
        // Menghitung jumlah baris/record dari query yang sudah difilter
        return $query->count();
    }
}
