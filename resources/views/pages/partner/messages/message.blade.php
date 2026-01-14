@extends('layouts.partner')

@section('title', 'Pesan')

@section('content')
<div class="modern-container">
    <div class="container-modern">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Pesan</h1>
                <p class="page-subtitle">Kelola semua pesan dan notifikasi Anda</p>
            </div>
        </div>

        <!-- Messages List -->
        <div class="modern-card">
            <div class="card-body-modern" style="padding: 0;">
                <div class="messages-list" id="messagesList">
                    <!-- Loading State -->
                    <div class="text-center py-5" id="messagesLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat pesan...</p>
                    </div>

                    <!-- Messages will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load messages on page load
    loadMessages();

    // Load messages function
    function loadMessages() {
        $('#messagesLoading').show();
        $('#messagesList .message-item').remove();
        $('#messagesList .messages-empty').remove();

        $.ajax({
            url: '{{ route("partner.messages.notifications") }}',
            method: 'GET',
            data: {
                page: 1,
                per_page: 100 // Load all messages
            },
            success: function(response) {
                $('#messagesLoading').hide();
                
                if (response.success) {
                    if (response.messages.length === 0) {
                        showEmptyState();
                    } else {
                        renderMessages(response.messages);
                    }
                }
            },
            error: function() {
                $('#messagesLoading').hide();
                toastr.error('Gagal memuat pesan');
            }
        });
    }

    // Render messages
    function renderMessages(messages) {
        messages.forEach(function(message) {
            const isRead = message.recipients && message.recipients.length > 0 && message.recipients[0].is_read;
            const messageDate = formatMessageDate(message.created_at);
            const senderName = message.sender ? message.sender.name : 'System';

            const messageHtml = `
                <div class="message-item ${!isRead ? 'unread' : ''}" data-id="${message.id}">
                    <div class="message-item-icon">
                        <span class="material-symbols-outlined">mail</span>
                    </div>
                    <div class="message-item-content">
                        <div class="message-item-header">
                            <div class="message-item-info">
                                <h3 class="message-item-title">${message.title || 'Tanpa Subjek'}</h3>
                                <div class="message-item-sender">Dari: ${senderName}</div>
                            </div>
                            <span class="message-item-date">${messageDate}</span>
                        </div>
                    </div>
                </div>
            `;

            $('#messagesList').append(messageHtml);
        });

        // Click handler for message items
        $('.message-item').on('click', function() {
            const messageId = $(this).data('id');
            window.location.href = `{{ route('partner.messages.index') }}/${messageId}`;
        });
    }

    // Show empty state
    function showEmptyState() {
        const emptyHtml = `
            <div class="messages-empty">
                <span class="material-symbols-outlined">mail_outline</span>
                <h3>Tidak ada pesan</h3>
                <p>Belum ada pesan yang diterima</p>
            </div>
        `;
        $('#messagesList').html(emptyHtml);
    }

    // Helper function to format message date
    function formatMessageDate(datetime) {
        const date = new Date(datetime);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        // Jika hari ini
        if (date.toDateString() === now.toDateString()) {
            return date.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        }
        
        // Jika kemarin
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (date.toDateString() === yesterday.toDateString()) {
            return 'Kemarin';
        }
        
        // Jika dalam seminggu terakhir
        if (diffInSeconds < 604800) {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return days[date.getDay()];
        }
        
        // Jika lebih dari seminggu
        return date.toLocaleDateString('id-ID', { 
            day: 'numeric',
            month: 'short',
            year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
        });
    }
});
</script>
@endsection