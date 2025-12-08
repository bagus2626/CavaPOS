<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Transaction\BookingOrder;
use App\Models\Product\OutletProduct;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use App\Models\MessageNotification\MessageAttachment;
use App\Models\Partner\Products\PartnerProduct;
use Carbon\Carbon;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        $partner = Auth::user();
        $now = Carbon::now();

        // Total karyawan di outlet ini
        $total_employees = Employee::where('partner_id', $partner->id)->count();

        // Total produk aktif di outlet ini
        $total_products = PartnerProduct::where('partner_id', $partner->id)->count();

        // Total akun (partner + karyawan)
        $total_accounts = $total_employees + 1;

        // Orders untuk outlet ini
        $orders = BookingOrder::where('partner_id', $partner->id)
            ->whereYear('created_at', now()->year);

        $total_sales = $orders->sum('total_order_value');
        $total_orders = $orders->count();

        // 10 pesanan terakhir
        $last_orders = $orders->latest()->take(10)->get();

        // Popup messages untuk partner
        $popups = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where('message_type', 'popup')
                    ->where(function ($qq) use ($partner) {
                        $qq->where(function ($qx) use ($partner) {
                            $qx->where('recipient_id', $partner->id)
                                ->where('recipient_type', 'outlet')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'outlet',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->get();

        // Timeline messages untuk partner
        $messages = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($partner) {
                        $qq->where(function ($qx) use ($partner) {
                            $qx->where('recipient_id', $partner->id)
                                ->where('recipient_type', 'outlet')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'outlet',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->paginate(5);

        $data = [
            'total_employees'    => $total_employees,
            'total_accounts'     => $total_accounts,
            'total_sales'        => $total_sales,
            'total_orders'       => $total_orders,
            'total_products'     => $total_products,
            'last_orders'        => $last_orders,
            'messages'           => $messages,
            'popups'             => $popups,
        ];

        return view('pages.partner.dashboard.index', compact('data'));
    }

    public function timelineMessages(Request $request)
    {
        $partner = Auth::user();
        $now = Carbon::now();
        $page = $request->get('page', 1);

        $messages = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($partner) {
                        $qq->where(function ($qx) use ($partner) {
                            $qx->where('recipient_id', $partner->id)
                                ->where('recipient_type', 'outlet')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'outlet',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->paginate(5, ['*'], 'page', $page);

        return view('pages.partner.dashboard.partials.timeline-items', compact('messages'));
    }
}
