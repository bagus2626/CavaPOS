<script>
// Configuration
const KITCHEN_CONFIG = {
    baseUrl: '{{ url("/") }}',
    refreshInterval: 30000,
    endpoints: {
        queue: '{{ route("employee.kitchen.orders.queue") }}',
        active: '{{ route("employee.kitchen.orders.active") }}',
        served: '{{ route("employee.kitchen.orders.served") }}',
        pickup: '{{ route("employee.kitchen.orders.pickup", ["orderId" => ":id"]) }}',
        serve: '{{ route("employee.kitchen.orders.serve", ["orderId" => ":id"]) }}'
    }
};

// API Service
class KitchenApiService {
    constructor() {
        this.csrfToken = this.getCsrfToken();
        console.log('🔐 CSRF Token:', this.csrfToken ? 'Loaded' : 'Not found');
    }

    getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    async getOrderQueue() {
        return this._fetch(KITCHEN_CONFIG.endpoints.queue);
    }

    async getActiveOrders() {
        return this._fetch(KITCHEN_CONFIG.endpoints.active);
    }

    async getServedOrders(date = null) {
        const today = new Date().toISOString().split('T')[0];
        const selectedDate = date || today;
        return this._fetch(`${KITCHEN_CONFIG.endpoints.served}?date=${selectedDate}`);
    }

    async pickUpOrder(orderId) {
        const endpoint = KITCHEN_CONFIG.endpoints.pickup.replace(':id', orderId);
        
        // === DEBUGGING API CALL ===
        console.log('🌐 [DEBUG] Making pickup API call to:', endpoint);
        console.log('🔑 [DEBUG] CSRF Token exists:', !!this.csrfToken);
        console.log('📦 [DEBUG] Order ID:', orderId);
        // === END DEBUGGING ===
        
        return this._fetch(endpoint, 'PUT');
    }

    async markAsServed(orderId) {
        const endpoint = KITCHEN_CONFIG.endpoints.serve.replace(':id', orderId);
        console.log('✅ Serve Endpoint:', endpoint);
        return this._fetch(endpoint, 'PUT');
    }

    async _fetch(endpoint, method = 'GET', body = null) {
        // === DEBUGGING REQUEST ===
        console.log('🌐 [DEBUG] _fetch called:', { endpoint, method, body });
        // === END DEBUGGING ===

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (method !== 'GET' && this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        const options = {
            method: method,
            headers: headers,
            credentials: 'same-origin'
        };

        if (body && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            options.body = JSON.stringify(body);
        }

        // === DEBUGGING REQUEST DETAILS ===
        console.log('📤 [DEBUG] Request options:', options);
        // === END DEBUGGING ===

        try {
            const response = await fetch(endpoint, options);
            
            // === DEBUGGING RESPONSE ===
            console.log('📥 [DEBUG] Response status:', response.status, response.statusText);
            // === END DEBUGGING ===
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ [DEBUG] Response error text:', errorText);
                
                // Try to parse JSON error message
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorJson = JSON.parse(errorText);
                    errorMessage = errorJson.message || errorMessage;
                } catch (e) {
                    // If not JSON, use the text as is
                    errorMessage = errorText || errorMessage;
                }
                
                throw new Error(errorMessage);
            }
            
            const result = await response.json();
            console.log('✅ [DEBUG] Response JSON:', result);
            return result;
            
        } catch (error) {
            console.error('❌ [DEBUG] Fetch error:', error);
            throw error;
        }
    }
}

class KitchenUIRenderer {
    static createQueueItem(order) {
        const hasNotes = order.order_details && order.order_details.some(detail => 
            detail.customer_note && detail.customer_note.trim() !== ''
        );
        
        const noteIndicator = hasNotes ? `
            <div class="flex items-center mt-1">
                <span class="material-symbols-outlined text-yellow-500 text-sm mr-0.5">info</span>
                <span class="text-xs text-yellow-400 font-medium">catatan </span>
            </div>
        ` : '';

        return `
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl card-elegant border border-gray-100 dark:border-gray-700 flex items-start space-x-4 kitchen-queue-item mb-3" data-order-id="${order.id}">
                <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 mt-1 shadow-md">${order.queue_number}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center min-w-0">
                            <h3 class="font-bold text-gray-900 dark:text-white truncate typography-heading">${order.customer_name}</h3>
                        </div>
                        <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 kitchen-details-btn flex-shrink-0 ml-2 transition-colors" data-order-id="${order.id}">
                            <span class="material-icons text-base">more_vert</span>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate mt-1">${order.order_time} • ${order.total_items} items</p>
                    ${noteIndicator}
                </div>
            </div>
        `;
    }

    static createActiveOrderCard(order) {
    console.log('🖼️ [CARD DEBUG] Creating card for order:', {
        id: order.id,
        customer: order.customer_name,
        details_count: order.order_details ? order.order_details.length : 0
    });

    const orderDetails = order.order_details || [];
    const displayItems = orderDetails.slice(0, 2);
    const remainingItems = orderDetails.length - displayItems.length;
    
    const hasNotes = orderDetails.some(detail => 
        detail.customer_note && detail.customer_note.trim() !== ''
    );

    // **FIX: Handle empty order details**
    let menuItemsHTML = '';
    if (displayItems.length === 0) {
        menuItemsHTML = `
            <div class="flex items-start mb-1.5">
                <span class="text-gray-500 dark:text-gray-400 text-xs lg:text-sm font-medium typography-enhanced">
                    No items details available
                </span>
            </div>
        `;
    } else {
        menuItemsHTML = displayItems.map(detail => {
            const productName = detail.product_name || 'Unknown Product';
            const quantity = detail.quantity || 1;
            const optionsText = detail.options && detail.options.length > 0 
                ? ` (${detail.options[0].name})`
                : '';
            
            return `
                <div class="flex items-start mb-1.5">
                    <span class="text-gray-900 dark:text-white text-xs lg:text-sm font-medium typography-enhanced line-clamp-1">
                        ${productName}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400 text-xs ml-1 typography-enhanced flex-shrink-0">
                        ×${quantity}
                    </span>
                </div>
            `;
        }).join('');
    }
    
    const cardHTML = `
        <div class="relative bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl shadow-lg overflow-hidden kitchen-active-order h-full min-h-[200px] lg:min-h-[240px] flex flex-col border-r-4 border-primary" data-order-id="${order.id}">
            <div class="p-2.5 lg:p-4 pb-2 lg:pb-3 flex justify-between items-start border-b border-gray-100 dark:border-gray-700">
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-sm lg:text-base text-gray-900 dark:text-white truncate typography-heading">${order.customer_name || 'Customer'}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 typography-enhanced">${order.order_time || '00:00'}</p>
                </div>
                <div class="flex items-center gap-1.5 lg:gap-2 flex-shrink-0">
                    ${hasNotes ? `
                        <div class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-full px-1 lg:px-1 py-1 flex items-center">
                            <span class="material-symbols-outlined text-">info</span>
                        </div>
                    ` : ''}
                    <div class="bg-primary text-white w-6 h-6 lg:w-7 lg:h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-sm">${order.queue_number || order.table_id || '0'}</div>
                </div>
            </div>

            <div class="flex-1 p-2.5 lg:p-4 pt-2 lg:pt-3 min-h-0 overflow-hidden">
                <div class="space-y-0.5 lg:space-y-1">
                    ${menuItemsHTML}
                </div>

                ${remainingItems > 0 ? `
                    <p class="text-primary font-semibold text-xs lg:text-sm mt-1.5 lg:mt-2 typography-enhanced">+${remainingItems} more</p>
                ` : ''}
            </div>

            <div class="p-2.5 lg:p-4 pt-0">
                <button class="w-full bg-primary hover:bg-primary-hover text-white py-2 lg:py-2.5 rounded-lg lg:rounded-xl font-semibold kitchen-details-btn transition-all duration-300 shadow-sm hover:shadow-md text-xs lg:text-sm typography-enhanced" data-order-id="${order.id}">
                    View Details
                </button>
            </div>
        </div>
    `;

    console.log('🖼️ [CARD DEBUG] Card HTML generated for order:', order.id);
    return cardHTML;
}

