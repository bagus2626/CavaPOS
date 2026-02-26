@extends('layouts.staff')

@section('title', $message->title ?? 'Detail Pesan')

@section('content')
<div class="modern-container">
    <div class="container-modern">

        @php $empRole = strtolower(Auth::guard('employee')->user()->role ?? 'manager'); @endphp

        <a href="{{ route("employee.{$empRole}.messages.index") }}" class="back-button">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Pesan
        </a>

        <div class="modern-card">
            <div class="message-detail-header">
                <div class="message-header-content">
                    <div class="message-header-top">
                        <div class="message-icon-large">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div class="message-title-section">
                            <h1 class="message-detail-title">{{ $message->title ?? 'Tanpa Subjek' }}</h1>
                            <div class="message-meta">
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">person</span>
                                    <span>Dari: {{ $message->sender->name ?? 'System' }}</span>
                                </div>
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">schedule</span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($message->scheduled_at ?? $message->created_at)
                                            ->translatedFormat('l, d F Y, H:i') }}
                                    </span>
                                </div>
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">campaign</span>
                                    <span class="badge badge-{{ $message->type === 'broadcast' ? 'success' : 'primary' }}">
                                        {{ $message->type === 'broadcast' ? 'Broadcast' : 'Pesan Langsung' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="message-detail-body">
                <div class="message-content">
                    {!! $message->body !!}
                </div>
            </div>

            @if ($message->attachments && $message->attachments->count())
                <div class="message-attachments">
                    <div class="section-header">
                        <div class="section-icon">
                            <span class="material-symbols-outlined">attach_file</span>
                        </div>
                        <h3 class="section-title">Lampiran</h3>
                    </div>
                    <div class="attachments-list">
                        @foreach ($message->attachments as $attachment)
                            @php
                                $url  = \Illuminate\Support\Facades\Storage::disk('public')->url($attachment->file_path);
                                $mime = $attachment->mime_type ?? '';
                            @endphp
                            @if (\Illuminate\Support\Str::startsWith($mime, 'image/'))
                                <a href="{{ $url }}" target="_blank" class="attachment-item">
                                    <div class="attachment-icon">
                                        <span class="material-symbols-outlined">image</span>
                                    </div>
                                    <div class="attachment-info">
                                        <div class="attachment-name">{{ $attachment->file_name }}</div>
                                    </div>
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank" class="attachment-item" download>
                                    <div class="attachment-icon">
                                        <span class="material-symbols-outlined">
                                            @if(str_contains($mime, 'pdf')) picture_as_pdf
                                            @elseif(str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) table_chart
                                            @else description
                                            @endif
                                        </span>
                                    </div>
                                    <div class="attachment-info">
                                        <div class="attachment-name">{{ $attachment->file_name }}</div>
                                        @if($attachment->file_size)
                                            <div class="attachment-size">{{ round($attachment->file_size / 1024, 1) }} KB</div>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection