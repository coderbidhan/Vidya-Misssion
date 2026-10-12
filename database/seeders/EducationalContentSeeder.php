<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\ClassSubject;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\Question;
use App\Models\RecordedClass;
use App\Models\Subject;
use App\Models\Test;
use Illuminate\Database\Seeder;

class EducationalContentSeeder extends Seeder
{
    /**
     * Seed all educational curriculum content for Classes 1 through 10.
     * Guaranteed to be idempotent, non-destructive, and safe for production staging.
     */
    public function run(): void
    {
        $this->command->info('Starting educational content seeding for Classes 1 to 10...');

        $this->call([
            SubjectSeeder::class,
            Class1To4MaterialSeeder::class,
            Class5To9MaterialSeeder::class,
            Class10MaterialSeeder::class,
        ]);

        $this->command->info('Educational Content Seeding Summary:');
        $this->command->table(
            ['Entity', 'Total Records'],
            [
                ['Subjects', Subject::count()],
                ['Class-Subject Mappings', ClassSubject::count()],
                ['Chapters (Classes 1–10)', Chapter::whereBetween('class_level', [1, 10])->count()],
                ['Study Notes', Note::count()],
                ['Recorded Video Classes', RecordedClass::count()],
                ['Live Classes', LiveClass::count()],
                ['Practice Tests', Test::count()],
                ['Assessment Questions', Question::count()],
            ]
        );
    }
}
