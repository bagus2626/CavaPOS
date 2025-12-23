<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class CustomerVerifyEmail extends Notification
{
    use Queueable;

    protected string $partnerSlug;
    protected string $tableCode;

    public function __construct(string $partnerSlug, string $tableCode)
    {
        $this->partnerSlug = $partnerSlug;
        $this->tableCode   = $tableCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    protected function verificationUrl($notifiable)
    {
        // Signed URL ke route: customer.verification.verify
        return URL::temporarySignedRoute(
            'customer.verification.verify',
            now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'           => $notifiable->getKey(),
                'hash'         => sha1($notifiable->getEmailForVerification()),
                'partner_slug' => $this->partnerSlug,
                'table_code'   => $this->tableCode,
            ]
        );
    }

    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email')
            ->line('Terima kasih telah mendaftar. Silakan klik tombol di bawah ini untuk memverifikasi email Anda.')
            ->action('Verifikasi Email', $url)
            ->line('Jika Anda tidak merasa mendaftar pada layanan kami, abaikan email ini.');
    }
}
