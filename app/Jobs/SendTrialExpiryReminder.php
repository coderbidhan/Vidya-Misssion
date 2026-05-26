<?php

namespace App\Jobs;

use App\Mail\TrialExpiryReminderMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTrialExpiryReminder implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 60;

    /**
     * Execute the job.
     * Finds all students whose trial ends tomorrow and sends a reminder email.
     */
    public function handle(): void
    {
        $tomorrow = now()->addDay();

        User::where('role', 'student')
            ->whereDate('trial_ends_at', $tomorrow->toDateString())
            ->whereDoesntHave('userSubscriptions', fn ($q) => $q->where('payment_status', 'completed')
                ->where('ends_at', '>=', now()))
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    Mail::to($user->email)->send(new TrialExpiryReminderMail($user));
                }
            });
    }
}
