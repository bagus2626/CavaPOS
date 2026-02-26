<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use Carbon\Carbon;

class StaffMessageController extends Controller
{
    private function getEmployee()
    {
        return Auth::guard('employee')->user();
    }

    private function employeeId()
    {
        return $this->getEmployee()->id;
    }

    public function getNotificationMessages(Request $request)
    {
        $employeeId = $this->employeeId();
        $now        = Carbon::now();

        $messages = Message::with(['recipients', 'attachments', 'sender'])
            ->whereHas('recipients', function ($q) use ($employeeId) {
                $q->where('message_type', 'message')
                    ->where(function ($qq) use ($employeeId) {
                        $qq->where(function ($qx) use ($employeeId) {
                            $qx->where('recipient_id', $employeeId)
                                ->where('recipient_type', 'employee')
                                ->where('recipient_target', 'single');
                        })
                            ->orWhere(function ($qx) {
                                $qx->where('recipient_target', 'broadcast')
                                    ->whereIn('recipient_type', ['employee', 'all']);
                            });
                    });
            })
            ->where(function ($q) use ($now) {
                $q->where(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
                })->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                });
            })
            ->orderByRaw("COALESCE(scheduled_at, created_at) DESC")
            ->paginate(10);

        $unreadCount = $this->getUnreadCount($employeeId);

        return response()->json([
            'success'      => true,
            'messages'     => $messages->items(),
            'pagination'   => [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'per_page'     => $messages->perPage(),
                'total'        => $messages->total(),
            ],
            'unread_count' => $unreadCount,
        ]);
    }

    public function markMessageAsRead($id)
    {
        $employeeId = $this->employeeId();

        $recipient = MessageRecipient::whereHas('message', fn($q) => $q->where('id', $id))
            ->where('message_type', 'message')
            ->where(function ($q) use ($employeeId) {
                $q->where(function ($qq) use ($employeeId) {
                    $qq->where('recipient_id', $employeeId)
                        ->where('recipient_type', 'employee')
                        ->where('recipient_target', 'single');
                })
                    ->orWhere(function ($qq) {
                        $qq->where('recipient_target', 'broadcast')
                            ->whereIn('recipient_type', ['employee', 'all']);
                    });
            })
            ->first();

        if ($recipient) {
            $recipient->update(['is_read' => true, 'read_at' => Carbon::now()]);
            return response()->json([
                'success'      => true,
                'message'      => 'Pesan ditandai telah dibaca',
                'unread_count' => $this->getUnreadCount($employeeId),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan'], 404);
    }

    public function markAllMessagesAsRead()
    {
        $employeeId = $this->employeeId();
        $now        = Carbon::now();

        $baseQuery = fn($q) => $q->where(function ($q) use ($now) {
            $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
        });

        MessageRecipient::whereHas('message', $baseQuery)
            ->where('message_type', 'message')
            ->where('is_read', false)
            ->where('recipient_id', $employeeId)
            ->where('recipient_type', 'employee')
            ->where('recipient_target', 'single')
            ->update(['is_read' => true, 'read_at' => $now]);

        MessageRecipient::whereHas('message', $baseQuery)
            ->where('message_type', 'message')
            ->where('is_read', false)
            ->where('recipient_target', 'broadcast')
            ->whereIn('recipient_type', ['employee', 'all'])
            ->update(['is_read' => true, 'read_at' => $now]);

        return response()->json([
            'success'      => true,
            'message'      => 'Semua pesan telah dibaca',
            'unread_count' => 0,
        ]);
    }

    public function index()
    {
        $employee = $this->getEmployee();
        $empRole  = strtolower($employee->role ?? 'manager');

        return view('pages.employee.staff.messages.message', compact('empRole'));
    }

    public function show($id)
    {
        $employeeId = $this->employeeId();

        $message = Message::with(['recipients', 'attachments'])
            ->whereHas('recipients', function ($q) use ($employeeId) {
                $q->where(function ($qq) use ($employeeId) {
                    $qq->where('recipient_id', $employeeId)
                        ->where('recipient_type', 'employee');
                })
                    ->orWhere(function ($qq) {
                        $qq->where('recipient_target', 'broadcast')
                            ->whereIn('recipient_type', ['employee', 'all']);
                    });
            })
            ->findOrFail($id);

        // Tandai otomatis sebagai sudah dibaca saat dibuka
        $recipient = MessageRecipient::where('message_id', $id)
            ->where('message_type', 'message')
            ->where(function ($q) use ($employeeId) {
                $q->where(function ($qq) use ($employeeId) {
                    $qq->where('recipient_id', $employeeId)
                        ->where('recipient_type', 'employee')
                        ->where('recipient_target', 'single');
                })
                    ->orWhere(function ($qq) {
                        $qq->where('recipient_target', 'broadcast')
                            ->whereIn('recipient_type', ['employee', 'all']);
                    });
            })
            ->first();

        if ($recipient && !$recipient->is_read) {
            $recipient->update(['is_read' => true, 'read_at' => Carbon::now()]);
        }

        return view('pages.employee.staff.messages.show-message', compact('message'));
    }

    private function getUnreadCount($employeeId)
    {
        $now = Carbon::now();

        return MessageRecipient::whereHas('message', function ($q) use ($now) {
            $q->where(function ($q) use ($now) {
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', $now);
            })->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
        })
            ->where('message_type', 'message')
            ->where('is_read', false)
            ->where(function ($q) use ($employeeId) {
                $q->where(function ($qq) use ($employeeId) {
                    $qq->where('recipient_id', $employeeId)
                        ->where('recipient_type', 'employee')
                        ->where('recipient_target', 'single');
                })
                    ->orWhere(function ($qq) {
                        $qq->where('recipient_target', 'broadcast')
                            ->whereIn('recipient_type', ['employee', 'all']);
                    });
            })
            ->count();
    }
}