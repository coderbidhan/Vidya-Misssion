<?php

namespace Database\Seeders;

use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds for class_subjects and teacher_subjects.
     */
    public function run(): void
    {
        // 1. Mapping Class Levels to Subjects
        $classMappings = [
            5 => [1, 2, 3, 6],                      // Bengali, English, Math, Our Environment
            6 => [1, 2, 3, 4, 5, 7],                // Bengali, English, Math, History, Geography, Science
            7 => [1, 2, 3, 4, 5, 7],                // Bengali, English, Math, History, Geography, Science
            8 => [1, 2, 3, 4, 5, 7],                // Bengali, English, Math, History, Geography, Science
            9 => [1, 2, 3, 4, 5, 8, 9],             // Bengali, English, Math, History, Geography, Physical Sc, Life Sc
            10 => [1, 2, 3, 4, 5, 8, 9],            // Bengali, English, Math, History, Geography, Physical Sc, Life Sc
            11 => [1, 2, 3, 10, 11, 12, 13, 4, 5],  // Bengali, English, Math, Physics, Chemistry, Biology, Comp App, History, Geography
            12 => [1, 2, 3, 10, 11, 12, 13, 4, 5],  // Bengali, English, Math, Physics, Chemistry, Biology, Comp App, History, Geography
        ];

        foreach ($classMappings as $classLevel => $subjectIds) {
            foreach ($subjectIds as $subjectId) {
                if (Subject::where('id', $subjectId)->exists()) {
                    ClassSubject::updateOrCreate(
                        [
                            'class_level' => $classLevel,
                            'subject_id' => $subjectId,
                        ]
                    );
                }
            }
        }

        // 2. Assign Teachers to Subjects and Classes (teacher_subjects)
        $teacherAssignments = [
            'teacher@vidyamission.com' => [
                'subjects' => [8, 10], // Physical Science, Physics
                'classes' => [9, 10, 11, 12],
            ],
            'ananya.roy@vidyamission.com' => [
                'subjects' => [1], // Bengali
                'classes' => [5, 6, 7, 8, 9, 10, 11, 12],
            ],
            'subhash.das@vidyamission.com' => [
                'subjects' => [3], // Mathematics
                'classes' => [5, 6, 7, 8, 9, 10, 11, 12],
            ],
            'priya.banerjee@vidyamission.com' => [
                'subjects' => [2], // English
                'classes' => [5, 6, 7, 8, 9, 10, 11, 12],
            ],
            'amitabha.mukherjee@vidyamission.com' => [
                'subjects' => [4], // History
                'classes' => [6, 7, 8, 9, 10, 11, 12],
            ],
            'ruma.bose@vidyamission.com' => [
                'subjects' => [5, 6], // Geography, Environmental Studies
                'classes' => [5, 6, 7, 8, 9, 10, 11, 12],
            ],
            'debashis.ghosh@vidyamission.com' => [
                'subjects' => [7, 9, 12], // Science, Life Science, Biology
                'classes' => [6, 7, 8, 9, 10, 11, 12],
            ],
            'tanmoy.chatterjee@vidyamission.com' => [
                'subjects' => [7, 8, 11], // Science, Physical Science, Chemistry
                'classes' => [6, 7, 8, 9, 10, 11, 12],
            ],
            'sneha.dutta@vidyamission.com' => [
                'subjects' => [13], // Computer Application
                'classes' => [11, 12],
            ],
        ];

        foreach ($teacherAssignments as $email => $assignment) {
            $teacher = User::where('email', $email)->first();
            if (! $teacher) {
                continue;
            }

            foreach ($assignment['subjects'] as $subjectId) {
                foreach ($assignment['classes'] as $classLevel) {
                    // Check if class teaches this subject
                    $validSubject = ClassSubject::where('class_level', $classLevel)
                        ->where('subject_id', $subjectId)
                        ->exists();

                    if ($validSubject) {
                        DB::table('teacher_subjects')->updateOrInsert(
                            [
                                'teacher_id' => $teacher->id,
                                'subject_id' => $subjectId,
                                'class_level' => $classLevel,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('Class-subject mappings and teacher-subject assignments seeded successfully.');
    }
}
