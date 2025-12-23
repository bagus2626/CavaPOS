@extends('layouts.owner')
@section('page_title', 'Status Verifikasi')

@section('content')
    @vite(['resources/css/app.css'])
    <section class="content">
        <div class="container-fluid">

            <div class="space-y-8">

                <div class="bg-[#8c1000] relative overflow-hidden rounded-3xl shadow-md mb-4">
                    <div class="relative z-10 p-8 md:p-10">
                        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                            Status Verifikasi Akun
                        </h1>
                        <p class="mt-2 text-white text-opacity-90 leading-relaxed text-base md:text-lg max-w-4xl">
                            Terima kasih telah mengirimkan data verifikasi. Berikut adalah status pengajuan Anda.
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
                                    <h3 class="text-gray-900 text-base font-bold">Menunggu Verifikasi</h3>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3">
                                    <i class="far fa-calendar text-[#8c1000] text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">Diajukan pada</p>
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
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">Estimasi Proses</p>
                                    <h3 class="text-gray-900 text-base font-bold">1-2 Hari Kerja</h3>
                                    <p class="text-gray-500 text-xs">Maks 2x24 jam</p>
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
                                    <h3 class="text-gray-900 text-base font-bold">Verifikasi Ditolak</h3>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-md hover:shadow-md transition-all duration-300 h-full">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="far fa-calendar text-gray-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">Diajukan pada</p>
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
                                    <p class="text-gray-500 text-xs font-medium mb-0.5 uppercase tracking-wide">Ditolak pada</p>
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
                                    <span>Alasan Penolakan</span>
                                </h3>
                                <p class="text-red-800 leading-relaxed text-base">{{ $verification->rejection_reason }}</p>
                                <div class="mt-4">
                                    <p class="text-sm text-red-700 mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Catatan:</strong> Silakan perbaiki data sesuai catatan di atas dan ajukan verifikasi ulang.
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
                            <h4 class="font-bold text-red-900 mb-3 text-lg">Informasi Penting</h4>
                            <ul class="text-sm text-red-800 space-y-3">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>Proses verifikasi memakan waktu maksimal <strong>2x24 jam kerja</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>Anda akan menerima notifikasi email ketika verifikasi selesai</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>Pastikan email Anda aktif untuk menerima notifikasi</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mt-0.5 mr-3 text-red-600 text-lg"></i>
                                    <span>Jika ada pertanyaan, hubungi support kami</span>
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
                                    <h2 class="text-lg font-semibold text-white m-0">Data Pribadi Owner</h2>
                                    <p class="text-sm text-white m-0">Informasi identitas pemilik usaha</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Lengkap Owner</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->owner_name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor HP/WhatsApp</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->owner_phone }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Owner</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->owner_email }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor KTP</label>
                                    <p class="text-gray-900 font-semibold text-base font-mono">{{ $verification->ktp_number_decrypted ?? '****************' }}</p>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Foto KTP</label>
                                    <div class="relative group inline-block">
                                        <img src="{{ route('owner.user-owner.verification.ktp-image') }}" 
                                             alt="Foto KTP" 
                                             class="max-w-md w-full rounded-2xl shadow-md border-2 border-gray-200 cursor-pointer hover:shadow-md transition-all duration-300 hover:scale-[1.02]"
                                             onclick="openImageModal(this.src, 'Foto KTP')">
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
                                    <h2 class="text-lg font-semibold text-white m-0">Informasi Usaha</h2>
                                    <p class="text-sm text-white m-0">Detail bisnis dan lokasi</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Usaha</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->business_name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Usaha</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ ($verification->businessCategory)->name ?? '-' }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Telepon/WhatsApp Bisnis</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->business_phone }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Bisnis</label>
                                    <p class="text-gray-900 font-semibold text-base">{{ $verification->business_email ?: '-' }}</p>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Lengkap Usaha</label>
                                    <p class="text-gray-900 font-medium text-base leading-relaxed">{{ $verification->business_address }}</p>
                                </div>
                                @if($verification->business_logo_path)
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Logo Usaha</label>
                                        <div class="relative group inline-block">
                                             <img src="{{ asset('storage/' . $verification->business_logo_path) }}" 
                                                 alt="Logo Usaha" 
                                                 class="max-w-md w-full rounded-2xl shadow-md border-2 border-gray-200 cursor-pointer hover:shadow-md transition-all duration-300 hover:scale-[1.02]"
                                                 onclick="openImageModal(this.src, 'Logo Usaha')">
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
                        Ajukan Verifikasi Ulang
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
    

    @push('styles')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }

        .animate-zoomIn {
            animation: zoomIn 0.3s ease-out;
        }
    </style>
    @endpush

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

        // Auto refresh every 30 seconds if status is pending
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
        }, 30000); // 30 seconds
        @endif

        document.addEventListener('DOMContentLoaded', function () {
        // Cek apakah ada flash message 'success' dari controller
        @if (session('success'))
            Swal.fire({
                title: 'Verifikasi Terkirim!',
                icon: 'success',
                html: `
                            <div class="text-left text-gray-700 leading-relaxed px-4">
                                <p class="mb-3">
                                    Data verifikasi Anda telah berhasil dikirim.
                                    Kami akan segera memprosesnya (maks. 2x24 jam kerja).
                                </p>
                                <p class="font-medium">
                                    Status verifikasi akan kami informasikan melalui email ke:
                                    <strong class="text-gray-900">{{ auth()->guard('owner')->user()->email }}</strong>
                                </p>
                                <br>
                                <p class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-3 rounded-lg">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Harap <strong>periksa email Anda secara berkala</strong>,
                                    termasuk folder <strong>Spam</strong>.
                                </p>
                            </div>
                        `,
                confirmButtonText: 'Baik, Mengerti',
                confirmButtonColor: '#8c1000', // Sesuai tema warna Anda
                customClass: {
                    popup: 'rounded-2xl',
                }
            });

        @endif
        });
    </script>
    @endpush
@endsection

