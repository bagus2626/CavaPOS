@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $lastDateLabel = null;

    // Fungsi untuk memperbaiki link dalam HTML
    function fixLinksInHtml($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Regex untuk menemukan semua tag <a> dengan href
        $pattern = '/<a([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i';

        $fixed = preg_replace_callback(
            $pattern,
            function ($matches) {
                $beforeHref = $matches[1];
                $href = $matches[2];
                $afterHref = $matches[3];

                // Skip jika sudah ada protokol atau javascript: atau mailto: atau #
                if (preg_match('/^(https?:\/\/|javascript:|mailto:|#)/i', $href)) {
                    return $matches[0];
                }

                // Tambahkan https:// untuk link yang tidak memiliki protokol
                $fixedHref = 'https://' . ltrim($href, '/');

                return '<a' . $beforeHref . 'href="' . $fixedHref . '"' . $afterHref . '>';
            },
            $html,
        );

        return $fixed;
    }
@endphp

@forelse ($messages as $message)
    @php
        $displayDate = $message->scheduled_at
            ? Carbon\Carbon::parse($message->scheduled_at)
            : Carbon\Carbon::parse($message->created_at);

        $isScheduled = $message->scheduled_at && Carbon\Carbon::parse($message->scheduled_at)->isFuture();
        $expiresAt = $message->expires_at ? Carbon\Carbon::parse($message->expires_at) : null;
        $isExpiringSoon = $expiresAt && $expiresAt->diffInHours(now()) < 24;

        if ($displayDate->isToday()) {
            $dateLabel = 'Today';
        } elseif ($displayDate->isYesterday()) {
            $dateLabel = 'Yesterday';
        } else {
            $dateLabel = $displayDate->format('d M Y');
        }

        switch ($message->type) {
            case 'single':
                $iconClass = 'fas fa-envelope bg-blue';
                break;
            case 'broadcast':
                $iconClass = 'fas fa-bullhorn bg-green';
                break;
            default:
                $iconClass = 'fas fa-info bg-gray';
        }

        $fixedBody = fixLinksInHtml($message->body);

    @endphp

    @if ($dateLabel !== $lastDateLabel)
        <div class="time-label">
            <span class="bg-red">{{ $dateLabel }}</span>
        </div>
        @php $lastDateLabel = $dateLabel; @endphp
    @endif

    <div>
        <i class="{{ $iconClass }}"></i>
        <div class="timeline-item">
            <span class="time">
                <i class="fas fa-clock"></i>
                {{ $displayDate->format('H:i') }}
            </span>

            <h3 class="timeline-header">
                <a href="javascript:void(0)">
                    {{ ucfirst($message->sender_role ?? 'system') }}
                </a>
                – {{ $message->title }}
            </h3>

            <div class="timeline-body">
                {!! $fixedBody !!}
            </div>

            @if ($message->attachments->count())
                <div class="timeline-footer">
                    @foreach ($message->attachments as $attachment)
                        @php
                            $url = Storage::disk('public')->url($attachment->file_path);
                            $mime = $attachment->mime_type;
                        @endphp

                        @if (Str::startsWith($mime, 'image/'))
                            <a href="{{ $url }}" target="_blank">
                                <img src="{{ $url }}" class="attachment-image-thumb"
                                    alt="{{ $attachment->file_name }}">
                            </a>
                        @else
                            <a href="{{ $url }}" target="_blank" class="attachment-badge text-dark">
                                @if (Str::contains($mime, 'pdf'))
                                    <i class="fas fa-file-pdf text-danger"></i>
                                @elseif(Str::contains($mime, 'excel') || Str::contains($mime, 'spreadsheet'))
                                    <i class="fas fa-file-excel text-success"></i>
                                @elseif(Str::contains($mime, 'word'))
                                    <i class="fas fa-file-word text-primary"></i>
                                @elseif(Str::contains($mime, 'zip') || Str::contains($mime, 'compressed'))
                                    <i class="fas fa-file-archive text-warning"></i>
                                @else
                                    <i class="fas fa-paperclip"></i>
                                @endif

                                {{ $attachment->file_name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@empty
    <div>
        <i class="fas fa-info bg-gray"></i>
        <div class="timeline-item">
            <div class="timeline-body text-muted">
                No messages at this time.
            </div>
        </div>
    </div>
@endforelse
