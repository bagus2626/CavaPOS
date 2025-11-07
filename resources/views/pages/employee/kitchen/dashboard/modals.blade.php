<!-- Order Details Modal (Queue Orders) -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden" id="queueOrderModal">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white typography-heading">Queue Order Details</h2>
                <button
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    onclick="kitchenDashboard.closeModal('queueOrderModal')">
                    <span class="material-icons text-xl">close</span>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col">
            <!-- Order Code Badge -->
            <div class="text-center mb-4">
                <div class="order-code-badge-modal text-red-600 font-black text-3xl leading-none mx-auto"
                    id="queueModalOrderCode"></div>
                <!-- TAMBAHAN: Tanggal Order -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="queueModalOrderDate">
                    <!-- Tanggal akan diisi oleh JS -->
                </div>
            </div>

            <!-- Customer Name -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2 typography-enhanced"
                id="queueModalCustomerName"></h3>

            <!-- Table Number -->
            <div class="flex justify-center gap-2 mb-4">
                <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full"
                    id="queueModalTableNumber">Table T</span>
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200"
                    id="queueModalTableType"></span>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                <div class="text-center">
                    <span class="block text-gray-500 dark:text-gray-400">Jam Order</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" id="queueModalOrderTime">00:00</span>
                </div>
                <div class="text-center">
                    <span class="block text-gray-500 dark:text-gray-400">Total Items</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" id="queueModalTotalItems">0</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-gray-300 dark:bg-gray-600 mb-4"></div>

            <!-- Items Section Header -->
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold text-gray-900 dark:text-white typography-heading">Order Items</h3>
            </div>

            <!-- Items List -->
            <div class="flex-1 overflow-y-auto max-h-40 mb-4 pr-2" id="queueModalItems"></div>

            <!-- Customer Order Note-->
            <div id="queueModalOrderNote" class="hidden mb-4">
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <span class="material-icons text-yellow-600 dark:text-yellow-400 text-sm">info</span>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">Order Note:</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300" id="queueModalOrderNoteText"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <button
                class="bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-md font-semibold transition-colors shadow-sm hover:shadow"
                id="startCookingBtn">
                Start Cooking
            </button>
        </div>
    </div>
</div>

