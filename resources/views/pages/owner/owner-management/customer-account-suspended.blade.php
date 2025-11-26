<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @php
        $data = session('customer_suspended_data', []);
        $suspendedBy = $data['suspended_by'] ?? 'unknown';
        $partnerName = $data['partner_name'] ?? 'Unknown';
        $ownerName = $data['owner_name'] ?? 'Unknown';
    @endphp

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Icon -->
                <div class="mb-6">
                    <svg class="mx-auto h-24 w-24 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Layanan Sementara Tidak Tersedia
                </h1>

                <!-- Message -->
                <div class="text-gray-600 mb-6 break-words" style="word-break: break-word; overflow-wrap: break-word;">
                    <p>Maaf atas ketidaknyamanannya, <strong>{{ $partnerName }}</strong> untuk sementara tidak dapat menerima pesanan. Silakan coba lagi nanti.</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        @if($suspendedBy == 'admin')
                            Layanan akan tersedia kembali setelah administrator mengaktifkan akses layanan.
                        @else
                            Layanan akan segera tersedia kembali setelah owner mengaktifkan akses layanan.
                        @endif
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="/" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                        Kembali ke Beranda
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-400">
                        Jika Anda memiliki pertanyaan, silakan hubungi 
                        @if($suspendedBy == 'admin')
                            administrator sistem atau customer service kami.
                        @else
                            staff outlet atau customer service kami.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>