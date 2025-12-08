<section class="py-16 lg:py-20 bg-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="font-poppins text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-12 lg:mb-20 text-gray-900">
                {{ __('messages.home.why_section') }}
            </h2>
            <div class="space-y-12 md:space-y-16 lg:space-y-24">
                <!-- Feature 1 - Mobile: Image top, Text bottom | Tablet/Desktop: Image left, Text right -->
                <div class="grid md:grid-cols-2 gap-6 md:gap-12 lg:gap-16 items-center">
                    <!-- Image - Always visible, responsive sizing -->
                    <div class="flex justify-center md:justify-start order-1">
                        <img src="images/cava-mengapa.jpg" 
                             alt="Kelola Bisnis" 
                             class="rounded-xl shadow-lg w-full max-w-sm md:max-w-md lg:max-w-lg h-[250px] md:h-[280px] lg:h-[400px] object-cover">
                    </div>
                    <!-- Text Content -->
                    <div class="text-center md:text-left order-2">
                        <h3 class="font-poppins text-xl md:text-xl lg:text-2xl font-semibold mb-4 md:mb-5 lg:mb-6 text-gray-900">
                            {{ __('messages.home.why_section_desc_bussiness') }}
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-base md:text-base lg:text-lg max-w-2xl mx-auto md:mx-0">
                            {{ __('messages.home.why_section_desc_sub_bussiness') }}
                        </p>
                    </div>
                </div>

                <!-- Feature 2 - Mobile: Image top, Text bottom | Tablet/Desktop: Text left, Image right -->
                <div class="grid md:grid-cols-2 gap-6 md:gap-12 lg:gap-16 items-center">
                    <!-- Text Content -->
                    <div class="order-2 md:order-1 text-center md:text-left">
                        <h3 class="font-poppins text-xl md:text-xl lg:text-2xl font-semibold mb-4 md:mb-5 lg:mb-6 text-gray-900">
                            {{ __('messages.home.why_section_desc_transaction') }}
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-base md:text-base lg:text-lg max-w-2xl mx-auto md:mx-0">
                           {{ __('messages.home.why_section_desc_sub_transaction') }}
                        </p>
                    </div>
                    <!-- Image - Always visible, responsive sizing -->
                    <div class="flex order-1 md:order-2 justify-center md:justify-end">
                        <img src="images/cava-mengapa2.jpg" 
                             alt="Visual Testimoni" 
                             class="rounded-xl shadow-lg w-full max-w-sm md:max-w-md lg:max-w-lg h-[250px] md:h-[280px] lg:h-[400px] object-cover">
                    </div>
                </div>

                <!-- Feature 3 - Mobile: Image top, Text bottom | Tablet/Desktop: Image left, Text right -->
                <div class="grid md:grid-cols-2 gap-6 md:gap-12 lg:gap-16 items-center">
                    <!-- Image - Always visible, responsive sizing -->
                    <div class="flex justify-center md:justify-start order-1">
                        <img src="images/cava-mengapa3.jpg" 
                             alt="Naik Kelas" 
                             class="rounded-xl shadow-lg w-full max-w-sm md:max-w-md lg:max-w-lg h-[250px] md:h-[280px] lg:h-[400px] object-cover">
                    </div>
                    <!-- Text Content -->
                    <div class="order-2 text-center md:text-left">
                        <h3 class="font-poppins text-xl md:text-xl lg:text-2xl font-semibold mb-4 md:mb-5 lg:mb-6 text-gray-900">
                            {{ __('messages.home.why_section_desc_bussiness_upgrade') }}
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-base md:text-base lg:text-lg max-w-2xl mx-auto md:mx-0">
                            {{ __('messages.home.why_section_desc_sub_bussiness_upgrade') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>