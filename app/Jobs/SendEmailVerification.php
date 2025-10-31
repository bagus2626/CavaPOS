<?php

namespace App\Jobs;

use App\Mail\AccountVerificationStatusMail;
use App\Models\Owner\OwnerVerification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailVerification implements ShouldQueue
{
    use Queueable;

    protected $owner;
    protected $verification;
    /**
     * Create a new job instance.
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
        Mail::to($this->owner->email)->send(new AccountVerificationStatusMail($this->owner, $this->verification));
    }
}
