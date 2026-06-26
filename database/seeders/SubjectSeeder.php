<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seed the 7 subjects for West Bengal curriculum.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'Bengali',       'color' => '#EF4444', 'icon' => 'book-bengali',    'sort_order' => 1],
            ['name' => 'English',       'color' => '#3B82F6', 'icon' => 'book-english',    'sort_order' => 2],
            ['name' => 'History',       'color' => '#84CC16', 'icon' => 'book-history',    'sort_order' => 3],
            ['name' => 'Mathematics',   'color' => '#8B5CF6', 'icon' => 'book-math',       'sort_order' => 4],
            ['name' => 'Geography',     'color' => '#F97316', 'icon' => 'book-geography',  'sort_order' => 5],
            ['name' => 'Physical Science', 'color' => '#22C55E', 'icon' => 'book-physics',    'sort_order' => 6],
            ['name' => 'Life Science',  'color' => '#06B6D4', 'icon' => 'book-science',    'sort_order' => 7],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['name' => $subject['name']], $subject + ['is_active' => true]);
        }

        $this->command->info('7 subjects seeded.');
    }
}
