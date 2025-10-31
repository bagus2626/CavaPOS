<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi Akun</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                    style="border-collapse: collapse; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 8px; overflow: hidden;">

                    <tr>
                        <td align="center" style="background-color: #8c1000; padding: 30px 20px;">
                            <h1 style="color: #ffffff; margin: 0; font-family: Arial, sans-serif; font-size: 24px;">
                                @if($owner->verification_status == 'approved')
                                    Verifikasi Akun Disetujui
                                @elseif($owner->verification_status == 'rejected')
                                    Verifikasi Akun Ditolak
                                @else
                                    Status Verifikasi Akun
                                @endif
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="padding: 40px 30px; color: #333333; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6;">

                            @if($owner->verification_status == 'approved')
                                <h2 style="margin-top: 0; color: #333333;">Selamat, {{ $owner->name }}!</h2>
                            @else
                                <h2 style="margin-top: 0; color: #333333;">Halo, {{ $owner->name }}.</h2>
                            @endif

                            @if($owner->verification_status == 'approved')
                                <p style="margin: 20px 0;">
                                    Kabar baik! Pengajuan verifikasi akun dan bisnis Anda telah kami setujui.
                            @elseif($owner->verification_status == 'rejected')
                                    <p style="margin: 20px 0;">
                                        Setelah peninjauan, kami mohon maaf untuk memberitahukan bahwa pengajuan verifikasi Anda
                                        belum dapat kami setujui.
                                @else
                                    <p style="margin: 20px 0;">
                                        Terima kasih telah mengirimkan data verifikasi Anda.
                                @endif
                                Status verifikasi Anda saat ini adalah:
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse;">
                                <tr>
                                    @if($owner->verification_status == 'approved')
                                        <td align="center"
                                            style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 5px; padding: 20px;">
                                            <strong style="color: #16a34a; font-size: 20px; text-transform: capitalize;">
                                                Disetujui (Approved)
                                            </strong>
                                        </td>
                                    @elseif($owner->verification_status == 'rejected')
                                        <td align="center"
                                            style="background-color: #fff1f2; border: 1px solid #ffdde0; border-radius: 5px; padding: 20px;">
                                            <strong style="color: #dc2626; font-size: 20px; text-transform: capitalize;">
                                                Ditolak (Rejected)
                                            </strong>
                                        </td>
                                    @else
                                        <td align="center"
                                            style="background-color: #f9f9f9; border: 1px solid #eeeeee; border-radius: 5px; padding: 20px;">
                                            <strong style="color: #8c1000; font-size: 20px; text-transform: capitalize;">
                                                {{ $owner->verification_status }}
                                            </strong>
                                        </td>
                                    @endif
                                </tr>
                            </table>

                            @if($owner->verification_status == 'rejected')
                                <p style="margin: 20px 0 0;">
                                    <strong>Alasan Penolakan:</strong>
                                </p>
                                <div
                                    style="background-color: #f9f9f9; border-left: 4px solid #8c1000; padding: 15px 20px; color: #555555; margin-top: 10px; font-style: italic;">
                                    {{-- Pastikan $verification (dengan rejection_reason) dikirim ke Mailable --}}
                                    {{ $verification->rejection_reason ?? 'Silakan periksa halaman verifikasi di akun Anda untuk detail.' }}
                                </div>
                            @endif


                            @if($owner->verification_status == 'approved')
                                <p style="margin: 30px 0 30px; text-align: center;">
                                    Anda sekarang memiliki akses penuh ke semua fitur. Silakan masuk ke dashboard Anda.
                                </p>
                            @elseif($owner->verification_status == 'rejected')
                                <p style="margin: 30px 0 30px; text-align: center;">
                                    Silakan perbarui data Anda dan kirimkan kembali formulir verifikasi Anda.
                                </p>
                            @else
                                <p style="margin: 30px 0 30px; text-align: center;">
                                    Anda dapat memeriksa detail pengajuan Anda kapan saja dengan mengeklik tombol di bawah
                                    ini:
                                </p>
                            @endif

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="border-collapse: collapse;">
                                <tr>
                                    <td align="center">
                                        @if($owner->verification_status == 'approved')
                                            <a href="{{ route('owner.user-owner.dashboard') }}" target="_blank"
                                                style="background-color: #8c1000; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-family: Arial, sans-serif; font-weight: bold; display: inline-block;">
                                                Masuk ke Dashboard
                                            </a>
                                        @elseif($owner->verification_status == 'rejected')
                                            <a href="{{ route('owner.user-owner.verification.index') }}" target="_blank"
                                                style="background-color: #8c1000; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-family: Arial, sans-serif; font-weight: bold; display: inline-block;">
                                                Ajukan Ulang Verifikasi
                                            </a>
                                        @else
                                            <a href="{{ route('owner.user-owner.verification.status') }}" target="_blank"
                                                style="background-color: #8c1000; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-family: Arial, sans-serif; font-weight: bold; display: inline-block;">
                                                Lihat Status Verifikasi
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if($owner->verification_status != 'approved')
                                <p style="margin: 30px 0 0;">
                                    Terima kasih sudah bergabung dengan kami!
                                </p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="background-color: #f9f9f9; padding: 30px 30px; text-align: center; color: #777777; font-family: Arial, sans-serif; font-size: 12px;">
                            <p style="margin: 0;">&copy; {{ date('Y') }} Cavaa. All rights reserved.</p>
                            <p style="margin: 5px 0 0;">Email ini dikirim secara otomatis, mohon untuk tidak membalas.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>