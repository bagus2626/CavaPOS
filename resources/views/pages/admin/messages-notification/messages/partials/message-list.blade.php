<div class="content-wrapper">
    <div class="content-body">

        <div class="email-app-list">

            {{-- Header list --}}
            <div class="email-header-bar">
                <div>
                    <div class="email-header-title">
                        @switch($folder ?? request('folder', 'inbox'))
                            @case('sent')
                                Sent Messages
                            @break

                            @case('broadcast')
                                Broadcast Messages
                            @break

                            @case('trash')
                                Trash
                            @break

                            @default
                                Inbox Messages
                        @endswitch
                    </div>

                    @if (isset($messages) && $messages->total() > 0)
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

                <div class="email-header-actions">
                    <input type="text" class="form-control form-control-sm" id="messageSearchInput"
                        placeholder="Search messages..." value="{{ request('search') }}">
                </div>

            </div>

            {{-- List pesan --}}
            <div class="email-user-list list-group">
                @php
                    $currentMessageId = request('message_id');
                @endphp

                @forelse($messages as $msg)
                    @php
                        $broadcastRecipient = $msg->recipients()->where('recipient_target', 'broadcast')->first();
                        $recipients = $msg->recipients()->where('recipient_target', 'single')->get();
                    @endphp
                    <a href="{{ route('admin.message-notification.messages.index', [
                        'folder' => $folder ?? request('folder', 'inbox'),
                        'message_id' => $msg->id,
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

                            @if ($msg->type === 'broadcast')
                                <span class="badge badge-light-primary">Broadcast
                                    {{ $broadcastRecipient->recipient_type ?? '' }}</span>
                            @elseif($msg->type === 'single')
                                <span class="badge badge-light-info">
                                    To:
                                    @foreach ($recipients as $recip)
                                        @if ($recip->recipient_type === 'owner')
                                            {{ $recip->owners->name ?? '-' }} (Owner){{ !$loop->last ? ',' : '' }}
                                        @elseif($recip->recipient_type === 'outlet')
                                            {{ $recip->outlets->name ?? '-' }} (Outlet){{ !$loop->last ? ',' : '' }}
                                        @elseif($recip->recipient_type === 'employee')
                                            {{ $recip->employees->name ?? '-' }}
                                            (Employee)
                                            {{ !$loop->last ? ',' : '' }}
                                        @endif
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-2 text-center text-muted">
                        No messages found.
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

