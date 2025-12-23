@extends('layouts.owner')
@section('page_title', __('messages.owner.verification.page_title'))

@section('content')
    @vite(['resources/css/app.css'])
    <section class="content pb-4">
        <div class="container-fluid">

            <div class="mb-8">
                <div class="relative overflow-hidden rounded-3xl shadow-sm bg-[#8c1000]">
                    <div class="relative z-10 p-8 md:p-10">
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                                        {{ __('messages.owner.verification.header_title') }}
                                    </h1>
                                </div>
                                <p class="text-white text-opacity-95 leading-relaxed text-base md:text-lg max-w-4xl">
                                    {{ __('messages.owner.verification.header_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($owner->verification_status === 'rejected' && $latestVerification && $latestVerification->rejection_reason)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-yellow-900 mb-2">
                                {{ __('messages.owner.verification.rejected_title') }}
                            </h3>
                            <div class="bg-white rounded-lg p-3 mb-3 border border-yellow-200">
                                <p class="text-sm font-medium text-yellow-800 mb-1">{{ __('messages.owner.verification.rejection_reason') }}</p>
                                <p class="text-gray-700 p-0 m-0">{{ $latestVerification->rejection_reason }}</p>
                            </div>
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ __('messages.owner.verification.rejection_instruction') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('owner.user-owner.verification.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="verificationForm">
                @csrf

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-[#8c1000] px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fas fa-user text-red-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white m-0" style="margin-bottom: 0;">{{ __('messages.owner.verification.personal_title') }}</h2>
                                <p class="text-sm text-white m-0">{{ __('messages.owner.verification.personal_subtitle') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.owner_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="owner_name" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('owner_name') border-red-500 @enderror"
                                    placeholder="{{ __('messages.owner.verification.owner_name_placeholder') }}" minlength="3" 
                                    value="{{ old('owner_name', $latestVerification->owner_name ?? $owner->name ?? '') }}">
                                @error('owner_name')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="owner_name">{{ __('messages.owner.verification.err_name_min') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.owner_phone') }} <span class="text-red-500">*</span></label>
                                <input type="tel" name="owner_phone" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('owner_phone') border-red-500 @enderror"
                                    placeholder="08xxxxxxxxxx" pattern="^(08|62)\d{8,12}$" minlength="10" maxlength="15"
                                    value="{{ old('owner_phone', $latestVerification->owner_phone ?? $owner->phone_number ?? '') }}">
                                @error('owner_phone')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="owner_phone">{{ __('messages.owner.verification.err_phone_format') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('messages.owner.verification.owner_email') }} 
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                        value="{{ $owner->email }}" 
                                        disabled
                                        class="w-full px-4 py-3 pr-10 rounded-xl border border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed">
                                </div>
                                <p class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __('messages.owner.verification.email_disabled_info') }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.ktp_number') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="ktp_number" required maxlength="16" minlength="16"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('ktp_number') border-red-500 @enderror"
                                    placeholder="{{ __('messages.owner.verification.ktp_number_placeholder') }}" pattern="\d{16}"
                                    value="{{ old('ktp_number', $latestVerification->ktp_number_decrypted ?? '') }}">
                                @error('ktp_number')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="ktp_number">{{ __('messages.owner.verification.err_ktp_format') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('messages.owner.verification.ktp_photo') }} 
                                    @if($latestVerification)
                                        <span class="text-gray-500">{{ __('messages.owner.verification.ktp_optional') }}</span>
                                    @else
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                
                                @if($latestVerification && $latestVerification->ktp_photo_path)
                                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl" id="old_ktp_preview">
                                        <div class="flex items-start justify-between mb-2">
                                            <p class="text-sm font-medium text-blue-900 flex items-center">
                                                <i class="fas fa-image mr-2"></i> {{ __('messages.owner.verification.ktp_prev_photo') }}
                                            </p>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg inline-block">
                                            <img src="{{ route('owner.user-owner.verification.ktp-image') }}" 
                                                alt="KTP Preview" 
                                                class="max-w-md rounded-lg shadow-sm border border-gray-200"
                                                style="max-height: 250px; object-fit: contain;">
                                        </div>
                                        <p class="text-xs text-blue-700 mt-3 flex items-start">
                                            <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                            <span>{{ __('messages.owner.verification.ktp_keep_info') }}</span>
                                        </p>
                                    </div>
                                @endif
                                
                                <div class="relative" id="ktp_upload_area">
                                    <input type="file" name="ktp_photo" id="ktp_photo" 
                                        {{ $latestVerification ? '' : 'required' }}
                                        accept="image/jpeg,image/jpg,image/png" class="hidden"
                                        onchange="previewImageWithRemove(this, 'ktp_preview_container', 'ktp_upload_area')">
                                    <label for="ktp_photo"
                                        class="flex items-center justify-center w-full px-4 py-8 border-2 border-dashed border-gray-300 rounded-xl hover:border-red-400 transition-all duration-200 cursor-pointer bg-gray-50 hover:bg-red-50 @error('ktp_photo') border-red-500 @enderror">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                            <p class="text-sm text-gray-600 font-medium">
                                                {{ $latestVerification ? __('messages.owner.verification.ktp_upload_new') : __('messages.owner.verification.ktp_upload_default') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ __('messages.owner.verification.ktp_upload_hint') }}</p>
                                            <p class="text-xs text-red-600 font-medium mt-2">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ __('messages.owner.verification.ktp_upload_warning') }}
                                            </p>
                                        </div>
                                    </label>
                                </div>
                                
                                @error('ktp_photo')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="ktp_photo">{{ __('messages.owner.verification.err_file_image') }} (Max 1MB)</span>
                                @enderror
                                
                                <div id="ktp_preview_container" class="hidden mt-4">
                                    <div class="relative inline-block max-w-md">
                                        <img id="ktp_preview_image" src="" alt="Preview KTP"
                                            class="w-full rounded-xl shadow-sm border border-gray-200">
                                        <button type="button" 
                                            onclick="removeNewPreview('ktp_photo', 'ktp_preview_container', 'ktp_upload_area')"
                                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-110"
                                            title="Hapus foto">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-[#8c1000] px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fas fa-store text-red-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-white" style="margin-bottom: 0;">{{ __('messages.owner.verification.business_title') }}</h2>
                                <p class="text-sm text-white m-0">{{ __('messages.owner.verification.business_subtitle') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.business_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="business_name" required minlength="3"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('business_name') border-red-500 @enderror"
                                    placeholder="{{ __('messages.owner.verification.business_name_placeholder') }}"
                                    value="{{ old('business_name', $latestVerification->business_name ?? '') }}">
                                @error('business_name')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="business_name">{{ __('messages.owner.verification.err_name_min') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('messages.owner.verification.business_category') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="business_category_id" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('business_category_id') border-red-500 @enderror">
                                    <option value="">{{ __('messages.owner.verification.business_category_select') }}</option>
                                    @foreach($businessCategories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('business_category_id', $latestVerification->business_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('business_category_id')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.business_address') }} <span class="text-red-500">*</span></label>
                                <textarea name="business_address" required rows="3" minlength="10"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none resize-none @error('business_address') border-red-500 @enderror"
                                    placeholder="{{ __('messages.owner.verification.business_address_placeholder') }}">{{ old('business_address', $latestVerification->business_address ?? '') }}</textarea>
                                @error('business_address')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="business_address">{{ __('messages.owner.verification.err_address_min') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.business_phone') }}
                                    <span class="text-red-500">*</span></label>
                                <input type="tel" name="business_phone" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('business_phone') border-red-500 @enderror"
                                    placeholder="08xxxxxxxxxx" pattern="^(08|62)\d{8,12}$" minlength="10" maxlength="15"
                                    value="{{ old('business_phone', $latestVerification->business_phone ?? '') }}">
                                @error('business_phone')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="business_phone">{{ __('messages.owner.verification.err_phone_format') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.business_email') }} <span class="text-gray-400">{{ __('messages.owner.verification.business_email_optional') }}</span></label>
                                <input type="email" name="business_email"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-200 outline-none @error('business_email') border-red-500 @enderror"
                                    placeholder="bisnis@email.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    value="{{ old('business_email', $latestVerification->business_email ?? '') }}">
                                @error('business_email')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="business_email">{{ __('messages.owner.verification.err_email_format') }}</span>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('messages.owner.verification.business_logo') }} <span class="text-gray-400">{{ __('messages.owner.verification.business_email_optional') }}</span></label>
                                
                                @if($latestVerification && $latestVerification->business_logo_path)
                                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl" id="old_logo_preview">
                                        <div class="flex items-start justify-between mb-2">
                                            <p class="text-sm font-medium text-blue-900 flex items-center">
                                                <i class="fas fa-image mr-2"></i> {{ __('messages.owner.verification.logo_prev') }}
                                            </p>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg inline-block">
                                            <img src="{{ Storage::url($latestVerification->business_logo_path) }}" 
                                                alt="Business Logo Preview" 
                                                class="rounded-lg shadow-sm border border-gray-200"
                                                style="width: 200px; height: 150px; object-fit: contain;">
                                        </div>
                                        <p class="text-xs text-blue-700 mt-3 flex items-start">
                                            <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                            <span>{{ __('messages.owner.verification.logo_keep_info') }}</span>
                                        </p>
                                    </div>
                                @endif
                                
                                <div class="relative" id="logo_upload_area">
                                    <input type="file" name="business_logo" id="business_logo"
                                        accept="image/jpeg,image/jpg,image/png" class="hidden"
                                        onchange="previewImageWithRemove(this, 'logo_preview_container', 'logo_upload_area')">
                                    <label for="business_logo"
                                        class="flex items-center justify-center w-full px-4 py-8 border-2 border-dashed border-gray-300 rounded-xl hover:border-red-400 transition-all duration-200 cursor-pointer bg-gray-50 hover:bg-red-50 @error('business_logo') border-red-500 @enderror">
                                        <div class="text-center">
                                            <i class="fas fa-image text-4xl text-gray-400 mb-3"></i>
                                            <p class="text-sm text-gray-600 font-medium">
                                                {{ $latestVerification && $latestVerification->business_logo_path ? __('messages.owner.verification.logo_upload_new') : __('messages.owner.verification.logo_upload_default') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ __('messages.owner.verification.logo_upload_hint') }}</p>
                                        </div>
                                    </label>
                                </div>
                                
                                @error('business_logo')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @else
                                    <span class="text-xs text-red-500 hidden error-message" data-error="business_logo">{{ __('messages.owner.verification.err_file_image') }} (Max 2MB)</span>
                                @enderror
                                
                                <div id="logo_preview_container" class="hidden mt-4">
                                    <div class="relative inline-block">
                                        <img id="logo_preview_image" src="" alt="Preview Logo"
                                            class="w-48 h-48 object-cover rounded-xl shadow-sm border border-gray-200">
                                        <button type="button" 
                                            onclick="removeNewPreview('business_logo', 'logo_preview_container', 'logo_upload_area')"
                                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-110"
                                            title="Hapus logo">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-blue-900 mb-2">{{ __('messages.owner.verification.important_info') }}</h4>
                            <ul class="text-sm text-blue-800 space-y-2">
                                <li class="flex items-start"><i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i><span>{{ __('messages.owner.verification.info_1') }}</span></li>
                                <li class="flex items-start"><i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i><span>{{ __('messages.owner.verification.info_2') }}</span></li>
                                <li class="flex items-start"><i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i><span>{{ __('messages.owner.verification.info_3') }}</span></li>
                                <li class="flex items-start"><i class="fas fa-check-circle mt-0.5 mr-2 text-blue-600"></i><span>{{ __('messages.owner.verification.info_4') }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="terms" name="terms" required
                            class="mt-1 h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-2 focus:ring-red-200">
                        <label for="terms" class="text-sm text-gray-600 leading-relaxed">
                            {{ __('messages.owner.verification.terms_agreement') }} 
                            <a href="#" class="text-red-600 hover:text-red-700 font-medium">{{ __('messages.owner.verification.terms_link') }}</a> 
                            {{ __('messages.owner.verification.agreement_suffix') }} 
                            <a href="#" class="text-red-600 hover:text-red-700 font-medium">{{ __('messages.owner.verification.privacy_link') }}</a>
                            {{ __('messages.owner.verification.agreement_suffix') }}
                        </label>
                    </div>
                    @error('terms')
                        <span class="text-xs text-red-500 ml-8 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="submitBtn" disabled
                        class="px-8 py-3 bg-[#8c1000] text-white rounded-xl transition-all duration-300 font-medium shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none hover:from-red-700 hover:to-red-800 hover:shadow-xl hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane mr-2"></i> 
                        {{ $latestVerification ? __('messages.owner.verification.btn_resend') : __('messages.owner.verification.btn_send') }}
                    </button>
                </div>
            </form>
        </div>
    </section>

@push('scripts')
        @vite(['resources/js/app.js'])
        <script>
            // Fungsi untuk menghapus preview foto lama dari database
            function removeOldPreview(previewId, inputId) {
                const preview = document.getElementById(previewId);
                const input = document.getElementById(inputId);
                
                if (preview) {
                    preview.remove();
                }
                
                // Set input menjadi required jika foto lama dihapus
                if (input && !input.hasAttribute('required')) {
                    input.setAttribute('required', 'required');
                }
                
                // Trigger form validity check
                checkFormValidity();
            }

            // Fungsi untuk preview gambar dengan tombol hapus
            function previewImageWithRemove(input, previewContainerId, uploadAreaId) {
                const previewContainer = document.getElementById(previewContainerId);
                const previewImage = document.getElementById(previewContainerId.replace('_container', '_image'));
                const uploadArea = document.getElementById(uploadAreaId);
                const errorSpan = document.querySelector(`[data-error="${input.name}"]`);

                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const maxSize = input.name === 'ktp_photo' ? 1 * 1024 * 1024 : 2 * 1024 * 1024;

                    // Validasi ukuran dan tipe file
                    if (file.size > maxSize) {
                        if (errorSpan) {
                            const sizeLimit = input.name === 'ktp_photo' ? '1MB' : '2MB';
                            errorSpan.textContent = "{{ __('messages.owner.verification.err_file_image') }} (Max " + sizeLimit + ")";
                            errorSpan.classList.remove('hidden');
                        }
                        input.value = '';
                        previewContainer.classList.add('hidden');
                        uploadArea.classList.remove('hidden');
                        checkFormValidity();
                        return;
                    }

                    if (!file.type.match('image/(jpeg|jpg|png)')) {
                        if (errorSpan) {
                            errorSpan.textContent = "{{ __('messages.owner.verification.err_file_image') }}";
                            errorSpan.classList.remove('hidden');
                        }
                        input.value = '';
                        previewContainer.classList.add('hidden');
                        uploadArea.classList.remove('hidden');
                        checkFormValidity();
                        return;
                    }

                    // Sembunyikan error jika validasi berhasil
                    if (errorSpan) errorSpan.classList.add('hidden');

                    // Preview gambar
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        uploadArea.classList.add('hidden');
                        checkFormValidity();
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.classList.add('hidden');
                    uploadArea.classList.remove('hidden');
                    previewImage.src = '';
                    checkFormValidity();
                }
            }

            // Fungsi untuk menghapus preview foto baru yang baru diupload
            function removeNewPreview(inputId, previewContainerId, uploadAreaId) {
                const input = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewContainerId);
                const previewImage = document.getElementById(previewContainerId.replace('_container', '_image'));
                const uploadArea = document.getElementById(uploadAreaId);
                
                // Reset input file
                input.value = '';
                
                // Sembunyikan preview dan tampilkan upload area
                previewContainer.classList.add('hidden');
                uploadArea.classList.remove('hidden');
                previewImage.src = '';
                
                // Trigger form validity check
                checkFormValidity();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('verificationForm');
                const submitBtn = document.getElementById('submitBtn');
                const termsCheckbox = document.getElementById('terms');
                const requiredInputs = form.querySelectorAll('input[required], select[required], textarea[required]');

                requiredInputs.forEach(input => {
                    input.addEventListener('input', () => {
                        validateField(input);
                        checkFormValidity();
                    });
                    input.addEventListener('blur', () => validateField(input));
                });
                
                const businessEmailInput = document.querySelector('input[name="business_email"]');
                businessEmailInput.addEventListener('input', () => validateField(businessEmailInput));
                businessEmailInput.addEventListener('blur', () => validateField(businessEmailInput));

                termsCheckbox.addEventListener('change', checkFormValidity);
                checkFormValidity();

                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    let isFormCompletelyValid = true;
                    form.querySelectorAll('input, select, textarea').forEach(input => {
                        if (!validateField(input)) {
                            isFormCompletelyValid = false;
                        }
                    });

                    if (!termsCheckbox.checked) {
                        isFormCompletelyValid = false;
                    }

                    if (!isFormCompletelyValid) {
                        toastr.error("{{ __('messages.owner.verification.err_form_invalid') }}");
                        form.querySelector('.border-red-500, :invalid')?.focus();
                        return;
                    }

                    Swal.fire({
                        title: "{{ __('messages.owner.verification.swal_confirm_title') }}",
                        text: "{{ __('messages.owner.verification.swal_confirm_text') }}",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('messages.owner.verification.swal_confirm_btn') }}",
                        cancelButtonText: "{{ __('messages.owner.verification.swal_cancel_btn') }}",
                        confirmButtonColor: '#8c1000',
                        cancelButtonColor: '#6c757d',
                        customClass: {
                            popup: 'rounded-2xl',
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + "{{ __('messages.owner.verification.btn_loading') }}";
                            form.submit();
                        }
                    });
                });
            });

            function validateField(field) {
                const errorSpan = document.querySelector(`[data-error="${field.name}"]`);
                if (!errorSpan) return true;

                // Jika field tidak wajib diisi dan kosong, maka selalu valid
                if (!field.hasAttribute('required') && field.value.trim() === '') {
                    errorSpan.classList.add('hidden');
                    field.classList.remove('border-red-500');
                    return true;
                }

                let isValid = field.checkValidity();

                if (!isValid) {
                    errorSpan.classList.remove('hidden');
                    field.classList.add('border-red-500');
                } else {
                    errorSpan.classList.add('hidden');
                    field.classList.remove('border-red-500');
                }
                return isValid;
            }

            function checkFormValidity() {
                const form = document.getElementById('verificationForm');
                const submitBtn = document.getElementById('submitBtn');
                const termsCheckbox = document.getElementById('terms');

                let isFormValid = form.checkValidity();

                submitBtn.disabled = !isFormValid || !termsCheckbox.checked;
            }

            document.querySelector('input[name="ktp_number"]').addEventListener('input', function (e) {
                this.value = this.value.replace(/\D/g, '').substring(0, 16);
            });

            document.querySelectorAll('input[type="tel"]').forEach(function (input) {
                input.addEventListener('input', function (e) {
                    this.value = this.value.replace(/\D/g, '');
                });
            });
        </script>
    @endpush
@endsection
                