<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use Carbon\Carbon;

class PartnerMessageController extends Controller
{
    /**
     * Get paginated notification messages for dropdown
     */
    public function getNotificationMessages(Request $request)
    {
        $partner = Auth::user();
        $now = Carbon::now();

        $messages = Message::with(['recipients' => function ($q) use ($partner) {
            $q->where(function ($qq) use ($partner) {
                $qq->where('recipient_id', $partner->id)
                    ->where('recipient_type', 'outlet');
            })
                ->orWhere('recipient_target', 'broadcast');
        }, 'attachments', 'sender'])
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
            ->paginate(10);

        // Hitung unread messages untuk partner ini
        $unreadCount = $this->getUnreadCount($partner);

        return response()->json([
            'success' => true,
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark specific message as read
     */
    public function markMessageAsRead($id)
    {
        $partner = Auth::user();

        $recipient = MessageRecipient::whereHas('message', function ($q) use ($id) {
            $q->where('id', $id);
        })
            ->where('message_type', 'message')
            ->where(function ($q) use ($partner) {
                $q->where(function ($qq) use ($partner) {
                    $qq->where('recipient_id', $partner->id)
                        ->where('recipient_type', 'outlet')
                        ->where('recipient_target', 'single');
                })
                    ->orWhere(function ($qq) {
                        $qq->where('recipient_target', 'broadcast')
                            ->whereIn('recipient_type', ['outlet', 'business-partner', 'all']);
                    });
            })
            ->first();

        if ($recipient) {
            $recipient->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);

            // Get updated unread count
            $unreadCount = $this->getUnreadCount($partner);

            return response()->json([
                'success' => true,
                'message' => 'Message marked as read',
                'unread_count' => $unreadCount
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Message not found'
        ], 404);
    }

    /**
     * Mark all messages as read
     */
    public function markAllMessagesAsRead()
    {
        $partner = Auth::user();
        $now = Carbon::now();

        // Update untuk single recipient
        MessageRecipient::whereHas('message', function ($q) use ($now) {
            $q->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            });
        })
            ->where('message_type', 'message')
            ->where('is_read', false)
            ->where('recipient_id', $partner->id)
            ->where('recipient_type', 'outlet')
            ->where('recipient_target', 'single')
            ->update([
                'is_read' => true,
                'read_at' => $now
            ]);

        // Update untuk broadcast recipient
        MessageRecipient::whereHas('message', function ($q) use ($now) {
            $q->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            });
        })
            ->where('message_type', 'message')
            ->where('is_read', false)
            ->where('recipient_target', 'broadcast')
            ->whereIn('recipient_type', ['outlet', 'business-partner', 'all'])
            ->update([
                'is_read' => true,
                'read_at' => $now
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read',
            'unread_count' => 0
        ]);
    }

    /**
     * Get unread count for current partner
     */
    private function getUnreadCount($partner)
    {
        $now = Carbon::now();

        return MessageRecipient::whereHas('message', function ($q) use ($now) {
            $q->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', $now);
                })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    });
            });
        })
            ->where('message_type', 'message')
            ->where('is_read', false)
            ->where(function ($q) use ($partner) {
                $q->where(function ($qq) use ($partner) {
                    $qq->where('recipient_id', $partner->id)
                        ->where('recipient_type', 'outlet')
                        ->where('recipient_target', 'single');
                })
                    ->orWhere(function ($qq) {
                        $qq->where('recipient_target', 'broadcast')
                            ->whereIn('recipient_type', ['outlet', 'business-partner', 'all']);
                    });
            })
            ->count();
    }

    /**
     * Display all messages page
     */
    public function index()
    {
        return view('pages.partner.messages.message');
    }

    /**
     * Show specific message detail
     */
    public function show($id)
    {
        $partner = Auth::user();

        $message = Message::with(['recipients', 'attachments', 'sender'])
            ->whereHas('recipients', function ($q) use ($partner) {
                $q->where(function ($qq) use ($partner) {
                    $qq->where('recipient_id', $partner->id)
                        ->where('recipient_type', 'outlet');
                })
                    ->orWhere('recipient_target', 'broadcast');
            })
            ->findOrFail($id);

        return view('pages.partner.messages.show-message', compact('message'));
    }
}
