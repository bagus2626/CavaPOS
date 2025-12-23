<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @php
        $data = session('employee_suspended_data', []);
        $suspendedBy = $data['suspended_by'] ?? 'unknown';
        $partnerName = $data['partner_name'] ?? 'Unknown';
        $ownerName = $data['owner_name'] ?? 'Unknown';
        $employeeName = $data['employee_name'] ?? 'Unknown';
        $deactivationReason = $data['deactivation_reason'] ?? null;
    @endphp

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Icon -->
                <div class="mb-6">
                    <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Layanan Tidak Tersedia
                </h1>

                <!-- Message -->
                <div class="text-gray-600 mb-6 break-words" style="word-break: break-word; overflow-wrap: break-word;">
                    @if($suspendedBy == 'admin')
                        @if($deactivationReason)
                            <p> Akun Anda ditangguhkan oleh administrator sistem.<br>
                                <strong>Alasan Penonaktifan:</strong> {{ $deactivationReason }}
                            </p>
                        @else
                            <p><strong>{{ $partnerName }}</strong> ditangguhkan sementara oleh administrator sistem dan Anda tidak dapat mengakses sistem saat ini.</p>
                        @endif
                    @elseif($suspendedBy == 'partner')
                        <p>Akun Anda telah dinonaktifkan sementara oleh partner. Anda tidak dapat mengakses sistem untuk sementara.</p>
                    @else
                        <p><strong>{{ $partnerName }}</strong> telah dinonaktifkan sementara oleh owner. Anda tidak dapat mengakses sistem untuk sementara.</p>
                    @endif
                </div>

                <!-- Info Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-yellow-800">
                        <strong>Informasi:</strong> 
                        @if($suspendedBy == 'admin')
                            Silakan hubungi administrator sistem untuk mengaktifkan kembali akses layanan.
                        @elseif($suspendedBy == 'partner')
                            Silakan hubungi partner outlet Anda untuk mengaktifkan kembali akses layanan.
                        @else
                            Silakan hubungi owner outlet Anda untuk mengaktifkan kembali akses layanan.
                        @endif
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <form action="{{ route('employee.logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                            Keluar dari Akun
                        </button>
                    </form>

                    <a href="/" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition duration-200">
                        Kembali ke Beranda
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        Butuh bantuan? Hubungi support kami
                    </p>
                    <p class="text-sm text-blue-600 font-medium mt-1">
                        developer@vastech.co.id
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>