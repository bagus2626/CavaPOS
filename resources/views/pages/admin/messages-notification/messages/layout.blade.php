@extends('pages.admin.layouts.app')

@section('content')
<div class="email-application">
    <div class="content-overlay"></div>

    <div class="content-area-wrapper email-shell card shadow-sm border-0 rounded-lg">
        {{-- Sidebar kiri --}}
        @include('pages.admin.messages-notification.messages.partials.left-sidebar')

        {{-- Panel tengah: bisa list atau detail --}}
        <div class="content-main flex-grow-1 email-main">
            @if($currentMessage)
                @include('pages.admin.messages-notification.messages.partials.message-content', [
                    'message' => $currentMessage,
                    'folder'  => $folder,
                ])
            @else
                @include('pages.admin.messages-notification.messages.partials.message-list', [
                    'messages' => $messages,
                    'folder'   => $folder,
                ])
            @endif
        </div>
    </div>

</div>

@include('pages.admin.messages-notification.messages.partials.compose-modal')
@endsection

@push('page-styling')
<style>
    .email-application {
        margin-top: -5rem;
        margin-left: -2rem; 
        margin-right: -2rem;
    }
    /* ===== Shell utama email app ===== */
    .email-application .content-area-wrapper.email-shell {
        display: flex;
        flex-direction: row; 
        min-height: 100vh;
        max-height: 100vh;
        background: #f9fafb;
        border-radius: 16px;
        overflow: hidden;
    }

    /* ===== Sidebar kiri (gunakan partial yang sudah ada) ===== */
    .email-application .sidebar-left {
        width: 250px;
        border-right: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .email-application .email-app-menu {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .email-application .email-app-menu .form-group-compose {
        padding: 0 1rem 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .email-application .sidebar-menu-list {
        padding: 0.75rem 0.5rem 1rem;
        flex: 1 1 auto;
        overflow-y: auto;
    }

    .email-application .list-group-messages .list-group-item {
        border: 0;
        border-radius: 999px;
        margin: 2px 4px;
        padding: .45rem .85rem;
        font-size: .88rem;
        display: flex;
        align-items: center;
        color: #4b5563;
        transition: background-color .15s ease, color .15s ease, transform .05s ease;
    }

    .email-application .list-group-messages .list-group-item i {
        font-size: 1.05rem;
        margin-right: .35rem;
    }

    .email-application .list-group-messages .list-group-item:hover {
        background: #f3f4ff;
        color: #111827;
        transform: translateY(-1px);
    }

    .email-application .list-group-messages .list-group-item.active {
        background: linear-gradient(135deg, #2a008c, #147fc1);
        color: #fff;
        box-shadow: 0 6px 14px rgba(140,16,0,.22);
    }

    /* ===== Panel utama (list / detail) ===== */
    .email-application .content-main.email-main {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        background: #f9fafb;
    }

    .email-application .content-main .content-wrapper {
        padding: 0.75rem 0.75rem 1rem;
    }

    .email-application .email-app-list,
    .email-application .email-app-details {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, .04);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    /* ===== Header list / header detail ===== */
    .email-application .email-header-bar {
        padding: .65rem .9rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f9fafb;
        border-radius: 12px 12px 0 0;
    }

    .email-application .email-header-title {
        font-weight: 600;
        font-size: .92rem;
        color: #111827;
    }

    .email-application .email-header-sub {
        font-size: .76rem;
        color: #6b7280;
    }

    /* optional: kecilkan search di header list (jika nanti ditambah) */
    .email-application .email-header-actions .form-control {
        height: 32px;
        font-size: .8rem;
    }

    /* ===== LIST ITEM ===== */
    .email-user-list {
        flex: 1 1 auto;
        overflow-y: auto;
    }

    .email-user-list .list-group-item {
        border: 0;
        border-bottom: 1px solid #f3f4f6;
        border-radius: 0;
        padding: .6rem .85rem;
        cursor: pointer;
        transition: background-color .1s ease, box-shadow .1s ease, transform .05s ease;
    }

    .email-user-list .list-group-item:last-child {
        border-bottom: 0;
    }

    .email-user-list .list-group-item:hover {
        background-color: #f9fafb;
        box-shadow: inset 3px 0 0 #8c1000;
    }

    .email-user-list .list-group-item.active-item {
        background-color: #f3f4ff;
        box-shadow: inset 3px 0 0 #8c1000;
    }

    .email-user-list .media-body h6 {
        font-size: .9rem;
        margin-bottom: .1rem;
        color: #111827;
        font-weight: 600;
    }

    .email-user-list .media-body small {
        font-size: .78rem;
        color: #6b7280;
    }

    .email-user-list .email-date {
        font-size: .75rem;
        color: #9ca3af;
    }

    .email-user-list .badge {
        font-size: .68rem;
        padding: .15rem .5rem;
        border-radius: 999px;
    }

    /* ===== DETAIL MESSAGE ===== */
    .email-app-details .card {
        border-radius: 12px;
        border: 0;
        box-shadow: none;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .email-app-details .card-header {
        padding: .75rem .9rem;
        border-bottom: 1px solid #eef1f4;
        background: #f9fafb;
    }

    .email-app-details .card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
    }

    .email-app-details .card-header small {
        font-size: .78rem;
    }

    .email-app-details .card-body {
        padding: .9rem;
        overflow-y: auto;
    }

    .email-app-details .btn-back-list {
        border-radius: 999px;
        font-size: .78rem;
        padding: .25rem .7rem;
    }

    /* ===== Gambar dari Quill dalam detail ===== */
    .email-app-details .card-body img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: .5rem auto;
        max-height: 480px;         /* bisa diubah sesuai selera */
        object-fit: contain;
    }

    /* Attachments */
    .email-app-details .attachments-list li {
        margin-bottom: .25rem;
        font-size: .82rem;
    }

    /* ===== Pagination bawah list ===== */
    .email-application .email-app-list .pagination {
        margin: .25rem .75rem .6rem;
        justify-content: flex-end;
    }

    .email-application .email-app-list .pagination .page-link {
        border-radius: 999px !important;
        font-size: .78rem;
        padding: .25rem .6rem;
    }

    /* ===== Responsive ===== */
    @media (max-width: 991.98px) {
        .email-application .content-area-wrapper.email-shell {
            flex-direction: column;
            max-height: none;
        }
        .email-application .sidebar-left {
            width: 100%;
            border-right: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .email-application .email-app-menu {
            flex-direction: row;
            align-items: center;
        }
        .email-application .sidebar-menu-list {
            flex: 1 1 auto;
            display: flex;
            overflow-x: auto;
            overflow-y: hidden;
        }
        .email-application .list-group-messages {
            display: flex;
        }
        .email-application .list-group-messages .list-group-item {
            white-space: nowrap;
        }
    }
</style>
@endpush
