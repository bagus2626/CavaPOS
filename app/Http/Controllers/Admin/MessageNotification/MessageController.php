<?php

namespace App\Http\Controllers\Admin\MessageNotification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MessageNotification\Message;
use App\Models\MessageNotification\MessageRecipient;
use App\Models\MessageNotification\MessageAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Owner;
use App\Models\Partner\HumanResource\Employee;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder', 'inbox');
        $search = $request->get('search');
        $user   = Auth::user();

        $query = Message::query()
            ->withCount('recipients')
            ->orderByDesc('created_at');

        // Filter berdasarkan folder
        switch ($folder) {
            case 'sent':
                $query->where('sender_id', $user->id)
                    ->where('status', 'sent');
                break;

            case 'broadcast':
                $query->where('sender_id', $user->id)
                    ->where('type', 'broadcast')
                    ->where('status', 'sent');
                break;

            case 'popup':
                $query->where('sender_id', $user->id)
                    ->whereHas('recipients', function ($q) use ($user) {
                        $q->where('message_type', 'popup');
                    });
                break;

            case 'inbox':
            default:
                $query->whereHas('recipients', function ($q) use ($user) {
                    $q->where('recipient_id', $user->id)
                        ->where('recipient_type', 'admin')
                        ->where('recipient_target', 'single');
                });
                break;
        }

        // Filter berdasarkan search query
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $messages = $query->with([
            'attachments',
            'recipients.outlets',
            'recipients.owners',
            'recipients.employees'
        ])->paginate(10)->withQueryString();

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'messages' => $messages->map(function ($msg) {
                    // Format data untuk response JSON
                    $broadcastRecipient = $msg->recipients()
                        ->where('recipient_target', 'broadcast')
                        ->first();

                    $recipients = $msg->recipients()
                        ->where('recipient_target', 'single')
                        ->get()
                        ->map(function ($recip) {
                            return [
                                'name' => $recip->recipient_type === 'owner'
                                    ? ($recip->owners->name ?? '-')
                                    : ($recip->recipient_type === 'outlet'
                                        ? ($recip->outlets->name ?? '-')
                                        : ($recip->employees->name ?? '-')),
                                'recipient_type' => $recip->recipient_type
                            ];
                        });

                    return [
                        'id' => $msg->id,
                        'title' => $msg->title,
                        'body' => $msg->body,
                        'type' => $msg->type,
                        'created_at_human' => optional($msg->created_at)->diffForHumans(),
                        'broadcast_recipient_type' => $broadcastRecipient->recipient_type ?? null,
                        'recipients' => $recipients
                    ];
                }),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'from' => $messages->firstItem(),
                    'to' => $messages->lastItem()
                ]
            ]);
        }

        // Return view normal untuk non-AJAX request
        $currentMessage = null;
        if ($request->filled('message_id')) {
            $currentMessage = Message::with(['attachments', 'recipients'])
                ->find($request->get('message_id'));
        }

        return view('pages.admin.messages-notification.messages.index', [
            'messages'       => $messages,
            'folder'         => $folder,
            'currentMessage' => $currentMessage,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        // 1. Validasi awal
        $data = $request->validate([
            'target' => 'required|in:single,broadcast',
            'target_group'    => 'required|in:all,business-partner,owner,outlet,employee,end-customer',
            'subject'   => 'required|string|max:255',
            'body'      => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // 2. Simpan ke database
            $message = Message::create([
                'sender_id'    => $user->id,
                'sender_role' => 'admin',
                'title'        => $data['subject'],
                'body'         => $data['body'],
                'type'      => $data['target'],
                'status'    => 'sent'
            ]);

            if ($request->filled('popup_link') && $request->message_type === 'popup') {
                $link = $request->popup_link;

                // Auto tambah https:// jika tidak ada protokol
                if (!preg_match('/^https?:\/\//i', $link)) {
                    $link = 'https://' . $link;
                }

                $message->body = '<a href="' . $link . '" target="_blank">' . $link . '</a>';
                $message->save();
            }


            if ($request->filled('schedule_start')) {
                $message->scheduled_at = $request->input('schedule_start');
                $message->save();
            }
            if ($request->filled('schedule_end')) {
                $message->expires_at = $request->input('schedule_end');
                $message->save();
            }

            if ($request->hasFile('attachments')) {
                // dd($request->file('attachments'));
                foreach ($request->file('attachments') as $file) {
                    if (!$file) continue;

                    $attachmentPath = $file->store('message_attachments', 'public');

                    $attachment = MessageAttachment::create([
                        'message_id' => $message->id,
                        'file_path'  => $attachmentPath,
                        'file_name'  => $file->getClientOriginalName(),
                        'mime_type'  => $file->getClientMimeType(),
                        'file_size'  => $file->getSize(),
                    ]);
                }
            }


            if ($request->target === 'single') {

                if ($request->filled('recipients_meta')) {
                    // Ubah JSON string jadi array PHP
                    $recipients = json_decode($request->input('recipients_meta'), true);

                    // Pastikan hasil decode adalah array
                    if (!is_array($recipients)) {
                        throw new \RuntimeException('recipients_meta is not a valid JSON array');
                    }

                    foreach ($recipients as $recipient) {
                        // dd($recipient);

                        MessageRecipient::create([
                            'message_id'       => $message->id,
                            'message_type'     => $request->message_type,
                            'recipient_target' => $request->target,
                            'recipient_type'   => $recipient['role'] ?? null,
                            'recipient_id'     => $recipient['id'] ?? null,
                            'is_read'          => false,
                            'is_starred'       => false,
                        ]);
                    }
                }
            } elseif ($request->target === 'broadcast') {
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'message_type' => $request->message_type,
                    'recipient_target' => $request->target,
                    'recipient_type' => $request->target_group,
                    'recipient_id' => null,
                    'is_read' => false,
                    'is_starred' => false,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.message-notification.messages.index')
                ->with('success', 'Pesan berhasil dibuat');

        } catch (\Throwable $e) {

            // 4. Rollback jika gagal
            DB::rollBack();

            // 5. Logging error untuk debug di laravel.log
            Log::error('Gagal membuat broadcast: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // 6. Kembalikan error ke user
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan broadcast!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getRecipients(Request $request)
    {
        // dd($request->all());
        try {
            $targetGroup = $request->input('target_group');
            $q = $request->input('q'); // Select2 mengirim 'q' untuk query
            $recipients = collect([]);

            // Helper filter by search term
            $filterBySearch = function ($query) use ($q) {
                if ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
                }
            };

            switch ($targetGroup) {
                case 'owner':
                    $recipients = Owner::select('id', 'name', 'email')
                        ->tap($filterBySearch)
                        ->get()
                        ->map(function ($item) {
                            $item->role = 'owner';
                            return $item;
                        });
                    break;
                case 'outlet':
                    $recipients = User::select('id', 'name', 'email')
                        ->where('role', 'partner')
                        ->tap($filterBySearch)
                        ->get()
                        ->map(function ($item) {
                            $item->role = 'outlet';
                            return $item;
                        });
                    break;

                case 'employee':
                    $recipients = Employee::select('id', 'name', 'email')
                        ->tap($filterBySearch)
                        ->get()
                        ->map(function ($item) {
                            $item->role = 'employee';
                            return $item;
                        });
                    break;

                case 'business-partner':
                case 'all':
                    $owners = Owner::select('id', 'name', 'email')
                        ->tap($filterBySearch)
                        ->get()
                        ->map(function ($item) {
                            $item->role = 'owner';
                            return $item;
                        });

                    $outlets = User::select('id', 'name', 'email')
                        ->where('role', 'partner')
                        ->tap($filterBySearch)
                        ->get()
                        ->map(function ($item) {
                            $item->role = 'outlet';
                            return $item;
                        });

                    $employees = Employee::select('id', 'name', 'email')
                        ->tap($filterBySearch)
                        ->get()
                        ->map(function ($item) {
                            $item->role = 'employee';
                            return $item;
                        });

                    $recipients = $owners->merge($outlets)->merge($employees);
                    break;

                // TODO: end-customer kalau sudah ada modelnya
                default:
                    $recipients = collect([]);
            }

            // Batasi misalnya 20 suggestion saja
            return response()->json($recipients->take(20)->values());

        } catch (\Throwable $e) {
            Log::error('Error getRecipients: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Internal server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
