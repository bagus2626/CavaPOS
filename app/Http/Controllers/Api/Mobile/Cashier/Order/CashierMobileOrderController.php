<?php

namespace App\Http\Controllers\Api\Mobile\Cashier\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Product\Product;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Transaction\BookingOrder;
use App\Models\Admin\Product\Category;
use App\Models\User;
use App\Models\Store\Table;

class CashierMobileOrderController extends Controller
{
    public function getProducts()
    {
        $partnerId = auth('employee_api')->user()->partner_id;
        $employeeId = auth('employee_api')->id();

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

        return response()->json([
            'debug' => true,
            'partner' => $partnerId,
            'partner_products' => $partner_products,
            'categories' => $categories,
            'tables' => $tables,
        ]);
    }
}