    static createServedOrderItem(order, showDate = false) {
        const servedTime = order.served_time ? `
            <p class="text-green-600 dark:text-green-400 font-medium text-sm typography-enhanced">Served: ${order.served_time}</p>
        ` : '';

        return `
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl card-elegant border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300 min-h-[80px]">
                <div class="flex justify-between items-start mb-2">
                    <h2 class="font-bold text-base text-gray-900 dark:text-white truncate flex-1 mr-2 typography-heading">${order.customer_name}</h2>
                    ${servedTime}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 typography-enhanced">
                    <div class="flex items-center flex-wrap gap-1 md:gap-1.5">
                        <span class="flex items-center truncate">
                            <span class="material-icons text-xs mr-1 hidden xs:inline">schedule</span>
                            ${order.order_time}
                        </span>
                        <span class="text-gray-300 dark:text-gray-600 hidden sm:inline">•</span>
                        <span class="flex items-center truncate">
                            <span class="material-icons text-xs mr-1 hidden xs:inline">restaurant</span>
                            ${order.total_items} items
                        </span>
                        <span class="text-gray-300 dark:text-gray-600 hidden md:inline">•</span>
                        <span class="flex items-center truncate hidden md:flex">
                            <span class="material-icons text-xs mr-1">receipt</span>
                            ${order.booking_order_code}
                        </span>
                        <span class="text-gray-300 dark:text-gray-600 hidden lg:inline">•</span>
                        <span class="flex items-center truncate">
                            <span class="material-icons text-xs mr-1 hidden xs:inline">table_restaurant</span>
                            Table ${order.table_id || 'T'}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }

    static showEmptyState(container, type) {
        const states = {
            queue: `
                <div class="text-center text-text-secondary-light dark:text-text-secondary-dark py-8">
                    <div class="text-lg mb-2">🔭</div>
                    <div>No orders in queue</div>
                    <div class="text-sm mt-1">All orders have been processed</div>
                </div>
            `,
            active: `
                <div class="col-span-full text-center py-12">
                    <div class="text-4xl mb-4">👨‍🍳</div>
                    <div class="text-text-secondary-light dark:text-text-secondary-dark text-lg">No orders cooking</div>
                    <div class="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-2">Pick up orders from the queue to start cooking</div>
                </div>
            `,
            served: `
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-full mx-auto flex items-center justify-center">
                        <span class="material-icons text-xl text-gray-400 dark:text-gray-400">check</span>
                    </div>
                    <p class="font-semibold mt-3 text-text-light dark:text-text-dark text-sm">No served orders</p>
                    <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">No orders served on selected date</p>
                </div>
            `
        };

        container.innerHTML = states[type] || states.active;
    }

    static showErrorState(container, errorMessage) {
        container.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="text-4xl mb-4">❌</div>
                <div class="text-red-600 dark:text-red-400 text-lg">Error Loading Data</div>
                <div class="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-2">${errorMessage}</div>
                <button class="mt-4 bg-primary hover:bg-primary-hover text-white font-semibold py-2 px-4 rounded-md transition-colors" onclick="window.kitchenDashboard.loadAllData()">
                    Try Again
                </button>
            </div>
        `;
    }

    static showLoadingState(container, type) {
        const states = {
            queue: `
                <div class="text-center text-text-secondary-light dark:text-text-secondary-dark py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
                    <div>Loading queue orders...</div>
                </div>
            `,
            active: `
                <div class="col-span-full text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
                    <div class="text-text-secondary-light dark:text-text-secondary-dark text-lg">Loading active orders...</div>
                </div>
            `,
            served: `
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mx-auto mb-2"></div>
                    <p class="font-semibold mt-3 text-text-light dark:text-text-dark text-sm">Loading served orders...</p>
                    <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">Please wait</p>
                </div>
            `
        };

        container.innerHTML = states[type] || states.active;
    }
}

// Main Kitchen Dashboard
class KitchenDashboard {
    constructor() {
        this.api = new KitchenApiService();
        this.queueOrders = [];
        this.activeOrders = [];
        this.servedOrders = [];
        this.currentOrderId = null;
        this.confirmationOrderId = null;
        this.globalSearchTerm = '';
        this.filteredQueueOrders = [];
        this.filteredServedOrders = [];
        this.searchTimeout = null;
        this.currentDate = 'all';
        
        console.log('🪄 Kitchen Dashboard Instance Created');
        console.log('🔗 API Endpoints:', KITCHEN_CONFIG.endpoints);
    }

    async init() {
        try {
            console.log('🚀 Initializing Kitchen Dashboard...');
            this.showLoadingStates();
            await this.loadAllData();
            this.setupEventListeners();
            this.startAutoRefresh();
            console.log('✅ Kitchen Dashboard initialized successfully');
        } catch (error) {
            console.error('❌ Failed to initialize kitchen dashboard:', error);
            this.showNotification('Failed to initialize dashboard: ' + error.message, 'error');
        }
    }

