<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Meja - {{ $partner->name }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col justify-center items-center p-4">
        {{-- Logo Outlet --}}
        @if($partner && $partner->logo)
            <div class="flex flex-col items-center mb-8">
                <span class="text-sm font-semibold text-gray-500 mt-2">
                    Selamat datang di
                </span>
                <p class="text-2xl md:text-3xl font-bold text-gray-800">
                    {{ $partner->name }}
                </p>
            </div>
        @endif

        {{-- Status Card --}}
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6 md:p-8">
            @php
                $statusConfig = match($table->status) {
                    'occupied' => [
                        'icon' => 'fas fa-users',
                        'color' => 'bg-orange-500',
                        'title' => 'Meja Sedang Sibuk',
                        'description' => 'Mohon maaf, meja ini sedang sibuk untuk sementara waktu. Silakan pilih meja lain atau tunggu hingga meja tersedia.',
                        'iconBg' => 'bg-orange-100 text-orange-600',
                        'badge' => 'Sedang Sibuk'
                    ],
                    'reserved' => [
                        'icon' => 'fas fa-calendar-check',
                        'color' => 'bg-blue-500',
                        'title' => 'Meja Sudah Dipesan',
                        'description' => 'Meja ini telah direservasi oleh pelanggan lain. Silakan pilih meja yang tersedia atau hubungi staff untuk bantuan.',
                        'iconBg' => 'bg-blue-100 text-blue-600',
                        'badge' => 'Sudah Dipesan'
                    ],
                    'not_available' => [
                        'icon' => 'fas fa-ban',
                        'color' => 'bg-red-500',
                        'title' => 'Meja Tidak Tersedia',
                        'description' => 'Mohon maaf, meja ini sedang dalam pemeliharaan atau tidak tersedia. Silakan pilih meja lain atau hubungi staff kami.',
                        'iconBg' => 'bg-red-100 text-red-600',
                        'badge' => 'Tidak Tersedia'
                    ],
                    default => [
                        'icon' => 'fas fa-exclamation-triangle',
                        'color' => 'bg-gray-500',
                        'title' => 'Meja Tidak Dapat Digunakan',
                        'description' => 'Mohon maaf, meja ini tidak dapat digunakan saat ini. Silakan hubungi staff untuk informasi lebih lanjut.',
                        'iconBg' => 'bg-gray-100 text-gray-600',
                        'badge' => 'Tidak Tersedia'
                    ]
                };
            @endphp

            {{-- Icon --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 {{ $statusConfig['iconBg'] }} rounded-full flex items-center justify-center">
                    <i class="{{ $statusConfig['icon'] }} text-4xl"></i>
                </div>
            </div>

            {{-- Title --}}
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-3">
                {{ $statusConfig['title'] }}
            </h2>

            {{-- Table Info --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 text-sm">Nomor Meja</span>
                    <span class="font-semibold text-gray-800">{{ $table->table_no }}</span>
                </div>
                @if($table->table_class)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Kelas Meja</span>
                    <span class="font-semibold text-gray-800">{{ $table->table_class }}</span>
                </div>
                @endif
            </div>

            {{-- Description --}}
            <p class="text-gray-600 text-center mb-6">
                {{ $statusConfig['description'] }}
            </p>

            {{-- Status Badge --}}
            <div class="flex justify-center mb-6">
                <span class="{{ $statusConfig['color'] }} text-white px-4 py-2 rounded-full text-sm font-semibold">
                    {{ $statusConfig['badge'] }}
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-3">
                {{-- Refresh Button --}}
                <button onclick="location.reload()" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Cek Lagi
                </button>

                {{-- Contact Staff Button (Optional) --}}
                @if($partner->pic_phone_number)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $partner->pic_phone_number) }}" 
                   target="_blank"
                   class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Hubungi Staff
                </a>
                @endif

                {{-- Back to Home --}}
                <a href="{{ url('/') }}" 
                   class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh setiap 30 detik (optional - uncomment jika diperlukan)
        // setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>