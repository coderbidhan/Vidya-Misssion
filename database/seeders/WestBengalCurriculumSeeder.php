<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\LiveClass;
use App\Models\Note;
use App\Models\Question;
use App\Models\RecordedClass;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;

class WestBengalCurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds for West Bengal Board Curriculum (Classes 5 to 12).
     */
    public function run(): void
    {
        // Teachers mapping by specialty
        $teachers = [
            'bengali' => User::where('email', 'ananya.roy@vidyamission.com')->first() ?? User::first(),
            'english' => User::where('email', 'priya.banerjee@vidyamission.com')->first() ?? User::first(),
            'math' => User::where('email', 'subhash.das@vidyamission.com')->first() ?? User::first(),
            'history' => User::where('email', 'amitabha.mukherjee@vidyamission.com')->first() ?? User::first(),
            'geography' => User::where('email', 'ruma.bose@vidyamission.com')->first() ?? User::first(),
            'science' => User::where('email', 'debashis.ghosh@vidyamission.com')->first() ?? User::first(),
            'physical_science' => User::where('email', 'teacher@vidyamission.com')->first() ?? User::first(),
            'life_science' => User::where('email', 'debashis.ghosh@vidyamission.com')->first() ?? User::first(),
            'physics' => User::where('email', 'teacher@vidyamission.com')->first() ?? User::first(),
            'chemistry' => User::where('email', 'tanmoy.chatterjee@vidyamission.com')->first() ?? User::first(),
            'biology' => User::where('email', 'debashis.ghosh@vidyamission.com')->first() ?? User::first(),
            'computer' => User::where('email', 'sneha.dutta@vidyamission.com')->first() ?? User::first(),
            'environment' => User::where('email', 'ruma.bose@vidyamission.com')->first() ?? User::first(),
        ];

        // Curriculum definitions across Class 5 to Class 12
        $curriculum = $this->getCurriculumData();

        foreach ($curriculum as $item) {
            $classLevel = $item['class_level'];
            $subjectId = $item['subject_id'];
            $teacherKey = $item['teacher_key'];
            $teacher = $teachers[$teacherKey] ?? User::first();

            $chapter = Chapter::updateOrCreate(
                [
                    'subject_id' => $subjectId,
                    'class_level' => $classLevel,
                    'title' => $item['chapter_title'],
                ],
                [
                    'image' => $item['image'],
                    'color' => $item['color'],
                    'theme' => $item['theme'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );

            // 1. Recorded Class (YouTube Video)
            $ytId = $item['youtube_id'];
            RecordedClass::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $item['video_title'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => $item['video_description'],
                    'gdrive_file_id' => $ytId,
                    'gdrive_embed_url' => "https://www.youtube.com/embed/{$ytId}",
                    'thumbnail_url' => "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg",
                    'duration_seconds' => $item['video_duration'] ?? 1800,
                    'is_active' => true,
                ]
            );

            // 2. Live Class
            LiveClass::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $item['live_title'] ?? ($item['chapter_title'].' Live Masterclass & Q&A'),
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => 'Live interactive doubt clearing session and key exam question analysis with '.$teacher->name.'.',
                    'platform' => 'other',
                    'meeting_url' => "https://www.youtube.com/watch?v={$ytId}",
                    'scheduled_at' => now()->addDays(rand(1, 14))->setHour(18)->setMinute(0),
                    'duration_minutes' => 60,
                    'status' => 'scheduled',
                ]
            );

            // 3. Chapter Notes (PDF)
            Note::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $item['notes_title'] ?? ($item['chapter_title'].' Complete Revision Notes'),
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => 'Comprehensive study notes, summary, formulas, and solved exercises for '.$item['chapter_title'].'.',
                    'file_url' => "https://vidyamission.com/materials/class{$classLevel}/chapter_{$chapter->id}_notes.pdf",
                    'file_type' => 'pdf',
                    'is_active' => true,
                ]
            );

            // 4. Chapter Test
            $test = Test::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $item['test_title'] ?? ($item['chapter_title'].' Assessment Test'),
                ],
                [
                    'total_marks' => count($item['questions']),
                    'pass_marks' => (int) ceil(count($item['questions']) * 0.5),
                    'duration_minutes' => 15,
                    'is_active' => true,
                ]
            );

            // 5. Questions for Test
            foreach ($item['questions'] as $qIndex => $q) {
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
                        'marks' => 1,
                        'sort_order' => $qIndex + 1,
                    ]
                );
            }
        }

        $this->command->info('Class 5 to 12 West Bengal curriculum, chapters, YouTube recorded classes, live classes, notes, tests, and questions seeded.');
    }

    /**
     * Get authentic syllabus data from Class 5 to Class 12.
     */
    private function getCurriculumData(): array
    {
        return [
            // ==========================================
            // CLASS 5 (WBBSE)
            // ==========================================
            [
                'class_level' => 5,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'গল্পবুড়ো (Golpoburo - Sunirmal Basu)',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'bengali-poetry',
                'sort_order' => 1,
                'video_title' => 'গল্পবুড়ো কবিতার সম্পূর্ণ ব্যাখ্যা ও প্রশ্ন উত্তর',
                'video_description' => 'সুনির্মল বসুর গল্পবুড়ো কবিতাটির প্রতিটি পঙক্তির সহজ ও স্পষ্ট বিশ্লেষণ।',
                'youtube_id' => '1la4bWtrteQ',
                'video_duration' => 1200,
                'questions' => [
                    [
                        'question_text' => 'গল্পবুড়ো কবিতাটির কবি কে?',
                        'option_a' => 'সুনির্মল বসু',
                        'option_b' => 'রবীন্দ্রনাথ ঠাকুর',
                        'option_c' => 'কাজী নজরুল ইসলাম',
                        'option_d' => 'সুকুমার রায়',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'গল্পবুড়ো কখন গল্প বলতে আসে?',
                        'option_a' => 'শীতের ভোরে',
                        'option_b' => 'গ্রীষ্মের দুপুরে',
                        'option_c' => 'বর্ষার সন্ধ্যায়',
                        'option_d' => 'শরতের সকালে',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'গল্পবুড়োর কাঁধের ঝুলিতে কী আছে?',
                        'option_a' => 'মনোরম রূপকথার গল্প',
                        'option_b' => 'খাবার জিনিস',
                        'option_c' => 'বই খাতা',
                        'option_d' => 'খেলনা সামগ্রী',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 5,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'A Feat on Feet (Mount Everest)',
                'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'adventure',
                'sort_order' => 1,
                'video_title' => 'A Feat on Feet - Line by Line Bengali Meaning & Analysis',
                'video_description' => 'Detailed explanation of Edmund Hillary and Tenzing Norgay\'s historic Mount Everest summit.',
                'youtube_id' => 'k2qgadSvNyU',
                'video_duration' => 1350,
                'questions' => [
                    [
                        'question_text' => 'Who were the first men to climb Mount Everest?',
                        'option_a' => 'Edmund Hillary and Tenzing Norgay',
                        'option_b' => 'George Mallory and Andrew Irvine',
                        'option_c' => 'Neil Armstrong and Buzz Aldrin',
                        'option_d' => 'Bachendri Pal and Santosh Yadav',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'In which year was Mount Everest conquered by Hillary and Norgay?',
                        'option_a' => '1953',
                        'option_b' => '1947',
                        'option_c' => '1965',
                        'option_d' => '1971',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 5,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'ভগ্নাংশের সহজ ধারণা ও তুলনা (Fractions)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'mathematics',
                'sort_order' => 1,
                'video_title' => 'ভগ্নাংশের লব, হর এবং সমমানের ভগ্নাংশ নির্ণয়',
                'video_description' => 'চিত্র ও উদাহরণের সাহায্যে ভগ্নাংশের প্রাথমিক ধারণা এবং যোগ-বিয়োগের নিয়ম।',
                'youtube_id' => '5jqtYg6l_vI',
                'video_duration' => 1400,
                'questions' => [
                    [
                        'question_text' => 'একটি ভগ্নাংশের ওপরের সংখ্যাটিকে কী বলা হয়?',
                        'option_a' => 'লব',
                        'option_b' => 'হর',
                        'option_c' => 'গুণক',
                        'option_d' => 'ভাগশেষ',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => '২/৪ এর সমমানের সরলতম ভগ্নাংশ কোনটি?',
                        'option_a' => '১/২',
                        'option_b' => '৩/৪',
                        'option_c' => '১/৪',
                        'option_d' => '২/৩',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 5,
                'subject_id' => 6, // Our Environment
                'teacher_key' => 'environment',
                'chapter_title' => 'মানবদেহ - ত্বক, হাড় ও পেশি (Human Body)',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'color' => '#10B981',
                'theme' => 'human-body',
                'sort_order' => 1,
                'video_title' => 'মানবদেহের বর্ম - ত্বক ও অস্থিসন্ধির গঠন ও যত্ন',
                'video_description' => 'ত্বকের স্তরসমূহ, মেলানিনের কাজ, এবং মানবদেহের প্রধান অস্থিগুলির পরিচিতি।',
                'youtube_id' => 'i63Lq1U_r7A',
                'video_duration' => 1100,
                'questions' => [
                    [
                        'question_text' => 'ত্বকের রঙের জন্য কোন রঞ্জক পদার্থ দায়ী?',
                        'option_a' => 'মেলানিন',
                        'option_b' => 'হিমোগ্লোবিন',
                        'option_c' => 'কেরাটিন',
                        'option_d' => 'ক্লোরোফিল',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'সূর্যালোকের উপস্থিতিতে ত্বক কোন ভিটামিন তৈরি করে?',
                        'option_a' => 'ভিটামিন D',
                        'option_b' => 'ভিটামিন A',
                        'option_c' => 'ভিটামিন C',
                        'option_d' => 'ভিটামিন B',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 6 (WBBSE)
            // ==========================================
            [
                'class_level' => 6,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'ভরদুপুরে (Bhordupure - Nirendranath Chakraborty)',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'bengali-poetry',
                'sort_order' => 1,
                'video_title' => 'ভরদুপুরে কবিতার ভাবার্থ ও প্রশ্নোত্তর আলোচনা',
                'video_description' => 'নীরেন্দ্রনাথ চক্রবর্তীর ভরদুপুরে কবিতার গ্রামীণ নিস্তব্ধতা ও রূপকল্পের আলোচনা।',
                'youtube_id' => 'kJQP7kiw5Fk',
                'video_duration' => 1500,
                'questions' => [
                    [
                        'question_text' => 'ভরদুপুরে কবিতাটির রচয়িতা কে?',
                        'option_a' => 'নীরেন্দ্রনাথ চক্রবর্তী',
                        'option_b' => 'শঙ্খ ঘোষ',
                        'option_c' => 'শক্তি চট্টোপাধ্যায়',
                        'option_d' => 'জীবনানন্দ দাশ',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'অশ্বত্থ গাছটিকে কবি কিসের সাথে তুলনা করেছেন?',
                        'option_a' => 'পথিকজনের ছাতা',
                        'option_b' => 'রাজার মুকুট',
                        'option_c' => 'পাহাড়ের চূড়া',
                        'option_d' => 'মেঘের দল',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 6,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'It All Began With Drip-Drip',
                'image' => 'https://images.unsplash.com/photo-1534274988757-a28bf1a57c17?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'story',
                'sort_order' => 1,
                'video_title' => 'It All Began With Drip-Drip - Complete Story & Exercises',
                'video_description' => 'A funny folk tale of Bholenath and the misunderstanding in the stormy night.',
                'youtube_id' => 'fJ9rUzIMcZQ',
                'video_duration' => 1450,
                'questions' => [
                    [
                        'question_text' => 'Who was looking for his lost donkey in the storm?',
                        'option_a' => 'Bholenath',
                        'option_b' => 'The King',
                        'option_c' => 'The Tiger',
                        'option_d' => 'The Old Woman',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'What did the tiger think \'Drip-Drip\' was?',
                        'option_a' => 'A dangerous mysterious creature',
                        'option_b' => 'Rainwater drops',
                        'option_c' => 'A gun shot',
                        'option_d' => 'Another wild animal',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 6,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'লসাগু ও গসাগু (LCM & HCF)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'arithmetic',
                'sort_order' => 1,
                'video_title' => 'মৌলিক উৎপাদক ও ভাগ প্রক্রিয়ায় লসাগু ও গসাগু নির্ণয়',
                'video_description' => 'গরিষ্ঠ সাধারণ গুণনীয়ক ও লঘিষ্ঠ সাধারণ গুণিতক নির্ণয়ের সহজ পদ্ধতি।',
                'youtube_id' => '3JZ_D3ELwOQ',
                'video_duration' => 1800,
                'questions' => [
                    [
                        'question_text' => 'গসাগু (H.C.F)-এর পূর্ণরূপ কী?',
                        'option_a' => 'গরিষ্ঠ সাধারণ গুণনীয়ক',
                        'option_b' => 'লঘিষ্ঠ সাধারণ গুণিতক',
                        'option_c' => 'গড় সাধারণ গুণক',
                        'option_d' => 'গরিষ্ঠ সাধারণ গুণিতক',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => '১২ এবং ১৮ এর গসাগু কত?',
                        'option_a' => '৬',
                        'option_b' => '৩',
                        'option_c' => '৩৬',
                        'option_d' => '২',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 6,
                'subject_id' => 4, // History
                'teacher_key' => 'history',
                'chapter_title' => 'হরপ্পা সভ্যতা ও প্রাচীন ভারতীয় নগরজীবন',
                'image' => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=600&q=80',
                'color' => '#D97706',
                'theme' => 'ancient-civilization',
                'sort_order' => 1,
                'video_title' => 'হরপ্পা ও মহেঞ্জোদারো সভ্যতার নগর পরিকল্পনা ও ড্রেনেজ ব্যবস্থা',
                'video_description' => 'সিন্ধু সভ্যতার প্রত্নতাত্ত্বিক নিদর্শন, স্নানাগার, শস্যাগার এবং বাণিজ্য ব্যবস্থার বিবরণ।',
                'youtube_id' => 'L_LUpnjgPso',
                'video_duration' => 1600,
                'questions' => [
                    [
                        'question_text' => 'হরপ্পা সভ্যতা কোন নদীর তীরে বিকাশ লাভ করেছিল?',
                        'option_a' => 'সিন্ধু ও রাভী নদী',
                        'option_b' => 'গঙ্গা নদী',
                        'option_c' => 'নর্মদা নদী',
                        'option_d' => 'যমুনা নদী',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'বিখ্যাত বৃহৎ স্নানাগারটি কোন প্রাচীন নগরে আবিষ্কৃত হয়েছিল?',
                        'option_a' => 'মহেঞ্জোদারো',
                        'option_b' => 'লোথাল',
                        'option_c' => 'কালিবঙ্গান',
                        'option_d' => 'রোপার',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 6,
                'subject_id' => 5, // Geography
                'teacher_key' => 'geography',
                'chapter_title' => 'আকাশভরা সূর্য-তারা (Solar System & Universe)',
                'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&q=80',
                'color' => '#F97316',
                'theme' => 'astronomy',
                'sort_order' => 1,
                'video_title' => 'সৌরজগৎ, গ্রহ, উপগ্রহ ও ছায়াপথ পরিচিতি',
                'video_description' => 'সৌর পরিবারের আটটি গ্রহ, নক্ষত্রমণ্ডল এবং আলোর গতিবেগের রহস্যময় জগৎ।',
                'youtube_id' => 'libKBYuqQSg',
                'video_duration' => 1700,
                'questions' => [
                    [
                        'question_text' => 'সৌরজগতের সবচেয়ে বড় গ্রহ কোনটি?',
                        'option_a' => 'বৃহস্পতি (Jupiter)',
                        'option_b' => 'শনি (Saturn)',
                        'option_c' => 'মঙ্গল (Mars)',
                        'option_d' => 'পৃথিবী (Earth)',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'পৃথিবীর একমাত্র প্রাকৃতিক উপগ্রহ কোনটি?',
                        'option_a' => 'চাঁদ (Moon)',
                        'option_b' => 'ফোবস',
                        'option_c' => 'টাইটান',
                        'option_d' => 'গ্যানিমিড',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 6,
                'subject_id' => 7, // Science
                'teacher_key' => 'science',
                'chapter_title' => 'পদার্থের ভৌত ও রাসায়নিক পরিবর্তন (States of Matter)',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'color' => '#14B8A6',
                'theme' => 'matter-science',
                'sort_order' => 1,
                'video_title' => 'কঠিন, তরল, গ্যাসীয় অবস্থার পরিবর্তন ও রাসায়নিক বিক্রিয়া',
                'video_description' => 'বরফ গলে জল হওয়া বনাম লোহার মোর্চে পড়া - ভৌত ও রাসায়নিক পরিবর্তনের স্পষ্ট পার্থক্য।',
                'youtube_id' => '9No-FiEInLA',
                'video_duration' => 1550,
                'questions' => [
                    [
                        'question_text' => 'লোহায় মরিচা পড়া কোন ধরনের পরিবর্তন?',
                        'option_a' => 'রাসায়নিক পরিবর্তন',
                        'option_b' => 'ভৌত পরিবর্তন',
                        'option_c' => 'সাময়িক পরিবর্তন',
                        'option_d' => 'কোনোটিই নয়',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'পদার্থের কঠিন অবস্থা থেকে সরাসরি বাষ্পে পরিণত হওয়ার প্রক্রিয়াকে কী বলে?',
                        'option_a' => 'উর্ধ্বপাতন (Sublimation)',
                        'option_b' => 'ঘনীভবন',
                        'option_c' => 'বাষ্পীভবন',
                        'option_d' => 'হিমায়ন',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 7 (WBBSE)
            // ==========================================
            [
                'class_level' => 7,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'পাগলা দাশু (Pagla Dashu - Sukumar Ray)',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'humor',
                'sort_order' => 1,
                'video_title' => 'সুকুমার রায়ের পাগলা দাশু গল্পের নিখুঁত চরিত্র বিশ্লেষণ',
                'video_description' => 'দাশুর স্কুলজীবনের অদ্ভুত কাণ্ডকারখানা ও হাসির গল্পের সামগ্রিক পাঠ।',
                'youtube_id' => 'y881t8ilMyc',
                'video_duration' => 1600,
                'questions' => [
                    [
                        'question_text' => 'পাগলা দাশু গল্পের রচয়িতা কে?',
                        'option_a' => 'সুকুমার রায়',
                        'option_b' => 'উপেন্দ্রকিশোর রায়চৌধুরী',
                        'option_c' => 'লীলা মজুমদার',
                        'option_d' => 'সত্যজিৎ রায়',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'দাশুর চোখ কেমন ছিল?',
                        'option_a' => 'গোল গোল ও চঞ্চল',
                        'option_b' => 'ছোট ও শান্ত',
                        'option_c' => 'লালচে',
                        'option_d' => 'অন্ধকার',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 7,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'The Book of Nature (Jawaharlal Nehru)',
                'image' => 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'nature',
                'sort_order' => 1,
                'video_title' => 'The Book of Nature - Letters from a Father to His Daughter',
                'video_description' => 'How rocks, mountains, fossils, and rivers tell the history of our early Earth.',
                'youtube_id' => 'oHg5SJYRHA0',
                'video_duration' => 1500,
                'questions' => [
                    [
                        'question_text' => 'Who wrote \'The Book of Nature\' letters?',
                        'option_a' => 'Jawaharlal Nehru',
                        'option_b' => 'Rabindranath Tagore',
                        'option_c' => 'Mahatma Gandhi',
                        'option_d' => 'Subhas Chandra Bose',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'To whom were these letters addressed?',
                        'option_a' => 'Indira Gandhi',
                        'option_b' => 'Kamala Nehru',
                        'option_c' => 'Sarojini Naidu',
                        'option_d' => 'Vijayalakshmi Pandit',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 7,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'বীজগণিতীয় সূত্রাবলী (Algebraic Identities: (a+b)², (a-b)²)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'algebra',
                'sort_order' => 1,
                'video_title' => 'বীজগণিতের মৌলিক সূত্রাবলীর জ্যামিতিক প্রমাণ ও প্রয়োগ',
                'video_description' => '(a+b)², (a-b)² এবং a²-b² সূত্রের সাহায্যে সহজে মান নির্ণয় ও সরলীকরণ।',
                'youtube_id' => 'EngW7tLk6R8',
                'video_duration' => 1900,
                'questions' => [
                    [
                        'question_text' => '(a + b)² এর বিস্তার কোনটি?',
                        'option_a' => 'a² + 2ab + b²',
                        'option_b' => 'a² - 2ab + b²',
                        'option_c' => 'a² + b²',
                        'option_d' => 'a² - b²',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => '(x + y)(x - y) এর সঠিক মান কী?',
                        'option_a' => 'x² - y²',
                        'option_b' => 'x² + y²',
                        'option_c' => '2xy',
                        'option_d' => '(x - y)²',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 7,
                'subject_id' => 4, // History
                'teacher_key' => 'history',
                'chapter_title' => 'মুঘল সাম্রাজ্য ও আকবরের শাসনব্যবস্থা (Mughal Empire)',
                'image' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=600&q=80',
                'color' => '#D97706',
                'theme' => 'medieval-history',
                'sort_order' => 1,
                'video_title' => 'সম্রাট আকবরের মনসবদারি প্রথা, দ্বীন-ই-ইলাহী ও সুলহ-ই-কুল নীতি',
                'video_description' => 'পানিপথের দ্বিতীয় যুদ্ধ থেকে আকবরের ধর্মনিরপেক্ষ প্রশাসনিক কাঠামোর সামগ্রিক রূপরেখা।',
                'youtube_id' => 'l482T0yNkeo',
                'video_duration' => 1750,
                'questions' => [
                    [
                        'question_text' => '১৫৫৬ সালে পানিপথের দ্বিতীয় যুদ্ধে আকবরের প্রতিপক্ষ কে ছিলেন?',
                        'option_a' => 'হিমু (Hemu)',
                        'option_b' => 'ইব্রাহিম লোদি',
                        'option_c' => 'রানা সঙ্গ',
                        'option_d' => 'শেরশাহ',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'আকবরের প্রবর্তিত মনসবদারি প্রথায় \'মনসব\' শব্দের অর্থ কী?',
                        'option_a' => 'পদমর্যাদা (Rank/Position)',
                        'option_b' => 'ঘোড়সওয়ার সেনা',
                        'option_c' => 'জমিদারি এলাকা',
                        'option_d' => 'রাজকীয় মুদ্রা',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 7,
                'subject_id' => 5, // Geography
                'teacher_key' => 'geography',
                'chapter_title' => 'বায়ুচাপ ও বায়ুপ্রবাহ (Air Pressure & Wind Systems)',
                'image' => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?w=600&q=80',
                'color' => '#F97316',
                'theme' => 'climatology',
                'sort_order' => 1,
                'video_title' => 'উচ্চচাপ ও নিম্নচাপ বলয়, কোরিওলিস বল ও নিয়ত বায়ুপ্রবাহ',
                'video_description' => 'আয়ন বায়ু, পশ্চিমা বায়ু এবং মেরু বায়ুর গতিপ্রকৃতি ও ঋতু পরিবর্তনের প্রভাব।',
                'youtube_id' => 'JGwWNGJdvx8',
                'video_duration' => 1650,
                'questions' => [
                    [
                        'question_text' => 'বায়ুচাপ মাপার যন্ত্রের নাম কী?',
                        'option_a' => 'ব্যারোমিটার (Barometer)',
                        'option_b' => 'থার্মোমিটার',
                        'option_c' => 'অ্যানিমোমিটার',
                        'option_d' => 'হাইড্রোমিটার',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'কোরিওপিস বলের প্রভাবে উত্তর গোলার্ধে বাতাস কোন দিকে বেঁকে যায়?',
                        'option_a' => 'ডান দিকে',
                        'option_b' => 'বাঁ দিকে',
                        'option_c' => 'সোজা চলে যায়',
                        'option_d' => 'নিচের দিকে',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 7,
                'subject_id' => 7, // Science
                'teacher_key' => 'science',
                'chapter_title' => 'পরমাণু, অণু ও রাসায়নিক বিক্রিয়া (Atoms & Molecules)',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'color' => '#14B8A6',
                'theme' => 'chemistry-basics',
                'sort_order' => 1,
                'video_title' => 'ইলেকট্রন, প্রোটন, নিউট্রন এবং রাসায়নিক সংকেত লেখার নিয়ম',
                'video_description' => 'যোজ্যতার ধারণা এবং রাসায়নিক সমীকরণ সমতাবিধান করার পদ্ধতি।',
                'youtube_id' => '_uQrJ0TkZlc',
                'video_duration' => 1800,
                'questions' => [
                    [
                        'question_text' => 'পরমাণুর নিউক্লিয়াসে কোন কোন কণা উপস্থিত থাকে?',
                        'option_a' => 'প্রোটন ও নিউট্রন',
                        'option_b' => 'ইলেকট্রন ও প্রোটন',
                        'option_c' => 'শুধুমাত্র ইলেকট্রন',
                        'option_d' => 'শুধুমাত্র নিউট্রন',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'জলের সঠিক রাসায়নিক সংকেত কোনটি?',
                        'option_a' => 'H₂O',
                        'option_b' => 'CO₂',
                        'option_c' => 'NaCl',
                        'option_d' => 'H₂SO₄',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 8 (WBBSE)
            // ==========================================
            [
                'class_level' => 8,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'বোঝাপড়া (Bojhapora - Rabindranath Tagore)',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'tagore-poetry',
                'sort_order' => 1,
                'video_title' => 'রবীন্দ্রনাথ ঠাকুরের বোঝাপড়া কবিতার সম্পূর্ণ ব্যাখ্যা ও দার্শনিক তাৎপর্য',
                'video_description' => 'জীবনের ঘাত-প্রতিঘাত ও সত্যকে মেনে নিয়ে পথ চলার অনুপম বার্তা।',
                'youtube_id' => 'ubWCqXp1V6w',
                'video_duration' => 1700,
                'questions' => [
                    [
                        'question_text' => 'বোঝাপড়া কবিতাটি কার রচনা?',
                        'option_a' => 'রবীন্দ্রনাথ ঠাকুর',
                        'option_b' => 'কাজী নজরুল ইসলাম',
                        'option_c' => 'দ্বিজেন্দ্রলাল রায়',
                        'option_d' => 'সুকান্ত ভট্টাচার্য',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'কবি কাকে ভালো-মন্দ যাই আসুক সত্যকে সহজে গ্রহণ করতে বলেছেন?',
                        'option_a' => 'মনকে',
                        'option_b' => 'বন্ধুকে',
                        'option_c' => 'শত্রুকে',
                        'option_d' => 'সমাজকে',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 8,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'The Great Escape (Subhas Chandra Bose)',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'national-hero',
                'sort_order' => 1,
                'video_title' => 'The Great Escape of Netaji Subhas Chandra Bose from Elgin Road',
                'video_description' => 'How Sisir Bose drove Netaji in the Wanderer car to Gomoh railway station.',
                'youtube_id' => 'e-ORhEE9VVg',
                'video_duration' => 1650,
                'questions' => [
                    [
                        'question_text' => 'What disguise did Subhas Chandra Bose assume for his secret escape?',
                        'option_a' => 'Muhammad Ziauddin',
                        'option_b' => 'Orlando Mazzotta',
                        'option_c' => 'Rahim Khan',
                        'option_d' => 'Ahmed Shah',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'Who assisted Netaji in driving away from Kolkata?',
                        'option_a' => 'Sisir Kumar Bose',
                        'option_b' => 'Sarat Chandra Bose',
                        'option_c' => 'Rash Behari Bose',
                        'option_d' => 'Chittaranjan Das',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 8,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'মূলদ সংখ্যার ধারণা ও বীজগাণিতিক গুণ-ভাগ (Rational Numbers)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'rational-numbers',
                'sort_order' => 1,
                'video_title' => 'মূলদ সংখ্যার ধর্ম, সংখ্যারেখায় স্থাপন ও বীজগণিতীয় সংখ্যামালা',
                'video_description' => 'p/q আকারের মূলদ সংখ্যার যোগ, বিয়োগ, গুণ ও ভাগ সংক্রান্ত ধারণার বিকাশ।',
                'youtube_id' => 'SQ5ky5kZtWw',
                'video_duration' => 1850,
                'questions' => [
                    [
                        'question_text' => 'যেসব সংখ্যাকে p/q (যেখানে q ≠ 0) আকারে প্রকাশ করা যায় তাদের কী বলে?',
                        'option_a' => 'মূলদ সংখ্যা',
                        'option_b' => 'অমূলদ সংখ্যা',
                        'option_c' => 'কাল্পনিক সংখ্যা',
                        'option_d' => 'স্বাভাবিক সংখ্যা',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => '০ (শূন্য) কি একটি মূলদ সংখ্যা?',
                        'option_a' => 'হ্যাঁ, কারণ ০/১ লেখা যায়',
                        'option_b' => 'না, এটি মূলদ নয়',
                        'option_c' => 'এটি একটি অমূলদ সংখ্যা',
                        'option_d' => 'শুধুমাত্র ঋণাত্মক সংখ্যা মূলদ',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 8,
                'subject_id' => 4, // History
                'teacher_key' => 'history',
                'chapter_title' => '১৮৫৭ সালের মহাবিদ্রোহ (The Great Revolt of 1857)',
                'image' => 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=600&q=80',
                'color' => '#D97706',
                'theme' => 'revolt-history',
                'sort_order' => 1,
                'video_title' => '১৮৫৭ সালের বিদ্রোহের কারণ, প্রসার, ফলাফল ও মহারানীর ঘোষণাপত্র',
                'video_description' => 'মঙ্গল পান্ডে, ঝাঁসির রানী লক্ষ্মীবাঈ এবং বিদ্রোহের জাতীয় চরিত্রের বিশদ মূল্যায়ন।',
                'youtube_id' => 'k2qgadSvNyU',
                'video_duration' => 1950,
                'questions' => [
                    [
                        'question_text' => '১৮৫৭ সালে ব্যারাকপুরে প্রথম বিদ্রোহের সূচনা কে করেছিলেন?',
                        'option_a' => 'মঙ্গল পান্ডে',
                        'option_b' => 'তাঁতিয়া তোপি',
                        'option_c' => 'নানা সাহেব',
                        'option_d' => 'কুনওয়ার সিং',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => '১৮৫৮ সালে ভারতের শাসনভার কার হাতে স্থানান্তরিত হয়?',
                        'option_a' => 'ব্রিটিশ রাজমুকুট (ব্রিটিশ পার্লামেন্ট)',
                        'option_b' => 'ইস্ট ইন্ডিয়া কোম্পানি',
                        'option_c' => 'মুঘল সম্রাট',
                        'option_d' => 'গভর্নর জেনারেল ব্যক্তিগতভাবে',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 8,
                'subject_id' => 5, // Geography
                'teacher_key' => 'geography',
                'chapter_title' => 'পৃথিবীর অন্দরমহল ও পাতসংস্থান (Earth\'s Interior & Plates)',
                'image' => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?w=600&q=80',
                'color' => '#F97316',
                'theme' => 'earth-interior',
                'sort_order' => 1,
                'video_title' => 'ভূত্বক, গুরুমণ্ডল, কেন্দ্রমণ্ডল ও ভূমিকম্পের কারণ',
                'video_description' => 'সিয়াল, সিমা, ক্রোফেসিমা, নিফে স্তর এবং মহাদেশীয় পাতের সঞ্চরণ তত্ত্ব।',
                'youtube_id' => 'libKBYuqQSg',
                'video_duration' => 1700,
                'questions' => [
                    [
                        'question_text' => 'ভূত্বকের প্রধান দুটি স্তর কী কী?',
                        'option_a' => 'সিয়াল ও সিমা (SIAL & SIMA)',
                        'option_b' => 'নিফে ও ক্রোফেসিমা',
                        'option_c' => 'গুরুমণ্ডল ও কেন্দ্রমণ্ডল',
                        'option_d' => 'অ্যাস্থেনোস্ফিয়ার ও লিথোস্ফিয়ার',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'ভূমিকম্পের তীব্রতা মাপার একক কোনটি?',
                        'option_a' => 'রিক্টার স্কেল (Richter Scale)',
                        'option_b' => 'ব্যারোমিটার',
                        'option_c' => 'হাইড্রোমিটার',
                        'option_d' => 'সিসমোগ্রাফ স্কেল',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 8,
                'subject_id' => 7, // Science
                'teacher_key' => 'science',
                'chapter_title' => 'বল ও চাপ (Force and Pressure)',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'color' => '#14B8A6',
                'theme' => 'physics-force',
                'sort_order' => 1,
                'video_title' => 'নিউটনের প্রথম গতিসূত্র, জড়তা, এবং তরলের চাপ ও প্লবতা',
                'video_description' => 'আর্কিমিডিসের নীতি এবং প্যাসকেলের নীতির ব্যবহারিক ব্যাখ্যা।',
                'youtube_id' => '1la4bWtrteQ',
                'video_duration' => 1800,
                'questions' => [
                    [
                        'question_text' => 'চাপের SI একক কোনটি?',
                        'option_a' => 'প্যাসকেল (Pascal / N/m²)',
                        'option_b' => 'নিউটন',
                        'option_c' => 'জুল',
                        'option_d' => 'ডাইন',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'কোনো বস্তুর নিমজ্জিত অবস্থায় প্রযুক্ত ঊর্ধ্বমুখী বলকে কী বলে?',
                        'option_a' => 'প্লবতা (Buoyancy)',
                        'option_b' => 'অভিকর্ষ বল',
                        'option_c' => 'ঘর্ষণ বল',
                        'option_d' => 'স্থিতিস্থাপক বল',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 9 (WBBSE)
            // ==========================================
            [
                'class_level' => 9,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'কলিঙ্গদেশে ঝড়-বৃষ্টি (Kalingadeshe Jhor-Brishti)',
                'image' => 'https://images.unsplash.com/photo-1516912481808-3406841bd33c?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'medieval-poetry',
                'sort_order' => 1,
                'video_title' => 'মুকুন্দরাম চক্রবর্তীর চণ্ডীমঙ্গল কাব্য ও কলিঙ্গদেশে ঝড়-বৃষ্টির বিশ্লেষণ',
                'video_description' => 'কলিঙ্গ দেশে বিধ্বংসী প্রাকৃতিক দুর্যোগ এবং প্রজাদের দুরবস্থার কাব্যিক রূপায়ণ।',
                'youtube_id' => '3JZ_D3ELwOQ',
                'video_duration' => 1900,
                'questions' => [
                    [
                        'question_text' => 'কলিঙ্গদেশে ঝড়-বৃষ্টি পাঠ্যাংশটি কোন কাব্যের অন্তর্গত?',
                        'option_a' => 'চণ্ডীমঙ্গল (অভয়ামঙ্গল)',
                        'option_b' => 'মনসামঙ্গল',
                        'option_c' => 'অন্নদামঙ্গল',
                        'option_d' => 'ধর্মমঙ্গল',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'কবি মুকুন্দরামের উপাধি কী ছিল?',
                        'option_a' => 'কবিকঙ্কণ',
                        'option_b' => 'রায়গুণাকর',
                        'option_c' => 'কবিরঞ্জন',
                        'option_d' => 'মহাকবি',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 9,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'Tales of Bhola Grandpa (Manoj Das)',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'humorous-story',
                'sort_order' => 1,
                'video_title' => 'Tales of Bhola Grandpa - Detailed Textual Analysis & Grammar',
                'video_description' => 'The unforgettable and hilarious adventures of forgetful Bhola Grandpa and the Royal Bengal Tiger.',
                'youtube_id' => 'kJQP7kiw5Fk',
                'video_duration' => 1750,
                'questions' => [
                    [
                        'question_text' => 'Where did Bhola Grandpa take his grandson during the festival?',
                        'option_a' => 'To the village festival',
                        'option_b' => 'To Kolkata market',
                        'option_c' => 'To school sports',
                        'option_d' => 'To the river bank',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'Which animal did Bhola Grandpa encounter on the banyan tree?',
                        'option_a' => 'A Royal Bengal Tiger',
                        'option_b' => 'A wild elephant',
                        'option_c' => 'A leopard',
                        'option_d' => 'A crocodile',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 9,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'বাস্তব সংখ্যা ও সূচকের নিয়মাবলী (Real Numbers & Indices)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'real-numbers',
                'sort_order' => 1,
                'video_title' => 'বাস্তব সংখ্যার শ্রেণিবিভাগ এবং সূচকের সূত্রাবলীর প্রয়োগ',
                'video_description' => 'মূলদ ও অমূলদ সংখ্যার পরিচয় এবং a^(m+n), a^(m-n) সূত্রের জটিল অঙ্ক সমাধান।',
                'youtube_id' => 'EngW7tLk6R8',
                'video_duration' => 2100,
                'questions' => [
                    [
                        'question_text' => '√2 (Root 2) কী ধরনের সংখ্যা?',
                        'option_a' => 'অমূলদ সংখ্যা (Irrational Number)',
                        'option_b' => 'মূলদ সংখ্যা',
                        'option_c' => 'পূর্ণসংখ্যা',
                        'option_d' => 'ভগ্নাংশ',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'যেকোনো অশূন্য সংখ্যা x এর ক্ষেত্রে x⁰ এর মান কত?',
                        'option_a' => '১',
                        'option_b' => '০',
                        'option_c' => 'x',
                        'option_d' => 'অসীম',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 9,
                'subject_id' => 8, // Physical Science
                'teacher_key' => 'physical_science',
                'chapter_title' => 'পরিমাপ ও বল-গতি (Measurement, Force & Motion)',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'color' => '#22C55E',
                'theme' => 'mechanics',
                'sort_order' => 1,
                'video_title' => 'নিউটনীয় গতিসূত্র, রৈখিক ভরবেগের সংরক্ষণ ও মাত্রা সমীকরণ',
                'video_description' => 'v = u + at, s = ut + 1/2 at² সমীকরণের গাণিতিক ও লেখচিত্রের সাহায্যে প্রমাণ।',
                'youtube_id' => 'libKBYuqQSg',
                'video_duration' => 2200,
                'questions' => [
                    [
                        'question_text' => 'বলের সংজ্ঞা নিউটনের কোন গতিসূত্র থেকে পাওয়া যায়?',
                        'option_a' => 'প্রথম গতিসূত্র',
                        'option_b' => 'দ্বিতীয় গতিসূত্র',
                        'option_c' => 'তৃতীয় গতিসূত্র',
                        'option_d' => 'মহাকর্ষ সূত্র',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'F = ma সূত্রটি নিউটনের কোন গতিসূত্র থেকে প্রাপ্ত?',
                        'option_a' => 'দ্বিতীয় গতিসূত্র',
                        'option_b' => 'প্রথম গতিসূত্র',
                        'option_c' => 'তৃতীয় গতিসূত্র',
                        'option_d' => 'চাপের সূত্র',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 9,
                'subject_id' => 9, // Life Science
                'teacher_key' => 'life_science',
                'chapter_title' => 'জীবন সংগঠনের স্তর - কোষ ও কলা (Cell Structure & Tissues)',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'color' => '#06B6D4',
                'theme' => 'cytology',
                'sort_order' => 1,
                'video_title' => 'উদ্ভিদ ও প্রাণী কোষের গঠন, মাইটোকনড্রিয়া ও কোষপর্দার কার্যপদ্ধতি',
                'video_description' => 'কোষ অঙ্গাণুর বিশদ গঠন ও উদ্ভিদকলার (ভাজক কলা ও স্থায়ী কলা) শ্রেণিবিভাগ।',
                'youtube_id' => '9No-FiEInLA',
                'video_duration' => 2050,
                'questions' => [
                    [
                        'question_text' => 'কোষের \'শক্তিঘর\' (Powerhouse of Cell) কাকে বলা হয়?',
                        'option_a' => 'মাইটোকনড্রিয়া (Mitochondria)',
                        'option_b' => 'গলগি বডি',
                        'option_c' => 'রাইবোজোম',
                        'option_d' => 'লাইসোজোম',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'উদ্ভিদের কাণ্ডের শীর্ষে কোন কলা উপস্থিত থাকে যা বৃদ্ধিতে সহায়তা করে?',
                        'option_a' => 'ভাজক কলা (Meristematic Tissue)',
                        'option_b' => 'স্থায়ী কলা',
                        'option_c' => 'সংবহন কলা',
                        'option_d' => 'প্যারেনকাইমা কলা',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 10 (Madhyamik / WBBSE)
            // ==========================================
            [
                'class_level' => 10,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'জ্ঞানচক্ষু (Gyananjon - Ashapurna Debi)',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'madhyamik-bengali',
                'sort_order' => 1,
                'video_title' => 'জ্ঞানচক্ষু গল্পের চরিত্র বিশ্লেষণ ও মাধ্যমিকের গুরুত্বপূর্ণ প্রশ্ন উত্তর',
                'video_description' => 'আশাপূর্ণা দেবীর জ্ঞানচক্ষু গল্পের তপনের অনুভূতি এবং নতুন মেসোমশাইয়ের ভূমিকা।',
                'youtube_id' => 'dQw4w9WgXcQ',
                'video_duration' => 1800,
                'questions' => [
                    [
                        'question_text' => 'জ্ঞানচক্ষু গল্পের লেখিকা কে?',
                        'option_a' => 'আশাপূর্ণা দেবী',
                        'option_b' => 'মহাশ্বেতা দেবী',
                        'option_c' => 'নবনীতা দেবসেন',
                        'option_d' => 'লীলা মজুমদার',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'তপনের লেখা প্রথম গল্পের নাম কী ছিল?',
                        'option_a' => 'প্রথম দিন',
                        'option_b' => 'স্কুল বেলা',
                        'option_c' => 'ছুটি',
                        'option_d' => 'আমার অভিজ্ঞতা',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 10,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'Father\'s Help (R.K. Narayan)',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'madhyamik-english',
                'sort_order' => 1,
                'video_title' => 'Father\'s Help - Line by Line Detailed Analysis (Malgudi Days)',
                'video_description' => 'Swami\'s reluctance to go to school, Samuel Sir\'s character, and moral resolution.',
                'youtube_id' => 'y881t8ilMyc',
                'video_duration' => 1900,
                'questions' => [
                    [
                        'question_text' => 'What excuse did Swami give initially for not wanting to go to school?',
                        'option_a' => 'Headache',
                        'option_b' => 'Fever',
                        'option_c' => 'Stomach ache',
                        'option_d' => 'Tooth ache',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'Who was the headmaster of Swami\'s school absent with leave on that day?',
                        'option_a' => 'The regular Headmaster was on a week\'s leave',
                        'option_b' => 'Mr. Samuel',
                        'option_c' => 'Swami\'s father',
                        'option_d' => 'The peon',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 10,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'একচলবিশিষ্ট দ্বিঘাত সমীকরণ (Quadratic Equations)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'madhyamik-math',
                'sort_order' => 1,
                'video_title' => 'দ্বিঘাত সমীকরণের বীজদ্বয়ের প্রকৃতি ও শ্রীধর আচার্যের সূত্রের প্রয়োগ',
                'video_description' => 'ax² + bx + c = 0 সমীকরণের সমাধান এবং নিরূপক (b² - 4ac) দ্বারা বীজের ধর্ম যাচাই।',
                'youtube_id' => 'EngW7tLk6R8',
                'video_duration' => 2200,
                'questions' => [
                    [
                        'question_text' => 'ax² + bx + c = 0 দ্বিঘাত সমীকরণের বীজদ্বয় বাস্তব ও সমান হওয়ার শর্ত কী?',
                        'option_a' => 'b² - 4ac = 0',
                        'option_b' => 'b² - 4ac > 0',
                        'option_c' => 'b² - 4ac < 0',
                        'option_d' => 'b = 0',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'দ্বিঘাত সমীকরণ সমাধানের জন্য কোন ভারতীয় গণিতবিদের সূত্র বিশ্ববিখ্যাত?',
                        'option_a' => 'শ্রীধর আচার্য',
                        'option_b' => 'আর্যভট্ট',
                        'option_c' => 'বরাহমিহির',
                        'option_d' => 'ভাস্করাচার্য',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 10,
                'subject_id' => 8, // Physical Science
                'teacher_key' => 'physical_science',
                'chapter_title' => 'গ্যাসের আচরণ ও বয়েলের সূত্র (Behavior of Gases)',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80',
                'color' => '#22C55E',
                'theme' => 'madhyamik-phy-sc',
                'sort_order' => 1,
                'video_title' => 'বয়েল, চার্লস এবং অ্যাভোগাড্রো সূত্রের সমন্বিত রূপ (PV = nRT)',
                'video_description' => 'আদর্শ গ্যাস সমীকরণ প্রতিষ্ঠা এবং পরম শূন্য তাপমাত্রার তাৎপর্য বিশ্লেষণ।',
                'youtube_id' => '1la4bWtrteQ',
                'video_duration' => 2000,
                'questions' => [
                    [
                        'question_text' => 'বয়েলের সূত্রে কোন রাশি দুটি ধ্রুবক থাকে?',
                        'option_a' => 'গ্যাসের ভর ও তাপমাত্রা',
                        'option_b' => 'গ্যাসের চাপ ও আয়তন',
                        'option_c' => 'গ্যাসের চাপ ও তাপমাত্রা',
                        'option_d' => 'গ্যাসের ভর ও আয়তন',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'পরম শূন্য তাপমাত্রা (Absolute Zero Temperature) এর মান কত?',
                        'option_a' => '-273°C (0 Kelvin)',
                        'option_b' => '0°C',
                        'option_c' => '100°C',
                        'option_d' => '-100°C',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 10,
                'subject_id' => 9, // Life Science
                'teacher_key' => 'life_science',
                'chapter_title' => 'জীবজগতে নিয়ন্ত্রণ ও সমন্বয় - উদ্ভিদ ও প্রাণী হরমোন',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'color' => '#06B6D4',
                'theme' => 'madhyamik-life-sc',
                'sort_order' => 1,
                'video_title' => 'অক্সিন, জিব্বেরেলিন, ইনসুলিন ও থাইরক্সিন হরমোনের কার্যাবলী',
                'video_description' => 'হরমোনকে রাসায়নিক দূত কেন বলা হয় এবং মানবদেহে অন্তঃক্ষরা গ্রন্থির ভূমিকা।',
                'youtube_id' => 'fJ9rUzIMcZQ',
                'video_duration' => 2100,
                'questions' => [
                    [
                        'question_text' => 'উদ্ভিদের অগ্রস্থ প্রকটতা ও ট্রপিক চলন নিয়ন্ত্রণকারী প্রধান হরমোন কোনটি?',
                        'option_a' => 'অক্সিন (Auxin)',
                        'option_b' => 'জিব্বেরেলিন',
                        'option_c' => 'সাইটোকাইনিন',
                        'option_d' => 'ইথিলিন',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'মানবদেহে রক্তে শর্করার মাত্রা কমাতে কোন হরমোন প্রধান ভূমিকা পালন করে?',
                        'option_a' => 'ইনসুলিন (Insulin)',
                        'option_b' => 'গ্লুকাগন',
                        'option_c' => 'অ্যাড্রেনালিন',
                        'option_d' => 'থাইরক্সিন',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 11 (WBCHSE Higher Secondary)
            // ==========================================
            [
                'class_level' => 11,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'কর্তার ভূত (Kartar Bhoot - Rabindranath Tagore)',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'hs-bengali',
                'sort_order' => 1,
                'video_title' => 'রবীন্দ্রনাথ ঠাকুরের কর্তার ভূত রূপক গল্পের গভীর রাজনৈতিক ও সামাজিক ভাবনা',
                'video_description' => 'অন্ধ সংস্কারের শাসন বনাম মুক্তচিন্তার প্রয়োজনীয়তার রূপক পাঠ।',
                'youtube_id' => 'ubWCqXp1V6w',
                'video_duration' => 2000,
                'questions' => [
                    [
                        'question_text' => 'কর্তার ভূত পাঠ্যাংশটি রবীন্দ্রনাথ ঠাকুরের কোন মূল ভাবনার রূপক?',
                        'option_a' => 'অন্ধ ঐতিহ্য ও সংস্কারাচ্ছন্ন ভীরু সমাজব্যবস্থা',
                        'option_b' => 'আধুনিক বিজ্ঞান ভাবনা',
                        'option_c' => 'বিদেশ ভ্রমণ কাহিনি',
                        'option_d' => 'প্রকৃতি বন্দনা',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'ভূতশাসনতন্ত্রে দেশের মানুষ কেমন থাকে?',
                        'option_a' => 'নিশ্চিন্ত থাকে কিন্তু অগ্রগতি থাকে না',
                        'option_b' => 'ভীষণ যুদ্ধমত্ত থাকে',
                        'option_c' => 'সম্পদশালী হয়ে ওঠে',
                        'option_d' => 'স্বাধীনভাবে চিন্তা করতে পারে',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 11,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'Leela\'s Friend (R.K. Narayan)',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'hs-english',
                'sort_order' => 1,
                'video_title' => 'Leela\'s Friend - Character Study of Sidda and Social Class Prejudices',
                'video_description' => 'A masterclass in character dynamics, innocence of Leela, and societal injustice.',
                'youtube_id' => 'y881t8ilMyc',
                'video_duration' => 1950,
                'questions' => [
                    [
                        'question_text' => 'Who was accused of stealing Leela\'s gold chain?',
                        'option_a' => 'Sidda',
                        'option_b' => 'Mr. Sivasanker',
                        'option_c' => 'The Cook',
                        'option_d' => 'The Neighbor',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'Where was the lost gold chain eventually found?',
                        'option_a' => 'Inside a tamarind pot in the kitchen',
                        'option_b' => 'In Sidda\'s pocket',
                        'option_c' => 'In the garden well',
                        'option_d' => 'At the police station',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 11,
                'subject_id' => 10, // Physics
                'teacher_key' => 'physics',
                'chapter_title' => 'ভেক্টর ও একমাত্রিক গতি (Vectors & Kinematics)',
                'image' => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?w=600&q=80',
                'color' => '#6366F1',
                'theme' => 'hs-physics',
                'sort_order' => 1,
                'video_title' => 'ভেক্টর বীজগণিত, ডট ও ক্রস গুণন এবং প্রক্ষেপকের গতি (Projectile Motion)',
                'video_description' => 'একমাত্রিক ও দ্বিমাত্রিক গতি, সর্বোচ্চ উচ্চতা এবং পাল্লা নির্ণয়ের পদার্থবিজ্ঞান সূত্র।',
                'youtube_id' => 'libKBYuqQSg',
                'video_duration' => 2500,
                'questions' => [
                    [
                        'question_text' => 'দুটি ভেক্টরের ডট গুণফল (Dot Product) কী জাতীয় রাশি উৎপন্ন করে?',
                        'option_a' => 'স্কেলার রাশি (Scalar Quantity)',
                        'option_b' => 'ভেক্টর রাশি',
                        'option_c' => 'টেনসর রাশি',
                        'option_d' => 'মাত্রাহীন রাশি',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'প্রক্ষেপকের সর্বোচ্চ পাল্লার (Maximum Range) জন্য প্রক্ষেপণ কোণ কত হতে হবে?',
                        'option_a' => '45°',
                        'option_b' => '90°',
                        'option_c' => '30°',
                        'option_d' => '60°',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 11,
                'subject_id' => 11, // Chemistry
                'teacher_key' => 'chemistry',
                'chapter_title' => 'পরমাণুর গঠন ও কোয়ান্টাম তত্ত্ব (Atomic Structure)',
                'image' => 'https://images.unsplash.com/photo-1603126857599-f6e157fa2fe6?w=600&q=80',
                'color' => '#EC4899',
                'theme' => 'hs-chemistry',
                'sort_order' => 1,
                'video_title' => 'বোর মডেল, দ্য ব্রগলি তরঙ্গদৈর্ঘ্য, হাইজেনবার্গের অনিশ্চয়তা নীতি ও অরবিটাল',
                'video_description' => 'কোয়ান্টাম সংখ্যা (n, l, m, s) এবং ইলেকট্রন বিন্যাসের আউফবাউ ও হুন্ডের নীতি।',
                'youtube_id' => '_uQrJ0TkZlc',
                'video_duration' => 2400,
                'questions' => [
                    [
                        'question_text' => 'একটি অরবিটালে বিপরীত ঘূর্ণনযুক্ত সর্বাধিক কতগুলি ইলেকট্রন থাকতে পারে?',
                        'option_a' => '২ টি (পাউলির অপবর্জন নীতি)',
                        'option_b' => '৬ টি',
                        'option_c' => '৮ টি',
                        'option_d' => '১০ টি',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'প্রধান কোয়ান্টাম সংখ্যা (n = 3) স্তরে মোট কতগুলি অরবিটাল থাকে?',
                        'option_a' => '৯ টি (n²)',
                        'option_b' => '১৮ টি',
                        'option_c' => '৩ টি',
                        'option_d' => '৬ টি',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 11,
                'subject_id' => 12, // Biology
                'teacher_key' => 'biology',
                'chapter_title' => 'কোষচক্র ও কোষ বিভাজন (Cell Cycle, Mitosis & Meiosis)',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'color' => '#84CC16',
                'theme' => 'hs-biology',
                'sort_order' => 1,
                'video_title' => 'মাইটোটিক ও মিয়োটিক বিভাজনের পর্যায়সমূহ এবং ক্রসিং ওভার',
                'video_description' => 'প্যাকাইটিন দশায় হোমোলোগাস ক্রোমোজোমের মধ্যে কায়াজমা গঠন ও জিনগত বৈচিত্র্যের ভিত্তি।',
                'youtube_id' => '9No-FiEInLA',
                'video_duration' => 2300,
                'questions' => [
                    [
                        'question_text' => 'কোষচক্রের ইন্টারফেজ দশার কোন উপদশায় ডিএনএ (DNA) দ্বিগুণ সংশ্লেষিত হয়?',
                        'option_a' => 'S দশা (Synthesis Phase)',
                        'option_b' => 'G1 দশা',
                        'option_c' => 'G2 দশা',
                        'option_d' => 'M দশা',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'মিয়োসিস বিভাজনের কোন উপদশায় কায়াজমা ও ক্রসিং ওভার সংঘটিত হয়?',
                        'option_a' => 'প্যাকাইটিন (Pachytene)',
                        'option_b' => 'লেপ্টোটিন',
                        'option_c' => 'জাইগোটিন',
                        'option_d' => 'ডায়াকাইনেসিস',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 11,
                'subject_id' => 13, // Computer Application
                'teacher_key' => 'computer',
                'chapter_title' => 'বুলিয়ান অ্যালজেব্রা ও লজিক গেটস (Boolean Algebra & Logic Gates)',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&q=80',
                'color' => '#0EA5E9',
                'theme' => 'hs-computer',
                'sort_order' => 1,
                'video_title' => 'AND, OR, NOT, NAND, NOR গেটস এবং ডি মর্গানের উপপাদ্য',
                'video_description' => 'ইউনিভার্সাল গেট থেকে অন্যান্য মৌলিক গেট গঠন এবং ট্রুথ টেবিলের মাধ্যমে সমীকরণ প্রমাণ।',
                'youtube_id' => 'SQ5ky5kZtWw',
                'video_duration' => 2100,
                'questions' => [
                    [
                        'question_text' => 'ডিজিটাল ইলেকট্রনিক্সে ইউনিভার্সাল গেট (Universal Gates) কোনগুলি?',
                        'option_a' => 'NAND এবং NOR',
                        'option_b' => 'AND এবং OR',
                        'option_c' => 'XOR এবং XNOR',
                        'option_d' => 'শুধুমাত্র NOT',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'ডি মর্গানের প্রথম উপপাদ্যটি কোনটি?',
                        'option_a' => '(A + B)\' = A\' . B\'',
                        'option_b' => '(A . B)\' = A\' . B\'',
                        'option_c' => 'A + A = A',
                        'option_d' => 'A . 0 = 1',
                        'correct_option' => 'a',
                    ],
                ],
            ],

            // ==========================================
            // CLASS 12 (WBCHSE Higher Secondary)
            // ==========================================
            [
                'class_level' => 12,
                'subject_id' => 1, // Bengali
                'teacher_key' => 'bengali',
                'chapter_title' => 'কে বাঁচায়, কে বাঁচে (Ke Bachay, Ke Banche - Manik Bandyopadhyay)',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80',
                'color' => '#EF4444',
                'theme' => 'hs-bengali-lit',
                'sort_order' => 1,
                'video_title' => 'কে বাঁচায়, কে বাঁচে গল্পের মনস্তাত্ত্বিক রূপ ও ৫০-এর মন্বন্তরের প্রেক্ষাপট',
                'video_description' => 'মৃত্যুঞ্জয় চরিত্রের আদর্শবাদী সংকট ও বাস্তব জীবনের মর্মান্তিক ট্র্যাজেডি।',
                'youtube_id' => 'dQw4w9WgXcQ',
                'video_duration' => 2100,
                'questions' => [
                    [
                        'question_text' => 'কে বাঁচায়, কে বাঁচে গল্পটির পটভূমি কোন ঐতিহাসিক ঘটনা?',
                        'option_a' => '১৯৪৩ সালের পঞ্চাশের মন্বন্তর (Great Bengal Famine)',
                        'option_b' => '১৯৪৭ সালের দেশভাগ',
                        'option_c' => '১৯৭১ সালের মুক্তিযুদ্ধ',
                        'option_d' => '১৯৪২ সালের ভারত ছাড়ো আন্দোলন',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'গল্পের কেন্দ্রীয় চরিত্র মৃত্যুঞ্জয়ের সহকর্মীর নাম কী ছিল?',
                        'option_a' => 'নিখিল',
                        'option_b' => 'টুনুর বাপ',
                        'option_c' => 'অনাথবন্ধু',
                        'option_d' => 'শৈলেন',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 12,
                'subject_id' => 2, // English
                'teacher_key' => 'english',
                'chapter_title' => 'The Eyes Have It (Ruskin Bond)',
                'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=600&q=80',
                'color' => '#3B82F6',
                'theme' => 'hs-english-lit',
                'sort_order' => 1,
                'video_title' => 'The Eyes Have It - Irony, Themes & Questions for WBCHSE Class 12',
                'video_description' => 'Comprehensive study of situational irony between the two blind co-passengers on the train to Dehra.',
                'youtube_id' => 'kJQP7kiw5Fk',
                'video_duration' => 2000,
                'questions' => [
                    [
                        'question_text' => 'At which station did the blind girl get down from the train?',
                        'option_a' => 'Saharanpur',
                        'option_b' => 'Dehradun',
                        'option_c' => 'Rohana',
                        'option_d' => 'Mussoorie',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'What striking revelation did the new passenger tell the narrator about the girl?',
                        'option_a' => 'That she was completely blind',
                        'option_b' => 'That she lost her bag',
                        'option_c' => 'That she was an author',
                        'option_d' => 'That she forgot her tickets',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 12,
                'subject_id' => 3, // Math
                'teacher_key' => 'math',
                'chapter_title' => 'ম্যাট্রিক্স ও নির্ণায়ক (Matrices and Determinants)',
                'image' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=600&q=80',
                'color' => '#8B5CF6',
                'theme' => 'hs-matrices',
                'sort_order' => 1,
                'video_title' => 'ম্যাট্রিক্সের গুণন, ইনভার্স নির্ণয় ও ক্র্যামারস রুল (Cramer\'s Rule)',
                'video_description' => '3x3 ম্যাট্রিক্সের অ্যাডজয়েন্ট এবং একঘাত সমীকরণ জোটের সুসংগত সমাধান।',
                'youtube_id' => 'EngW7tLk6R8',
                'video_duration' => 2600,
                'questions' => [
                    [
                        'question_text' => 'যদি একটি ম্যাট্রিক্স A এর ডিটারমিন্যান্ট |A| = 0 হয়, তবে তাকে কী ম্যাট্রিক্স বলা হয়?',
                        'option_a' => 'সিঙ্গুলার ম্যাট্রিক্স (Singular Matrix)',
                        'option_b' => 'নন-সিঙ্গুলার ম্যাট্রিক্স',
                        'option_c' => 'সিমেট্রিক ম্যাট্রিক্স',
                        'option_d' => 'আইডেন্টিটি ম্যাট্রিক্স',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'একটি ম্যাট্রিক্সের ইনভার্স A⁻¹ বিদ্যমান হওয়ার শর্ত কী?',
                        'option_a' => '|A| ≠ 0',
                        'option_b' => '|A| = 0',
                        'option_c' => 'A একটি রো ম্যাট্রিক্স হওয়া',
                        'option_d' => 'কোনোটিই নয়',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 12,
                'subject_id' => 10, // Physics
                'teacher_key' => 'physics',
                'chapter_title' => 'স্থির তড়িৎ ও কুলম্বের সূত্র (Electrostatics & Gauss\'s Law)',
                'image' => 'https://images.unsplash.com/photo-1636466497217-26a8cbeaf0aa?w=600&q=80',
                'color' => '#6366F1',
                'theme' => 'hs-electrostatics',
                'sort_order' => 1,
                'video_title' => 'তড়িৎ ক্ষেত্রপ্রাবল্য, তড়িৎ দ্বিমেরু ও গাউসের উপপাদ্যের প্রয়োগ',
                'video_description' => 'আধানের কোয়ান্টায়ন, কুলম্বের সূত্র এবং ধারকত্বের (Capacitance) সমস্যা সমাধান।',
                'youtube_id' => 'libKBYuqQSg',
                'video_duration' => 2700,
                'questions' => [
                    [
                        'question_text' => 'শূন্য মাধ্যমে দুটি বিন্দু আধানের মধ্যে ক্রিয়াশীল বল সম্পর্কিত সূত্র কোনটি?',
                        'option_a' => 'কুলম্বের সূত্র (Coulomb\'s Law)',
                        'option_b' => 'ওহমের সূত্র',
                        'option_c' => 'ফ্যারাডের সূত্র',
                        'option_d' => 'অ্যাম্পিয়ারের সূত্র',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'একটি সমান্তরাল পাত ধারকের ধারকত্ব বৃদ্ধি করার উপায় কী?',
                        'option_a' => 'পাতদ্বয়ের মধ্যবর্তী স্থানে পরাবৈদ্যুতিক মাধ্যম (Dielectric) স্থাপন করা',
                        'option_b' => 'পাতদ্বয়ের দূরত্ব বৃদ্ধি করা',
                        'option_c' => 'পাতের ক্ষেত্রফল কমানো',
                        'option_d' => 'পাত দুটি উন্মুক্ত রাখা',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 12,
                'subject_id' => 11, // Chemistry
                'teacher_key' => 'chemistry',
                'chapter_title' => 'তড়িৎ রসায়ন ও রাসায়নিক গতিবিদ্যা (Electrochemistry & Kinetics)',
                'image' => 'https://images.unsplash.com/photo-1603126857599-f6e157fa2fe6?w=600&q=80',
                'color' => '#EC4899',
                'theme' => 'hs-chemistry-kinetics',
                'sort_order' => 1,
                'video_title' => 'নার্নস্ট সমীকরণ (Nernst Equation), গ্যালভানিক সেল ও প্রথম ক্রম বিক্রিয়া',
                'video_description' => 'তড়িৎদ্বার বিভব, সক্রিয়করণ শক্তি (Arrhenius Equation) এবং অর্ধায়ুর গাণিতিক সমস্যা।',
                'youtube_id' => '_uQrJ0TkZlc',
                'video_duration' => 2500,
                'questions' => [
                    [
                        'question_text' => 'প্রথম ক্রম বিক্রিয়ার অর্ধায়ু (Half-life, t_1/2) কোন সমীকরণ দ্বারা প্রকাশিত?',
                        'option_a' => 't_1/2 = 0.693 / k',
                        'option_b' => 't_1/2 = 1 / ka',
                        'option_c' => 't_1/2 = a / 2k',
                        'option_d' => 't_1/2 = 2.303 / k',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'তড়িৎ রাসায়নিক কোষে স্বতঃস্ফূর্ত বিক্রিয়ার জন্য গিভস মুক্ত শক্তির পরিবর্তন (ΔG) কেমন হবে?',
                        'option_a' => 'ঋণাত্মক (ΔG < 0)',
                        'option_b' => 'ধনাত্মক (ΔG > 0)',
                        'option_c' => 'শূন্য (ΔG = 0)',
                        'option_d' => 'অসীম',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 12,
                'subject_id' => 12, // Biology
                'teacher_key' => 'biology',
                'chapter_title' => 'বংশগতি ও ডিএনএ-এর আণবিক ভিত্তি (Genetics & Molecular Biology)',
                'image' => 'https://images.unsplash.com/photo-1530026405186-ed1ea0ac7a63?w=600&q=80',
                'color' => '#84CC16',
                'theme' => 'hs-genetics',
                'sort_order' => 1,
                'video_title' => 'ডিএনএ রেপ্লিকেশন, ট্রান্সক্রিপশন ও ট্রান্সলেশন (Central Dogma)',
                'video_description' => 'ওয়াটসন ও ক্রিকের ডিএনএ মডেল এবং জিন এক্সপ্রেশনের ল্যাক ওপেরন মডেল।',
                'youtube_id' => '9No-FiEInLA',
                'video_duration' => 2600,
                'questions' => [
                    [
                        'question_text' => 'ডিএনএ অণুর ডাবল হেলিক্স মডেল কে আবিষ্কার করেছিলেন?',
                        'option_a' => 'ওয়াটসন ও ক্রিক (Watson & Crick, 1953)',
                        'option_b' => 'গ্রেগর জোহান মেন্ডেল',
                        'option_c' => 'হারগোবিন্দ খোরানা',
                        'option_d' => 'চার্লস ডারউইন',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'আরএনএ (RNA) অণুতে থাইমিনের পরিবর্তে কোন নাইট্রোজেন বেসটি উপস্থিত থাকে?',
                        'option_a' => 'ইউরাসিল (Uracil)',
                        'option_b' => 'অ্যাডেনিন',
                        'option_c' => 'গুয়ানিন',
                        'option_d' => 'সাইটোসিন',
                        'correct_option' => 'a',
                    ],
                ],
            ],
            [
                'class_level' => 12,
                'subject_id' => 13, // Computer Application
                'teacher_key' => 'computer',
                'chapter_title' => 'ডেটাবেস ম্যানেজমেন্ট ও এসকিউএল (DBMS & SQL Queries)',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&q=80',
                'color' => '#0EA5E9',
                'theme' => 'hs-dbms',
                'sort_order' => 1,
                'video_title' => 'রিলেশনাল মডেল, প্রাইমারি কি, ফরেইন কি এবং SQL DDL/DML স্টেটমেন্ট',
                'video_description' => 'CREATE, SELECT, UPDATE, DELETE এবং ER ডায়াগ্রামের মাধ্যমে ডেটাবেস ডিজাইন।',
                'youtube_id' => 'SQ5ky5kZtWw',
                'video_duration' => 2300,
                'questions' => [
                    [
                        'question_text' => 'একটি রিলেশনাল টেবিলে প্রতিটি রেকর্ডকে অনন্যভাবে শনাক্ত করার জন্য কোন কি (Key) ব্যবহৃত হয়?',
                        'option_a' => 'Primary Key (প্রাইমারি কি)',
                        'option_b' => 'Foreign Key',
                        'option_c' => 'Candidate Key',
                        'option_d' => 'Alternate Key',
                        'correct_option' => 'a',
                    ],
                    [
                        'question_text' => 'এসকিউএল (SQL)-এ ডেটা অনুসন্ধান ও ফিল্টার করতে কোন ক্লজ ব্যবহৃত হয়?',
                        'option_a' => 'SELECT ... FROM ... WHERE',
                        'option_b' => 'INSERT ... VALUES',
                        'option_c' => 'DROP TABLE',
                        'option_d' => 'ALTER TABLE',
                        'correct_option' => 'a',
                    ],
                ],
            ],
        ];
    }
}
