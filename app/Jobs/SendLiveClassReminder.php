<?php

namespace App\Jobs;

use App\Mail\LiveClassReminderMail;
use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLiveClassReminder implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    /**
     * Execute the job.
     * Sends a reminder 30 minutes before each scheduled live class.
     * Runs every minute via the scheduler.
     */
    public function handle(): void
    {
        $windowStart = now()->addMinutes(29);
        $windowEnd = now()->addMinutes(31);

        LiveClass::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
            ->with(['teacher:id,name,email', 'chapter.subject'])
            ->chunk(50, function ($classes) {
                foreach ($classes as $class) {
                    // Get students enrolled (those who have accessed this chapter's content)
                    $subject = $class->chapter->subject;

                    // Send to students who are in trial or subscribed
                    User::where('role', 'student')
                        ->where('is_active', true)
                        ->whereNotNull('trial_starts_at')
                        ->chunk(100, function ($students) use ($class) {
                            foreach ($students as $student) {
                                Mail::to($student->email)->send(new LiveClassReminderMail($student, $class));
                            }
                        });
                }
            });
    }
}