    showLoadingStates() {
        const queueContainer = document.getElementById('orderQueue');
        const activeContainer = document.getElementById('activeOrders');
        const servedContainer = document.getElementById('servedOrders');
        
        if (queueContainer) KitchenUIRenderer.showLoadingState(queueContainer, 'queue');
        if (activeContainer) KitchenUIRenderer.showLoadingState(activeContainer, 'active');
        if (servedContainer) KitchenUIRenderer.showLoadingState(servedContainer, 'served');
    }

    async loadAllData() {
    console.log('🚀 [DEBUG] loadAllData called');
    try {
        console.log('📥 [DEBUG] Starting to load all data...');
        
        await this.loadOrderQueue();
        console.log('✅ [DEBUG] Queue data loaded');
        
        await this.loadActiveOrders();
        console.log('✅ [DEBUG] Active orders data loaded');
        
        await this.loadServedOrders();
        console.log('✅ [DEBUG] Served orders data loaded');
        
        // **FIX: Force update UI setelah semua data loaded**
        this.renderOrderQueue();
        this.renderActiveOrders();
        this.renderServedOrders();
        this.updateCounters();
        
        console.log('🎉 [DEBUG] All data loaded and rendered successfully');
        console.log('📊 Final counts - Queue:', this.queueOrders.length, 'Active:', this.activeOrders.length, 'Served:', this.servedOrders.length);
        
    } catch (error) {
        console.error('❌ [DEBUG] Error in loadAllData:', error);
        this.showNotification('Failed to load data: ' + error.message, 'error');
    }
}

    async loadOrderQueue() {
        try {
            console.log('📋 [DEBUG] loadOrderQueue called');
            const result = await this.api.getOrderQueue();
            
            console.log('📡 [DEBUG] Queue API Response:', result);
            
            if (result.success) {
                this.queueOrders = result.data.queue_orders || [];
                console.log(`✅ [DEBUG] Loaded ${this.queueOrders.length} queue orders:`, 
                    this.queueOrders.map(o => ({ 
                        id: o.id, 
                        customer: o.customer_name,
                        code: o.booking_order_code,
                        status: o.order_status 
                    }))
                );
                this.filterAllSections();
                this.renderOrderQueue();
                this.updateCounters();
            } else {
                console.error('❌ [DEBUG] Queue API failed:', result.message);
                throw new Error(result.message || 'Failed to load order queue');
            }
        } catch (error) {
            console.error('❌ [DEBUG] Error in loadOrderQueue:', error);
            const container = document.getElementById('orderQueue');
            if (container) {
                KitchenUIRenderer.showErrorState(container, error.message);
            }
        }
    }

    async loadActiveOrders() {
    try {
        console.log('🔥 [DEBUG] loadActiveOrders called');
        const result = await this.api.getActiveOrders();
        
        console.log('📡 [DEBUG] Active Orders API Response:', result);
        
        if (result.success) {
            this.activeOrders = result.data.active_orders || [];
            
            console.log(`✅ [DEBUG] Loaded ${this.activeOrders.length} active orders:`, 
                this.activeOrders.map(o => ({ 
                    id: o.id, 
                    customer: o.customer_name,
                    code: o.booking_order_code,
                    status: o.order_status,
                    order_details_count: o.order_details ? o.order_details.length : 0,
                    has_order_details: !!o.order_details && o.order_details.length > 0
                }))
            );
            
            // **FIX: Force render dan update counters**
            this.renderActiveOrders();
            this.updateCounters();
            
        } else {
            console.error('❌ [DEBUG] Active Orders API failed:', result.message);
            throw new Error(result.message || 'Failed to load active orders');
        }
    } catch (error) {
        console.error('❌ [DEBUG] Error in loadActiveOrders:', error);
        const container = document.getElementById('activeOrders');
        if (container) {
            KitchenUIRenderer.showErrorState(container, error.message);
        }
    }
}

    async loadServedOrders() {
        try {
            console.log('✅ Loading served orders...');
            const result = await this.api.getServedOrders(this.currentDate);
            
            if (result.success) {
                this.servedOrders = result.data.served_orders || [];
                console.log(`✅ Loaded ${this.servedOrders.length} served orders`);
                
                const dateFilterInfo = document.getElementById('dateFilterInfo');
                const displayDate = document.getElementById('displayDate');
                if (dateFilterInfo && displayDate) {
                    if (this.currentDate === 'all') {
                        dateFilterInfo.classList.add('hidden');
                    } else {
                        dateFilterInfo.classList.remove('hidden');
                        displayDate.textContent = this.currentDate;
                    }
                }
                
                this.filterAllSections();
                this.renderServedOrders();
                this.updateCounters();
            } else {
                throw new Error(result.message || 'Failed to load served orders');
            }
        } catch (error) {
            console.error('❌ Error loading served orders:', error);
            const container = document.getElementById('servedOrders');
            if (container) {
                KitchenUIRenderer.showErrorState(container, error.message);
            }
        }
    }

