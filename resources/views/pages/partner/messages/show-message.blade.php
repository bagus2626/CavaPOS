@extends('layouts.partner')

@section('title', 'Detail Pesan')

@section('content')
<div class="modern-container">
    <div class="container-modern">
        <!-- Back Button -->
        <a href="{{ route('partner.messages.index') }}" class="back-button">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Pesan
        </a>

        <!-- Message Detail Card -->
        <div class="modern-card">
            <!-- Message Header -->
            <div class="message-detail-header">
                <div class="message-header-content">
                    <div class="message-header-top">
                        <div class="message-icon-large">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div class="message-title-section">
                            <h1 class="message-detail-title" id="messageTitle">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </h1>
                            <div class="message-meta">
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">person</span>
                                    <span id="messageSender">Loading...</span>
                                </div>
                                <div class="message-meta-item">
                                    <span class="material-symbols-outlined">schedule</span>
                                    <span id="messageDate">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Body -->
            <div class="message-detail-body">
                <div class="message-content" id="messageContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat pesan...</p>
                    </div>
                </div>
            </div>

            <!-- Message Attachments (if any) -->
            <div class="message-attachments" id="messageAttachments" style="display: none;">
                <div class="section-header">
                    <div class="section-icon">
                        <span class="material-symbols-outlined">attach_file</span>
                    </div>
                    <h3 class="section-title">Lampiran</h3>
                </div>
                <div class="attachments-list" id="attachmentsList">
                    <!-- Attachments will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const messageId = '{{ $message->id }}';
    
    // Load message detail
    loadMessageDetail();

    function loadMessageDetail() {
        const message = @json($message);
        
        // Set title
        $('#messageTitle').text(message.title || 'Tanpa Subjek');
        
        // Set sender
        const senderName = message.sender ? message.sender.name : 'System';
        $('#messageSender').text('Dari: ' + senderName);
        
        // Set date
        const messageDate = formatFullDate(message.created_at);
        $('#messageDate').text(messageDate);
        
        // Set content
        $('#messageContent').html(message.body);
        
        // Load attachments if any
        if (message.attachments && message.attachments.length > 0) {
            renderAttachments(message.attachments);
        }
        
        // Mark as read
        markAsRead(messageId);
    }

    function renderAttachments(attachments) {
        $('#messageAttachments').show();
        $('#attachmentsList').empty();
        
        attachments.forEach(function(attachment) {
            const fileSize = formatFileSize(attachment.file_size);
            const fileIcon = getFileIcon(attachment.mime_type);
            
            const attachmentHtml = `
                <a href="${attachment.file_path}" target="_blank" class="attachment-item" download>
                    <div class="attachment-icon">
                        <span class="material-symbols-outlined">${fileIcon}</span>
                    </div>
                    <div class="attachment-info">
                        <div class="attachment-name">${attachment.file_name}</div>
                        <div class="attachment-size">${fileSize}</div>
                    </div>
                </a>
            `;
            
            $('#attachmentsList').append(attachmentHtml);
        });
    }

    function markAsRead(id) {
        $.ajax({
            url: '{{ route("partner.messages.mark-read", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            }
        });
    }

    function formatFullDate(datetime) {
        const date = new Date(datetime);
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('id-ID', options);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function getFileIcon(mimeType) {
        if (!mimeType) return 'description';
        
        if (mimeType.startsWith('image/')) return 'image';
        if (mimeType.startsWith('video/')) return 'videocam';
        if (mimeType.startsWith('audio/')) return 'audiotrack';
        if (mimeType.includes('pdf')) return 'picture_as_pdf';
        if (mimeType.includes('word')) return 'description';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'table_chart';
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'slideshow';
        if (mimeType.includes('zip') || mimeType.includes('rar')) return 'folder_zip';
        
        return 'description';
    }
});
</script>
@endsection