<div class="flex flex-col h-full">
    <div class="flex-shrink-0 px-4 pt-5 pb-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-black text-gray-900 dark:text-white typography-heading mb-4">
            Order Queue
        </h2>
        
        <div class="relative">
            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
            <input class="w-full pl-10 pr-10 py-3 text-base bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent text-gray-800 dark:text-gray-200 placeholder-gray-400 hover:shadow transition-all typography-enhanced" 
                   placeholder="Search all orders" 
                   type="text" 
                   id="globalSearch"
                   autocomplete="off"/>
            <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600 hidden p-1 rounded transition-colors" 
                    id="clearGlobalSearch"
                    type="button">
                <span class="material-icons text-lg">close</span>
            </button>
        </div>
    </div>
    
    <div class="flex-1 overflow-hidden px-4">
        <div class="h-full overflow-y-auto scrollbar-thin space-y-3 py-4" id="orderQueue">
            <!-- Container kosong, akan diisi oleh JavaScript -->
        </div>
    </div>
</div>