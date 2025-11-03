<?php

namespace App\Jobs;

use App\Mail\AdminVerificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminEmailVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $owner;
    protected $verification;

    /**
     * Create a new job instance.
     * Ambil semua data yang dibutuhkan oleh Mailable
     */
    public function __construct($owner, $verification)
    {
        $this->owner = $owner;
        $this->verification = $verification;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // sleep(10);
        Mail::send(new AdminVerificationMail(
            $this->owner,
            $this->verification
        ));
    }
}
