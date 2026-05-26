<?php

namespace App\Jobs;

use App\Mail\SubscriptionConfirmationMail;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSubscriptionConfirmation implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly UserSubscription $userSubscription
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new SubscriptionConfirmationMail($this->user, $this->userSubscription));
    }
}
