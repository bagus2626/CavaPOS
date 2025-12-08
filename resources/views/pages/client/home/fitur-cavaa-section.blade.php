<section id="fitur" class="py-12 lg:py-16 bg-gradient-to-br from-amber-50 to-cream/30 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-30" style="background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 100&quot;><defs><pattern id=&quot;dots&quot; width=&quot;15&quot; height=&quot;15&quot; patternUnits=&quot;userSpaceOnUse&quot;><circle cx=&quot;7.5&quot; cy=&quot;7.5&quot; r=&quot;1&quot; fill=&quot;rgba(185,28,28,0.1)&quot;/></pattern></defs><rect width=&quot;100&quot; height=&quot;100&quot; fill=&quot;url(%23dots)&quot;/></svg>');"></div>
    
    <div class="max-w-6xl mx-auto px-4 relative z-10">
        <div class="text-center mb-12 lg:mb-16">
            <h2 class="font-poppins text-2xl md:text-3xl lg:text-4xl font-bold mb-3 lg:mb-4 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                {{ __('messages.home.feature_section') }}
            </h2>
            <p class="text-gray-600 text-base lg:text-lg max-w-2xl mx-auto">
                {{ __('messages.home.feature_section_desc') }}
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-8">
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-t-4 border-primary">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-chart-bar text-white text-base md:text-xl"></i>
                </div>
                <h3 class="font-poppins text-sm md:text-lg font-semibold mb-2 md:mb-3">Dashboard Analytics</h3>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">
                    {{ __('messages.home.feature_dashboard') }}
                </p>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-t-4 border-accent">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-cash-register text-white text-base md:text-xl"></i>
                </div>
                <h3 class="font-poppins text-sm md:text-lg font-semibold mb-2 md:mb-3">POS System</h3>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">
                    {{ __('messages.home.feature_pos_system') }}
                </p>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-t-4 border-primary">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-box-open text-white text-base md:text-xl"></i>
                </div>
                <h3 class="font-poppins text-sm md:text-lg font-semibold mb-2 md:mb-3">Inventory Management</h3>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">
                    {{ __('messages.home.feature_inventory') }}
                </p>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-t-4 border-accent">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-chart-line text-white text-base md:text-xl"></i>
                </div>
                <h3 class="font-poppins text-sm md:text-lg font-semibold mb-2 md:mb-3">Sales Analytics</h3>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">
                    {{ __('messages.home.feature_sales') }}
                </p>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-t-4 border-primary">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-users text-white text-base md:text-xl"></i>
                </div>
                <h3 class="font-poppins text-sm md:text-lg font-semibold mb-2 md:mb-3">Customer Management</h3>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">
                    {{ __('messages.home.feature_customer') }}
                </p>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-t-4 border-accent">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-br from-primary to-accent rounded-xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-laptop-code text-white text-base md:text-xl"></i>
                </div>
                <h3 class="font-poppins text-sm md:text-lg font-semibold mb-2 md:mb-3">Multi-Device Support</h3>
                <p class="text-gray-600 text-xs md:text-sm leading-relaxed">
                    {{ __('messages.home.feature_multi_device') }}
                </p>
            </div>
        </div>
    </div>
</section>