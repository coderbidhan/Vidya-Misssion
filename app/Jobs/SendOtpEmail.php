<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\User  $user  The user to send the OTP to.
     * @param  string            $otp   The plain-text OTP (before hashing).
     */
    public function __construct(
        public readonly User $user,
        public readonly string $otp
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new OtpMail($this->user, $this->otp));
    }
}
