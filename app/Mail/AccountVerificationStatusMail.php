<?php

namespace App\Mail;

use App\Models\Owner\OwnerVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountVerificationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;
    public $verification;

    /**
     * Create a new message instance.
     */
    public function __construct($owner, $verification)
    {
        $this->owner = $owner;
        $this->verification = $verification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Buat subjek dinamis
        $subject = 'Update Status Verifikasi Akun Anda';
        if ($this->owner->verification_status == 'approved') {
            $subject = 'Selamat, ' . $this->owner->name . '! Akun Anda Telah Disetujui';
        } elseif ($this->owner->verification_status == 'rejected') {
            $subject = 'Informasi Penting Mengenai Verifikasi Akun Anda';
        }

        return new Envelope(
            subject: $subject, // <-- GUNAKAN SUBJEK DINAMIS
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.owner-verification-status', // Versi HTML
            text: 'emails.owner-verification-status2', // <-- TAMBAHKAN INI
            with: [
                'owner' => $this->owner,
                'verification' => $this->verification,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
