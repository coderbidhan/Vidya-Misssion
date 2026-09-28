<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seed the core subjects for West Bengal curriculum (WBBSE & WBCHSE).
     */
    public function run(): void
    {
        $subjects = [
            [
                'id' => 1,
                'name' => 'বাংলা (Bengali)',
                'color' => '#EF4444',
                'icon' => 'https://img.icons8.com/fluency/96/book.png',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'English',
                'color' => '#3B82F6',
                'icon' => 'https://img.icons8.com/fluency/96/alphabet.png',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'গণিত (Mathematics)',
                'color' => '#8B5CF6',
                'icon' => 'https://img.icons8.com/fluency/96/calculator.png',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'name' => 'ইতিহাস (History)',
                'color' => '#D97706',
                'icon' => 'https://img.icons8.com/fluency/96/parchment.png',
                'image' => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=600&q=80',
                'sort_order' => 4,
            ],
            [
                'id' => 5,
                'name' => 'ভূগোল (Geography)',
                'color' => '#F97316',
                'icon' => 'https://img.icons8.com/fluency/96/globe.png',
                'image' => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?w=600&q=80',
                'sort_order' => 5,
            ],
            [
                'id' => 6,
                'name' => 'আমাদের পরিবেশ (Environmental Studies)',
                'color' => '#10B981',
                'icon' => 'https://img.icons8.com/fluency/96/forest.png',
                'image' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?w=600&q=80',
                'sort_order' => 6,
            ],
            [
                'id' => 7,
                'name' => 'পরিবেশ ও বিজ্ঞান (Science & Environment)',
                'color' => '#14B8A6',
                'icon' => 'https://img.icons8.com/fluency/96/test-tube.png',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'sort_order' => 7,
            ],
            [
                'id' => 8,
                'name' => 'ভৌত বিজ্ঞান (Physical Science)',
                'color' => '#22C55E',
                'icon' => 'https://img.icons8.com/fluency/96/physics.png',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'sort_order' => 8,
            ],
            [
                'id' => 9,
                'name' => 'জীবন বিজ্ঞান (Life Science)',
                'color' => '#06B6D4',
                'icon' => 'https://img.icons8.com/fluency/96/microscope.png',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'sort_order' => 9,
            ],
            [
                'id' => 10,
                'name' => 'পদার্থবিদ্যা (Physics)',
                'color' => '#6366F1',
                'icon' => 'https://img.icons8.com/fluency/96/atom.png',
                'image' => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?w=600&q=80',
                'sort_order' => 10,
            ],
            [
                'id' => 11,
                'name' => 'রসায়ন (Chemistry)',
                'color' => '#EC4899',
                'icon' => 'https://img.icons8.com/fluency/96/potion.png',
                'image' => 'https://images.unsplash.com/photo-1603126857599-f6e157fa2fe6?w=600&q=80',
                'sort_order' => 11,
            ],
            [
                'id' => 12,
                'name' => 'জীববিদ্যা (Biological Sciences)',
                'color' => '#84CC16',
                'icon' => 'https://img.icons8.com/fluency/96/dna-helix.png',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'sort_order' => 12,
            ],
            [
                'id' => 13,
                'name' => 'কম্পিউটার অ্যাপ্লিকেশন (Computer Application)',
                'color' => '#0EA5E9',
                'icon' => 'https://img.icons8.com/fluency/96/laptop.png',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&q=80',
                'sort_order' => 13,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['id' => $subject['id']],
                $subject + ['is_active' => true]
            );
        }

        $this->command->info(count($subjects).' subjects seeded for West Bengal curriculum.');
    }
}
