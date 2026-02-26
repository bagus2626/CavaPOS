@extends('layouts.staff')

@section('title', 'Pesan')

@section('content')
<div class="modern-container">
    <div class="container-modern">

        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Pesan</h1>
                <p class="page-subtitle">Semua pesan dan notifikasi untuk outlet Anda</p>
            </div>
        </div>

        <div class="modern-card">
            <div class="card-body-modern" style="padding: 0;">
                <div class="messages-list" id="messagesList">
                    <div class="text-center py-5" id="messagesLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat pesan...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let currentPage = 1;
    let lastPage    = 1;

    function loadMessages(page = 1) {
        $('#messagesLoading').show();

        $.ajax({
            url: '{{ route("employee." . $empRole . ".messages.notifications") }}',
            method: 'GET',
            data: { page: page },
            success: function (response) {
                $('#messagesLoading').hide();

                if (response.messages.length === 0 && page === 1) {
                    $('#messagesList').html(`
                        <div class="messages-empty">
                            <span class="material-symbols-outlined">mail_outline</span>
                            <h3>Tidak ada pesan</h3>
                            <p>Belum ada pesan yang diterima</p>
                        </div>`);
                    return;
                }

                currentPage = response.pagination.current_page;
                lastPage    = response.pagination.last_page;

                response.messages.forEach(function (msg) {
                    const isRead     = msg.recipients && msg.recipients.length > 0 && msg.recipients[0].is_read;
                    const msgDate    = formatMessageDate(msg.created_at);
                    const senderName = msg.sender ? msg.sender.name : 'System';
                    const msgUrl     = '{{ route("employee." . $empRole . ".messages.show", ":id") }}'.replace(':id', msg.id);

                    const html = `
                        <div class="message-item ${!isRead ? 'unread' : ''}" data-id="${msg.id}" data-url="${msgUrl}" style="cursor:pointer;">
                            <div class="message-item-icon">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                            <div class="message-item-content">
                                <div class="message-item-header">
                                    <div class="message-item-info">
                                        <h3 class="message-item-title">${msg.title || 'Tanpa Subjek'}</h3>
                                        <div class="message-item-sender">Dari: ${senderName}</div>
                                    </div>
                                    <span class="message-item-date">${msgDate}</span>
                                </div>
                            </div>
                        </div>`;
                    $('#messagesList').append(html);
                });

                if (currentPage < lastPage) {
                    $('#messagesList').append(`
                        <div class="text-center py-3" id="loadMoreContainer">
                            <button class="btn btn-outline-primary btn-sm" id="loadMoreBtn">Muat lebih banyak</button>
                        </div>`);
                }
            },
            error: function () {
                $('#messagesLoading').hide();
                toastr.error('Gagal memuat pesan');
            }
        });
    }

    $(document).on('click', '.message-item', function () {
        window.location.href = $(this).data('url');
    });

    $(document).on('click', '#loadMoreBtn', function () {
        $('#loadMoreContainer').remove();
        loadMessages(currentPage + 1);
    });

    function formatMessageDate(datetime) {
        const date = new Date(datetime);
        const now  = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (date.toDateString() === now.toDateString()) {
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
        }

        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (date.toDateString() === yesterday.toDateString()) return 'Kemarin';

        if (diff < 604800) {
            return ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date.getDay()];
        }

        return date.toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short',
            year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
        });
    }

    loadMessages(1);
});
</script>
@endpush