@push('style')
    <style>
        .email-user-list {
            transition: opacity .25s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            let searchTimeout = null;
            const searchInput = document.querySelector('.email-header-actions input[type="text"]');
            const messageList = document.querySelector('.email-user-list');
            const paginationContainer = document.querySelector('.mt-1.pl-1');

            if (!searchInput || !messageList) return;

            // Fungsi untuk melakukan search
            function performSearch(query) {
                const folder = new URLSearchParams(window.location.search).get('folder') || 'inbox';
                const currentMessageId = new URLSearchParams(window.location.search).get('message_id');

                showLoading();

                fetch(`${window.location.pathname}?folder=${folder}&search=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {

                        let filtered = data.messages.filter(msg => messageMatches(msg, query));

                        updateMessageList(filtered, folder, currentMessageId, true);
                        updatePagination(data.pagination, query);
                        updateShowingText(data.pagination);
                        hideLoading();
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        hideLoading();
                    });
            }

            // Event listener untuk input search
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();

                // Clear timeout sebelumnya
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }

                // Debounce: tunggu 500ms setelah user berhenti mengetik
                searchTimeout = setTimeout(() => {
                    if (query.length >= 3 || query.length === 0) {
                        performSearch(query);
                    }
                }, 150);
            });

            // Fungsi untuk update list pesan
            function updateMessageList(messages, folder, currentMessageId, noAnimation = false) {

                // Nonaktifkan atau aktifkan transition
                if (noAnimation) {
                    messageList.style.transition = "none";
                    messageList.style.opacity = "1";
                } else {
                    messageList.style.transition = "opacity .25s ease";
                    messageList.style.opacity = "0";
                }

                setTimeout(() => {

                    if (!messages || messages.length === 0) {
                        messageList.innerHTML = `
                <div class="p-2 text-center text-muted">
                    No messages found.
                </div>
            `;
                    } else {

                        let html = '';

                        messages.forEach(msg => {
                            const isActive = currentMessageId == msg.id ? 'active-item' : '';
                            const subject = msg.title || '(No subject)';
                            const excerpt = stripHtml(msg.body).substring(0, 60);
                            const timeAgo = msg.created_at_human || '';

                            let badge = '';
                            if (msg.type === 'broadcast') {
                                const recipientType = msg.broadcast_recipient_type || '';
                                badge =
                                    `<span class="badge badge-light-primary">Broadcast ${recipientType}</span>`;
                            } else if (msg.type === 'single' && msg.recipients) {
                                let recipientText = 'To: ';
                                msg.recipients.forEach((recip, index) => {
                                    const name = recip.name || '-';
                                    const role = recip.recipient_type || '';
                                    recipientText += `${name} (${capitalizeFirst(role)})`;
                                    if (index < msg.recipients.length - 1) recipientText +=
                                    ', ';
                                });
                                badge = `<span class="badge badge-light-info">${recipientText}</span>`;
                            }

                            html += `
                    <a href="?folder=${folder}&message_id=${msg.id}" 
                       class="list-group-item d-flex justify-content-between align-items-center ${isActive}">
                        <div class="media-body">
                            <h6 class="mb-0">${escapeHtml(subject)}</h6>
                            <small class="text-muted">${escapeHtml(excerpt)}</small>
                        </div>
                        <div class="text-right ml-2">
                            <small class="email-date d-block">${timeAgo}</small>
                            ${badge}
                        </div>
                    </a>
                `;
                        });

                        messageList.innerHTML = html;
                    }

                    if (!noAnimation) {
                        messageList.style.opacity = "1";
                    }

                }, noAnimation ? 0 : 120);
            }

            // Fungsi untuk update pagination
            function updatePagination(pagination, query = '') {
                if (!paginationContainer || !pagination) return;

                if (pagination.last_page <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                let html = '<nav><ul class="pagination">';

                // Previous button
                if (pagination.current_page > 1) {
                    html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>`;
                }

                // Page numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    const active = i === pagination.current_page ? 'active' : '';
                    html += `<li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
                }

                // Next button
                if (pagination.current_page < pagination.last_page) {
                    html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>`;
                }

                html += '</ul></nav>';
                paginationContainer.innerHTML = html;

                // Add event listeners to pagination links
                attachPaginationListeners(query);
            }

            // Fungsi untuk update showing text
            function updateShowingText(pagination) {
                const headerSubElement = document.querySelector('.email-header-sub');
                if (!headerSubElement || !pagination) return;

                if (pagination.total > 0) {
                    headerSubElement.textContent = `Showing ${pagination.from}–${pagination.to} of ${pagination.total}`;
                } else {
                    headerSubElement.textContent = 'No messages in this folder.';
                }
            }

            // Fungsi untuk attach event listener ke pagination
            function attachPaginationListeners(query = '') {
                const paginationLinks = paginationContainer.querySelectorAll('.page-link');
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = this.dataset.page;
                        const currentQuery = query || searchInput.value.trim();

                        if (currentQuery) {
                            performSearchWithPage(currentQuery, page);
                        } else {
                            // Jika tidak ada search, reload dengan parameter page saja
                            const folder = new URLSearchParams(window.location.search).get('folder') ||
                                'inbox';
                            const messageId = new URLSearchParams(window.location.search).get(
                                'message_id') || '';
                            let url = `?folder=${folder}&page=${page}`;
                            if (messageId) url += `&message_id=${messageId}`;
                            window.location.href = url;
                        }
                    });
                });
            }

            // Fungsi search dengan pagination
            function performSearchWithPage(query, page) {
                const folder = new URLSearchParams(window.location.search).get('folder') || 'inbox';
                const currentMessageId = new URLSearchParams(window.location.search).get('message_id');

                showLoading();

                fetch(`${window.location.pathname}?folder=${folder}&search=${encodeURIComponent(query)}&page=${page}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateMessageList(data.messages, folder, currentMessageId, true);
                        updatePagination(data.pagination, query);
                        updateShowingText(data.pagination);
                        hideLoading();
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        hideLoading();
                    });
            }

            // Helper functions
            function showLoading() {

            }

            function hideLoading() {

            }

            function showError(message) {
                messageList.innerHTML = `
            <div class="p-2 text-center text-danger">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </div>
        `;
            }

            function stripHtml(html) {
                const tmp = document.createElement('div');
                tmp.innerHTML = html;
                return tmp.textContent || tmp.innerText || '';
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            function capitalizeFirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function norm(s) {
                return (s || "").toString().toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .trim();
            }

            function messageMatches(msg, q) {
                if (!q) return true;

                const nq = norm(q);

                const title = norm(msg.title || "");
                const body = norm(stripHtml(msg.body || ""));
                const type = norm(msg.type || "");

                let recipientsText = "";
                if (msg.recipients) {
                    recipientsText = norm(
                        msg.recipients.map(r => `${r.name} ${r.recipient_type}`).join(" ")
                    );
                }

                return (
                    title.includes(nq) ||
                    body.includes(nq) ||
                    recipientsText.includes(nq) ||
                    type.includes(nq)
                );
            }

        })();
    </script>
@endpush
