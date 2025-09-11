<?php

namespace App\Http\Controllers\Customer\Transaction;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CustomerPaymentController extends Controller
{
    public function index($partner_slug, $table_code)
    {

    }

    public function getPaymentCash(Request $request, $partner_slug, $table_code)
    {
        try {
            // Ambil token dari query dan decrypt
            $payload = Crypt::decrypt($request->query('token')); // hasilnya array

            $partnerSlugDecrypted = $payload['p'] ?? null;
            $tableCodeDecrypted   = $payload['t'] ?? null;
            $orderId     = $payload['o'] ?? null;

            // dd($partnerSlugDecrypted, $tableCodeDecrypted, $orderId);

            if (!$partnerSlugDecrypted || !$tableCodeDecrypted || !$orderId) {
                abort(400, 'Token tidak valid.');
            }

            $order = BookingOrder::findOrFail($orderId);
            $partner = User::where('slug', $partner_slug)->first();
            $table = Table::where('table_code', $table_code)->first();
            return view('pages.customer.payment.cash.index', compact('order', 'partner', 'table'));

            // return response()->json([
            //     'ok' => true,
            //     'partner' => $partnerSlug,
            //     'table' => $tableCode,
            //     'order_id' => $orderId,
            //     'flash' => session('success')
            // ]);
        } catch (\Throwable $e) {
            // Signature/expiry sudah dicek oleh middleware `signed`,
            // ini untuk jaga-jaga kalau decrypt gagal.
            abort(403, 'Token tidak valid atau kadaluarsa.');
        }
    }
}
