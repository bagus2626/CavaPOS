<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Transaction\BookingOrder;
use App\Models\Product\MasterProduct;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use App\Models\MessageNotification\MessageAttachment;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        $now = Carbon::now();
        // dd($owner);

        $outlets = User::where('owner_id', $owner->id)
            ->where('role', 'partner');

        $total_products = MasterProduct::where('owner_id', $owner->id)->count();

        $total_outlets = $outlets->count();
        $outlet_ids = $outlets->pluck('id')->toArray();
        $total_employees = Employee::whereIn('partner_id', $outlet_ids)->count();
        $total_accounts = $total_outlets + $total_employees + 1;
        $orders = BookingOrder::whereIn('partner_id', $outlet_ids)
            ->whereYear('created_at', now()->year);

        $orders_gross_income = $orders->sum('total_order_value');
        $total_orders = $orders->count();

        $last_orders = $orders->whereIn('order_status', ['PAID', 'PROCESSED', 'SERVED'])->latest()->take(10)->get();

        $popups = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($owner) {
                $q->where('message_type', 'popup')
                    ->where(function ($qq) use ($owner) {
                        $qq->where(function ($qx) use ($owner) {
                            $qx->where('recipient_id', $owner->id)
                                ->where('recipient_type', 'owner')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'owner',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                // Pesan sudah waktunya ditampilkan
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    // Pesan belum expired
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->get();

        $messages = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($owner) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($owner) {
                        $qq->where(function ($qx) use ($owner) {
                            $qx->where('recipient_id', $owner->id)
                                ->where('recipient_type', 'owner')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'owner',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                // Pesan sudah waktunya ditampilkan
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    // Pesan belum expired
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->paginate(5);

        // ->get();


        // dd($messages);

        $data = [
            'total_outlets'      => $total_outlets,
            'total_employees'    => $total_employees,
            'total_accounts'     => $total_accounts,
            'orders_gross_income'=> $orders_gross_income,
            'total_orders'       => $total_orders,
            'total_products'     => $total_products,
            'last_orders'        => $last_orders,
            'messages'           => $messages,
            'popups'             => $popups,
        ];

        // dd($data);

        return view('pages.owner.dashboard.index', compact('data'));
    }

    public function timelineMessages(Request $request)
    {
        $owner = Auth::user();
        $now = Carbon::now();
        $page = $request->get('page', 1);

        $messages = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($owner) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($owner) {
                        $qq->where(function ($qx) use ($owner) {
                            $qx->where('recipient_id', $owner->id)
                                ->where('recipient_type', 'owner')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', [
                                        'owner',
                                        'business-partner',
                                        'all'
                                    ]);
                            });
                    });
            })
            ->where(function ($query) use ($now) {
                // Pesan sudah waktunya ditampilkan
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    // Pesan belum expired
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->paginate(5, ['*'], 'page', $page);

        return view('pages.owner.dashboard.partials.timeline-items', compact('messages'));
    }

}
