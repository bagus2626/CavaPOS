<div class="content-wrapper">
    <div class="content-body">

        <div class="email-app-list">

            {{-- Header list --}}
            <div class="email-header-bar">
                <div>
                    <div class="email-header-title">
                        @switch($folder ?? request('folder', 'inbox'))
                            @case('sent') Sent Messages @break
                            @case('broadcast') Broadcast Messages @break
                            @case('trash') Trash @break
                            @default Inbox Messages
                        @endswitch
                    </div>

                    @if(isset($messages) && $messages->total() > 0)
                        <div class="email-header-sub">
                            Showing {{ $messages->firstItem() }}–{{ $messages->lastItem() }}
                            of {{ $messages->total() }}
                        </div>
                    @else
                        <div class="email-header-sub">
                            No messages in this folder.
                        </div>
                    @endif
                </div>

                {{-- (Optional) tempat search/filter kalau mau nanti --}}
                {{-- 
                <div class="email-header-actions">
                    <input type="text" class="form-control form-control-sm" placeholder="Search messages...">
                </div> 
                --}}
            </div>

            {{-- List pesan --}}
            <div class="email-user-list list-group">
                @php
                    $currentMessageId = request('message_id');
                @endphp

                @forelse($messages as $msg)
                @php
                    $broadcastRecipient = $msg->recipients()
                        ->where('recipient_target', 'broadcast')
                        ->first();
                    $recipients = $msg->recipients()
                        ->where('recipient_target', 'single')
                        ->get();
                @endphp
                    <a href="{{ route('admin.message-notification.messages.index', [
                            'folder'     => $folder ?? request('folder', 'inbox'),
                            'message_id' => $msg->id
                        ]) }}"
                       class="list-group-item d-flex justify-content-between align-items-center
                              {{ $currentMessageId == $msg->id ? 'active-item' : '' }}">

                        <div class="media-body">
                            <h6 class="mb-0">
                                {{ $msg->title ?? '(No subject)' }}
                            </h6>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit(strip_tags($msg->body), 60) }}
                            </small>
                        </div>

                        <div class="text-right ml-2">
                            <small class="email-date d-block">
                                {{ optional($msg->created_at)->diffForHumans() }}
                            </small>

                            @if($msg->type === 'broadcast')
                                <span class="badge badge-light-primary">Broadcast {{ $broadcastRecipient->recipient_type ?? '' }}</span>
                            @elseif($msg->type === 'single')
                                <span class="badge badge-light-info">
                                    To:
                                    @foreach($recipients as $recip)
                                        @if ($recip->recipient_type === 'owner')
                                            {{ $recip->owners->name ?? '-' }} (Owner){{ !$loop->last ? ',' : '' }}
                                        @elseif($recip->recipient_type === 'outlet')
                                            {{ $recip->outlets->name ?? '-' }} (Outlet){{ !$loop->last ? ',' : '' }}
                                        @elseif($recip->recipient_type === 'employee')
                                            {{ $recip->employees->name ?? '-' }} (Employee){{ !$loop->last ? ',' : '' }}
                                        @endif
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-2 text-center text-muted">
                        Tidak ada pesan pada folder ini.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-1 pl-1">
                {{ $messages->links() }}
            </div>

        </div>
    </div>
</div>
