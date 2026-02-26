@extends('layouts.staff')

@section('title', $message->title ?? 'Detail Pesan')

@section('content')
<div class="modern-container">
    <div class="container-modern">

        <div class="modern-card">
            <!-- Message Header -->
            <div class="message-detail-header">
                <div class="message-header-content">
                    <div class="message-header-top">
                        <div class="message-icon-large">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div class="message-title-section">
                            <h1 class="message-detail-title">
                                {{ $message->title }}
                            </h1>
                            <div class="message-meta">
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">person</span>
                                    <span>Dari: {{ $message->sender->name ?? 'System' }}</span>
                                </div>
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">schedule</span>
                                    <span>
                                        @php
                                            $dt = \Carbon\Carbon::parse($message->scheduled_at ?? $message->created_at);
                                            $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                                            $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                            echo $days[$dt->dayOfWeek] . ', ' . $dt->day . ' ' . $months[$dt->month - 1] . ' ' . $dt->year . ' ' . $dt->format('H:i');
                                        @endphp
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Body -->
            <div class="message-detail-body">
                <div class="message-content">
                    {!! $message->body !!}
                </div>
            </div>

            <!-- Attachments -->
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

                                if (str_starts_with($mime, 'image/'))                                       $icon = 'image';
                                elseif (str_starts_with($mime, 'video/'))                                   $icon = 'videocam';
                                elseif (str_starts_with($mime, 'audio/'))                                   $icon = 'audiotrack';
                                elseif (str_contains($mime, 'pdf'))                                         $icon = 'picture_as_pdf';
                                elseif (str_contains($mime, 'word'))                                        $icon = 'description';
                                elseif (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) $icon = 'table_chart';
                                elseif (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation')) $icon = 'slideshow';
                                elseif (str_contains($mime, 'zip') || str_contains($mime, 'rar'))           $icon = 'folder_zip';
                                else                                                                        $icon = 'description';

                                $bytes = $attachment->file_size ?? 0;
                                if ($bytes >= 1073741824)     $size = round($bytes / 1073741824, 2) . ' GB';
                                elseif ($bytes >= 1048576)    $size = round($bytes / 1048576, 2)    . ' MB';
                                elseif ($bytes >= 1024)       $size = round($bytes / 1024, 2)       . ' KB';
                                else                          $size = $bytes . ' Bytes';
                            @endphp

                            <a href="{{ $url }}" target="_blank" class="attachment-item" download="{{ $attachment->file_name }}">
                                <div class="attachment-icon">
                                    <span class="material-symbols-outlined">{{ $icon }}</span>
                                </div>
                                <div class="attachment-info">
                                    <div class="attachment-name">{{ $attachment->file_name }}</div>
                                    <div class="attachment-size">{{ $size }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection