<div class="content-wrapper">
    <div class="content-body">
        <div class="email-app-details">

            @if($message)
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $message->title ?? '(No subject)' }}</h5>
                            <small class="text-muted">
                                {{ optional($message->created_at)->format('d M Y H:i') }}
                            </small>
                        </div>

                        {{-- Tombol Back ke list --}}
                        <a href="{{ route('admin.message-notification.messages.index', ['folder' => $folder ?? request('folder','inbox')]) }}"
                        class="btn btn-sm btn-light btn-back-list">
                            <i class="bx bx-chevron-left mr-20"></i> Back to list
                        </a>

                    </div>

                    <div class="card-body">
                        @php
                            $processedBody = $message->body;
                            
                            $processedBody = preg_replace_callback(
                                '/href=["\'](?!https?:\/\/|\/|#|\?)([^"\']+)["\']/i',
                                function($matches) {
                                    return 'href="https://' . $matches[1] . '"';
                                },
                                $processedBody
                            );
                        @endphp
                        {!! $processedBody !!}

                        @if($message->attachments->count())
                            <hr>
                            <h6>Attachments</h6>
                            <ul class="list-unstyled">
                                @foreach($message->attachments as $att)
                                    <li>
                                        <a href="{{ Storage::disk('public')->url($att->file_path) }}" target="_blank">
                                            {{ $att->file_name }}
                                        </a>
                                        <small class="text-muted">
                                            ({{ number_format($att->file_size / 1024, 1) }} KB)
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            @else
                <div class="card">
                    <div class="card-body text-center text-muted">
                        Tidak ada pesan yang dipilih.
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>


<style>
    /* Batasi semua gambar di detail message */
    .email-app-details .card-body img {
        max-width: 100%;       /* supaya tidak melewati lebar card */
        height: auto;          /* proporsi terjaga */
        display: block;
        margin: .5rem auto;    /* kasih sedikit jarak, center */
    }

    /* Kalau mau batasi tinggi maksimum */
    .email-app-details .card-body img {
        max-height: 480px;     /* bebas: 300–500px */
        object-fit: contain;
    }
</style>