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
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Class10MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure a teacher user exists to link to the study materials
        $teacher = User::updateOrCreate(
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

        // 2. Define Class 10 subjects, chapters, and materials details with YouTube URLs
        $curriculum = [
            [
                'subject' => 'Bengali',
                'chapters' => [
                    [
                        'title' => 'Gyananjon (জ্ঞানচক্ষু)',
                        'notes' => [
                            'title' => 'Gyananjon Summary & Analysis',
                            'description' => 'Detailed thematic analysis and key questions with answers for Gyananjon.',
                            'file_url' => 'https://vidyamission.com/materials/bengali/gyananjon_summary.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Understanding Gyananjon Character Arc',
                            'description' => 'A video lecture focusing on Tapati\'s character development.',
                            'youtube_video_id' => 'E7wJTI-1dqQ',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/E7wJTI-1dqQ',
                            'video_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
                        ],
                        'live_classes' => [
                            'title' => 'Bengali Gyananjon Doubt Clearing Session',
                            'description' => 'Interactive session resolving questions from previous year papers.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=E7wJTI-1dqQ',
                        ],
                        'test' => [
                            'title' => 'Gyananjon MCQ Practice Test',
                            'questions' => [
                                [
                                    'question_text' => 'জ্ঞানচক্ষু গল্পটির লেখিকা কে?',
                                    'option_a' => 'আশাপূর্ণা দেবী',
                                    'option_b' => 'মহাশ্বেতা দেবী',
                                    'option_c' => 'লীলা মজুমদার',
                                    'option_d' => 'নবনীতা দেবসেন',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'তপনের লেখা গল্পের নাম কী ছিল?',
                                    'option_a' => 'প্রথম দিন',
                                    'option_b' => 'স্কুল লাইফ',
                                    'option_c' => 'ছুটি',
                                    'option_d' => 'আমাদের বাড়ি',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'subject' => 'English',
                'chapters' => [
                    [
                        'title' => 'Father\'s Help',
                        'notes' => [
                            'title' => 'Father\'s Help Chapter Notes',
                            'description' => 'Important word meanings, summary, and comprehension exercises.',
                            'file_url' => 'https://vidyamission.com/materials/english/fathers_help_notes.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Line by Line Explanation of Father\'s Help',
                            'description' => 'Comprehensive explanation of Unit 1 & Unit 2.',
                            'youtube_video_id' => 'y881t8ilMyc',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/y881t8ilMyc',
                        ],
                        'live_classes' => [
                            'title' => 'Father\'s Help Textual Grammar Discussion',
                            'description' => 'Live class on narration change and voice change exercises.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=y881t8ilMyc',
                        ],
                        'test' => [
                            'title' => 'Father\'s Help Comprehension Test',
                            'questions' => [
                                [
                                    'question_text' => 'Who is the author of the story "Father\'s Help"?',
                                    'option_a' => 'R. K. Narayan',
                                    'option_b' => 'Rabindranath Tagore',
                                    'option_c' => 'Ruskin Bond',
                                    'option_d' => 'Mulk Raj Anand',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'What was the name of Swami\'s class teacher?',
                                    'option_a' => 'Samuel',
                                    'option_b' => 'Albert',
                                    'option_c' => 'Peter',
                                    'option_d' => 'George',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'subject' => 'History',
                'chapters' => [
                    [
                        'title' => 'Ideas of History (ইতিহাসের ধারণা)',
                        'notes' => [
                            'title' => 'Modern Historiography Study Guide',
                            'description' => 'Notes on new social history, sports history, and local history.',
                            'file_url' => 'https://vidyamission.com/materials/history/ideas_of_history.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Introduction to New Social History',
                            'description' => 'Video explaining the shift from traditional to social historiography.',
                            'youtube_video_id' => 'tPEE9ZwTmy0',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/tPEE9ZwTmy0',
                        ],
                        'live_classes' => [
                            'title' => 'History Chapter 1 Exam Preparation',
                            'description' => 'Important 4-mark and 8-mark questions and how to write answers.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=tPEE9ZwTmy0',
                        ],
                        'test' => [
                            'title' => 'Ideas of History Concept Test',
                            'questions' => [
                                [
                                    'question_text' => 'ভারতে খেলার ইতিহাস চর্চা প্রথম কে শুরু করেন?',
                                    'option_a' => 'রামচন্দ্র গুহ',
                                    'option_b' => 'কৌশিক বন্দ্যোপাধ্যায়',
                                    'option_c' => 'সৌমেন বন্দ্যোপাধ্যায়',
                                    'option_d' => 'আশীষ নন্দী',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'জীবনের ঝরাপাতা আত্মজীবনীটি কার লেখা?',
                                    'option_a' => 'সরলাদেবী চৌধুরানী',
                                    'option_b' => 'রাসমণি দেবী',
                                    'option_c' => 'স্বর্ণকুমারী দেবী',
                                    'option_d' => 'আশালতা সেন',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'subject' => 'Geography',
                'chapters' => [
                    [
                        'title' => 'Exogenetic Processes and Resultant Landforms (বহির্জাত প্রক্রিয়া ও সৃষ্ট ভূমিরূপ)',
                        'notes' => [
                            'title' => 'River, Wind and Glacial Landforms Notes',
                            'description' => 'Diagrammatic notes on erosion and deposition landforms.',
                            'file_url' => 'https://vidyamission.com/materials/geography/exogenetic_landforms.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Visualizing Glacial Landforms',
                            'description' => 'Video displaying animations of Moraines, Eskers, and Drumlins.',
                            'youtube_video_id' => 'V-_O752s1Hc',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/V-_O752s1Hc',
                        ],
                        'live_classes' => [
                            'title' => 'Map Pointing & Landform Diagrams',
                            'description' => 'Drawing practice for Geography board exam diagrams.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=V-_O752s1Hc',
                        ],
                        'test' => [
                            'title' => 'Exogenetic Processes Test',
                            'questions' => [
                                [
                                    'question_text' => 'নদীর ক্ষয়কার্যের ফলে সৃষ্ট একটি ইংরেজি I আকৃতির উপত্যকাকে কী বলা হয়?',
                                    'option_a' => 'ক্যানিয়ন',
                                    'option_b' => 'গিরিখাত',
                                    'option_c' => 'জলপ্রপাত',
                                    'option_d' => 'মন্থকূপ',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'মরু অঞ্চলে বায়ুর ক্ষয়কার্যের ফলে গঠিত ব্যাঙের ছাতার মতো ভূমিরূপকে কী বলে?',
                                    'option_a' => 'গৌড় (Yardang)',
                                    'option_b' => 'ইয়ারদাং',
                                    'option_c' => 'জিউগেন',
                                    'option_d' => 'ইনসেলবার্জ',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'subject' => 'Mathematics',
                'chapters' => [
                    [
                        'title' => 'Quadratic Equation with One Variable (একচল বিশিষ্ট দ্বিঘাত সমীকরণ)',
                        'notes' => [
                            'title' => 'Sridhar Acharya Formula & Examples',
                            'description' => 'Formula sheet and solved examples of quadratic equation exercises.',
                            'file_url' => 'https://vidyamission.com/materials/math/quadratic_equation.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Solving Word Problems using Quadratic Equations',
                            'description' => 'Video solving complex textual problems of class 10 math book.',
                            'youtube_video_id' => '3JZ_D3N3DTE',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/3JZ_D3N3DTE',
                        ],
                        'live_classes' => [
                            'title' => 'Quadratic Equations Core Concepts Live',
                            'description' => 'Derivation of Sridhar Acharya formula and root nature analysis.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=3JZ_D3N3DTE',
                        ],
                        'test' => [
                            'title' => 'Quadratic Equations Quiz',
                            'questions' => [
                                [
                                    'question_text' => 'ax^2 + bx + c = 0 দ্বিঘাত সমীকরণের বীজদ্বয় বাস্তব ও সমান হওয়ার শর্ত কী?',
                                    'option_a' => 'b^2 - 4ac > 0',
                                    'option_b' => 'b^2 - 4ac = 0',
                                    'option_c' => 'b^2 - 4ac < 0',
                                    'option_d' => 'b^2 - 4ac >= 0',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'x^2 - 7x + 12 = 0 সমীকরণের বীজদ্বয় কত?',
                                    'option_a' => '3, 4',
                                    'option_b' => '-3, -4',
                                    'option_c' => '2, 6',
                                    'option_d' => '1, 12',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'subject' => 'Physical Science',
                'chapters' => [
                    [
                        'title' => 'Behavior of Gases (গ্যাসের আচরণ)',
                        'notes' => [
                            'title' => 'Boyle\'s Law & Charles\'s Law Derivations',
                            'description' => 'Graphs, formula derivations, and mathematical problem-solving guide.',
                            'file_url' => 'https://vidyamission.com/materials/physics/behavior_of_gases.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Deriving Ideal Gas Equation (PV = nRT)',
                            'description' => 'Step by step video combining Boyle, Charles, and Avogadro laws.',
                            'youtube_video_id' => 'L_LUpnjgPso',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/L_LUpnjgPso',
                        ],
                        'live_classes' => [
                            'title' => 'Gas Laws Numericals Solving session',
                            'description' => 'Live practice session solving previous 5 years board numerical problems.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=L_LUpnjgPso',
                        ],
                        'test' => [
                            'title' => 'Behavior of Gases Test',
                            'questions' => [
                                [
                                    'question_text' => 'পরম শূন্য তাপমাত্রার মান সেলসিয়াস স্কেলে কত?',
                                    'option_a' => '-273 °C',
                                    'option_b' => '0 °C',
                                    'option_c' => '273 °C',
                                    'option_d' => '-273 K',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'স্থির তাপমাত্রায় নির্দিষ্ট ভরের গ্যাসের ক্ষেত্রে চাপ (P) ও আয়তন (V) এর সম্পর্কটি কেমন?',
                                    'option_a' => 'সমানুপাতিক',
                                    'option_b' => 'ব্যস্তানুপাতিক',
                                    'option_c' => 'কোনো সম্পর্ক নেই',
                                    'option_d' => 'বর্গের সমানুপাতিক',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'subject' => 'Life Science',
                'chapters' => [
                    [
                        'title' => 'Control and Coordination in Living Organisms (জীবজগতের নিয়ন্ত্রণ ও সমন্বয়)',
                        'notes' => [
                            'title' => 'Plant Hormones & Animal Hormones Summary',
                            'description' => 'Comparative charts of Auxin, Gibberellin, Cytokinin, Thyroxine, and Insulin.',
                            'file_url' => 'https://vidyamission.com/materials/lifescience/control_coordination.pdf',
                        ],
                        'recorded_classes' => [
                            'title' => 'Structure and Function of Neurons',
                            'description' => '3D visual learning video explaining synapses and nerve impulses.',
                            'youtube_video_id' => 'ZtG4c_J2tUk',
                            'youtube_embed_url' => 'https://www.youtube.com/embed/ZtG4c_J2tUk',
                        ],
                        'live_classes' => [
                            'title' => 'Reflex Action and Eye Anatomy Discussion',
                            'description' => 'Important diagrams of human eye and reflex arc pathway discussion.',
                            'youtube_live_url' => 'https://www.youtube.com/watch?v=ZtG4c_J2tUk',
                        ],
                        'test' => [
                            'title' => 'Hormones & Nerve Coordination Quiz',
                            'questions' => [
                                [
                                    'question_text' => 'কোনটি উদ্ভিদ হরমোন নয়?',
                                    'option_a' => 'অক্সিন',
                                    'option_b' => 'জিব্বেরেলিন',
                                    'option_c' => 'ইনসুলিন',
                                    'option_d' => 'সাইটোকাইনিন',
                                    'correct_option' => 'c',
                                ],
                                [
                                    'question_text' => 'জরুরীকালীন বা আপদকালীন হরমোন কাকে বলা হয়?',
                                    'option_a' => 'অ্যাড্রেনালিন',
                                    'option_b' => 'থাইরক্সিন',
                                    'option_c' => 'ইস্ট্রোজেন',
                                    'option_d' => 'টেস্টোস্টেরন',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 3. Populate subjects, chapters, class subjects and materials
        foreach ($curriculum as $item) {
            // Find or create the subject
            $subject = Subject::where('name', $item['subject'])
                ->orWhere('name', 'like', "%({$item['subject']})%")
                ->orWhere('name', 'like', "%{$item['subject']}%")
                ->first();
            if (! $subject) {
                $subject = Subject::create([
                    'name' => $item['subject'],
                    'color' => '#3B82F6',
                    'icon' => 'book-default',
                    'sort_order' => 10,
                    'is_active' => true,
                ]);
            }

            // Map the subject to Class 10
            ClassSubject::firstOrCreate([
                'class_level' => 10,
                'subject_id' => $subject->id,
            ]);

            foreach ($item['chapters'] as $chIdx => $chData) {
                // Find or create chapter
                $chapter = Chapter::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'class_level' => 10,
                        'title' => $chData['title'],
                    ],
                    [
                        'sort_order' => $chIdx + 1,
                        'is_active' => true,
                    ]
                );

                // Create Notes
                Note::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'title' => $chData['notes']['title'],
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'description' => $chData['notes']['description'],
                        'file_url' => $chData['notes']['file_url'],
                        'file_type' => 'pdf',
                        'is_active' => true,
                    ]
                );

                // Create Recorded Classes
                $ytId = $chData['recorded_classes']['youtube_video_id'] ?? null;
                $videoUrl = $chData['recorded_classes']['video_url'] ?? ($ytId ? "https://www.youtube.com/watch?v={$ytId}" : null);
                RecordedClass::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'title' => $chData['recorded_classes']['title'],
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'description' => $chData['recorded_classes']['description'],
                        'video_url' => $videoUrl,
                        'gdrive_file_id' => $ytId,
                        'gdrive_embed_url' => $chData['recorded_classes']['youtube_embed_url'] ?? ($ytId ? "https://www.youtube.com/embed/{$ytId}" : null),
                        'duration_seconds' => 1800,
                        'thumbnail_url' => $ytId ? \App\Services\YouTubeService::toThumbnailUrl($ytId) : null,
                        'is_active' => true,
                    ]
                );

                // Create Live Classes
                LiveClass::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'title' => $chData['live_classes']['title'],
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'description' => $chData['live_classes']['description'],
                        'platform' => 'other',
                        'meeting_url' => $chData['live_classes']['youtube_live_url'],
                        'scheduled_at' => now()->addDays(2),
                        'duration_minutes' => 60,
                        'status' => 'scheduled',
                    ]
                );

                // Create Test
                $test = Test::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'title' => $chData['test']['title'],
                    ],
                    [
                        'total_marks' => count($chData['test']['questions']),
                        'pass_marks' => 1,
                        'duration_minutes' => 15,
                        'is_active' => true,
                    ]
                );

                // Create Questions for Test
                foreach ($chData['test']['questions'] as $qIdx => $qData) {
                    Question::updateOrCreate(
                        [
                            'test_id' => $test->id,
                            'question_text' => $qData['question_text'],
                        ],
                        [
                            'option_a' => $qData['option_a'],
                            'option_b' => $qData['option_b'],
                            'option_c' => $qData['option_c'],
                            'option_d' => $qData['option_d'],
                            'correct_option' => $qData['correct_option'],
                            'marks' => 1,
                            'sort_order' => $qIdx + 1,
                        ]
                    );
                }
            }
        }

        $this->command->info('Class 10 West Bengal Board study materials seeded successfully with YouTube URLs.');
    }
}
