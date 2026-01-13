<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use Carbon\Carbon;

class OwnerMessageController extends Controller
{
    /**
     * Get paginated notification messages for dropdown
     */
    public function getNotificationMessages(Request $request)
    {
        $owner = Auth::user();
        $now = Carbon::now();
        
        $messages = Message::with(['recipients' => function($q) use ($owner) {
                $q->where(function($qq) use ($owner) {
                    $qq->where('recipient_id', $owner->id)
                       ->where('recipient_type', 'owner');
                })
                ->orWhere('recipient_target', 'broadcast');
            }, 'attachments', 'sender'])
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

        // Hitung unread messages untuk owner ini
        $unreadCount = $this->getUnreadCount($owner);

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
        $owner = Auth::user();
        
        $recipient = MessageRecipient::whereHas('message', function($q) use ($id) {
            $q->where('id', $id);
        })
        ->where('message_type', 'message')
        ->where(function ($q) use ($owner) {
            $q->where(function($qq) use ($owner) {
                $qq->where('recipient_id', $owner->id)
                   ->where('recipient_type', 'owner')
                   ->where('recipient_target', 'single');
            })
            ->orWhere(function($qq) {
                $qq->where('recipient_target', 'broadcast')
                   ->whereIn('recipient_type', ['owner', 'business-partner', 'all']);
            });
        })
        ->first();

        if ($recipient) {
            $recipient->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);

            // Get updated unread count
            $unreadCount = $this->getUnreadCount($owner);

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
        $owner = Auth::user();
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
        ->where('recipient_id', $owner->id)
        ->where('recipient_type', 'owner')
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
        ->whereIn('recipient_type', ['owner', 'business-partner', 'all'])
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
     * Get unread count for current owner
     */
    private function getUnreadCount($owner)
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
        ->where(function ($q) use ($owner) {
            $q->where(function($qq) use ($owner) {
                $qq->where('recipient_id', $owner->id)
                   ->where('recipient_type', 'owner')
                   ->where('recipient_target', 'single');
            })
            ->orWhere(function($qq) {
                $qq->where('recipient_target', 'broadcast')
                   ->whereIn('recipient_type', ['owner', 'business-partner', 'all']);
            });
        })
        ->count();
    }

    /**
     * Display all messages page (untuk future implementation)
     */
    public function index()
    {
        return view('pages.owner.messages.message');
    }

    /**
     * Show specific message detail
     */
    public function show($id)
    {
        $owner = Auth::user();
        
        $message = Message::with(['recipients', 'attachments', 'sender'])
            ->whereHas('recipients', function ($q) use ($owner) {
                $q->where(function ($qq) use ($owner) {
                    $qq->where('recipient_id', $owner->id)
                       ->where('recipient_type', 'owner');
                })
                ->orWhere('recipient_target', 'broadcast');
            })
            ->findOrFail($id);

        // Mark as read akan dilakukan via AJAX di view

        return view('pages.owner.messages.show-message', compact('message'));
    }
}