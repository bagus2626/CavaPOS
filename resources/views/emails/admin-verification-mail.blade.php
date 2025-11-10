<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftar Verifikasi Baru</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4;">

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 600px; margin: 20px auto; border-collapse: collapse;">

        <tr>
            <td
                style="background-color: #8c1000; padding: 30px 40px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">
                    Verifikasi Owner Baru
                </h1>
            </td>
        </tr>

        <tr>
            <td
                style="background-color: #ffffff; padding: 40px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                <h2 style="font-size: 20px; color: #333333; margin-top: 0; margin-bottom: 20px;">
                    Halo Admin,
                </h2>
                <p style="color: #555555; margin-bottom: 25px;">
                    Seorang owner baru telah mengirimkan data verifikasi mereka dan menunggu persetujuan Anda.
                </p>

                <div
                    style="background-color: #f9f9f9; border: 1px solid #eeeeee; border-radius: 5px; padding: 20px; margin-bottom: 25px;">
                    <ul style="list-style-type: none; padding-left: 0; margin: 0;">
                        <li style="margin-bottom: 12px; color: #555555;">
                            <strong style="color: #333333; min-width: 80px; display: inline-block;">Nama:</strong>
                            {{ $owner->name }}
                        </li>
                        <li style="color: #555555;">
                            <strong style="color: #333333; min-width: 80px; display: inline-block;">Email:</strong>
                            {{ $owner->email }}
                        </li>
                    </ul>
                </div>

                <p style="color: #555555; margin-bottom: 30px;">
                    Silakan tinjau pengajuan mereka di panel admin untuk memverifikasi atau menolak.
                </p>

                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="center">
                            <a href="{{ route('admin.owner-verification.show', $verification->id) }}"
                                style="background-color: #8c1000; color: #ffffff; padding: 14px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px;">
                                Review Pengajuan
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 40px; text-align: center; color: #999999; font-size: 12px;">
                <p style="margin: 0;">© {{ date('Y') }} NamaPerusahaanAnda. Semua hak cipta dilindungi.</p>
                <p style="margin: 5px 0 0 0;">Ini adalah email otomatis, mohon untuk tidak membalas.</p>
            </td>
        </tr>

    </table>

</body>

</html>