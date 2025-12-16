<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store\Table;
use App\Models\User;

class CheckTableStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $partnerSlug = $request->route('partner_slug');
        $tableCode = $request->route('table_code');

        // Validasi partner exists
        $partner = User::where('slug', $partnerSlug)
            ->where('role', 'partner')
            ->first();

        if (!$partner) {
            abort(404, 'Partner tidak ditemukan');
        }

        // Validasi table exists
        $table = Table::where('table_code', $tableCode)
            ->where('partner_id', $partner->id)
            ->first();

        if (!$table) {
            abort(404, 'Meja tidak ditemukan');
        }

        // Check table status
        if ($table->status === 'available') {
            // Allow access untuk halaman menu/checkout
            return $next($request);
        }

        // Jika status bukan available, redirect ke halaman status
        return redirect()->route('customer.table.status', [
            'partner_slug' => $partnerSlug,
            'table_code' => $tableCode
        ]);
    }
}
