<div class="flex flex-col h-full">
    <div class="flex-shrink-0">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg typography-heading">
                Served Orders 
                <div class="text-base font-normal text-subtext-light dark:text-subtext-dark ml-0 typography-enhanced" id="servedOrdersCount">
                    0 orders
                </div>
            </h3>
            <div class="relative">
                <input type="date" 
                       id="datePicker" 
                       class="bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark rounded-xl px-3 py-2 text-sm focus:ring-primary focus:border-primary transition-colors typography-enhanced"
                       max="<?php echo date('Y-m-d'); ?>"
                       onchange="kitchenDashboard.applyDateFilter(this.value)">
            </div>
        </div>
    </div>
    
    <div class="flex-1 overflow-hidden">
        <div id="servedOrders" class="h-full overflow-y-auto custom-scrollbar pr-2 space-y-2">
            <div class="flex flex-col items-center justify-center text-center h-full py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mb-2"></div>
                <h4 class="font-semibold text-text-light dark:text-text-dark typography-heading">Loading served orders...</h4>
                <p class="text-sm text-subtext-light dark:text-subtext-dark typography-enhanced">Please wait</p>
            </div>
        </div>
    </div>
</div>