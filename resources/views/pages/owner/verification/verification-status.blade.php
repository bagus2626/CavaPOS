@extends('layouts.owner')
@section('page_title', __('messages.owner.verification.status.page_title'))

@section('content')
    @vite(['resources/css/app.css'])
    <section class="content">
        <div class="container-fluid">

            <div class="space-y-8">

                <div class="bg-[#8c1000] relative overflow-hidden rounded-3xl shadow-md mb-4">
                    <div class="relative z-10 p-8 md:p-10">
                        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                            {{ __('messages.owner.verification.status.header_title') }}
                        </h1>
                        <p class="mt-2 text-white text-opacity-90 leading-relaxed text-base md:text-lg max-w-4xl">
                            {{ __('messages.owner.verification.status.header_desc') }}
                        </p>
                    </div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white opacity-5 rounded-full -ml-40 -mb-40"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($verification->status === 'pending')
                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-clock text-[#8c1000] text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">Status</p>
                                    <h3 class="text-gray-900 text-base font-bold">{{ __('messages.owner.verification.status.pending') }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3">
                                    <i class="far fa-calendar text-[#8c1000] text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">{{ __('messages.owner.verification.status.submitted_at') }}</p>
                                    <h3 class="text-gray-900 text-base font-bold">{{ $verification->created_at->format('d M Y') }}</h3>
                                    <p class="text-gray-500 text-xs">{{ $verification->created_at->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-hourglass-half text-[#8c1000] text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">{{ __('messages.owner.verification.status.process_estimation') }}</p>
                                    <h3 class="text-gray-900 text-base font-bold">{{ __('messages.owner.verification.status.estimation_time') }}</h3>
                                    <p class="text-gray-500 text-xs">{{ __('messages.owner.verification.status.max_time') }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif($verification->status === 'rejected')
                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">Status</p>
                                    <h3 class="text-gray-900 text-base font-bold">{{ __('messages.owner.verification.status.rejected') }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="far fa-calendar text-gray-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">{{ __('messages.owner.verification.status.submitted_at') }}</p>
                                    <h3 class="text-gray-900 text-base font-bold">{{ $verification->created_at->format('d M Y') }}</h3>
                                    <p class="text-gray-500 text-xs">{{ $verification->created_at->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="far fa-calendar-times text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">{{ __('messages.owner.verification.status.reviewed_at') }}</p>
                                    <h3 class="text-gray-900 text-base font-bold">{{ $verification->reviewed_at ? $verification->reviewed_at->format('d M Y') : '-' }}</h3>
                                    <p class="text-gray-500 text-xs">{{ $verification->reviewed_at ? $verification->reviewed_at->format('H:i') . ' WIB' : '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if($verification->status === 'rejected' && $verification->rejection_reason)
                <div class="bg-red-50 rounded-2xl shadow-md overflow-hidden animate-fadeIn">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-exclamation-triangle text-red-700 text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-red-900 mb-3 flex items-center">
                                    <span>{{ __('messages.owner.verification.status.rejection_title') }}</span>
                                </h3>
                                <p class="text-red-800 leading-relaxed text-base">{{ $verification->rejection_reason }}</p>
                                <div class="mt-4">
                                    <p class="text-sm text-red-700 mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>{{ __('messages.owner.verification.status.rejection_note') }}</strong> {{ __('messages.owner.verification.status.rejection_instruction') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($verification->status === 'pending')
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-md">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-info-circle text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-red-900 mb-3 text-lg">{{ __('messages.owner.verification.status.info_title') }}</h4>
                            <ul class="text-sm text-red-800 space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>{{ __('messages.owner.verification.status.info_point_1') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>{{ __('messages.owner.verification.status.info_point_2') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>{{ __('messages.owner.verification.status.info_point_3') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>{{ __('messages.owner.verification.status.info_point_4') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="px-6 py-4 border-b border-gray-200 bg-[#8c1000]">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-user text-[#8c1000]"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-white m-0">{{ __('messages.owner.verification.status.owner_data') }}</h2>
                                    <p class="text-sm text-white m-0">{{ __('messages.owner.verification.personal_subtitle') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.owner_name') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->owner_name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.owner_phone') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->owner_phone }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.owner_email') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->owner_email }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.ktp_number') }}</label>
                                    <p class="text-gray-900 font-semibold text-base font-mono">{{ $verification->ktp_number_decrypted ?? __('messages.owner.verification.status.ktp_hidden') }}</p>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('messages.owner.verification.status.view_ktp') }}</label>
                                    <div class="relative group inline-block">
                                        <img src="{{ route('owner.user-owner.verification.ktp-image') }}" 
                                             alt="{{ __('messages.owner.verification.status.view_ktp') }}" 
                                             class="max-w-md w-full rounded-2xl shadow-md border-2 border-gray-200 cursor-pointer hover:shadow-md transition-all duration-300 hover:scale-[1.02]"
                                             onclick="openImageModal(this.src, '{{ __('messages.owner.verification.status.view_ktp') }}')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="px-6 py-4 border-b border-gray-200 bg-[#8c1000]">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-store text-[#8c1000]"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-white m-0">{{ __('messages.owner.verification.status.business_data') }}</h2>
                                    <p class="text-sm text-white m-0">{{ __('messages.owner.verification.business_subtitle') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.business_name') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->business_name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.business_category') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ ($verification->businessCategory)->name ?? '-' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.business_phone') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->business_phone }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.business_email') }}</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->business_email ?: '-' }}</p>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('messages.owner.verification.business_address') }}</label>
                                    <p class="text-gray-900 font-medium text-base leading-relaxed">{{ $verification->business_address }}</p>
                                </div>
                                @if($verification->business_logo_path)
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('messages.owner.verification.status.view_logo') }}</label>
                                        <div class="relative group inline-block">
                                             <img src="{{ asset('storage/' . $verification->business_logo_path) }}" 
                                                 alt="{{ __('messages.owner.verification.status.view_logo') }}" 
                                                 class="max-w-md w-full rounded-2xl shadow-md border-2 border-gray-200 cursor-pointer hover:shadow-md transition-all duration-300 hover:scale-[1.02]"
                                                 onclick="openImageModal(this.src, '{{ __('messages.owner.verification.status.view_logo') }}')">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($verification->status === 'rejected')
                <div class="flex justify-center py-10 mt-0">
                    <a href="{{ route('owner.user-owner.verification.index') }}" 
                       class="px-8 py-4 bg-[#8c1000] text-white rounded-2xl transition-all duration-300 font-bold text-lg shadow-md hover:bg-[#a11b0b] hover:shadow-md hover:-translate-y-1 inline-flex items-center group">
                        <i class="fas fa-redo mr-3 group-hover:rotate-180 transition-transform duration-500"></i>
                        {{ __('messages.owner.verification.status.btn_resubmit') }}
                    </a>
                </div>
                @endif

            </div> </div>
    </section>

    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-transparent bg-opacity-90 backdrop-blur-sm" onclick="closeImageModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative max-w-5xl w-full animate-zoomIn" onclick="event.stopPropagation()">
                <button onclick="closeImageModal()" class="absolute -top-16 right-0 text-white hover:text-gray-300 transition-colors bg-[#8c1000] backdrop-blur-md rounded-full w-12 h-12 flex items-center justify-center hover:bg-opacity-20">
                    <i class="fas fa-times text-2xl"></i>
                </button>
                <img id="modalImage" src="" alt="" class="w-full h-auto rounded-2xl shadow-md">
                <p id="modalCaption" class="text-white text-center mt-6 text-xl font-bold"></p>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/app.js'])
        <script>
        function openImageModal(src, caption) {
            document.getElementById('modalImage').src = src;
            document.getElementById('modalCaption').textContent = caption;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        @if($verification->status === 'pending')
        let refreshInterval = setInterval(function() {
            fetch(window.location.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status && data.status !== 'pending') {
                    clearInterval(refreshInterval);
                    location.reload();
                }
            })
            .catch(error => {
                console.log('Auto refresh error:', error);
            });
        }, 30000);
        @endif

        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    title: "{{ __('messages.owner.verification.status.swal_success_title') }}",
                    icon: 'success',
                    html: `
                        <div class="text-left text-gray-700 leading-relaxed px-4">
                            <p class="mb-3">{{ __('messages.owner.verification.status.swal_success_p1') }}</p>
                            <p class="font-medium">
                                {{ __('messages.owner.verification.status.swal_success_p2') }}
                                <strong class="text-gray-900">{{ auth()->guard('owner')->user()->email }}</strong>
                            </p>
                            <br>
                            <p class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded-lg">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                {{ __('messages.owner.verification.status.swal_success_warning') }}
                            </p>
                        </div>
                    `,
                    confirmButtonText: "{{ __('messages.owner.verification.status.swal_success_btn') }}",
                    confirmButtonColor: '#8c1000',
                    customClass: {
                        popup: 'rounded-2xl',
                    }
                });
            @endif
        });
        </script>
    @endpush
@endsection