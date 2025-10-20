<div class="flex flex-col h-full">
    <div class="flex-shrink-0">
        <h2 class="text-xl font-semibold text-text-light dark:text-text-dark typography-heading">
            Order Queue 
            <span class="text-base text-text-secondary-light dark:text-text-secondary-dark ml-2 typography-enhanced" id="queueOrdersCount">
                0 orders waiting
            </span>
        </h2>
        <div class="relative mt-4">
            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary-light dark:text-text-secondary-dark text-sm">search</span>
            <input class="w-full pl-10 pr-4 py-2 bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark rounded-xl focus:ring-primary focus:border-primary text-text-light dark:text-text-dark typography-enhanced" 
                   placeholder="Search all orders" 
                   type="text" 
                   id="globalSearch"
                   autocomplete="off"/>
            <button class="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark hidden" 
                    id="clearGlobalSearch"
                    type="button">
                <span class="material-icons text-sm">close</span>
            </button>
        </div>
    </div>
    
    <div class="flex-1 overflow-hidden mt-4">
        <div class="h-full overflow-y-auto custom-scrollbar space-y-3 pr-2" id="orderQueue">
            <div class="text-center text-text-secondary-light dark:text-text-secondary-dark py-8">
                <div class="text-lg mb-2">📭</div>
                <div>Loading orders...</div>
            </div>
        </div>
    </div>
</div>