    setupEventListeners() {
        console.log('🔧 Setting up event listeners...');
        
        const globalSearchInput = document.getElementById('globalSearch');
        if (globalSearchInput) {
            globalSearchInput.addEventListener('input', (e) => {
                this.globalSearch(e.target.value);
            });
        }
        
        const clearGlobalSearch = document.getElementById('clearGlobalSearch');
        if (clearGlobalSearch) {
            clearGlobalSearch.addEventListener('click', () => {
                this.clearGlobalSearch();
            });
        }

        const datePicker = document.getElementById('datePicker');
        if (datePicker) {
            datePicker.value = new Date().toISOString().split('T')[0];
        }

        // Setup Mobile Sidebar
        this.setupMobileSidebar();

        // Event delegation untuk tombol yang dinamis
        document.addEventListener('click', (e) => {
            console.log('🎯 Click event detected on:', e.target);
            
            // Tombol details
            if (e.target.closest('.kitchen-details-btn')) {
                const orderId = e.target.closest('.kitchen-details-btn').dataset.orderId;
                const orderContainer = e.target.closest('.kitchen-queue-item, .kitchen-active-order');
                
                if (orderContainer && orderContainer.classList.contains('kitchen-queue-item')) {
                    console.log('📋 Tombol details di queue orders:', orderId);
                    this.showQueueOrderDetails(orderId);
                } else {
                    console.log('🔥 Tombol details di active orders:', orderId);
                    this.showOrderDetails(orderId);
                }
            }

            // Tombol start cooking
            if (e.target.id === 'startCookingBtn') {
                console.log('🎯 [DEBUG] Start Cooking button clicked!');
                console.log('📝 [DEBUG] Current orderId:', this.currentOrderId);
                console.log('🔍 [DEBUG] Event target:', e.target);
                
                if (this.currentOrderId) {
                    console.log('🚀 [DEBUG] Calling pickUpOrder with:', this.currentOrderId);
                    this.pickUpOrder(this.currentOrderId);
                } else {
                    console.error('❌ [DEBUG] currentOrderId is null/undefined!');
                }
            }

            // Tombol serve order
            const serveBtn = e.target.closest('#serveOrderBtn');
            if (serveBtn && !serveBtn.disabled) {
                console.log('✅ Serve order button clicked, orderId:', this.currentOrderId);
                if (this.currentOrderId) {
                    this.showServeConfirmation(this.currentOrderId);
                }
            }

            // Tombol confirm serve
            if (e.target.id === 'confirmServeBtn' || e.target.closest('#confirmServeBtn')) {
                console.log('✅ Confirm serve button clicked, confirmationOrderId:', this.confirmationOrderId);
                
                // **FIX: Validasi sebelum eksekusi**
                if (this.confirmationOrderId) {
                    const order = this.activeOrders.find(o => o.id == this.confirmationOrderId);
                    if (order) {
                        this.markAsServed(this.confirmationOrderId);
                    } else {
                        this.showNotification('Order tidak ditemukan di daftar aktif', 'error');
                        this.closeModal('serveConfirmationModal');
                        this.loadAllData(); // Refresh data
                    }
                } else {
                    this.showNotification('Order ID tidak valid', 'error');
                }
            }

            // Close modal ketika klik di luar
            if (e.target.classList.contains('fixed') && e.target.id.includes('Modal')) {
                this.closeModal(e.target.id);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
            if (e.key === 'F5') {
                e.preventDefault();
                this.loadAllData();
            }
        });

        console.log('✅ Event listeners setup completed');
    }

    setupMobileSidebar() {
        console.log('📱 Setting up mobile sidebar...');
        
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeMobileSidebar = document.getElementById('closeMobileSidebar');
        const queueTabBtn = document.getElementById('queueTabBtn');
        const servedTabBtn = document.getElementById('servedTabBtn');
        const queueTabContent = document.getElementById('queueTabContent');
        const servedTabContent = document.getElementById('servedTabContent');

        // Open mobile sidebar
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', () => {
                console.log('📱 Opening mobile sidebar');
                if (mobileSidebar) mobileSidebar.classList.remove('translate-x-full');
                if (mobileSidebarOverlay) mobileSidebarOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        }

        // Close mobile sidebar
        const closeSidebar = () => {
            console.log('📱 Closing mobile sidebar');
            if (mobileSidebar) mobileSidebar.classList.add('translate-x-full');
            if (mobileSidebarOverlay) mobileSidebarOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        };

        if (closeMobileSidebar) {
            closeMobileSidebar.addEventListener('click', closeSidebar);
        }

        if (mobileSidebarOverlay) {
            mobileSidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Tab switching
        if (queueTabBtn && servedTabBtn && queueTabContent && servedTabContent) {
            queueTabBtn.addEventListener('click', () => {
                console.log('📋 Switching to queue tab');
                queueTabBtn.classList.add('text-primary', 'border-primary');
                queueTabBtn.classList.remove('text-text-secondary-light', 'dark:text-text-secondary-dark', 'border-transparent');
                servedTabBtn.classList.remove('text-primary', 'border-primary');
                servedTabBtn.classList.add('text-text-secondary-light', 'dark:text-text-secondary-dark', 'border-transparent');
                queueTabContent.classList.remove('hidden');
                servedTabContent.classList.add('hidden');
            });

            servedTabBtn.addEventListener('click', () => {
                console.log('✅ Switching to served tab');
                servedTabBtn.classList.add('text-primary', 'border-primary');
                servedTabBtn.classList.remove('text-text-secondary-light', 'dark:text-text-secondary-dark', 'border-transparent');
                queueTabBtn.classList.remove('text-primary', 'border-primary');
                queueTabBtn.classList.add('text-text-secondary-light', 'dark:text-text-secondary-dark', 'border-transparent');
                servedTabContent.classList.remove('hidden');
                queueTabContent.classList.add('hidden');
            });
        }

        // Mobile search functionality
        const mobileQueueSearch = document.getElementById('mobileQueueSearch');
        if (mobileQueueSearch) {
            mobileQueueSearch.addEventListener('input', (e) => {
                this.globalSearch(e.target.value);
            });
        }

        // Mobile date picker
        const mobileDatePicker = document.getElementById('mobileDatePicker');
        if (mobileDatePicker) {
            mobileDatePicker.value = new Date().toISOString().split('T')[0];
        }

        console.log('✅ Mobile sidebar setup completed');
    }

    startAutoRefresh() {
        setInterval(() => {
            console.log('🔄 Auto-refreshing data...');
            this.loadAllData();
        }, KITCHEN_CONFIG.refreshInterval);
    }

    showOrderDetails(orderId) {
        console.log('🔥 Showing active order details:', orderId);
        const order = this.activeOrders.find(o => o.id == orderId);
        if (!order) {
            this.showNotification('Order not found', 'error');
            return;
        }

        this.currentOrderId = orderId;
        console.log('📝 Set currentOrderId to:', this.currentOrderId);
        
        const modalTitle = document.getElementById('modalOrderTitle');
        const customerName = document.getElementById('modalCustomerName');
        const orderTime = document.getElementById('modalOrderTime');
        const tableNumber = document.getElementById('modalTableNumber');
        
        if (modalTitle) modalTitle.textContent = `Order #${order.booking_order_code}`;
        if (customerName) customerName.textContent = order.customer_name;
        if (orderTime) orderTime.textContent = `Ordered at ${order.order_time}`;
        if (tableNumber) tableNumber.textContent = `Table ${order.table_id || 'T'}`;
        
        this.populateOrderItems(order);
        this.setupCheckboxFunctionality();
        this.openModal('orderDetailsModal');
    }

    showQueueOrderDetails(orderId) {
        console.log('📋 Showing queue order details:', orderId);
        const order = this.queueOrders.find(o => o.id == orderId);
        if (!order) {
            this.showNotification('Order not found', 'error');
            return;
        }

        this.currentOrderId = orderId;
        console.log('📝 Set currentOrderId to:', this.currentOrderId);
        
        const modalTitle = document.getElementById('queueModalTitle');
        const customerName = document.getElementById('queueModalCustomerName');
        const orderTime = document.getElementById('queueModalOrderTime');
        const tableNumber = document.getElementById('queueModalTableNumber');
        
        if (modalTitle) modalTitle.textContent = `Order #${order.booking_order_code}`;
        if (customerName) customerName.textContent = order.customer_name;
        if (orderTime) orderTime.textContent = `Ordered at ${order.order_time}`;
        if (tableNumber) tableNumber.textContent = `Table ${order.table_id || 'T'}`;

        this.populateQueueOrderItems(order);
        this.openModal('queueOrderModal');
    }

    showServeConfirmation(orderId) {
        console.log('📢 [SERVE FIXED] Showing serve confirmation for order:', orderId);
        
        // **FIX: Validasi order masih ada di active orders**
        const order = this.activeOrders.find(o => o.id == orderId);
        if (!order) {
            this.showNotification('Order tidak ditemukan di daftar aktif. Memuat ulang data...', 'warning');
            this.loadAllData(); // Refresh data
            return;
        }
        
        // **FIX: Validasi order status**
        if (order.order_status !== 'PROCESSED') {
            this.showNotification(`Order status: ${order.order_status}. Tidak bisa ditandai sebagai served.`, 'error');
            return;
        }

        this.confirmationOrderId = orderId;
        console.log('📝 Set confirmationOrderId to:', this.confirmationOrderId);

        const customerName = document.getElementById('confirmCustomerName');
        const tableNumber = document.getElementById('confirmTableNumber');
        const orderCode = document.getElementById('confirmOrderCode');
        const totalItems = document.getElementById('confirmTotalItems');

        if (customerName) customerName.textContent = order.customer_name;
        if (tableNumber) tableNumber.textContent = `Table ${order.table_id || 'T'}`;
        if (orderCode) orderCode.textContent = order.booking_order_code;
        if (totalItems) totalItems.textContent = `${order.total_items} items`;

        this.openModal('serveConfirmationModal');
    }

    populateOrderItems(order) {
        const container = document.getElementById('modalOrderItems');
        const specialNotesContainer = document.getElementById('modalSpecialNotes');

        if (specialNotesContainer) {
            specialNotesContainer.innerHTML = '';
            specialNotesContainer.classList.add('hidden');
        }

        let itemsHTML = '';
        
        console.log('📦 Populating order items for order:', order.id);
        console.log('📋 Order details:', order.order_details);
        
        order.order_details.forEach((detail, index) => {
            const optionsHTML = detail.options && detail.options.length > 0 
                ? detail.options.map(opt => 
                    `<div class="text-xs text-text-secondary-light dark:text-text-secondary-dark ml-4">• ${opt.name} </div>`
                  ).join('')
                : '';

            const noteHTML = detail.customer_note 
                ? `<div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 ml-4 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded">📝 <span class="font-medium">Catatan:</span> ${detail.customer_note}</div>`
                : '';

            itemsHTML += `
                <div class="flex items-start gap-3 p-3 bg-background-light dark:bg-background-dark rounded-lg">
                    <input type="checkbox" class="item-checkbox mt-1" data-item-id="${detail.id}" id="item-${detail.id}">
                    <div class="flex-1">
                        <label for="item-${detail.id}" class="cursor-pointer block">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-medium text-text-light dark:text-text-dark mb-1">${detail.product_name} × ${detail.quantity}</p>
                                    ${optionsHTML}
                                    ${noteHTML}
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            `;
        });

        container.innerHTML = itemsHTML;
        console.log('✅ Order items populated, total items:', order.order_details.length);
    }

    populateQueueOrderItems(order) {
        const container = document.getElementById('queueModalItems');
        if (!container) return;

        let itemsHTML = '';

        order.order_details.forEach((detail) => {
            const optionsHTML = detail.options && detail.options.length > 0 
                ? detail.options.map(opt => 
                    `<div class="text-xs text-text-secondary-light dark:text-text-secondary-dark ml-4">• ${opt.name}</div>`
                  ).join('')
                : '';

            const noteHTML = detail.customer_note 
                ? `<div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 ml-4 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded">📝 <span class="font-medium">Catatan:</span> ${detail.customer_note}</div>`
                : '';

            itemsHTML += `
                <div class="p-3 bg-background-light dark:bg-background-dark rounded-lg mb-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-text-light dark:text-text-dark">${detail.product_name} × ${detail.quantity}</p>
                            ${optionsHTML}
                            ${noteHTML}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = itemsHTML;
    }

    setupCheckboxFunctionality() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const serveBtn = document.getElementById('serveOrderBtn');
        const checkAllBtn = document.getElementById('checkAllBtn');
        const progressText = document.getElementById('progressText');

        console.log('🔧 Setting up checkbox functionality...');
        console.log('📊 Total checkboxes:', checkboxes.length);
        console.log('🔘 Serve button found:', !!serveBtn);

        const updateButtonStates = () => {
            const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            const totalCount = checkboxes.length;
            const allChecked = checkedCount === totalCount;
            
            console.log('✅ Checked items:', checkedCount, '/', totalCount, 'All checked:', allChecked);
            
            if (serveBtn) {
                serveBtn.disabled = !allChecked;
                console.log('🔘 Serve button disabled:', serveBtn.disabled);
                
                if (allChecked) {
                    serveBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                    serveBtn.classList.add('cursor-pointer', 'hover:bg-primary-hover');
                } else {
                    serveBtn.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                    serveBtn.classList.remove('cursor-pointer', 'hover:bg-primary-hover');
                }
            }
            
            if (progressText) {
                progressText.textContent = `${checkedCount}/${totalCount} items ready`;
                
                if (allChecked) {
                    progressText.className = 'text-xs text-green-600 dark:text-green-400 font-semibold';
                } else if (checkedCount > 0) {
                    progressText.className = 'text-xs text-yellow-600 dark:text-yellow-400 font-semibold';
                } else {
                    progressText.className = 'text-xs text-text-secondary-light dark:text-text-secondary-dark';
                }
            }
        };

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateButtonStates);
            console.log('📝 Added event listener to checkbox:', checkbox.dataset.itemId);
        });

        if (checkAllBtn) {
            checkAllBtn.addEventListener('click', () => {
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                const newState = !allChecked;
                
                console.log('🔘 Check all clicked. New state:', newState);
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = newState;
                });
                
                checkboxes.forEach(checkbox => {
                    checkbox.dispatchEvent(new Event('change'));
                });
                
                updateButtonStates();
            });
        }

        updateButtonStates();
    }

    globalSearch(searchTerm) {
        this.globalSearchTerm = searchTerm.toLowerCase().trim();
        const clearButton = document.getElementById('clearGlobalSearch');
        
        if (clearButton) {
            clearButton.classList.toggle('hidden', !this.globalSearchTerm);
        }
        
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.filterAllSections();
            this.renderAllSections();
        }, 300);
    }

    clearGlobalSearch() {
        const searchInput = document.getElementById('globalSearch');
        const clearButton = document.getElementById('clearGlobalSearch');
        
        if (searchInput) searchInput.value = '';
        if (clearButton) clearButton.classList.add('hidden');
        
        this.globalSearchTerm = '';
        this.filterAllSections();
        this.renderAllSections();
    }

    filterAllSections() {
        if (!this.globalSearchTerm) {
            this.filteredQueueOrders = [...this.queueOrders];
            this.filteredServedOrders = [...this.servedOrders];
            return;
        }

        this.filteredQueueOrders = this.queueOrders.filter(order => {
            const searchableText = [
                order.customer_name?.toLowerCase() || '',
                order.booking_order_code?.toLowerCase() || '',
                `table ${order.table_id}`.toLowerCase(),
                order.product_names?.toLowerCase() || ''
            ].join(' ');

            return searchableText.includes(this.globalSearchTerm);
        });

        this.filteredServedOrders = this.servedOrders.filter(order => {
            const searchableText = [
                order.customer_name?.toLowerCase() || '',
                order.booking_order_code?.toLowerCase() || '',
                `table ${order.table_id}`.toLowerCase(),
                order.product_names?.toLowerCase() || ''
            ].join(' ');

            return searchableText.includes(this.globalSearchTerm);
        });
    }

    renderOrderQueue() {
        const container = document.getElementById('orderQueue');
        const mobileContainer = document.getElementById('mobileOrderQueue');
        
        if (!container && !mobileContainer) return;
        
        const ordersToRender = this.globalSearchTerm ? this.filteredQueueOrders : this.queueOrders;
        
        const html = ordersToRender.length === 0 
            ? this.getEmptyStateHTML('queue')
            : ordersToRender.map(order => KitchenUIRenderer.createQueueItem(order)).join('');
        
        if (container) container.innerHTML = html;
        if (mobileContainer) mobileContainer.innerHTML = html;
        
        const queueTabCount = document.getElementById('queueTabCount');
        if (queueTabCount) {
            queueTabCount.textContent = `(${ordersToRender.length})`;
        }
    }

    getEmptyStateHTML(type) {
        const states = {
            queue: `
                <div class="text-center text-text-secondary-light dark:text-text-secondary-dark py-8">
                    <div class="text-lg mb-2">🔭</div>
                    <div>No orders in queue</div>
                    <div class="text-sm mt-1">All orders have been processed</div>
                </div>
            `,
            served: `
                <div class="flex flex-col items-center justify-center text-center py-8">
                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-full mx-auto flex items-center justify-center">
                        <span class="material-icons text-xl text-gray-400 dark:text-gray-400">check</span>
                    </div>
                    <p class="font-semibold mt-3 text-text-light dark:text-text-dark text-sm">No served orders</p>
                    <p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">No orders served on selected date</p>
                </div>
            `
        };
        return states[type] || states.queue;
    }

    renderActiveOrders() {
    const container = document.getElementById('activeOrders');
    if (!container) {
        console.error('❌ [RENDER DEBUG] Active orders container not found!');
        return;
    }
    
    console.log('🎨 [RENDER DEBUG] Rendering active orders:', this.activeOrders.length);
    
    if (this.activeOrders.length === 0) {
        console.log('🎨 [RENDER DEBUG] No active orders, showing empty state');
        KitchenUIRenderer.showEmptyState(container, 'active');
        return;
    }

    // **FIX: Pastikan data order_details ada**
    const ordersWithDetails = this.activeOrders.map(order => {
        console.log('📦 [RENDER DEBUG] Order details for:', order.customer_name, order.order_details);
        return {
            ...order,
            order_details: order.order_details || [] // Pastikan tidak undefined
        };
    });

    const html = ordersWithDetails.map(order => 
        KitchenUIRenderer.createActiveOrderCard(order)
    ).join('');
    
    console.log('🎨 [RENDER DEBUG] Generated HTML length:', html.length);
    container.innerHTML = html;
    
    // **FIX: Log untuk debugging render**
    const renderedCards = container.querySelectorAll('.kitchen-active-order');
    console.log('🎨 [RENDER DEBUG] Rendered cards count:', renderedCards.length);
    
    renderedCards.forEach((card, index) => {
        const orderId = card.dataset.orderId;
        console.log(`🎨 [RENDER DEBUG] Card ${index + 1}:`, { 
            orderId, 
            hasDetails: card.innerHTML.length > 0 
        });
    });
}

    renderServedOrders() {
        const container = document.getElementById('servedOrders');
        const mobileContainer = document.getElementById('mobileServedOrders');
        
        if (!container && !mobileContainer) return;
        
        const ordersToRender = this.globalSearchTerm ? this.filteredServedOrders : this.servedOrders;
        
        const html = ordersToRender.length === 0
            ? this.getEmptyStateHTML('served')
            : ordersToRender.map(order => KitchenUIRenderer.createServedOrderItem(order, false)).join('');
        
        if (container) container.innerHTML = html;
        if (mobileContainer) mobileContainer.innerHTML = html;
        
        const servedTabCount = document.getElementById('servedTabCount');
        if (servedTabCount) {
            servedTabCount.textContent = `(${ordersToRender.length})`;
        }
    }

    renderAllSections() {
        this.renderOrderQueue();
        this.renderServedOrders();
        this.updateCounters();
    }

    updateCounters() {
    const waitingCount = this.queueOrders.length;
    const cookingCount = this.activeOrders.length;
    const servedCount = this.servedOrders.length;
    
    const waitingElement = document.getElementById('waitingCount');
    const cookingElement = document.getElementById('cookingCount');
    const completedElement = document.getElementById('completedCount');
    const activeOrdersCountElement = document.getElementById('activeOrdersCount');
    const queueCountElement = document.getElementById('queueOrdersCount');
    const servedCountElement = document.getElementById('servedOrdersCount');
    
    if (waitingElement) waitingElement.textContent = waitingCount;
    if (cookingElement) cookingElement.textContent = cookingCount;
    if (completedElement) completedElement.textContent = servedCount;
    if (activeOrdersCountElement) activeOrdersCountElement.textContent = `${cookingCount} orders cooking`;
    
    if (queueCountElement) {
        const totalQueue = this.queueOrders.length;
        const filteredQueue = this.filteredQueueOrders.length;
        if (this.globalSearchTerm) {
            queueCountElement.textContent = `${filteredQueue} of ${totalQueue} orders waiting`;
        } else {
            queueCountElement.textContent = `${totalQueue} orders waiting`;
        }
    }

    if (servedCountElement) {
        const totalServed = this.servedOrders.length;
        const filteredServed = this.filteredServedOrders.length;
        if (this.globalSearchTerm) {
            servedCountElement.textContent = `${filteredServed} of ${totalServed} orders`;
        } else {
            servedCountElement.textContent = `${totalServed} orders`;
        }
    }
    
    // Update badge on floating button
    this.updatePendingOrdersBadge();
}
updatePendingOrdersBadge() {
    const pendingCount = this.queueOrders.length;
    const badge = document.getElementById('pendingOrdersBadge');
    
    if (badge) {
        if (pendingCount > 0) {
            badge.textContent = pendingCount > 99 ? '99+' : pendingCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

    async pickUpOrder(orderId) {
    try {
        console.log('🔍 [DEBUG] pickUpOrder called with orderId:', orderId);
        console.log('📋 [DEBUG] Current queue orders:', this.queueOrders.map(o => ({ 
            id: o.id, 
            customer: o.customer_name,
            code: o.booking_order_code 
        })));

        console.log('👨‍🍳 Picking up order:', orderId);
        this.showNotification('Processing order...', 'info');
        
        const result = await this.api.pickUpOrder(orderId);
        
        console.log('📡 [DEBUG] Pickup API Response:', result);
        console.log('✅ [DEBUG] Response success:', result.success);
        console.log('📝 [DEBUG] Response message:', result.message);
        
        if (result.success) {
            console.log('🔄 [DEBUG] Before updating queues - Queue count:', this.queueOrders.length);
            console.log('🔄 [DEBUG] Before updating queues - Active count:', this.activeOrders.length);

            // **FIX: Hapus dari queue orders secara langsung untuk UX yang lebih baik**
            this.queueOrders = this.queueOrders.filter(order => order.id != orderId);
            this.filteredQueueOrders = this.filteredQueueOrders.filter(order => order.id != orderId);
            
            // **FIX: Update UI queue segera**
            this.renderOrderQueue();
            this.updateCounters();
            
            this.showNotification('Order moved to active orders! 🎯 Now Cooking', 'success');
            this.closeModal('queueOrderModal');
            
            // **FIX: Refresh active orders secara agresif**
            console.log('🔄 [DEBUG] Immediately refreshing active orders...');
            await this.loadActiveOrders();
            await this.loadOrderQueue(); // Refresh queue juga untuk konsistensi
            
            // **FIX: Force render ulang active orders**
            this.renderActiveOrders();
            this.updateCounters();
            
            console.log('✅ [DEBUG] After refresh - Active count:', this.activeOrders.length);
            console.log('✅ [DEBUG] After refresh - Queue count:', this.queueOrders.length);

        } else {
            console.error('❌ [DEBUG] Pickup failed with message:', result.message);
            this.showNotification(result.message || 'Failed to pick up order', 'error');
        }
    } catch (error) {
        console.error('❌ [DEBUG] Error in pickUpOrder:', error);
        console.error('❌ [DEBUG] Error stack:', error.stack);
        this.showNotification('Error picking up order: ' + error.message, 'error');
    }
}

    async markAsServed(orderId) {
        try {
            console.log('✅ [SERVE FIXED] Marking order as served:', orderId);
            
            // **FIX: Validasi orderId**
            if (!orderId) {
                this.showNotification('Order ID tidak valid', 'error');
                return;
            }

            // **FIX: Cari order di active orders untuk memastikan masih ada**
            const order = this.activeOrders.find(o => o.id == orderId);
            if (!order) {
                this.showNotification('Order tidak ditemukan di daftar aktif. Memuat ulang data...', 'warning');
                this.closeModal('serveConfirmationModal');
                // Refresh data karena mungkin sudah berubah
                await this.loadAllData();
                return;
            }

            console.log('📦 [SERVE FIXED] Order details before serve:', {
                orderId: orderId,
                orderCode: order.booking_order_code,
                orderStatus: order.order_status,
                inActiveOrders: !!order
            });

            this.closeModal('serveConfirmationModal');
            
            // **FIX: Show loading state**
            this.showNotification('Menandai order sebagai served...', 'info');
            
            // **FIX: Disable button untuk prevent multiple clicks**
            const serveBtn = document.getElementById('confirmServeBtn');
            if (serveBtn) {
                serveBtn.disabled = true;
                serveBtn.innerHTML = '<span class="material-icons mr-2 text-sm">hourglass_empty</span>Processing...';
            }

            const result = await this.api.markAsServed(orderId);
            
            if (result.success) {
                console.log('🎉 [SERVE FIXED] Order successfully marked as served:', result.data);
                
                // **FIX: Hapus dari active orders secara langsung**
                this.activeOrders = this.activeOrders.filter(order => order.id != orderId);
                
                // **FIX: Update UI segera**
                this.renderActiveOrders();
                this.updateCounters();
                
                this.showNotification('Order berhasil ditandai sebagai served! ✅', 'success');
                
                // **FIX: Refresh data dari server untuk konsistensi**
                setTimeout(async () => {
                    await this.loadServedOrders();
                    await this.loadActiveOrders();
                    this.updateCounters();
                }, 500);
                
                this.closeModal('orderDetailsModal');
                
            } else {
                // **FIX: Handle specific error messages**
                const errorMessage = result.message || 'Gagal menandai order sebagai served';
                console.error('❌ [SERVE FIXED] Serve failed:', errorMessage);
                
                // **FIX: Berikan pesan yang lebih spesifik**
                if (errorMessage.includes('tidak dalam status PROCESSED')) {
                    this.showNotification('Order status sudah berubah. Memuat ulang data...', 'warning');
                } else if (errorMessage.includes('tidak ditemukan')) {
                    this.showNotification('Order tidak ditemukan. Memuat ulang data...', 'warning');
                } else {
                    this.showNotification(errorMessage, 'error');
                }
                
                // **FIX: Refresh data karena ada ketidaksesuaian**
                await this.loadAllData();
            }
            
        } catch (error) {
            console.error('❌ [SERVE FIXED] Error marking order as served:', error);
            
            // **FIX: Handle different error types**
            let userMessage = 'Error menandai order sebagai served: ';
            
            if (error.message.includes('404')) {
                userMessage = 'Order tidak ditemukan. Memuat ulang data...';
                // Refresh data
                await this.loadAllData();
            } else if (error.message.includes('500')) {
                userMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
            } else if (error.message.includes('403')) {
                userMessage = 'Order sedang diproses oleh koki lain.';
            } else {
                userMessage += error.message;
            }
            
            this.showNotification(userMessage, 'error');
            
        } finally {
            // **FIX: Reset button state**
            const serveBtn = document.getElementById('confirmServeBtn');
            if (serveBtn) {
                serveBtn.disabled = false;
                serveBtn.innerHTML = '<span class="material-icons mr-2 text-sm">check_circle</span>Confirm Serve';
            }
            
            // **FIX: Reset confirmation state**
            this.confirmationOrderId = null;
            this.currentOrderId = null;
        }
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('modal-open');
            modal.setAttribute('aria-hidden', 'false');
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('modal-open');
            
            if (modalId === 'orderDetailsModal' || modalId === 'queueOrderModal') {
                this.currentOrderId = null;
            }
            
            if (modalId === 'serveConfirmationModal') {
                this.confirmationOrderId = null;
            }
            
            modal.setAttribute('aria-hidden', 'true');
        }
    }

    closeAllModals() {
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                this.closeModal(modal.id);
            }
        });
    }

    showNotification(message, type = 'info') {
        const notificationContainer = document.getElementById('notificationContainer');
        if (!notificationContainer) return;

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg text-white z-50 transition-all duration-300 transform translate-x-0 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        notification.textContent = message;

        notificationContainer.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('translate-x-0');
        }, 10);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    applyDateFilter(selectedDate) {
        console.log('📅 Applying date filter:', selectedDate);
        this.currentDate = selectedDate || 'all';
        this.loadServedOrders();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Initializing Kitchen Dashboard');
    window.kitchenDashboard = new KitchenDashboard();
    window.kitchenDashboard.init();
});

// Global modal functions
function openModal(modalId) {
    if (window.kitchenDashboard) {
        window.kitchenDashboard.openModal(modalId);
    }
}

function closeModal(modalId) {
    if (window.kitchenDashboard) {
        window.kitchenDashboard.closeModal(modalId);
    }
}

// Global refresh function
function refreshKitchenDashboard() {
    if (window.kitchenDashboard) {
        window.kitchenDashboard.loadAllData();
    }
}
</script>


<style>
/* Sidebar Fixed Width */
.w-120 {
    width: 28rem;
    min-width: 28rem;
    max-width: 28rem;
    flex-shrink: 0;
}

/* Ensure full height utilization */
.h-full {
    height: 100%;
}

.h-1\/2 {
    height: 50%;
}

.min-h-0 {
    min-height: 0;
}

.flex-1 {
    flex: 1 1 0%;
}

/* Enhanced Font Styling */
.font-display {
    font-family: 'Inter', system-ui, sans-serif;
    font-feature-settings: 'kern' 1, 'liga' 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.typography-enhanced {
    font-family: 'Inter', system-ui, sans-serif;
    letter-spacing: -0.01em;
    line-height: 1.5;
}

.typography-heading {
    font-family: 'Inter', system-ui, sans-serif;
    font-weight: 600;
    letter-spacing: -0.02em;
    line-height: 1.3;
}

/* Enhanced Card Shadows */
.card-elegant {
    box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08),
                0 2px 6px -1px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.03);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dark .card-elegant {
    box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.25),
                0 2px 6px -1px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.card-elegant:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.12),
                0 6px 15px -3px rgba(0, 0, 0, 0.08);
}

.dark .card-elegant:hover {
    box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.35),
                0 6px 15px -3px rgba(0, 0, 0, 0.25);
}

/* Modal Shadows */
.modal-enhanced {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(0, 0, 0, 0.05);
}

.dark .modal-enhanced {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.08);
}

/* Scroll containers */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 8px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 8px;
    border: 1px solid #f8fafc;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
    background: #1e293b;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
    border: 1px solid #1e293b;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

.modal-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.modal-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.modal-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 8px;
}

.dark .modal-scrollbar::-webkit-scrollbar-thumb {
    background: #475569;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.modal-open {
    overflow: hidden;
}

/* Animation for notifications */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Ensure text doesn't break layout */
.kitchen-active-order {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.kitchen-active-order .truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Prevent layout shift */
.flex-shrink-0 {
    flex-shrink: 0;
}

.overflow-hidden {
    overflow: hidden;
}

/* Checkbox Styling */
.item-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid #d1d5db;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    -webkit-appearance: none;
    position: relative;
}

.item-checkbox:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.item-checkbox:checked::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 12px;
    font-weight: bold;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.dark .item-checkbox {
    border-color: #4b5563;
    background: #374151;
}

.dark .item-checkbox:checked {
    background-color: #10b981;
    border-color: #10b981;
}

/* Serve Button Styling */
#serveOrderBtn {
    transition: all 0.3s ease;
}

#serveOrderBtn:not(:disabled) {
    cursor: pointer;
    background-color: #b91c1c !important;
}

#serveOrderBtn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(185, 28, 28, 0.4);
    background-color: #dc2626 !important;
}

#serveOrderBtn:disabled {
    background-color: #9ca3af !important;
    cursor: not-allowed !important;
    transform: none !important;
    box-shadow: none !important;
    opacity: 0.6;
}

/* Label styling for checkbox */
label[for^="item-"] {
    cursor: pointer;
    user-select: none;
    width: 100%;
}

/* Strikethrough effect for checked items */
.item-checkbox:checked + div label .font-medium {
    text-decoration: line-through;
    color: #10b981 !important;
}

.item-checkbox:checked + div label .text-text-secondary-light,
.item-checkbox:checked + div label .text-text-secondary-dark {
    text-decoration: line-through;
    color: #10b981 !important;
}

/* Confirmation Modal Animations */
.confirm-modal-enter {
    transform: scale(0.9);
    opacity: 0;
}

.confirm-modal-enter-active {
    transform: scale(1);
    opacity: 1;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.confirm-modal-exit {
    transform: scale(1);
    opacity: 1;
}

.confirm-modal-exit-active {
    transform: scale(0.9);
    opacity: 0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Progress indicator styling */
#progressIndicator {
    font-weight: 500;
}

/* Check all button styling */
#checkAllBtn {
    transition: color 0.2s ease;
}

#checkAllBtn:hover {
    color: #dc2626;
}

/* Modal items styling */
#modalOrderItems {
    max-height: 300px;
    overflow-y: auto;
}

/* Confirm serve button styling */
#confirmServeBtn {
    transition: all 0.3s ease;
}

#confirmServeBtn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px -3px rgba(185, 28, 28, 0.4);
}

/* Line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Loading states */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Responsive design untuk modal */
@media (max-width: 640px) {
    .modal-enhanced {
        margin: 1rem;
        width: calc(100% - 2rem);
    }
    
    #modalOrderItems {
        max-height: 250px;
    }
}

/* Focus states */
button:focus-visible,
input:focus-visible {
    outline: 2px solid #b91c1c;
    outline-offset: 2px;
}

/* Note indicators styling */
.bg-blue-50 {
    background-color: #eff6ff;
}

.dark .bg-blue-50 {
    background-color: rgba(59, 130, 246, 0.1);
}

/* Warning confirmation styling */
.bg-yellow-50 {
    background-color: #fefce8;
}

.dark .bg-yellow-50 {
    background-color: rgba(234, 179, 8, 0.1);
}
</style>