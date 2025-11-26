<div class="sidebar-left">
    <div class="sidebar">
        <div class="sidebar-content email-app-sidebar d-flex">
            
            <div class="email-app-menu">
                <div class="form-group form-group-compose text-center mt-3">
                    <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#composeModal">
                        <i class="bx bx-edit"></i> Compose
                    </button>
                </div>

                <div class="sidebar-menu-list">
                    <div class="list-group list-group-messages">

                        @php
                            $currentFolder = request('folder', 'inbox');
                        @endphp

                        <a href="{{ route('admin.message-notification.messages.index', ['folder' => 'inbox']) }}"
                           class="list-group-item d-flex align-items-center {{ $currentFolder === 'inbox' ? 'active' : '' }}">
                            <i class="bx bx-envelope mr-1"></i> Inbox
                        </a>

                        <a href="{{ route('admin.message-notification.messages.index', ['folder' => 'sent']) }}"
                           class="list-group-item d-flex align-items-center {{ $currentFolder === 'sent' ? 'active' : '' }}">
                            <i class="bx bx-send mr-1"></i> Sent
                        </a>

                        <a href="{{ route('admin.message-notification.messages.index', ['folder' => 'broadcast']) }}"
                           class="list-group-item d-flex align-items-center {{ $currentFolder === 'broadcast' ? 'active' : '' }}">
                            <i class="bx bx-broadcast mr-1"></i> Broadcast
                        </a>

                        <a href="{{ route('admin.message-notification.messages.index', ['folder' => 'popup']) }}"
                           class="list-group-item d-flex align-items-center {{ $currentFolder === 'popup' ? 'active' : '' }}">
                            <i class="bx bx-notification mr-1"></i> Popup Messages
                        </a>

                        {{-- Trash nanti kalau sudah ada status/soft delete --}}
                        <a href="{{ route('admin.message-notification.messages.index', ['folder' => 'trash']) }}"
                           class="list-group-item d-flex align-items-center {{ $currentFolder === 'trash' ? 'active' : '' }}">
                            <i class="bx bx-trash mr-1"></i> Trash
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
