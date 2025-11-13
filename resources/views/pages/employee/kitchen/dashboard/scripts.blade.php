<script>
    // Configuration - Tanpa auto refresh
    const KITCHEN_CONFIG = {
        endpoints: {
            queue: '{{ route('employee.kitchen.orders.queue') }}',
            active: '{{ route('employee.kitchen.orders.active') }}',
            served: '{{ route('employee.kitchen.orders.served') }}',
            pickup: '{{ route('employee.kitchen.orders.pickup', ['orderId' => ':id']) }}',
            serve: '{{ route('employee.kitchen.orders.serve', ['orderId' => ':id']) }}',
            cancel: '{{ route('employee.kitchen.orders.cancel', ['orderId' => ':id']) }}'
        }
    };


    class KitchenApiService {
        constructor() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            this.cache = new Map();
            this.cacheTimeout = 30000;
            this.pendingRequests = new Map();
        }


        async getOrderQueue(page = 1) {
            const endpoint = `${KITCHEN_CONFIG.endpoints.queue}?page=${page}`;
            return this._fetch(endpoint);
        }

        async getActiveOrders() {
            return this._fetchWithCache('active', KITCHEN_CONFIG.endpoints.active);
        }

        async getServedOrders(date = null, page = 1) {
            const selectedDate = date || new Date().toISOString().split('T')[0];
            const endpoint = `${KITCHEN_CONFIG.endpoints.served}?date=${selectedDate}&page=${page}`;
            return this._fetch(endpoint);
        }

        async getAllServedOrders() {
            return this._fetchWithCache('all_served', `${KITCHEN_CONFIG.endpoints.served}?date=all`);
        }

        async pickUpOrder(orderId) {
            this.clearCache();
            const endpoint = KITCHEN_CONFIG.endpoints.pickup.replace(':id', orderId);
            return this._fetch(endpoint, 'PUT');
        }

        async cancelOrder(orderId) {
            this.clearCache();
            const endpoint = KITCHEN_CONFIG.endpoints.cancel.replace(':id', orderId);
            return this._fetch(endpoint, 'PUT');
        }

        async markAsServed(orderId) {
            this.clearCache();
            const endpoint = KITCHEN_CONFIG.endpoints.serve.replace(':id', orderId);
            return this._fetch(endpoint, 'PUT');
        }

        async _fetchWithCache(cacheKey, endpoint) {
            const cached = this.cache.get(cacheKey);
            if (cached && Date.now() - cached.timestamp < this.cacheTimeout) {
                return cached.data;
            }

            if (this.pendingRequests.has(cacheKey)) {
                return this.pendingRequests.get(cacheKey);
            }

            const requestPromise = this._fetch(endpoint).then(data => {
                this.cache.set(cacheKey, {
                    data,
                    timestamp: Date.now()
                });
                this.pendingRequests.delete(cacheKey);
                return data;
            }).catch(error => {
                this.pendingRequests.delete(cacheKey);
                throw error;
            });

            this.pendingRequests.set(cacheKey, requestPromise);
            return requestPromise;
        }

        clearCache() {
            this.cache.clear();
        }

        async _fetch(endpoint, method = 'GET') {
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (method !== 'GET' && this.csrfToken) {
                headers['X-CSRF-TOKEN'] = this.csrfToken;
            }

            const options = {
                method,
                headers,
                credentials: 'same-origin'
            };

            try {
                const response = await fetch(endpoint, options);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                throw error;
            }
        }
    }

    class KitchenUIRenderer {
        static createQueueItem(order) {
            if (!order) return '';

            const hasNotes = order.order_details?.some(detail => detail.customer_note?.trim());
            const isTakenByCashier = order.cashier_process_id;

            return `<div class="compact-queue-card kitchen-queue-item kitchen-details-btn bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all cursor-pointer p-2.5 mb-2 border border-gray-200 dark:border-gray-700 hover:border-red-500 ${isTakenByCashier ? 'opacity-60 cursor-not-allowed border-orange-300 bg-orange-50 dark:bg-orange-900/10' : ''}" data-order-id="${order.id}" data-customer-name="${(order.customer_name || '').toLowerCase()}" data-order-code="${(order.booking_order_code || '').toLowerCase()}" data-cashier-taken="${isTakenByCashier ? 'true' : 'false'}">${isTakenByCashier ? `
                <div class="flex items-center gap-1 mb-1.5 text-orange-600 dark:text-orange-400 text-xs"><span class="material-icons" style="font-size: 12px;">person</span><span class="font-semibold">Sedang diproses kasir</span></div>` : ''}
                <div class="flex items-start justify-between mb-1"><div class="text-red-600 font-black text-sm leading-none">${order.booking_order_code || 'N/A'}</div></div>
                <div class="flex items-center justify-between mb-1"><div class="text-xs font-bold text-gray-900 dark:text-white truncate flex-1 pr-2" title="${order.customer_name || 'Customer'}">${order.customer_name || 'Customer'}</div>${hasNotes ? `
                <div class="bg-yellow-400 rounded w-4 h-4 flex items-center justify-center shadow-sm flex-shrink-0"><span class="material-icons text-white" style="font-size: 12px;">sticky_note_2</span></div>` : ''}</div><div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400"><span class="font-semibold text-red-500 text-xs">${order.order_time || '00:00'}</span>
                    <span>•</span><span>${order.total_items || 0} items</span><span>•</span><span class="text-xs">T${order.table?.table_no || '0'}</span></div></div>`;
        }

        static createActiveOrderCard(order) {
            if (!order) return '';

            const orderDetails = order.order_details || [];
            const isMobile = window.innerWidth < 768;
            const displayItems = isMobile ? orderDetails.slice(0, 1) : orderDetails.slice(0, 3);
            const remainingItems = orderDetails.length - displayItems.length;
            const hasNotes = orderDetails.some(detail => detail.customer_note?.trim());
            const tableTypeText = order.table_type_badge || 'Indoor';
            const orderNumber = order.active_queue_number || 0;

            let itemsHTML = displayItems.length === 0 ?
                `<div class="text-gray-400 text-center py-1 text-xs">—</div>` : displayItems.map(detail =>
                    `<div class="flex justify-between items-baseline border-b border-dotted border-gray-300 dark:border-gray-600 py-1"><span class="text-gray-800 dark:text-gray-200 text-xs truncate pr-2">${detail.product_name || 'Item'}</span><span class="text-gray-600 dark:text-gray-400 text-xs font-mono flex-shrink-0">${detail.quantity || 1}×</span></div>`
                ).join('');

            if (remainingItems > 0) {
                itemsHTML +=
                    `<button class="show-more-items-btn text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300 text-xs pt-1 text-right font-medium w-full transition-colors hover:underline" data-order-id="${order.id}" type="button">+ ${remainingItems} more</button>`;
            }

            return `<div class="kitchen-active-order bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all cursor-pointer h-full flex flex-col relative group min-w-0" id="kitchen-active-order-${order.id}" style="border: 1px solid #e5e7eb;" data-order-id="${order.id}" data-order-number="${orderNumber}">
                <button class="cancel-order-btn absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg md:opacity-0 md:group-hover:opacity-100 transition-all duration-200 z-10" data-order-id="${order.id}" title="Cancel and return to queue"><span class="material-icons text-sm">close</span></button><div class="p-3 flex-1 flex flex-col">
                    <div class="flex items-start justify-between mb-2"><div><div class="text-red-600 font-black text-xl leading-none mb-0.5">#${orderNumber}</div><div class="text-[10px] text-gray-500 dark:text-gray-400 uppercase">Active</div></div></div><div class="h-px bg-gray-300 dark:bg-gray-600 my-1"></div>
                    <div class="mb-2 relative"><label class="text-[10px] text-gray-500 dark:text-gray-400 uppercase">Customer</label><div class="flex items-center justify-between"><h3 class="text-sm font-bold text-gray-900 dark:text-white truncate flex-1 pr-8">${order.customer_name || 'Walk-in'}</h3>${hasNotes ? `
                        <div class="bg-white"><span class="material-icons text-yellow-500 dark:text-yellow-400 text-md" style="font-size: 14px;">sticky_note_2</span></div>` : ''}</div></div><div class="grid grid-cols-3 gap-1 mb-2 text-[10px]"><div><span class="text-gray-500 dark:text-gray-400 block">Time</span><span class="font-semibold text-gray-800 dark:text-gray-200">${order.order_time || '00:00'}</span></div><div>
                            <span class="text-gray-500 dark:text-gray-400 block">Table</span><span class="font-semibold text-gray-800 dark:text-gray-200">${order.table?.table_no || '0'}</span></div><div><span class="text-gray-500 dark:text-gray-400 block">Table Class</span><span class="font-semibold text-gray-800 dark:text-gray-200 text-[9px]">${tableTypeText}</span></div></div><div class="h-px bg-gray-300 dark:bg-gray-600 mb-2"></div>
                            <div class="flex-1 mb-2 text-xs">${itemsHTML}</div><button class="kitchen-details-btn bg-red-600 hover:bg-red-700 text-white py-2 px-2 rounded-md font-semibold text-xs w-full transition-colors shadow-sm hover:shadow" data-order-id="${order.id}">View Details</button></div></div>`;
        }

        static createServedOrderItem(order) {
            if (!order) return '';

            const tableClass = order.table_class || 'Indoor';
            const tableBadgeText = this.getTableBadgeText(tableClass);

            return `<div class="compact-served-card served-order-details-btn bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all cursor-pointer p-2.5 mb-2 border border-gray-200 dark:border-gray-700 hover:border-green-500" data-order-id="${order.id}" data-customer-name="${(order.customer_name || '').toLowerCase()}" data-order-code="${(order.booking_order_code || '').toLowerCase()}">
                <div class="flex items-start justify-between mb-1"><div class="flex items-center gap-1"><span class="text-sm font-bold text-green-600 dark:text-green-400">${order.booking_order_code || 'N/A'}</span></div><span class="text-[9px] font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded uppercase tracking-wide">${tableBadgeText}</span></div>
                <div class="text-xs font-bold text-gray-900 dark:text-white mb-1 truncate">${order.customer_name || 'Customer'}</div><div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400"><span class="font-semibold text-green-600 dark:text-green-400 text-xs">${order.served_time || '00:00'}</span><span>•</span><span>${order.total_items || 0} items</span><span>•</span>
                    <span class="text-xs">T${order.table?.table_no || '0'}</span></div></div>`;
        }

        static getTableBadgeText(tableClass) {
            const texts = {
                'Indoor': 'Indoor',
                'Outdoor': 'Outdoor',
                'REGULER': 'Reguler'
            };
            return texts[tableClass] || tableClass;
        }

        static showEmptyState(container, type) {
            const states = {
                active: `<div class="col-span-full flex flex-col items-center justify-center text-center py-40 "><div class="text-5xl mb-4 text-gray-400">🍳</div> <div class="text-sm text-text-secondary-light dark:text-text-secondary-dark">No Active Orders</div></div>`,
                served: `<div class="flex flex-col items-center justify-center text-center py-12 text-gray-500 dark:text-gray-400"><div class="text-4xl mb-4">✅</div><div class="text-sm">No Served Orders</div></div>`
            };
            container.innerHTML = states[type] || states.active;
        }

        static showLoadingState(container) {
            container.innerHTML =
                `<div class="col-span-full flex flex-col items-center justify-center text-center text-center py-40">Loading...</div>`;
        }
    }

    class IncrementalUpdater {
        addOrderToQueue(order) {
            const container = document.getElementById('orderQueue');
            if (!container) return;

            if (document.querySelector(`[data-order-id="${order.id}"]`)) return;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = KitchenUIRenderer.createQueueItem(order);
            container.insertBefore(tempDiv.firstChild, container.firstChild);
        }

        removeOrderFromQueue(orderId) {
            const element = document.querySelector(`#orderQueue [data-order-id="${orderId}"]`);
            if (element) {
                element.style.transition = 'opacity 0.3s';
                element.style.opacity = '0';
                setTimeout(() => element.remove(), 300);
            }
        }

        addOrderToActive(order) {
            const container = document.getElementById('activeOrders');
            if (!container) return;

            if (document.getElementById(`kitchen-active-order-${order.id}`)) return;

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = KitchenUIRenderer.createActiveOrderCard(order);
            container.appendChild(tempDiv.firstChild);
        }

        removeOrderFromActive(orderId) {
            const element = document.getElementById(`kitchen-active-order-${orderId}`);
            if (element) {
                element.style.transition = 'opacity 0.3s';
                element.style.opacity = '0';
                setTimeout(() => element.remove(), 300);
            }
        }

        repositionActiveOrders() {
            const container = document.getElementById('activeOrders');
            if (!container) return;
            container.style.display = 'grid';
        }

        filterOrdersVisibility(containerId, searchTerm) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const items = container.querySelectorAll('[data-customer-name][data-order-code]');
            let visibleCount = 0;

            items.forEach(item => {
                const customerName = item.dataset.customerName || '';
                const orderCode = item.dataset.orderCode || '';
                const matches = customerName.includes(searchTerm) || orderCode.includes(searchTerm);

                item.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            const emptyState = container.querySelector('.empty-state');
            if (visibleCount === 0 && !emptyState) {
                const div = document.createElement('div');
                div.className = 'empty-state text-center text-gray-500 py-8';
                div.innerHTML = `<div class="text-4xl mb-2"></div><div class="text-sm"></div>`;
                container.appendChild(div);
            } else if (visibleCount > 0 && emptyState) {
                emptyState.remove();
            }
        }
    }

    class KitchenDashboard {
        constructor() {
            this.api = new KitchenApiService();
            this.updater = new IncrementalUpdater();
            this.queueOrders = [];
            this.activeOrders = [];
            this.servedOrders = [];
            this.allServedOrders = [];
            this.currentOrderId = null;
            this.confirmationOrderId = null;
            this.currentDate = new Date().toISOString().split('T')[0];
            this.searchTerm = '';
            this.isGlobalSearch = false;
            this.isRefreshing = false;
            this.servedOrdersDisplayCount = 20;
            this.servedOrdersLoadMoreSize = 10;

            // Queue pagination
            this.queueCurrentPage = 1;
            this.queueTotalPages = 1;
            this.queueTotal = 0;
            this.queueHasMore = false;
            this.isLoadingMoreQueue = false;

            // Served pagination
            this.servedCurrentPage = 1;
            this.servedTotalPages = 1;
            this.servedTotal = 0;
            this.servedHasMore = false;
            this.isLoadingMoreServed = false;
        }

        async init() {
            this.showLoadingStates();
            await this.loadAllData();
            this.setupEventListeners();
            this.initializeMobile();
            this.setupRefreshButton();
        }

        // Tombol refresh manual
        setupRefreshButton() {
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');

            if (refreshBtn) {
                refreshBtn.addEventListener('click', async () => {
                    if (this.isRefreshing) return;

                    refreshIcon.style.animation = 'spin 1s linear infinite';
                    refreshBtn.disabled = true;

                    try {
                        this.api.clearCache();
                        await this.loadAllData();
                    } finally {
                        setTimeout(() => {
                            refreshIcon.style.animation = '';
                            refreshBtn.disabled = false;
                        }, 500);
                    }
                });
            }
        }

        showLoadingStates() {
            ['orderQueue', 'activeOrders', 'servedOrders'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = '<div class="col-span-full text-center py-40">Loading...</div>';
            });
        }

        async loadAllData() {
            if (this.isRefreshing) return;

            try {
                this.isRefreshing = true;
                this.queueCurrentPage = 1;
                this.servedCurrentPage = 1;

                const results = await Promise.allSettled([
                    this.api.getActiveOrders(),
                    this.api.getOrderQueue(1),
                    this.api.getServedOrders(this.currentDate, 1)
                ]);

                results.forEach((result, index) => {
                    if (result.status === 'fulfilled' && result.value?.success) {
                        switch (index) {
                            case 0:
                                this.activeOrders = result.value.data?.active_orders || [];
                                break;
                            case 1:
                                this.queueOrders = result.value.data?.queue_orders || [];
                                this.queueTotal = result.value.data?.total_waiting || 0;
                                this.queueTotalPages = result.value.data?.total_pages || 1;
                                this.queueHasMore = result.value.data?.has_more || false;
                                this.queueCurrentPage = result.value.data?.current_page || 1;
                                break;
                            case 2:

                                this.servedOrders = result.value.data?.served_orders || [];
                                this.servedTotal = result.value.data?.total_served || 0;
                                this.servedTotalPages = result.value.data?.total_pages || 1;
                                this.servedHasMore = result.value.data?.has_more || false;
                                this.servedCurrentPage = result.value.data?.current_page || 1;
                                break;
                        }
                    }
                });

                this.batchRender();

            } catch (error) {
                throw error;
            } finally {
                this.isRefreshing = false;
            }
        }

        async loadMoreQueueOrders() {
            if (this.isLoadingMoreQueue || !this.queueHasMore) return;

            try {
                this.isLoadingMoreQueue = true;
                const nextPage = this.queueCurrentPage + 1;

                this.showLoadMoreButton(false);

                const result = await this.api.getOrderQueue(nextPage);

                if (result?.success) {
                    const newOrders = result.data?.queue_orders || [];

                    this.queueOrders = [...this.queueOrders, ...newOrders];
                    this.queueHasMore = result.data?.has_more || false;
                    this.queueCurrentPage = result.data?.current_page || nextPage;
                    this.queueTotalPages = result.data?.total_pages || 1;

                    this.appendQueueOrders(newOrders);

                    this.updateLoadMoreButton();

                }
            } catch (error) {
                throw error;
            } finally {
                this.isLoadingMoreQueue = false;
            }
        }

        appendQueueOrders(newOrders) {
            const container = document.getElementById('orderQueue');
            if (!container) return;

            const loadMoreBtn = document.getElementById('loadMoreQueueBtn');

            const fragment = document.createDocumentFragment();
            const tempDiv = document.createElement('div');

            tempDiv.innerHTML = newOrders.map(order =>
                KitchenUIRenderer.createQueueItem(order)
            ).join('');

            while (tempDiv.firstChild) {
                fragment.appendChild(tempDiv.firstChild);
            }

            if (loadMoreBtn) {
                container.insertBefore(fragment, loadMoreBtn);
            } else {
                container.appendChild(fragment);
            }
        }

        batchRender() {
            requestAnimationFrame(() => {
                this.renderOrderQueue();
                this.renderActiveOrdersDirect();
                this.renderServedOrders();
                this.updateCounters();
                this.updateMobileTabCounts();
                this.loadMobileData();
            });
        }

        renderOrderQueue() {
            const container = document.getElementById('orderQueue');
            if (!container) return;

            try {
                const filteredOrders = (this.queueOrders || []).filter(order =>
                    this.orderMatchesSearch(order, this.searchTerm)
                );

                if (filteredOrders.length === 0 && !this.searchTerm) {
                    KitchenUIRenderer.showEmptyState(container, 'queue');
                    return;
                }

                if (filteredOrders.length === 0 && this.searchTerm) {
                    container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <div class="text-4xl mb-2">🔍</div>
                    <div class="text-sm">No results for "${this.searchTerm}"</div>
                </div>
            `;
                    return;
                }

                const fragment = document.createDocumentFragment();
                const tempDiv = document.createElement('div');

                tempDiv.innerHTML = filteredOrders.map(order =>
                    KitchenUIRenderer.createQueueItem(order)
                ).join('');

                while (tempDiv.firstChild) {
                    fragment.appendChild(tempDiv.firstChild);
                }

                container.innerHTML = '';
                container.appendChild(fragment);


                if (!this.searchTerm && this.queueHasMore) {
                    this.addLoadMoreButton(container);
                }
            } catch (error) {
                throw error;
            }
        }

        addLoadMoreButton(container) {
            if (!this.queueHasMore) return;

            const existingBtn = document.getElementById('loadMoreQueueBtn');
            if (existingBtn) existingBtn.remove();

            const remaining = this.queueTotal - this.queueOrders.length;
            const btnDiv = document.createElement('div');
            btnDiv.id = 'loadMoreQueueBtn';
            btnDiv.className = 'pagination-text';
            btnDiv.innerHTML = `
        <span class="pagination-link load-more-queue-btn">
            Show ${remaining} more orders (${this.queueOrders.length}/${this.queueTotal})
        </span>
    `;

            container.appendChild(btnDiv);
        }

        showLoadMoreButton(show = true) {
            const btnContainer = document.getElementById('loadMoreQueueBtn');
            if (!btnContainer) return;

            if (show) {
                const remaining = this.queueTotal - this.queueOrders.length;
                btnContainer.innerHTML = `
            <span class="pagination-link load-more-queue-btn">
                Show ${remaining} more orders (${this.queueOrders.length}/${this.queueTotal})
            </span>
        `;
            } else {
                btnContainer.innerHTML = `
            <span class="pagination-loading-text">Loading...</span>
        `;
            }
        }

        updateLoadMoreButton() {
            const btnContainer = document.getElementById('loadMoreQueueBtn');
            const container = document.getElementById('orderQueue');

            if (this.queueHasMore) {
                if (btnContainer) {
                    btnContainer.remove();
                }
                if (container) {
                    this.addLoadMoreButton(container);
                }
            } else {
                if (btnContainer) {
                    btnContainer.remove();
                }
            }
        }

        async loadMoreServedOrders() {
            if (this.isLoadingMoreServed || !this.servedHasMore) return;

            try {
                this.isLoadingMoreServed = true;
                const nextPage = this.servedCurrentPage + 1;

                this.showLoadMoreServedButton(false);

                // Fetch 10 data berikutnya
                const result = await this.api.getServedOrders(this.currentDate, nextPage);

                if (result?.success) {
                    const newOrders = result.data?.served_orders || [];

                    // APPEND data baru
                    this.servedOrders = [...this.servedOrders, ...newOrders];
                    this.servedHasMore = result.data?.has_more || false;
                    this.servedCurrentPage = result.data?.current_page || nextPage;
                    this.servedTotalPages = result.data?.total_pages || 1;

                    // Render new orders
                    this.appendServedOrders(newOrders);
                    this.updateLoadMoreServedButton();
                    this.updateCounters();
                }
            } catch (error) {
                throw error;
                this.showLoadMoreServedButton(true);
            } finally {
                this.isLoadingMoreServed = false;
            }
        }

        appendServedOrders(newOrders) {
            const container = document.getElementById('servedOrders');
            if (!container) return;

            const loadMoreBtn = document.getElementById('loadMoreServedBtn');

            const fragment = document.createDocumentFragment();
            const tempDiv = document.createElement('div');

            tempDiv.innerHTML = newOrders.map(order =>
                KitchenUIRenderer.createServedOrderItem(order)
            ).join('');

            while (tempDiv.firstChild) {
                fragment.appendChild(tempDiv.firstChild);
            }

            if (loadMoreBtn) {
                container.insertBefore(fragment, loadMoreBtn);
            } else {
                container.appendChild(fragment);
            }

        }

        renderServedOrders() {
            const container = document.getElementById('servedOrders');
            if (!container) return;

            try {
                const filteredOrders = (this.servedOrders || []).filter(order =>
                    this.orderMatchesSearch(order, this.searchTerm)
                );

                if (filteredOrders.length === 0 && !this.searchTerm) {
                    KitchenUIRenderer.showEmptyState(container, 'served');
                    return;
                }

                if (filteredOrders.length === 0 && this.searchTerm) {
                    container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <div class="text-4xl mb-2">🔍</div>
                    <div class="text-sm">No results for "${this.searchTerm}"</div>
                </div>
            `;
                    return;
                }

                const fragment = document.createDocumentFragment();
                const tempDiv = document.createElement('div');

                tempDiv.innerHTML = filteredOrders.map(order =>
                    KitchenUIRenderer.createServedOrderItem(order)
                ).join('');

                while (tempDiv.firstChild) {
                    fragment.appendChild(tempDiv.firstChild);
                }

                container.innerHTML = '';
                container.appendChild(fragment);

                if (!this.searchTerm && this.servedHasMore) {
                    this.addLoadMoreServedButton(container);
                }
            } catch (error) {
                throw error;
            }
        }

        addLoadMoreServedButton(container) {
            if (!this.servedHasMore) return;

            const existingBtn = document.getElementById('loadMoreServedBtn');
            if (existingBtn) existingBtn.remove();

            const remaining = this.servedTotal - this.servedOrders.length;
            const btnDiv = document.createElement('div');
            btnDiv.id = 'loadMoreServedBtn';
            btnDiv.className = 'pagination-text';
            btnDiv.innerHTML = `
        <span class="pagination-link served load-more-served-btn">
            Show ${remaining} more orders (${this.servedOrders.length}/${this.servedTotal})
        </span>
    `;

            container.appendChild(btnDiv);
        }

        showLoadMoreServedButton(show = true) {
            const btnContainer = document.getElementById('loadMoreServedBtn');
            if (!btnContainer) return;

            if (show) {
                const remaining = this.servedTotal - this.servedOrders.length;
                btnContainer.innerHTML = `
            <span class="pagination-link served load-more-served-btn">
                Show ${remaining} more orders (${this.servedOrders.length}/${this.servedTotal})
            </span>
        `;
            } else {
                btnContainer.innerHTML = `
            <span class="pagination-loading-text">Loading...</span>
        `;
            }
        }

        updateLoadMoreServedButton() {
            const btnContainer = document.getElementById('loadMoreServedBtn');
            const container = document.getElementById('servedOrders');

            if (this.servedHasMore) {
                if (btnContainer) {
                    btnContainer.remove();
                }
                if (container) {
                    this.addLoadMoreServedButton(container);
                }
            } else {
                if (btnContainer) {
                    btnContainer.remove();
                }
            }
        }

        renderActiveOrdersDirect(filteredOrders = null) {
            const container = document.getElementById('activeOrders');
            if (!container) return;

            try {
                const ordersToRender = filteredOrders || this.activeOrders;
                const sortedActiveOrders = (ordersToRender || []).slice().sort((a, b) => {
                    return (a.active_queue_number || 0) - (b.active_queue_number || 0);
                });

                if (sortedActiveOrders.length === 0) {
                    KitchenUIRenderer.showEmptyState(container, 'active');
                } else {
                    const fragment = document.createDocumentFragment();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = sortedActiveOrders.map(order =>
                        KitchenUIRenderer.createActiveOrderCard(order)
                    ).join('');

                    while (tempDiv.firstChild) {
                        fragment.appendChild(tempDiv.firstChild);
                    }

                    container.innerHTML = '';
                    container.appendChild(fragment);
                }
            } catch (error) {
                throw error;
            }
        }


        filterAndRenderOrdersWithVirtualScroll(containerId, orders) {
            const container = document.getElementById(containerId);
            if (!container) return;

            try {
                const filteredOrders = (orders || []).filter(order =>
                    this.orderMatchesSearch(order, this.searchTerm)
                );

                if (filteredOrders.length === 0) {
                    KitchenUIRenderer.showEmptyState(container, 'served');
                } else {
                    const itemsToRender = filteredOrders.slice(0, this.servedOrdersDisplayCount);

                    const fragment = document.createDocumentFragment();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = itemsToRender.map(order =>
                        KitchenUIRenderer.createServedOrderItem(order)
                    ).join('');

                    while (tempDiv.firstChild) {
                        fragment.appendChild(tempDiv.firstChild);
                    }

                    container.innerHTML = '';
                    container.appendChild(fragment);

                    if (filteredOrders.length > this.servedOrdersDisplayCount) {
                        this.setupInfiniteScrollForServed(container, filteredOrders);
                    }
                }
            } catch (error) {
                throw error;
            }
        }

        updateCounters() {
            try {

                let displayedQueueCount = this.queueTotal || 0;
                let displayedServedCount = this.servedTotal || 0;

                if (this.searchTerm && this.searchTerm.trim()) {
                    displayedQueueCount = (this.queueOrders || []).filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    ).length;

                    displayedServedCount = (this.servedOrders || []).filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    ).length;
                }


                const waitingCount = document.getElementById('waitingCount');
                if (waitingCount) waitingCount.textContent = displayedQueueCount;

                const cookingCount = document.getElementById('cookingCount');
                if (cookingCount) cookingCount.textContent = this.activeOrders.length;

                const completedCount = document.getElementById('completedCount');
                if (completedCount) completedCount.textContent = displayedServedCount;

                this.updateMobileBadge();
            } catch (error) {
                throw error;
            }
        }

        orderMatchesSearch(order, searchTerm) {
            if (!searchTerm) return true;
            if (!order) return false;

            const term = searchTerm.toLowerCase();
            return (
                (order.customer_name || '').toLowerCase().includes(term) ||
                (order.booking_order_code || '').toLowerCase().includes(term) ||
                (order.table?.table_no && order.table.table_no.toString().includes(term))
            );
        }



        updateMobileBadge() {
            const pendingBadge = document.getElementById('pendingOrdersBadge');
            if (pendingBadge) {

                const totalPending = this.queueTotal || 0;
                if (totalPending > 0) {
                    pendingBadge.classList.remove('hidden');
                    pendingBadge.textContent = totalPending > 99 ? '99+' : totalPending;
                } else {
                    pendingBadge.classList.add('hidden');
                }
            }
        }

        showServedOrderDetails(orderId) {
            try {
                let order = (this.servedOrders || []).find(o => o.id == orderId);

                if (!order && this.allServedOrders && this.allServedOrders.length > 0) {
                    order = this.allServedOrders.find(o => o.id == orderId);
                }

                if (!order) {
                    throw error;
                    return;
                }

                const customerName = document.getElementById('servedModalCustomerName');
                const orderCode = document.getElementById('servedModalOrderCode');
                const orderTime = document.getElementById('servedModalOrderTime');
                const servedTime = document.getElementById('servedModalServedTime');
                const tableNumber = document.getElementById('servedModalTableNumber');
                const tableType = document.getElementById('servedModalTableType');
                const totalItems = document.getElementById('servedModalTotalItems');


                const servedModalOrderDate = document.getElementById('servedModalOrderDate');
                if (servedModalOrderDate) servedModalOrderDate.textContent = order.order_date || '';

                if (customerName) customerName.textContent = order.customer_name || 'Customer';
                if (orderCode) orderCode.textContent = `Order #${order.booking_order_code || 'N/A'}`;
                if (orderTime) orderTime.textContent = order.order_time || '00:00';
                if (servedTime) servedTime.textContent = order.served_time || '00:00';
                if (tableNumber) tableNumber.textContent = `Table ${order.table?.table_no || '0'}`;
                if (tableType) {
                    tableType.textContent = order.table_type_badge || 'Indoor';
                    const colorClass = order.table_type_color === 'green' ?
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' :
                        order.table_type_color === 'blue' ?
                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100' :
                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
                    tableType.className = `text-xs font-medium px-2 py-1 rounded-full ${colorClass}`;
                }
                if (totalItems) totalItems.textContent = `${order.total_items || 0} items`;

                this.populateServedOrderItems(order);
                this.openModal('servedOrderDetailsModal');
            } catch (error) {
                throw error;
            }
        }

        populateServedOrderItems(order) {
            try {
                const container = document.getElementById('servedModalItems');
                if (!container) return;

                const orderDetails = order.order_details || [];

                if (orderDetails.length === 0) {
                    container.innerHTML =
                        `<div class="text-center text-gray-400 py-4 text-sm">No items found</div>`;
                } else {
                    const fragment = document.createDocumentFragment();
                    const tempDiv = document.createElement('div');

                    tempDiv.innerHTML = orderDetails.map((detail) => {
                        const optionsHTML = detail.options?.length > 0 ?
                            detail.options.map(opt =>
                                `<div class="text-xs text-gray-500 dark:text-gray-400 ml-4">• ${opt.name || ''}</div>`
                            ).join('') : '';

                        const noteHTML = detail.customer_note ?
                            `<div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 ml-4 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded">📝 <span class="font-medium">Catatan:</span> ${detail.customer_note}</div>` :
                            '';

                        return `<div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mb-2"><div class="flex items-center gap-2 mb-1"><div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-icons text-white" style="font-size: 14px;">check</span></div><p class="font-medium text-gray-700 dark:text-gray-300">${detail.product_name || 'Unknown'} × ${detail.quantity || 1}</p></div>${optionsHTML}${noteHTML}</div>`;
                    }).join('');
                    while (tempDiv.firstChild) {
                        fragment.appendChild(tempDiv.firstChild);
                    }

                    container.innerHTML = '';
                    container.appendChild(fragment);
                }
            } catch (error) {
                throw error;
            }
        }

        async cancelOrder(orderId) {
            try {
                const order = this.activeOrders.find(o => o.id == orderId);
                if (!order) return;

                this.closeModal('cancelConfirmationModal');

                const activeElement = document.getElementById(`kitchen-active-order-${orderId}`);
                if (activeElement) {
                    activeElement.style.pointerEvents = 'none';
                    activeElement.style.opacity = '0.5';
                }

                try {
                    const result = await this.api.cancelOrder(orderId);

                    if (result && result.success) {
                        const oldQueueNumber = order.active_queue_number;
                        this.activeOrders = this.activeOrders.filter(o => o.id != orderId);
                        this.renumberActiveOrdersAfter(oldQueueNumber);

                        const restoredOrder = {
                            ...order,
                            order_status: 'WAITING',
                            active_queue_number: undefined
                        };

                        this.queueOrders.push(restoredOrder);
                        this.queueOrders.sort((a, b) => {
                            const timeA = a.order_time ? a.order_time.split(':').map(Number) : [0, 0];
                            const timeB = b.order_time ? b.order_time.split(':').map(Number) : [0, 0];
                            const minutesA = timeA[0] * 60 + timeA[1];
                            const minutesB = timeB[0] * 60 + timeB[1];

                            if (minutesA !== minutesB) return minutesA - minutesB;
                            return a.id - b.id;
                        });


                        if (activeElement) {
                            activeElement.style.transition = 'all 0.3s ease-out';
                            activeElement.style.transform = 'scale(0.8)';
                            activeElement.style.opacity = '0';
                            setTimeout(() => activeElement.remove(), 300);
                        }

                        setTimeout(() => {
                            const container = document.getElementById('orderQueue');
                            if (container) {
                                const scrollTop = container.scrollTop;

                                this.renderOrderQueue();

                                container.scrollTop = scrollTop;

                                setTimeout(() => {
                                    const returnedCard = container.querySelector(
                                        `[data-order-id="${orderId}"]`);
                                    if (returnedCard) {
                                        returnedCard.style.backgroundColor =
                                            '#fef3c7';
                                        returnedCard.style.transition = 'background-color 2s ease';

                                        setTimeout(() => {
                                            returnedCard.style.backgroundColor = '';
                                        }, 2000);
                                    }
                                }, 100);
                            }

                            this.renderMobileQueue();
                        }, 350);

                        this.updateMobileCountersOnly();

                    } else {
                        if (activeElement) {
                            activeElement.style.pointerEvents = '';
                            activeElement.style.opacity = '';
                        }
                    }
                } catch (error) {
                    if (activeElement) {
                        activeElement.style.pointerEvents = '';
                        activeElement.style.opacity = '';
                    }
                    throw error;
                }

            } catch (error) {
                throw error;
            }
        }

        setupEventListeners() {
            document.addEventListener('click', (e) => {
                this.handleClick(e);
            }, {
                passive: true
            });

            this.setupSearchWithDebounce(300);
            this.setupMobileSidebar();
        }

        setupSearchWithDebounce(delay) {
            const searchInputs = [{
                    id: 'globalSearch',
                    callback: () => this.handleSearch()
                },
                {
                    id: 'mobileQueueSearch',
                    callback: () => {
                        this.handleSearch();
                        this.updater.filterOrdersVisibility('mobileOrderQueue', this.searchTerm);
                        this.updater.filterOrdersVisibility('mobileServedOrders', this.searchTerm);
                    }
                }
            ];

            searchInputs.forEach(({
                id,
                callback
            }) => {
                const input = document.getElementById(id);
                if (!input) return;

                let timer;
                const newInput = input.cloneNode(true);
                input.parentNode.replaceChild(newInput, input);

                newInput.addEventListener('input', (e) => {
                    clearTimeout(timer);
                    const searchValue = (e.target.value || '').toLowerCase().trim();

                    timer = setTimeout(() => {
                        this.searchTerm = searchValue;
                        this.isGlobalSearch = !!this.searchTerm;
                        if (callback) callback();
                    }, delay);
                }, {
                    passive: true
                });
            });
        }

        handleClick(e) {
            try {
                if (e.target.closest('.kitchen-queue-item')) {
                    const queueItem = e.target.closest('.kitchen-queue-item');
                    const isTakenByCashier = queueItem.dataset.cashierTaken === 'true';
                    if (isTakenByCashier) return;
                }

                if (e.target.closest('.kitchen-details-btn')) {
                    const button = e.target.closest('.kitchen-details-btn');
                    const orderId = button?.dataset?.orderId;
                    if (orderId) {
                        const isQueue = e.target.closest('.kitchen-queue-item');
                        if (isQueue) {
                            this.showQueueOrderDetails(orderId);
                        } else {
                            this.showOrderDetails(orderId);
                        }
                        this.closeMobileSidebar();
                    }
                }

                if (e.target.closest('.load-more-queue-btn')) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.loadMoreQueueOrders();
                    return;
                }

                if (e.target.closest('.load-more-served-btn')) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.loadMoreServedOrders();
                    return;
                }

                if (e.target.closest('.cancel-order-btn')) {
                    e.stopPropagation();
                    const button = e.target.closest('.cancel-order-btn');
                    const orderId = button?.dataset?.orderId;
                    if (orderId) {
                        this.showCancelConfirmation(orderId);
                    }
                }

                if (e.target.closest('.show-more-items-btn')) {
                    e.stopPropagation();
                    const button = e.target.closest('.show-more-items-btn');
                    const orderId = button?.dataset?.orderId;
                    if (orderId) {
                        this.showOrderDetails(orderId);
                    }
                }

                if (e.target.closest('.served-order-details-btn')) {
                    const button = e.target.closest('.served-order-details-btn');
                    const orderId = button?.dataset?.orderId;
                    if (orderId) {
                        this.showServedOrderDetails(orderId);
                        this.closeMobileSidebar();
                    }
                }

                if (e.target.closest('#startCookingBtn') && this.currentOrderId) {
                    this.pickUpOrder(this.currentOrderId);
                }

                if (e.target.closest('#serveOrderBtn') && this.currentOrderId) {
                    const serveBtn = e.target.closest('#serveOrderBtn');
                    if (!serveBtn.disabled) {
                        this.showServeConfirmation(this.currentOrderId);
                    }
                }

                if (e.target.closest('#confirmCancelBtn') && this.confirmationOrderId) {
                    this.cancelOrder(this.confirmationOrderId);
                }

                if (e.target.closest('#confirmServeBtn') && this.confirmationOrderId) {
                    this.markAsServed(this.confirmationOrderId);
                }

                if (e.target.classList.contains('fixed') && e.target.id.includes('Modal')) {
                    this.closeModal(e.target.id);
                }

                if (e.target.closest('[onclick*="closeModal"]')) {
                    const onclickAttr = e.target.closest('[onclick*="closeModal"]').getAttribute('onclick');
                    if (onclickAttr) {
                        const modalIdMatch = onclickAttr.match(/'([^']+)'/);
                        if (modalIdMatch) {
                            this.closeModal(modalIdMatch[1]);
                        }
                    }
                }
            } catch (error) {
                throw error;
            }
        }

        handleSearch() {
            try {
                const searchInput = document.getElementById('globalSearch');
                const mobileSearchInput = document.getElementById('mobileQueueSearch');

                if (mobileSearchInput && document.activeElement === mobileSearchInput) {
                    this.searchTerm = (mobileSearchInput.value || '').toLowerCase().trim();
                } else if (searchInput) {
                    this.searchTerm = (searchInput.value || '').toLowerCase().trim();
                }

                this.isGlobalSearch = !!this.searchTerm;

                this.renderOrderQueue();
                this.renderServedOrders();

                this.updateCounters();


            } catch (error) {
                throw error;
            }
        }

        async applyDateFilter(selectedDate) {
            this.currentDate = selectedDate || new Date().toISOString().split('T')[0];
            this.servedCurrentPage = 1;

            const result = await this.api.getServedOrders(this.currentDate, 1);
            if (result?.success) {
                this.servedOrders = result.data?.served_orders || [];
                this.servedTotal = result.data?.total_served || 0;
                this.servedTotalPages = result.data?.total_pages || 1;
                this.servedHasMore = result.data?.has_more || false;
                this.servedCurrentPage = result.data?.current_page || 1;

                this.renderServedOrders();
                this.renderMobileServed();
                this.updateCounters();
            }
        }
        async loadAllServedOrders() {
            const result = await this.api.getAllServedOrders();
            if (result?.success) {
                this.allServedOrders = result.data?.served_orders || [];
            }
        }

        setupMobileSidebar() {
            const mobileToggle = document.getElementById('mobileSidebarToggle');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileSidebarOverlay');
            const closeSidebar = document.getElementById('closeMobileSidebar');

            if (mobileToggle && mobileSidebar) {
                mobileToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    mobileSidebar.classList.remove('translate-x-full');
                    if (mobileOverlay) mobileOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    this.loadMobileData();
                });
            }

            if (closeSidebar) {
                closeSidebar.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    this.closeMobileSidebar();
                });
            }

            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    this.closeMobileSidebar();
                });
            }

            this.setupMobileTabs();
            this.setupMobileSearch();
        }

        setupMobileSearch() {
            const mobileQueueSearch = document.getElementById('mobileQueueSearch');
            const mobileDatePicker = document.getElementById('mobileDatePicker');

            if (mobileQueueSearch) {
                let searchTimeout;

                const newSearch = mobileQueueSearch.cloneNode(true);
                mobileQueueSearch.parentNode.replaceChild(newSearch, mobileQueueSearch);

                newSearch.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    const searchTerm = (e.target.value || '').toLowerCase().trim();

                    searchTimeout = setTimeout(() => {
                        this.searchTerm = searchTerm;
                        this.isGlobalSearch = !!searchTerm;

                        if (this.isGlobalSearch && this.searchTerm) {
                            this.loadAllServedOrders().then(() => {
                                this.renderMobileQueue();
                                this.renderMobileServed();
                                this.updateMobileTabCounts();
                            });
                        } else {
                            this.renderMobileQueue();
                            this.renderMobileServed();
                            this.updateMobileTabCounts();
                        }
                    }, 500);
                }, {
                    passive: true
                });

                newSearch.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        newSearch.value = '';
                        newSearch.blur();
                        this.searchTerm = '';
                        this.isGlobalSearch = false;
                        this.renderMobileQueue();
                        this.renderMobileServed();
                        this.updateMobileTabCounts();
                    }
                });
            }

            if (mobileDatePicker) {
                mobileDatePicker.addEventListener('change', (e) => {
                    const mobileQueueSearch = document.getElementById('mobileQueueSearch');
                    if (mobileQueueSearch) mobileQueueSearch.value = '';
                    this.searchTerm = '';
                    this.isGlobalSearch = false;
                    this.applyDateFilter(e.target.value);
                });
            }
        }

        closeMobileSidebar() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileSidebarOverlay');

            if (mobileSidebar) mobileSidebar.classList.add('translate-x-full');
            if (mobileOverlay) mobileOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        setupMobileTabs() {
            const queueTabBtn = document.getElementById('queueTabBtn');
            const servedTabBtn = document.getElementById('servedTabBtn');

            if (queueTabBtn) {
                queueTabBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.switchMobileTab('queue');
                });
            }

            if (servedTabBtn) {
                servedTabBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.switchMobileTab('served');
                });
            }
        }

        switchMobileTab(tab) {
            const queueTabBtn = document.getElementById('queueTabBtn');
            const servedTabBtn = document.getElementById('servedTabBtn');
            const queueTabContent = document.getElementById('queueTabContent');
            const servedTabContent = document.getElementById('servedTabContent');

            if (tab === 'queue') {
                queueTabBtn.classList.add('text-primary', 'border-primary');
                queueTabBtn.classList.remove('text-text-secondary-light', 'dark:text-text-secondary-dark',
                    'border-transparent');
                servedTabBtn.classList.remove('text-primary', 'border-primary');
                servedTabBtn.classList.add('text-text-secondary-light', 'dark:text-text-secondary-dark',
                    'border-transparent');
                queueTabContent.classList.remove('hidden');
                servedTabContent.classList.add('hidden');
                this.renderMobileQueue();
            } else {
                servedTabBtn.classList.add('text-primary', 'border-primary');
                servedTabBtn.classList.remove('text-text-secondary-light', 'dark:text-text-secondary-dark',
                    'border-transparent');
                queueTabBtn.classList.remove('text-primary', 'border-primary');
                queueTabBtn.classList.add('text-text-secondary-light', 'dark:text-text-secondary-dark',
                    'border-transparent');
                servedTabContent.classList.remove('hidden');
                queueTabContent.classList.add('hidden');

                if (this.searchTerm && this.isGlobalSearch) {
                    this.loadAllServedOrders().then(() => {
                        this.renderMobileServed();
                    });
                } else {
                    this.renderMobileServed();
                }
            }
        }

        loadMobileData() {
            if (this.searchTerm && this.isGlobalSearch) {
                this.loadAllServedOrders().then(() => {
                    this.renderMobileQueue();
                    this.renderMobileServed();
                    this.updateMobileTabCounts();
                });
            } else {
                this.renderMobileQueue();
                this.renderMobileServed();
                this.updateMobileTabCounts();
            }
        }

        renderMobileQueue() {
            const container = document.getElementById('mobileOrderQueue');
            if (!container) return;

            try {
                let filteredQueue = this.queueOrders || [];

                if (this.searchTerm && this.searchTerm.length > 0) {
                    filteredQueue = filteredQueue.filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    );
                }

                if (filteredQueue.length === 0) {
                    const message = this.searchTerm ?
                        `No orders found for "${this.searchTerm}"` :
                        'No orders in queue';
                    container.innerHTML = `
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <div class="text-sm font-medium">${message}</div>
                    ${this.searchTerm ? '<div class="text-xs mt-2">Try a different search term</div>' : ''}
                </div>
            `;
                } else {
                    const fragment = document.createDocumentFragment();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = filteredQueue.map(order =>
                        KitchenUIRenderer.createQueueItem(order)
                    ).join('');

                    while (tempDiv.firstChild) {
                        fragment.appendChild(tempDiv.firstChild);
                    }

                    container.innerHTML = '';
                    container.appendChild(fragment);
                }
            } catch (error) {
                throw error;
                container.innerHTML = `
            <div class="text-center text-red-500 py-8">
                <div class="text-sm">Error loading orders</div>
            </div>
        `;
            }
        }

        renderMobileServed() {
            const container = document.getElementById('mobileServedOrders');
            if (!container) return;

            try {
                let ordersToFilter = this.isGlobalSearch && this.searchTerm ?
                    this.allServedOrders : this.servedOrders;

                let filteredServed = ordersToFilter || [];

                if (this.searchTerm && this.searchTerm.length > 0) {
                    filteredServed = filteredServed.filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    );
                }

                if (filteredServed.length === 0) {
                    const message = this.searchTerm ?
                        `No served orders found for "${this.searchTerm}"` :
                        'No served orders';
                    container.innerHTML = `
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <div class="text-4xl mb-2">✅</div>
                    <div class="text-sm font-medium">${message}</div>
                    ${this.searchTerm ? '<div class="text-xs mt-2">Try a different search term</div>' : ''}
                </div>
            `;
                } else {
                    const fragment = document.createDocumentFragment();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = filteredServed.map(order =>
                        KitchenUIRenderer.createServedOrderItem(order)
                    ).join('');

                    while (tempDiv.firstChild) {
                        fragment.appendChild(tempDiv.firstChild);
                    }

                    container.innerHTML = '';
                    container.appendChild(fragment);
                }
            } catch (error) {
                throw error;
                container.innerHTML = `
            <div class="text-center text-red-500 py-8">
                <div class="text-sm">Error loading served orders</div>
            </div>
        `;
            }
        }

        updateMobileTabCounts() {
            const queueTabCount = document.getElementById('queueTabCount');
            const servedTabCount = document.getElementById('servedTabCount');

            if (queueTabCount) {
                let queueCount = this.queueOrders.length;
                if (this.searchTerm) {
                    queueCount = this.queueOrders.filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    ).length;
                }
                queueTabCount.textContent = `(${queueCount})`;
            }

            if (servedTabCount) {
                let servedCount = this.servedOrders.length;
                if (this.searchTerm && this.isGlobalSearch) {
                    servedCount = (this.allServedOrders || []).filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    ).length;
                } else if (this.searchTerm) {
                    servedCount = this.servedOrders.filter(order =>
                        this.orderMatchesSearch(order, this.searchTerm)
                    ).length;
                }
                servedTabCount.textContent = `(${servedCount})`;
            }
        }

        initializeMobile() {
            this.updateMobileTabCounts();
            this.updateMobileBadge();

            const datePicker = document.getElementById('datePicker');
            const mobileDatePicker = document.getElementById('mobileDatePicker');
            if (datePicker) datePicker.value = this.currentDate;
            if (mobileDatePicker) mobileDatePicker.value = this.currentDate;
        }

        showQueueOrderDetails(orderId) {
            try {
                const order = (this.queueOrders || []).find(o => o.id == orderId);
                if (!order) return;

                this.currentOrderId = orderId;

                const orderCodeBadge = document.getElementById('queueModalOrderCode');
                if (orderCodeBadge) orderCodeBadge.textContent = order.booking_order_code || 'N/A';

                const orderDate = document.getElementById('queueModalOrderDate');
                if (orderDate) orderDate.textContent = order.order_date || '';

                const customerName = document.getElementById('queueModalCustomerName');
                if (customerName) customerName.textContent = order.customer_name || 'Customer';

                const orderTime = document.getElementById('queueModalOrderTime');
                if (orderTime) orderTime.textContent = order.order_time || '00:00';

                const totalItems = document.getElementById('queueModalTotalItems');
                if (totalItems) totalItems.textContent = `${order.total_items || 0} items`;

                const tableNumber = document.getElementById('queueModalTableNumber');
                if (tableNumber) tableNumber.textContent = `Table ${order.table?.table_no || '0'}`;

                const tableType = document.getElementById('queueModalTableType');
                if (tableType) {
                    tableType.textContent = order.table_type_badge || 'Indoor';
                    const colorClass = order.table_type_color === 'green' ?
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' :
                        order.table_type_color === 'blue' ?
                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100' :
                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
                    tableType.className = `text-xs font-medium px-2 py-1 rounded-full ${colorClass}`;
                }

                const orderNoteContainer = document.getElementById('queueModalOrderNote');
                const orderNoteText = document.getElementById('queueModalOrderNoteText');
                if (order.customer_order_note && order.customer_order_note.trim()) {
                    if (orderNoteContainer) orderNoteContainer.classList.remove('hidden');
                    if (orderNoteText) orderNoteText.textContent = order.customer_order_note;
                } else {
                    if (orderNoteContainer) orderNoteContainer.classList.add('hidden');
                }

                this.populateQueueOrderItems(order);
                this.openModal('queueOrderModal');
            } catch (error) {
                throw error;
            }
        }

        showOrderDetails(orderId) {
            try {
                const order = (this.activeOrders || []).find(o => o.id == orderId);
                if (!order) return;

                this.currentOrderId = orderId;

                const modalCustomerName = document.getElementById('modalCustomerName');
                const modalOrderTime = document.getElementById('modalOrderTime');
                const modalTableNumber = document.getElementById('modalTableNumber');
                const modalTableType = document.getElementById('modalTableType');
                const orderCode = document.getElementById('modalOrderCode');
                const orderNumberBadge = document.querySelector('.order-number-badge-modal');

                const modalOrderDate = document.getElementById('modalOrderDate');
                if (modalOrderDate) modalOrderDate.textContent = order.order_date || '';

                if (modalCustomerName) modalCustomerName.textContent = order.customer_name || 'Customer';
                if (modalOrderTime) modalOrderTime.textContent = ` ${order.order_time || ''}`;
                if (orderCode) orderCode.textContent = `Order #${order.booking_order_code || 'N/A'}`;
                if (modalTableNumber) modalTableNumber.textContent = `Table ${order.table?.table_no || 'T'}`;
                if (modalTableType) {
                    modalTableType.textContent = order.table_type_badge || '';
                    modalTableType.className = `text-xs font-medium px-2 py-0.5 rounded-full ${order.table_type_color === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 
                order.table_type_color === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100' : 
                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100'}`;
                }

                if (orderNumberBadge) {
                    orderNumberBadge.textContent = order.active_queue_number || '';
                }

                this.populateOrderItems(order);
                this.setupCheckboxFunctionality();
                this.openModal('orderDetailsModal');
            } catch (error) {
                throw error;
            }
        }

        showServeConfirmation(orderId) {
            try {
                const order = (this.activeOrders || []).find(o => o.id == orderId);
                if (!order) return;

                this.confirmationOrderId = orderId;

                const confirmCustomerName = document.getElementById('confirmCustomerName');
                const confirmTableNumber = document.getElementById('confirmTableNumber');
                const confirmTotalItems = document.getElementById('confirmTotalItems');
                const orderNumberBadge = document.querySelector(
                    '#serveConfirmationModal .order-number-badge-modal');

                const confirmServeOrderDate = document.getElementById('confirmServeOrderDate');
                if (confirmServeOrderDate) confirmServeOrderDate.textContent = order.order_date || '';

                if (confirmCustomerName) confirmCustomerName.textContent = order.customer_name || 'Customer';
                if (confirmTableNumber) confirmTableNumber.textContent = `${order.table?.table_no || 'T'}`;
                if (confirmTotalItems) confirmTotalItems.textContent = `${order.total_items || 0} items`;

                if (orderNumberBadge) {
                    orderNumberBadge.textContent = order.active_queue_number || '';
                }

                this.openModal('serveConfirmationModal');
            } catch (error) {
                throw error;
            }
        }

        showCancelConfirmation(orderId) {
            try {
                const order = (this.activeOrders || []).find(o => o.id == orderId);
                if (!order) return;

                this.confirmationOrderId = orderId;

                const confirmCancelOrderCode = document.getElementById('confirmCancelOrderCode');
                const confirmCancelTableNumber = document.getElementById('confirmCancelTableNumber');
                const confirmCancelCustomerName = document.getElementById('confirmCancelCustomerName');
                const orderNumberBadge = document.querySelector(
                    '#cancelConfirmationModal .order-number-badge-modal');

                const confirmCancelOrderDate = document.getElementById('confirmCancelOrderDate');
                if (confirmCancelOrderDate) confirmCancelOrderDate.textContent = order.order_date || '';

                if (confirmCancelOrderCode) confirmCancelOrderCode.textContent = order.booking_order_code || 'N/A';
                if (confirmCancelTableNumber) confirmCancelTableNumber.textContent =
                    `Table ${order.table?.table_no || 'T'}`;
                if (confirmCancelCustomerName) confirmCancelCustomerName.textContent = order.customer_name ||
                    'Customer';
                if (orderNumberBadge) orderNumberBadge.textContent = order.active_queue_number || '';

                this.openModal('cancelConfirmationModal');
            } catch (error) {
                throw error;
            }
        }

        populateOrderItems(order) {
            try {
                const container = document.getElementById('modalOrderItems');
                if (!container) return;

                const orderDetails = order.order_details || [];
                const fragment = document.createDocumentFragment();
                const tempDiv = document.createElement('div');

                tempDiv.innerHTML = orderDetails.map((detail) => {
                    const optionsHTML = detail.options?.length > 0 ?
                        detail.options.map(opt =>
                            `<div class="text-xs text-gray-500 dark:text-gray-400 ml-4">• ${opt.name || ''}</div>`
                        ).join('') : '';

                    const noteHTML = detail.customer_note ?
                        `<div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 ml-4 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded">📝 <span class="font-medium">Catatan:</span> ${detail.customer_note}</div>` :
                        '';

                    return `<div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mb-2"><input type="checkbox" class="item-checkbox mt-1 w-4 h-4 rounded border-2 border-gray-300 bg-white cursor-pointer appearance-none checked:bg-green-500 checked:border-green-500 relative" data-item-id="${detail.id || ''}">
                        <div class="flex-1"><p class="font-medium text-gray-700 dark:text-gray-300 mb-1">${detail.product_name || 'Unknown'} × ${detail.quantity || 1}</p>${optionsHTML}${noteHTML}</div></div>`;
                }).join('');

                while (tempDiv.firstChild) {
                    fragment.appendChild(tempDiv.firstChild);
                }

                container.innerHTML = '';
                container.appendChild(fragment);
            } catch (error) {
                throw error;
            }
        }

        populateQueueOrderItems(order) {
            try {
                const container = document.getElementById('queueModalItems');
                if (!container) return;

                const orderDetails = order.order_details || [];

                if (orderDetails.length === 0) {
                    container.innerHTML =
                        `<div class="text-center text-gray-400 py-4 text-sm">No items found</div>`;
                } else {
                    const fragment = document.createDocumentFragment();
                    const tempDiv = document.createElement('div');

                    tempDiv.innerHTML = orderDetails.map((detail) => {
                        const optionsHTML = detail.options?.length > 0 ?
                            detail.options.map(opt =>
                                `<div class="text-xs text-gray-500 dark:text-gray-400 ml-4 mt-1">• ${opt.name || ''}</div>`
                            ).join('') : '';

                        const hasNote = detail.customer_note && detail.customer_note.trim() !== '';
                        const noteHTML = hasNote ?
                            `<div class="text-xs text-yellow-600 mt-2 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded border border-yellow-200 dark:border-yellow-800"><div class="flex items-start gap-1"><span class="material-icons" style="font-size: 14px;">sticky_note_2</span><div><span class="font-semibold">Catatan:</span><span class="ml-1">${detail.customer_note}</span></div></div></div>` :
                            '';

                        return `<div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg mb-2 border border-gray-200 dark:border-gray-700"><div class="flex items-start justify-between mb-1"><p class="font-semibold text-gray-800 dark:text-gray-200">${detail.product_name || 'Unknown'}</p><span class="text-gray-600 dark:text-gray-400 font-mono text-sm">×${detail.quantity || 1}</span></div>${optionsHTML}${noteHTML}</div>`;
                    }).join('');

                    while (tempDiv.firstChild) {
                        fragment.appendChild(tempDiv.firstChild);
                    }

                    container.innerHTML = '';
                    container.appendChild(fragment);
                }
            } catch (error) {
                throw error;
            }
        }

        setupCheckboxFunctionality() {
            try {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                const serveBtn = document.getElementById('serveOrderBtn');

                const updateButtonStates = () => {
                    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                    if (serveBtn) {
                        serveBtn.disabled = !allChecked;
                    }
                };

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateButtonStates);
                });

                const checkAllBtn = document.getElementById('checkAllBtn');
                if (checkAllBtn) {
                    checkAllBtn.addEventListener('click', () => {
                        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                        checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
                        updateButtonStates();
                    });
                }

                updateButtonStates();
            } catch (error) {
                throw error;
            }
        }


        updateMobileCountersOnly() {
            const queueTabCount = document.getElementById('queueTabCount');
            const servedTabCount = document.getElementById('servedTabCount');

            if (queueTabCount) {
                queueTabCount.textContent = `(${this.queueOrders.length})`;
            }

            if (servedTabCount) {
                servedTabCount.textContent = `(${this.servedOrders.length})`;
            }
        }

        renumberActiveOrdersAfter(oldNumber) {
            this.activeOrders = this.activeOrders.map(o => {
                if (o.active_queue_number > oldNumber) {
                    return {
                        ...o,
                        active_queue_number: o.active_queue_number - 1
                    };
                }
                return o;
            });

            this.activeOrders.forEach(order => {
                if (order.active_queue_number >= oldNumber) {
                    const card = document.getElementById(`kitchen-active-order-${order.id}`);
                    if (card) {
                        const badge = card.querySelector('.text-red-600.font-black.text-xl');
                        if (badge) {
                            badge.style.transition = 'transform 0.2s ease';
                            badge.style.transform = 'scale(1.2)';
                            badge.textContent = `#${order.active_queue_number}`;

                            setTimeout(() => {
                                badge.style.transform = 'scale(1)';
                            }, 200);
                        }
                    }
                }
            });
        }


        addSingleActiveOrderCard(order) {
            const container = document.getElementById('activeOrders');
            if (!container) return;

            const emptyState = container.querySelector('.col-span-full');
            if (emptyState && this.activeOrders.length > 0) {
                emptyState.remove();
            }

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = KitchenUIRenderer.createActiveOrderCard(order);
            const newElement = tempDiv.firstChild;

            newElement.style.opacity = '0';
            newElement.style.transform = 'scale(0.8)';
            newElement.style.transition = 'all 0.3s ease-out';

            container.appendChild(newElement);

            requestAnimationFrame(() => {
                newElement.style.opacity = '1';
                newElement.style.transform = 'scale(1)';
            });
        }

        addSingleServedOrderCard(order) {
            const container = document.getElementById('servedOrders');
            if (!container) return;

            const emptyState = container.querySelector('.text-center.py-12, .col-span-full');
            if (emptyState && this.servedOrders.length > 0) {
                emptyState.remove();
            }

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = KitchenUIRenderer.createServedOrderItem(order);
            const newElement = tempDiv.firstChild;

            newElement.style.opacity = '0';
            newElement.style.transform = 'translateY(-20px)';
            newElement.style.transition = 'all 0.3s ease-out';

            container.insertBefore(newElement, container.firstChild);

            requestAnimationFrame(() => {
                newElement.style.opacity = '1';
                newElement.style.transform = 'translateY(0)';
            });

        }



        async pickUpOrder(orderId) {
            try {
                const order = this.queueOrders.find(o => o.id == orderId);
                if (!order) return;

                this.closeModal('queueOrderModal');

                const queueElement = document.querySelector(`#orderQueue [data-order-id="${orderId}"]`);
                if (queueElement) {
                    queueElement.style.pointerEvents = 'none';
                    queueElement.style.opacity = '0.5';
                }

                try {
                    const result = await this.api.pickUpOrder(orderId);

                    if (result && result.success) {
                        this.queueOrders = this.queueOrders.filter(o => o.id != orderId);

                        
                        const now = new Date();
                        const currentTime =
                            `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

                        const newActiveOrder = {
                            ...order,
                            order_status: 'PROCESSED',
                            active_queue_number: this.activeOrders.length + 1,
                            picked_up_at: now.toISOString(),
                            order_time: currentTime 
                        };
                        this.activeOrders.push(newActiveOrder);

                        if (queueElement) {
                            queueElement.style.transition = 'all 0.3s ease-out';
                            queueElement.style.transform = 'translateX(100%)';
                            queueElement.style.opacity = '0';

                            setTimeout(() => {
                                queueElement.remove();
                            }, 300);
                        }

                        setTimeout(() => {
                            this.addSingleActiveOrderCard(newActiveOrder);
                        }, 350);

                        this.updateMobileCountersOnly();

                    } else {
                        if (queueElement) {
                            queueElement.style.pointerEvents = '';
                            queueElement.style.opacity = '';
                        }
                    }
                } catch (error) {
                    if (queueElement) {
                        queueElement.style.pointerEvents = '';
                        queueElement.style.opacity = '';
                    }
                    throw error;
                }

            } catch (error) {
                throw error;
            }
        }

        async markAsServed(orderId) {
            try {
                const order = this.activeOrders.find(o => o.id == orderId);
                if (!order) return;

                this.closeModal('serveConfirmationModal');
                this.closeModal('orderDetailsModal');

                const activeElement = document.getElementById(`kitchen-active-order-${orderId}`);
                if (activeElement) {
                    activeElement.style.pointerEvents = 'none';
                    activeElement.style.opacity = '0.5';
                }

                try {
                    const result = await this.api.markAsServed(orderId);

                    if (result && result.success) {
                        const oldQueueNumber = order.active_queue_number;
                        this.activeOrders = this.activeOrders.filter(o => o.id != orderId);

                        this.renumberActiveOrdersAfter(oldQueueNumber);

                        const servedOrder = {
                            ...order,
                            order_status: 'SERVED',
                            served_time: new Date().toTimeString().slice(0, 5)
                        };
                        this.servedOrders.unshift(servedOrder);


                        if (activeElement) {
                            activeElement.style.transition = 'all 0.3s ease-out';
                            activeElement.style.transform = 'scale(0.8)';
                            activeElement.style.opacity = '0';

                            setTimeout(() => {
                                activeElement.remove();
                            }, 300);
                        }

                        setTimeout(() => {
                            this.addSingleServedOrderCard(servedOrder);
                        }, 350);

                        this.updateMobileCountersOnly();

                    } else {
                        if (activeElement) {
                            activeElement.style.pointerEvents = '';
                            activeElement.style.opacity = '';
                        }
                        alert('Failed to mark as served. Please try again.');
                    }
                } catch (error) {
                    if (activeElement) {
                        activeElement.style.pointerEvents = '';
                        activeElement.style.opacity = '';
                    }
                    throw error;
                }

            } catch (error) {
                throw error;
            }
        }

        openModal(modalId) {
            try {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    if (window.innerWidth < 1024) {
                        document.body.style.overflow = 'hidden';
                    }
                }
            } catch (error) {
                throw error;
            }
        }

        closeModal(modalId) {
            try {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }

                if (modalId === 'orderDetailsModal' || modalId === 'queueOrderModal') {
                    this.currentOrderId = null;
                }
                if (modalId === 'serveConfirmationModal') {
                    this.confirmationOrderId = null;
                }
            } catch (error) {
                throw error;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            if (document.getElementById('headerTime')) {
                updateHeaderTime();
                setInterval(updateHeaderTime, 1000);
            }

            window.kitchenDashboard = new KitchenDashboard();
            window.kitchenDashboard.init();
        } catch (error) {
            throw error;
        }
    });
</script>

<style>
    .flex.h-screen {
        height: 100vh;
        overflow: hidden
    }

    .flex-1.overflow-auto {
        overflow: auto;
        will-change: scroll-position
    }

    .h-screen.sticky {
        position: sticky;
        top: 0
    }

    .kitchen-queue-item,
    .kitchen-active-order,
    .compact-served-card {
        will-change: transform;
        transform: translateZ(0);
        backface-visibility: hidden
    }

    .transition-all {
        transition: all .15s cubic-bezier(.4, 0, .2, 1)
    }

    #orderQueue,
    #servedOrders,
    #activeOrders {
        -webkit-overflow-scrolling: touch;
        contain: layout style paint
    }

    .min-h-0 {
        min-height: 0
    }

    .flex-1 {
        flex: 1 1 0%
    }

    .scrollbar-thin {
        scrollbar-width: thin;
        scrollbar-color: #c1c1c1 #f1f1f1
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 6px
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8
    }

    .cancel-order-btn {
        transform: translateZ(0);
        will-change: opacity
    }

    .hover\:shadow-md:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .1), 0 2px 4px -1px rgba(0, 0, 0, .06)
    }

    .hover\:shadow-lg:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -2px rgba(0, 0, 0, .05)
    }

    @media(max-width:414px) {
        .kitchen-active-order {
            min-height: 180px
        }

        #activeOrders {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px
        }

        .kitchen-active-order {
            min-width: 0
        }
    }

    @media(max-width:380px) {
        #activeOrders {
            grid-template-columns: 1fr;
            gap: 10px
        }
    }

    @media(min-width:768px) {
        #activeOrders {
            grid-template-columns: repeat(3, 1fr);
            gap: 16px
        }
    }

    @media(min-width:1024px) {
        #activeOrders {
            grid-template-columns: repeat(4, 1fr);
            gap: 20px
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg)
        }

        to {
            transform: rotate(360deg)
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite
    }

    /* Style untuk refresh button animation */
    #refreshIcon {
        transition: transform 0.3s ease;
    }

    /* Text-only Pagination Styles */
    .pagination-text {
        text-align: center;
        padding: 12px 0;
    }

    .pagination-link {
        color: #dc2626;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-block;
    }

    .pagination-link:hover {
        color: #b91c1c;
        text-decoration: underline;
    }

    .pagination-link.served {
        color: #16a34a;
    }

    .pagination-link.served:hover {
        color: #15803d;
    }

    .pagination-loading-text {
        text-align: center;
        padding: 12px 0;
        color: #6b7280;
        font-size: 14px;
    }

    .dark .pagination-link {
        color: #ef4444;
    }

    .dark .pagination-link.served {
        color: #22c55e;
    }

    .dark .pagination-loading-text {
        color: #9ca3af;
    }
</style>
