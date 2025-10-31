Halo, {{ $owner->name }}.

@if($owner->verification_status == 'approved')
    Verifikasi Akun Disetujui
    Selamat! Pengajuan verifikasi Anda telah kami setujui.
    Anda sekarang dapat masuk ke dashboard Anda: {{ route('owner.user-owner.dashboard') }}

@elseif($owner->verification_status == 'rejected')
    Verifikasi Akun Ditolak
    Mohon maaf, pengajuan verifikasi Anda ditolak.
    Alasan: {{ $verification->rejection_reason ?? 'Silakan periksa akun Anda.' }}
    Silakan ajukan ulang di sini: {{ route('owner.user-owner.verification.index') }}

@else
    Status Verifikasi Akun
    Terima kasih telah mendaftar. Status verifikasi Anda saat ini: {{ $owner->verification_status }}.
    Anda dapat melihat status di sini: {{ route('owner.user-owner.verification.status') }}
@endif

Terima kasih,
Tim Anda