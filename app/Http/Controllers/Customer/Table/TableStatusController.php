<?php

namespace App\Http\Controllers\Customer\Table;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store\Table;
use App\Models\User;

class TableStatusController extends Controller
{
    public function show(Request $request, $partner_slug, $table_code)
    {
        $partner = User::where('slug', $partner_slug)
            ->where('role', 'partner')
            ->firstOrFail();

        $table = Table::where('table_code', $table_code)
            ->where('partner_id', $partner->id)
            ->firstOrFail();

        // Jika status available, redirect ke menu
        if ($table->status === 'available') {
            return redirect()->route('customer.menu.index', [
                'partner_slug' => $partner_slug,
                'table_code' => $table_code
            ]);
        }

        // Tampilkan halaman status untuk occupied, reserved, not_available
        return view('pages.customer.table.table-status', [
            'partner' => $partner,
            'table' => $table,
            'partner_slug' => $partner_slug,
            'table_code' => $table_code,
        ]);
    }
}
