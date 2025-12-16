<div class="flex flex-col h-full">
    <div class="flex-shrink-0 px-4 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-xl font-black text-gray-900 dark:text-white typography-heading">
                Served Orders
            </h2>
        </div>
        
        <div class="relative">
            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base">calendar_today</span>
            <input type="date" 
                   id="datePicker" 
                   class="w-full pl-9 pr-3 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent text-gray-800 dark:text-gray-200 placeholder-gray-400 hover:shadow transition-all typography-enhanced"
                   max="<?php echo date('Y-m-d'); ?>"
                   onchange="kitchenDashboard.applyDateFilter(this.value)">
        </div>
    </div>
    
    <div class="flex-1 overflow-hidden px-4">
        <div id="servedOrders" class="h-full overflow-y-auto scrollbar-thin space-y-2 py-3">
            <!-- Container kosong, akan diisi oleh JavaScript -->
        </div>
    </div>
</div>