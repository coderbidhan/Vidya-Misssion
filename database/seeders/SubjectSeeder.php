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
            ['name' => 'Bengali',       'color' => '#EF4444', 'icon' => 'https://img.icons8.com/fluency/96/book.png',       'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&q=80', 'sort_order' => 1],
            ['name' => 'English',       'color' => '#3B82F6', 'icon' => 'https://img.icons8.com/fluency/96/alphabet.png',   'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=400&q=80', 'sort_order' => 2],
            ['name' => 'History',       'color' => '#84CC16', 'icon' => 'https://img.icons8.com/fluency/96/parchment.png',  'image' => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=400&q=80', 'sort_order' => 3],
            ['name' => 'Mathematics',   'color' => '#8B5CF6', 'icon' => 'https://img.icons8.com/fluency/96/calculator.png', 'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=400&q=80', 'sort_order' => 4],
            ['name' => 'Geography',     'color' => '#F97316', 'icon' => 'https://img.icons8.com/fluency/96/globe.png',      'image' => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?w=400&q=80', 'sort_order' => 5],
            ['name' => 'Physical Science', 'color' => '#22C55E', 'icon' => 'https://img.icons8.com/fluency/96/physics.png',    'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=400&q=80', 'sort_order' => 6],
            ['name' => 'Life Science',  'color' => '#06B6D4', 'icon' => 'https://img.icons8.com/fluency/96/microscope.png', 'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=400&q=80', 'sort_order' => 7],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(['name' => $subject['name']], $subject + ['is_active' => true]);
        }

        $this->command->info('7 subjects seeded.');
    }
}
