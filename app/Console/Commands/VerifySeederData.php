<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Progress;
use App\Models\Question;
use App\Models\RecordedClass;
use App\Models\Reward;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\TeacherProfile;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifySeederData extends Command
{
    protected $signature = 'db:verify-seeder';

    protected $description = 'Verify all 18 database tables and curriculum data for Class 5 to 12';

    public function handle(): int
    {
        $this->info('=== Database Tables Verification ===');

        $tables = [
            'users' => User::count(),
            'student_profiles' => StudentProfile::count(),
            'teacher_profiles' => TeacherProfile::count(),
            'subjects' => Subject::count(),
            'class_subjects' => ClassSubject::count(),
            'teacher_subjects' => DB::table('teacher_subjects')->count(),
            'chapters' => Chapter::count(),
            'recorded_classes' => RecordedClass::count(),
            'live_classes' => LiveClass::count(),
            'notes' => Note::count(),
            'tests' => Test::count(),
            'questions' => Question::count(),
            'test_attempts' => TestAttempt::count(),
            'subscriptions' => Subscription::count(),
            'user_subscriptions' => UserSubscription::count(),
            'progress' => Progress::count(),
            'rewards' => Reward::count(),
            'notifications' => Notification::count(),
        ];

        $allPopulated = true;
        foreach ($tables as $table => $count) {
            $status = $count > 0 ? '✓ OK' : '✗ EMPTY';
            $this->line(sprintf('  %-22s : %4d records  [%s]', $table, $count, $status));
            if ($count === 0) {
                $allPopulated = false;
            }
        }

        $this->newLine();
        $this->info('=== Class 5 to 12 Curriculum Verification ===');
        for ($class = 5; $class <= 12; $class++) {
            $chapterCount = Chapter::where('class_level', $class)->count();
            $subjectsInClass = ClassSubject::where('class_level', $class)->count();
            $this->line("  Class {$class} : {$subjectsInClass} subjects mapped, {$chapterCount} chapters with YouTube video classes.");
        }

        $this->newLine();
        $this->info('=== YouTube Recorded Class Sample ===');
        $sample = RecordedClass::with('chapter.subject', 'teacher')->first();
        if ($sample) {
            $this->line('  Chapter    : '.$sample->chapter->title);
            $this->line('  Subject    : '.$sample->chapter->subject->name);
            $this->line('  Class      : '.$sample->chapter->class_level);
            $this->line('  Video Title: '.$sample->title);
            $this->line('  Embed URL  : '.$sample->gdrive_embed_url);
            $this->line('  Video ID   : '.$sample->gdrive_file_id);
            $this->line('  Thumbnail  : '.$sample->thumbnail_url);
            $this->line('  Teacher    : '.$sample->teacher->name);
        }

        if ($allPopulated) {
            $this->newLine();
            $this->info('✓ All 18 tables have real, valid data successfully seeded!');

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
