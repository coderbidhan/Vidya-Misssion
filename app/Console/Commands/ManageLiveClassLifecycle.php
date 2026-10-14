<?php

namespace App\Console\Commands;

use App\Models\LiveClass;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class ManageLiveClassLifecycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live-classes:lifecycle {--auto-complete : Automatically complete sessions that have passed their scheduled end time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage live class lifecycle states, auto-completion, and scheduled reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running Live Class Lifecycle management...');

        $now = now();

        // 1. Auto-complete sessions that were started (status: 'live') and whose end time + 15 min grace period has elapsed
        $completedCount = 0;
        $liveClassesPastEnd = LiveClass::where('status', 'live')
            ->where('ends_at', '<', $now->subMinutes(15))
            ->get();

        foreach ($liveClassesPastEnd as $class) {
            $class->update(['status' => 'completed']);
            $completedCount++;
            $this->line("Auto-completed live class #{$class->id} ('{$class->title}')");
        }

        // 2. Report sessions requiring attention
        $attentionQuery = LiveClass::requiringAttention();
        $attentionCount = $attentionQuery->count();

        // 3. Ensure upcoming sessions (starting in next 15-30 minutes) have in-app student notifications created idempotently
        $startingSoon = LiveClass::where('status', 'scheduled')
            ->where('is_published', true)
            ->whereBetween('scheduled_at', [$now, $now->copy()->addMinutes(30)])
            ->get();

        $notificationsCreated = 0;
        foreach ($startingSoon as $class) {
            // Check student users for this class level
            $students = User::where('role', 'student')
                ->where('is_active', true)
                ->when($class->class_level, function ($q) use ($class) {
                    $q->whereHas('studentProfile', function ($sp) use ($class) {
                        $sp->where('class', 'like', "%{$class->class_level}%");
                    });
                })
                ->get();

            foreach ($students as $student) {
                // Idempotently check if notification already exists
                $alreadySent = Notification::where('user_id', $student->id)
                    ->where('type', 'live_class_reminder')
                    ->whereJsonContains('data->live_class_id', $class->id)
                    ->exists();

                if (! $alreadySent) {
                    Notification::create([
                        'user_id' => $student->id,
                        'title' => "Live Class Starting Soon: {$class->title}",
                        'body' => "Your live session starts at {$class->scheduled_at->format('h:i A')}. Get ready to join!",
                        'type' => 'live_class_reminder',
                        'data' => [
                            'live_class_id' => $class->id,
                            'scheduled_at' => $class->scheduled_at->toIso8601String(),
                            'platform' => $class->platform,
                        ],
                        'read_at' => null,
                    ]);
                    $notificationsCreated++;
                }
            }
        }

        $this->info("Summary: {$completedCount} sessions completed, {$attentionCount} sessions require attention, {$notificationsCreated} student reminders dispatched.");

        return Command::SUCCESS;
    }
}
