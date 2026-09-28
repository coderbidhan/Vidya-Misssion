<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Notification;
use App\Models\Progress;
use App\Models\Reward;
use App\Models\Subscription;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Seeder;

class UserActivitySeeder extends Seeder
{
    /**
     * Run the database seeds for student user activity:
     * - user_subscriptions
     * - progress
     * - test_attempts
     * - rewards
     * - notifications
     */
    public function run(): void
    {
        // 1. Get subscriptions
        $monthlyPlan = Subscription::where('name', 'Monthly')->first();
        $quarterlyPlan = Subscription::where('name', 'Quarterly')->first();
        $annualPlan = Subscription::where('name', 'Annual')->first();

        // 2. Get students
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            $studentProfile = $student->studentProfile;
            $classLevel = $studentProfile ? (int) $studentProfile->class : 10;

            // Seed User Subscription for the student
            $selectedPlan = ($classLevel >= 10) ? ($annualPlan ?? $monthlyPlan) : ($quarterlyPlan ?? $monthlyPlan);
            if ($selectedPlan) {
                UserSubscription::updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'subscription_id' => $selectedPlan->id,
                    ],
                    [
                        'payment_id' => 'pay_wb_'.uniqid(),
                        'payment_gateway' => 'razorpay',
                        'payment_status' => 'completed',
                        'starts_at' => now()->subDays(rand(5, 20)),
                        'ends_at' => now()->addDays($selectedPlan->duration_days - 10),
                    ]
                );
            }

            // Find chapters for this student's class level
            $chapters = Chapter::where('class_level', $classLevel)->get();

            foreach ($chapters as $index => $chapter) {
                // Seed Progress
                $isCompleted = ($index === 0); // First chapter marked as complete
                Progress::updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'chapter_id' => $chapter->id,
                    ],
                    [
                        'is_completed' => $isCompleted,
                        'completed_at' => $isCompleted ? now()->subDays(2) : null,
                    ]
                );

                // Seed Rewards
                if ($isCompleted) {
                    Reward::updateOrCreate(
                        [
                            'user_id' => $student->id,
                            'chapter_id' => $chapter->id,
                            'reward_type' => 'chapter_complete',
                        ],
                        [
                            'reward_data' => [
                                'chapter_title' => $chapter->title,
                                'points' => 50,
                                'badge' => 'Chapter Champion',
                                'message' => 'Congratulations on completing '.$chapter->title.'!',
                            ],
                            'earned_at' => now()->subDays(2),
                        ]
                    );

                    Reward::updateOrCreate(
                        [
                            'user_id' => $student->id,
                            'chapter_id' => $chapter->id,
                            'reward_type' => 'video_watch_complete',
                        ],
                        [
                            'reward_data' => [
                                'chapter_title' => $chapter->title,
                                'points' => 10,
                                'message' => 'Completed YouTube video lecture for '.$chapter->title,
                            ],
                            'earned_at' => now()->subDays(3),
                        ]
                    );
                }

                // Seed Test Attempts
                $test = Test::where('chapter_id', $chapter->id)->first();
                if ($test && $isCompleted) {
                    $testQuestions = $test->questions;
                    $answers = [];
                    foreach ($testQuestions as $q) {
                        $answers[$q->id] = $q->correct_option;
                    }

                    TestAttempt::updateOrCreate(
                        [
                            'test_id' => $test->id,
                            'user_id' => $student->id,
                        ],
                        [
                            'score' => $test->total_marks,
                            'total_marks' => $test->total_marks,
                            'is_passed' => true,
                            'answers' => $answers,
                            'started_at' => now()->subDays(1)->subMinutes(20),
                            'completed_at' => now()->subDays(1),
                        ]
                    );

                    // Test pass reward
                    Reward::updateOrCreate(
                        [
                            'user_id' => $student->id,
                            'chapter_id' => $chapter->id,
                            'reward_type' => 'test_pass',
                        ],
                        [
                            'reward_data' => [
                                'test_title' => $test->title,
                                'score' => $test->total_marks,
                                'points' => 30,
                                'badge' => 'Quiz Master',
                                'message' => 'You scored full marks in '.$test->title.'!',
                            ],
                            'earned_at' => now()->subDays(1),
                        ]
                    );
                }
            }

            // Seed Notifications for Student
            Notification::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'title' => 'Welcome to VidyaMission!',
                ],
                [
                    'body' => 'Welcome '.$student->name.'! Your Class '.$classLevel.' West Bengal Board study dashboard is now ready.',
                    'type' => 'general',
                    'data' => ['class_level' => $classLevel],
                    'read_at' => now()->subDays(3),
                ]
            );

            Notification::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'title' => 'New Video Lecture Available',
                ],
                [
                    'body' => 'New video lesson with complete explanations has been uploaded for Class '.$classLevel.'.',
                    'type' => 'chapter_update',
                    'data' => ['class_level' => $classLevel],
                    'read_at' => null, // Unread notification
                ]
            );

            Notification::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'title' => 'Subscription Active',
                ],
                [
                    'body' => 'Your subscription is active. Enjoy unlimited access to recorded classes, live streams, notes, and tests.',
                    'type' => 'subscription',
                    'data' => ['plan' => $selectedPlan?->name ?? 'Standard'],
                    'read_at' => now()->subDays(1),
                ]
            );
        }

        // Also add notifications for Teachers and Admin
        $teachers = User::where('role', 'teacher')->get();
        foreach ($teachers as $teacher) {
            Notification::updateOrCreate(
                [
                    'user_id' => $teacher->id,
                    'title' => 'Upcoming Live Session Reminder',
                ],
                [
                    'body' => 'You have a scheduled live doubt-clearing session on VidyaMission this week.',
                    'type' => 'live_class',
                    'data' => ['status' => 'scheduled'],
                    'read_at' => null,
                ]
            );
        }

        $this->command->info('User subscriptions, progress, test attempts, rewards, and notifications seeded.');
    }
}