<!-- Order Details Modal (Active Orders)-->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden" id="orderDetailsModal">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white typography-heading">Order Details</h2>
                <button
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    data-modal-close onclick="kitchenDashboard.closeModal('orderDetailsModal')">
                    <span class="material-icons text-xl">close</span>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col">
            <!-- Order Number -->
            <div class="text-center mb-4">
                <div class="order-number-badge-modal text-red-600 font-black text-4xl leading-none mx-auto"></div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mt-1">Nomor Antrian</div>
                <!-- TAMBAHAN: Tanggal Order -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="modalOrderDate">
                    <!-- Tanggal akan diisi oleh JS -->
                </div>
            </div>

            <!-- Customer Name -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2 typography-enhanced"
                id="modalCustomerName"></h3>

            <p class="text-xs text-center text-red-700 text-bold dark:text-gray-400 mb-4" id="modalOrderCode"></p>

            <!-- Table Info -->
            <div class="flex justify-center gap-2 mb-4">
                <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full" id="modalTableNumber">Table
                    T</span>
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200"
                    id="modalTableType"><!-- JS --></span>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                <div class="text-center">
                    <span class="block text-gray-500 dark:text-gray-400">Jam Proses</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" id="modalOrderTime">00:00</span>
                </div>
                <div class="text-center">
                    <span class="block text-gray-500 dark:text-gray-400">Status</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Aktif</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-gray-300 dark:bg-gray-600 mb-4"></div>

            <!-- Items Section Header -->
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold text-gray-900 dark:text-white typography-heading">Order Items</h3>
                <button class="text-sm font-semibold text-primary hover:text-primary-hover transition-colors"
                    id="checkAllBtn">
                    Check All
                </button>
            </div>

            <!-- Items List -->
            <div class="flex-1 overflow-y-auto max-h-40 mb-4 pr-2" id="modalOrderItems"></div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-2">
                <button
                    class="enabled:bg-red-600 enabled:hover:bg-red-700 enabled:shadow-lg enabled:hover:shadow-xl disabled:bg-gray-300 dark:disabled:bg-gray-600 disabled:text-gray-500 dark:disabled:text-gray-400 disabled:cursor-not-allowed text-white py-2.5 rounded-md font-semibold transition-all duration-200"
                    id="serveOrderBtn" disabled>
                    Mark as Served
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI SERVE ORDER -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden"
    id="serveConfirmationModal">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white typography-heading">Confirm Serve Order</h2>
                <button
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    onclick="kitchenDashboard.closeModal('serveConfirmationModal')">
                    <span class="material-icons text-xl">close</span>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col">
            <!-- Warning -->
            <div
                class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 text-center">
                <span class="material-icons text-yellow-600 dark:text-yellow-400 mb-1">warning</span>
                <p class="font-semibold text-yellow-800 dark:text-yellow-200">Are you sure?</p>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">This will mark the order as served.</p>
            </div>

            <!-- Order Number -->
            <div class="text-center mb-4">
                <div class="order-number-badge-modal text-red-600 font-black text-4xl leading-none mx-auto">
                    <!-- Diisi oleh JS -->
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mt-1">Nomor Antrian</div>
                <!-- TAMBAHAN: Tanggal Order -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="confirmServeOrderDate">
                    <!-- Tanggal akan diisi oleh JS -->
                </div>
            </div>

            <!-- Order Details Grid -->
            <div class="grid grid-cols-2 gap-3 mb-5 text-sm">
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Customer:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" id="confirmCustomerName"></span>
                </div>
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Table:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" id="confirmTableNumber"></span>
                </div>
                <div>
                    <span class="block text-gray-500 dark:text-gray-400">Items:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200" id="confirmTotalItems"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2.5 rounded-md font-semibold transition-colors shadow-sm hover:shadow"
                    onclick="kitchenDashboard.closeModal('serveConfirmationModal')">Cancel</button>
                <button id="confirmServeBtn"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-md font-semibold transition-colors shadow-sm hover:shadow">
                    Confirm Serve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI CANCEL ORDER -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden"
    id="cancelConfirmationModal">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white typography-heading">Cancel Order</h2>
                <button
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    onclick="kitchenDashboard.closeModal('cancelConfirmationModal')">
                    <span class="material-icons text-xl">close</span>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col">
            <!-- Warning -->
            <div
                class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 text-center">
                <span class="material-icons text-red-600 dark:text-red-400 mb-1 text-3xl">cancel</span>
                <p class="font-semibold text-yellow-800 dark:text-yellow-200 mt-2">Return to Queue?</p>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">This order will be moved back to the order
                    queue.</p>
            </div>

            <!-- Order Number -->
            <div class="text-center mb-4">
                <div class="order-number-badge-modal text-red-600 font-black text-4xl leading-none mx-auto"></div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mt-1">Nomor Antrian</div>
                <!-- TAMBAHAN: Tanggal Order -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="confirmCancelOrderDate">
                    <!-- Tanggal akan diisi oleh JS -->
                </div>
            </div>

            <!-- Order Details -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-5">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="block text-gray-500 dark:text-gray-400 text-xs mb-1">Order Code:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200"
                            id="confirmCancelOrderCode">-</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 dark:text-gray-400 text-xs mb-1">Table:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200"
                            id="confirmCancelTableNumber">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-gray-500 dark:text-gray-400 text-xs mb-1">Customer:</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200"
                            id="confirmCancelCustomerName">-</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2.5 rounded-md font-semibold transition-colors shadow-sm hover:shadow"
                    onclick="kitchenDashboard.closeModal('cancelConfirmationModal')">
                    No
                </button>
                <button id="confirmCancelBtn"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-md font-semibold transition-colors shadow-sm hover:shadow flex items-center justify-center gap-1">
                    Yes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Served Order Details Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center hidden"
    id="servedOrderDetailsModal">
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white typography-heading">Served Order Details
                </h2>
                <button
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    onclick="kitchenDashboard.closeModal('servedOrderDetailsModal')">
                    <span class="material-icons text-xl">close</span>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col">
            <!-- Status Badge -->
            <div class="text-center mb-4">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-green-500 rounded-full mb-2">
                    <span class="material-icons text-white text-2xl">check_circle</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Order Completed</div>
                <!-- TAMBAHAN: Tanggal Order -->
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="servedModalOrderDate">
                    <!-- Tanggal akan diisi oleh JS -->
                </div>
            </div>

            <!-- Customer Name -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2 typography-enhanced"
                id="servedModalCustomerName"></h3>

            <!-- Order Code -->
            <p class="text-xs text-center text-green-600 text-bold dark:text-gray-400 mb-4" id="servedModalOrderCode">
            </p>

            <!-- Table Info -->
            <div class="flex justify-center gap-2 mb-4">
                <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-full"
                    id="servedModalTableNumber">Table T</span>
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200"
                    id="servedModalTableType"></span>
            </div>

            <!-- Time Info Grid -->
            <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                <div class="text-center">
                    <span class="block text-gray-500 dark:text-gray-400">Jam Order</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200"
                        id="servedModalOrderTime">00:00</span>
                </div>
                <div class="text-center">
                    <span class="block text-gray-500 dark:text-gray-400">Jam Disajikan</span>
                    <span class="font-semibold text-green-600 dark:text-green-400"
                        id="servedModalServedTime">00:00</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-gray-300 dark:bg-gray-600 mb-4"></div>

            <!-- Items Section Header -->
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold text-gray-900 dark:text-white typography-heading">Order Items</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400" id="servedModalTotalItems">0 items</span>
            </div>

            <!-- Items List -->
            <div class="flex-1 overflow-y-auto max-h-40 mb-4 pr-2" id="servedModalItems"></div>
        </div>
    </div>
</div>
