<!-- Order Details Modal for Active Orders -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden" id="orderDetailsModal">
    <div class="bg-white dark:bg-gray-800 rounded-2xl modal-enhanced p-6 w-full max-w-md mx-4 border-0">
        <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-600 pb-3 mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white typography-heading" id="modalOrderTitle">Order Details</h2>
            <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" onclick="kitchenDashboard.closeModal('orderDetailsModal')">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <!-- Customer & Time Info -->
        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white typography-enhanced" id="modalCustomerName"></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 typography-enhanced" id="modalOrderTime"></p>
                </div>
                <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full" id="modalTableNumber">
                    Table T
                </span>
            </div>
        </div>
        
        <!-- Special Notes Warning Container -->
        <div id="modalSpecialNotes" class="hidden"></div>
        
        <!-- Progress Indicator -->
        <div class="flex items-center justify-between mb-4 text-xs text-gray-600 dark:text-gray-400" id="progressIndicator">
            <span>Progress:</span>
            <span id="progressText">0/0 items ready</span>
        </div>
        
        <!-- Check All Button -->
        <div class="flex justify-between items-center mb-4">
            <button class="text-sm font-semibold text-primary hover:text-primary-hover flex items-center transition-colors" id="checkAllBtn">
                <span class="material-icons text-sm mr-1">checklist</span>
                Check All Items
            </button>
        </div>
        
        <!-- Order Items Section -->
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center typography-heading">
                <span class="material-icons text-primary mr-2 text-sm">restaurant_menu</span>
                Order Items
            </h3>
            <div class="space-y-3 max-h-80 overflow-y-auto pr-2 modal-scrollbar" id="modalOrderItems"></div>
        </div>
        
        
        <button class="w-full bg-primary hover:bg-primary-hover text-white py-3 rounded-xl font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center" id="serveOrderBtn" disabled>
            <span class="material-icons mr-2 text-sm">check_circle</span>
            Mark as Served
        </button>
    </div>
</div>

<!-- Order Details Modal for Queue Orders -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden" id="queueOrderModal">
    <div class="bg-white dark:bg-gray-800 rounded-2xl modal-enhanced p-6 w-full max-w-md mx-4 border-0">
        <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-600 pb-3 mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white typography-heading" id="queueModalTitle">Order Details</h2>
            <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" onclick="kitchenDashboard.closeModal('queueOrderModal')">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <!-- Customer & Time Info -->
        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white typography-enhanced" id="queueModalCustomerName"></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 typography-enhanced" id="queueModalOrderTime"></p>
                </div>
                <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full" id="queueModalTableNumber">
                    Table T
                </span>
            </div>
        </div>
        
        <!-- Order Items Section -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center typography-heading">
                <span class="material-icons text-primary mr-2 text-sm">restaurant_menu</span>
                Order Items
            </h3>
            <div class="space-y-3 max-h-80 overflow-y-auto pr-2 modal-scrollbar" id="queueModalItems"></div>
        </div>
        
        <button class="w-full bg-primary hover:bg-primary-hover text-white py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center" id="startCookingBtn">
            Start Cooking
        </button>
    </div>
</div>

<!-- TAMBAHKAN MODAL KONFIRMASI SERVE ORDER -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden" id="serveConfirmationModal">
    <div class="bg-white dark:bg-gray-800 rounded-2xl modal-enhanced p-6 w-full max-w-md mx-4 border-0">
        <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-600 pb-3 mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white typography-heading">Confirm Serve Order</h2>
            <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" onclick="kitchenDashboard.closeModal('serveConfirmationModal')">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <!-- Order Info -->
        <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center mb-2">
                <span class="material-icons text-yellow-600 dark:text-yellow-400 mr-2">warning</span>
                <p class="font-semibold text-yellow-800 dark:text-yellow-200">Confirm Order Serving</p>
            </div>
            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                Are you sure you want to mark this order as served?
            </p>
        </div>
        
        <!-- Order Details -->
        <div class="mb-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-text-secondary-light dark:text-text-secondary-dark">Customer:</span>
                <span class="font-semibold text-text-light dark:text-text-dark" id="confirmCustomerName"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary-light dark:text-text-secondary-dark">Table:</span>
                <span class="font-semibold text-text-light dark:text-text-dark" id="confirmTableNumber"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary-light dark:text-text-secondary-dark">Order Code:</span>
                <span class="font-semibold text-text-light dark:text-text-dark" id="confirmOrderCode"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary-light dark:text-text-secondary-dark">Total Items:</span>
                <span class="font-semibold text-text-light dark:text-text-dark" id="confirmTotalItems"></span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <button 
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg"
                onclick="kitchenDashboard.closeModal('serveConfirmationModal')"
            >
                Cancel
            </button>
            <button 
                id="confirmServeBtn"
                class="flex-1 bg-primary hover:bg-primary-hover text-white py-3 rounded-xl font-semibold transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center"
            >
                <span class="material-icons mr-2 text-sm">check_circle</span>
                Confirm Serve
            </button>
        </div>
    </div>
</div>