<?php

namespace Database\Seeders\Concerns;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\Question;
use App\Models\RecordedClass;
use App\Models\Subject;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait SeedsCurriculumContent
{
    /**
     * Ensure a verified teacher exists to author learning materials.
     */
    protected function getOrCreateTeacher(): User
    {
        return User::updateOrCreate(
            ['email' => 'teacher@vidyamission.com'],
            [
                'name' => 'Dr. Sourav Sen',
                'mobile' => '9876543220',
                'password' => Hash::make('Teacher@2024'),
                'role' => 'teacher',
                'is_verified' => true,
                'is_active' => true,
            ]
        );
    }

    /**
     * Resolve a subject by its English or Bengali name.
     */
    protected function resolveSubject(string $name): Subject
    {
        $subject = Subject::where('name', $name)
            ->orWhere('name', 'like', "%({$name})%")
            ->orWhere('name', 'like', "%{$name}%")
            ->first();

        if (! $subject) {
            $subject = Subject::create([
                'name' => $name,
                'color' => '#6D4AFF',
                'icon' => 'book-default',
                'sort_order' => 20,
                'is_active' => true,
            ]);
        }

        return $subject;
    }

    /**
     * Seed a full curriculum unit (Chapter + Notes + Recorded Class + Live Class + Test + Questions).
     */
    protected function seedCurriculumUnit(
        int $classLevel,
        string $subjectName,
        array $chapterData,
        User $teacher
    ): Chapter {
        $subject = $this->resolveSubject($subjectName);

        // 1. Ensure ClassSubject mapping exists
        ClassSubject::firstOrCreate([
            'class_level' => $classLevel,
            'subject_id' => $subject->id,
        ]);

        // 2. Ensure Chapter exists
        $chapter = Chapter::updateOrCreate(
            [
                'subject_id' => $subject->id,
                'class_level' => $classLevel,
                'title' => $chapterData['title'],
            ],
            [
                'sort_order' => $chapterData['sort_order'] ?? 1,
                'is_active' => true,
            ]
        );

        // 3. Notes
        if (! empty($chapterData['notes'])) {
            $notesData = $chapterData['notes'];
            Note::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $notesData['title'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => $notesData['description'] ?? 'Comprehensive chapter notes and study summary.',
                    'file_url' => $notesData['file_url'] ?? 'https://vidyamission.com/materials/notes/sample_chapter.pdf',
                    'file_type' => $notesData['file_type'] ?? 'pdf',
                    'is_active' => true,
                ]
            );
        }

        // 4. Recorded Video Class
        if (! empty($chapterData['recorded_class'])) {
            $rcData = $chapterData['recorded_class'];
            $videoUrl = $rcData['video_url'] ?? null;
            $videoId = $rcData['video_id'] ?? ($videoUrl ? \App\Services\YouTubeService::extractVideoId($videoUrl) : null);
            if (empty($videoUrl) && $videoId) {
                $videoUrl = "https://www.youtube.com/watch?v={$videoId}";
            }

            RecordedClass::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $rcData['title'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => $rcData['description'] ?? 'Video explanation covering chapter concepts and exercises.',
                    'video_url' => $videoUrl,
                    'gdrive_file_id' => $videoId,
                    'gdrive_embed_url' => $videoId ? "https://www.youtube.com/embed/{$videoId}" : null,
                    'duration_seconds' => $rcData['duration_seconds'] ?? 1800,
                    'thumbnail_url' => $rcData['thumbnail_url'] ?? ($videoId ? \App\Services\YouTubeService::toThumbnailUrl($videoId) : null),
                    'is_active' => true,
                ]
            );
        }

        // 5. Live Class
        if (! empty($chapterData['live_class'])) {
            $lcData = $chapterData['live_class'];
            LiveClass::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $lcData['title'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => $lcData['description'] ?? 'Interactive live doubt clearance and discussion.',
                    'platform' => $lcData['platform'] ?? 'google_meet',
                    'meeting_url' => $lcData['meeting_url'] ?? 'https://meet.google.com/vid-miss-edu',
                    'scheduled_at' => now()->addDays(rand(1, 7))->setTime(17, 0),
                    'duration_minutes' => $lcData['duration_minutes'] ?? 60,
                    'status' => 'scheduled',
                ]
            );
        }

        // 6. Test & Questions
        if (! empty($chapterData['test'])) {
            $testData = $chapterData['test'];
            $questions = $testData['questions'] ?? [];

            $test = Test::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $testData['title'],
                ],
                [
                    'total_marks' => count($questions) ?: 10,
                    'pass_marks' => max(1, (int) round(count($questions) * 0.4)),
                    'duration_minutes' => $testData['duration_minutes'] ?? 20,
                    'is_active' => true,
                ]
            );

            foreach ($questions as $qIdx => $q) {
                Question::updateOrCreate(
                    [
                        'test_id' => $test->id,
                        'question_text' => $q['question_text'],
                    ],
                    [
                        'option_a' => $q['option_a'],
                        'option_b' => $q['option_b'],
                        'option_c' => $q['option_c'],
                        'option_d' => $q['option_d'],
                        'correct_option' => $q['correct_option'],
                        'marks' => $q['marks'] ?? 1,
                        'sort_order' => $qIdx + 1,
                    ]
                );
            }
        }

        return $chapter;
    }
}
