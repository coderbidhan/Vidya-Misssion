<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsCurriculumContent;
use Illuminate\Database\Seeder;

class Class1To4MaterialSeeder extends Seeder
{
    use SeedsCurriculumContent;

    public function run(): void
    {
        $teacher = $this->getOrCreateTeacher();

        $curriculum = [
            // ==========================================
            // CLASS 1 (Primary - Foundation)
            // ==========================================
            1 => [
                'Bengali' => [
                    [
                        'title' => 'সহজ পাঠ - বর্ণপরিচয় ও প্রথম পাঠ (Alphabet & First Rhymes)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'সহজ পাঠ প্রথম পাঠের সংক্ষিপ্ত আলোচনা ও বর্ণমালা চার্ট',
                            'description' => 'বাংলা স্বরবর্ণ ও ব্যঞ্জনবর্ণের উচ্চারণ এবং ছোট ছোট বাক্য পড়ার নির্দেশিকা।',
                            'file_url' => 'https://vidyamission.com/materials/class1/bengali/sahaj_path_1.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'সহজ পাঠ: স্বরবর্ণ ও ব্যঞ্জনবর্ণ চেনার মজার ক্লাস',
                            'description' => 'ছবির মাধ্যমে শিশুদের বাংলা বর্ণমালা ও শব্দ গঠনের পাঠ।',
                            'video_id' => 'k3_tw44UUZQ',
                            'duration_seconds' => 1200,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ১ বাংলা আবৃত্তি ও উচ্চারণ সেশন',
                            'description' => 'শিশুদের সাথে সরাসরি ছড়া পাঠ ও উচ্চারণ অভ্যাস।',
                        ],
                        'test' => [
                            'title' => 'বর্ণমালা ও সহজ শব্দ চেনার কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'বাংলা স্বরবর্ণের প্রথম বর্ণ কোনটি?',
                                    'option_a' => 'অ',
                                    'option_b' => 'আ',
                                    'option_c' => 'ক',
                                    'option_d' => 'খ',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '"বনে থাকে বাঘ, গাছে থাকে ___" — শূন্যস্থানে কী বসবে?',
                                    'option_a' => 'পাখি',
                                    'option_b' => 'মাছ',
                                    'option_c' => 'সিংহ',
                                    'option_d' => 'হাতি',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'The Alphabet World & Fun Phonics',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'Class 1 English Alphabet and Phonics Worksheet',
                            'description' => 'Capital and small letter tracing, phonics sounds (A to Z) with colourful pictures.',
                            'file_url' => 'https://vidyamission.com/materials/class1/english/phonics_alphabet.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'Learning English Letters A to Z with Phonics Sounds',
                            'description' => 'Engaging animated lesson on letter recognition and sound pronunciation.',
                            'video_id' => 'hq3yfQnllfQ',
                            'duration_seconds' => 1100,
                        ],
                        'live_class' => [
                            'title' => 'English Fun with Phonics Live Interaction',
                            'description' => 'Interactive session for Class 1 students practicing spoken greetings and sounds.',
                        ],
                        'test' => [
                            'title' => 'Letter Recognition and Phonics Quiz',
                            'questions' => [
                                [
                                    'question_text' => 'Which letter comes immediately after "C"?',
                                    'option_a' => 'B',
                                    'option_b' => 'D',
                                    'option_c' => 'E',
                                    'option_d' => 'F',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => '"A" is for ___ (Choose the correct word):',
                                    'option_a' => 'Apple',
                                    'option_b' => 'Ball',
                                    'option_c' => 'Cat',
                                    'option_d' => 'Dog',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'আমার গণিত - ১ থেকে ২০ গণনা ও আকারের ধারণা (Numbers 1-20 & Shapes)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => '১ থেকে ২০ সংখ্যা গণনা ও বস্তু গোনার ওয়ার্কশিট',
                            'description' => 'ছবি দেখে সংখ্যা চেনা, দাগ টেনে সংখ্যা মেলানো ও ছোট-বড় তুলনা।',
                            'file_url' => 'https://vidyamission.com/materials/class1/math/counting_1_to_20.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'সহজ পদ্ধতিতে ১ থেকে ২০ সংখ্যা শেখা ও বস্তু গণনা',
                            'description' => 'ভিজ্যুয়াল উপায়ে সংখ্যার ধারণা ও গোল-চারকোনা আকারের পরিচিতি।',
                            'video_id' => 'ea5-SIe5l7M',
                            'duration_seconds' => 1300,
                        ],
                        'live_class' => [
                            'title' => 'মজার গণিত: সংখ্যা গোনা ও ছবি আঁকা',
                            'description' => 'লাইভ সেশনে শিক্ষক ও শিক্ষার্থীদের সাথে সংখ্যার ধাঁধা সমাধান।',
                        ],
                        'test' => [
                            'title' => 'সংখ্যা গণনা ও প্রাথমিক আকার কুইজ',
                            'questions' => [
                                [
                                    'question_text' => '৪-এর ঠিক পরের সংখ্যাটি কী?',
                                    'option_a' => '৩',
                                    'option_b' => '৫',
                                    'option_c' => '৬',
                                    'option_d' => '২',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'আমাদের এক হাতে কয়টি আঙুল আছে?',
                                    'option_a' => '৪',
                                    'option_b' => '৫',
                                    'option_c' => '৬',
                                    'option_d' => '১০',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Environmental Studies' => [
                    [
                        'title' => 'আমাদের শরীর, স্বাস্থ্য ও পরিষ্কার-পরিচ্ছন্নতা (Body & Cleanliness)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মানবদেহের অঙ্গপ্রত্যঙ্গ ও স্বাস্থ্যবিধি নোটস',
                            'description' => 'হাত ধোয়া, দাঁত মাজা ও স্বাস্থ্যকর অভ্যাসের নির্দেশিকা।',
                            'file_url' => 'https://vidyamission.com/materials/class1/evs/our_body_health.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'শরীর ও স্বাস্থ্য: প্রতিদিনের ভালো অভ্যাস',
                            'description' => 'শরীরের বিভিন্ন অঙ্গের কাজ এবং পরিচ্ছন্ন থাকার সহজ নিয়ম।',
                            'video_id' => 'QkHQ0CYwjaI',
                            'duration_seconds' => 1000,
                        ],
                        'live_class' => [
                            'title' => 'স্বাস্থ্যবিধি ও পরিবেশ পরিচিতি লাইভ ক্লাস',
                            'description' => 'পরিচ্ছন্নতা বজায় রাখা এবং চারপাশের পরিবেশ পরিচ্ছন্ন রাখার গল্প।',
                        ],
                        'test' => [
                            'title' => 'আমাদের শরীর ও স্বাস্থ্য সচেতনতা কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'আমরা কোন অঙ্গের সাহায্যে দেখি?',
                                    'option_a' => 'কান',
                                    'option_b' => 'চোখ',
                                    'option_c' => 'নাক',
                                    'option_d' => 'জিভ',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'খাওয়ার আগে কী দিয়ে হাত ধোয়া উচিত?',
                                    'option_a' => 'শুধু জল',
                                    'option_b' => 'সাবান ও জল',
                                    'option_c' => 'তেল',
                                    'option_d' => 'ধুলাবালি',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 2
            // ==========================================
            2 => [
                'Bengali' => [
                    [
                        'title' => 'সহজ পাঠ (দ্বিতীয় ভাগ) - আলো হয় গেল ভয় ও রাম বনে ফুল পাড়ে',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'সহজ পাঠ ২য় ভাগ - শব্দার্থ ও প্রশ্নোত্তর সহায়িকা',
                            'description' => 'যুক্তবর্ণের ধারণা, ছোট গল্পের ভাবার্থ এবং সঠিক বানান লেখার অনুশীলন।',
                            'file_url' => 'https://vidyamission.com/materials/class2/bengali/sahaj_path_2.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'সহজ পাঠ ২য় ভাগ: যুক্তবর্ণ ও ছোট গল্পের পাঠ',
                            'description' => 'যুক্তাক্ষর চেনা এবং সুন্দর করে রিডিং পড়ার কৌশল।',
                            'video_id' => 'y1rZbX7V7vQ',
                            'duration_seconds' => 1400,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ২ বাংলা বানান ও যুক্তবর্ণ অনুশীলন',
                            'description' => 'লাইভ বোর্ডে বানান শেখা ও প্রশ্নোত্তর আলোচনা।',
                        ],
                        'test' => [
                            'title' => 'যুক্তবর্ণ ও সহজ পাঠ ২য় ভাগ কুইজ',
                            'questions' => [
                                [
                                    'question_text' => '"আলো হয় গেল ভয়, চারদিক দেয় ___" — শূন্যস্থানে কী হবে?',
                                    'option_a' => 'রোদ',
                                    'option_b' => 'সায়',
                                    'option_c' => 'ডাক',
                                    'option_d' => 'ঘুম',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => '"রাম বনে ফুল পাড়ে" — রাম বনে কী পাড়তে গেছে?',
                                    'option_a' => 'ফল',
                                    'option_b' => 'ফুল',
                                    'option_c' => 'পাতা',
                                    'option_d' => 'কাঠ',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Fun with Words, Actions & Animal Kingdom',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'Class 2 English Vocabulary and Action Words Notes',
                            'description' => 'Verbs (run, jump, eat, sleep), animal babies, and simple sentence making.',
                            'file_url' => 'https://vidyamission.com/materials/class2/english/action_words.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'Action Words (Verbs) and Sentence Formation',
                            'description' => 'Animated video explaining doing words with real-life actions.',
                            'video_id' => 'dUXk8Nc5qQ8',
                            'duration_seconds' => 1250,
                        ],
                        'live_class' => [
                            'title' => 'Speaking English: Describe What You Do Daily',
                            'description' => 'Interactive live conversation class building everyday English speaking confidence.',
                        ],
                        'test' => [
                            'title' => 'Action Words and Animal Vocabulary Quiz',
                            'questions' => [
                                [
                                    'question_text' => 'What is the young one (baby) of a Dog called?',
                                    'option_a' => 'Kitten',
                                    'option_b' => 'Puppy',
                                    'option_c' => 'Calf',
                                    'option_d' => 'Cub',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'Birds ___ in the sky (Choose the correct action word):',
                                    'option_a' => 'Swim',
                                    'option_b' => 'Fly',
                                    'option_c' => 'Crawl',
                                    'option_d' => 'Run',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'আমার গণিত - দুই অঙ্কের যোগ ও বিয়োগ (Addition & Subtraction)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'দুই অঙ্কের সাধারণ যোগ ও বিয়োগের সহজ নিয়ম',
                            'description' => 'হাতে না রেখে এবং হাতে রেখে যোগ-বিয়োগের ধাপে ধাপে সমাধান।',
                            'file_url' => 'https://vidyamission.com/materials/class2/math/addition_subtraction.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'সহজ পদ্ধতিতে দুই অঙ্কের যোগ ও বিয়োগ সমাধান',
                            'description' => 'কাঠি ও মার্বেলের সাহায্যে যোগ ও বিয়োগের বাস্তবধর্মী উদাহরণ।',
                            'video_id' => 'IgKWPfJVxGg',
                            'duration_seconds' => 1500,
                        ],
                        'live_class' => [
                            'title' => 'যোগ-বিয়োগের লাইভ সমস্যার সমাধান ও স্পিড ম্যাথ',
                            'description' => 'লাইভ ক্লাসে মৌখিক যোগ-বিয়োগ প্রতিযোগিতা।',
                        ],
                        'test' => [
                            'title' => 'প্রাথমিক যোগ ও বিয়োগ মূল্যায়ন কুইজ',
                            'questions' => [
                                [
                                    'question_text' => '১২ + ৫ = কত?',
                                    'option_a' => '১৬',
                                    'option_b' => '১৭',
                                    'option_c' => '১৮',
                                    'option_d' => '১৫',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'তোমার কাছে ৮টি মার্বেল ছিল, ৩টি বন্ধুকে দিলে কটি থাকবে?',
                                    'option_a' => '৪টি',
                                    'option_b' => '৫টি',
                                    'option_c' => '৬টি',
                                    'option_d' => '১১টি',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Environmental Studies' => [
                    [
                        'title' => 'আমাদের পরিবার ও চারপাশের উদ্ভিদ জগৎ (Family & Plants Around Us)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'গাছের বিভিন্ন অংশ ও তাদের প্রয়োজনীয়তা নোটস',
                            'description' => 'মূল, কাণ্ড, পাতা, ফুল ও ফলের পরিচয় এবং আমাদের পরিবার ও সমাজের সদস্যদের কাজ।',
                            'file_url' => 'https://vidyamission.com/materials/class2/evs/plants_family.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'গাছ আমাদের পরম বন্ধু: উদ্ভিদের বিভিন্ন অংশ',
                            'description' => 'বাস্তব উদাহরণ দিয়ে গাছের বিভিন্ন অংশ ও গাছ কেন যত্ন নেওয়া দরকার তা ব্যাখ্যা।',
                            'video_id' => 'bLhxT5e5T98',
                            'duration_seconds' => 1200,
                        ],
                        'live_class' => [
                            'title' => 'প্রকৃতি পর্যবেক্ষণ ও পরিবেশ রক্ষা লাইভ সেশন',
                            'description' => 'কীভাবে আমরা বাড়ির গাছপালার যত্ন নেব এবং জল বাঁচাব।',
                        ],
                        'test' => [
                            'title' => 'উদ্ভিদ ও পরিবার পরিচিতি কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'গাছের কোন অংশ মাটির নিচে থাকে এবং জল শোষণ করে?',
                                    'option_a' => 'পাতা',
                                    'option_b' => 'শিকড় (মূল)',
                                    'option_c' => 'ফুল',
                                    'option_d' => 'ফল',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'গাছের কোন অংশকে গাছের রান্নাঘর বলা হয়?',
                                    'option_a' => 'কাণ্ড',
                                    'option_b' => 'পাতা',
                                    'option_c' => 'ফুল',
                                    'option_d' => 'মূল',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 3
            // ==========================================
            3 => [
                'Bengali' => [
                    [
                        'title' => 'পাতাবাহার - সত্যি সোনা (প্রচলিত গল্প) ও সাথী',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'পাতাবাহার "সত্যি সোনা" গল্পের সারসংক্ষেপ ও শব্দার্থ',
                            'description' => 'কঠোর পরিশ্রমের গুরুত্ব, কৃষিকাজের মূল্যবোধ এবং অনুশীলনী প্রশ্ন-উত্তর।',
                            'file_url' => 'https://vidyamission.com/materials/class3/bengali/sotti_sona_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'পাতাবাহার: সত্যি সোনা গল্পের মনোজ্ঞ বিশ্লেষণ',
                            'description' => 'বুড়ো চাষী ও তার অলস ছেলের গল্প থেকে কীভাবে জীবনের শিক্ষা মেলে।',
                            'video_id' => 'uXWycx8c8r0',
                            'duration_seconds' => 1500,
                        ],
                        'live_class' => [
                            'title' => 'গল্প বলা ও শব্দার্থ চর্চা: সত্যি সোনা',
                            'description' => 'শিক্ষার্থীদের নিয়ে গল্প পাঠ ও অনুশীলনের প্রশ্ন সমাধান।',
                        ],
                        'test' => [
                            'title' => 'সত্যি সোনা ও সাথী অধ্যায় মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'বুড়ো চাষির ছেলে কেমন স্বভাবের ছিল?',
                                    'option_a' => 'খুব অলস',
                                    'option_b' => 'খুব পরিশ্রমী',
                                    'option_c' => 'পণ্ডিত',
                                    'option_d' => 'বীর যোদ্ধা',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'বুড়ো চাষি ছেলেকে লুকানো সোনার কথা কোথায় বলেছিল?',
                                    'option_a' => 'নদীতে',
                                    'option_b' => 'জমির নিচে পোঁতা আছে',
                                    'option_c' => 'ঘরের সিন্দুকে',
                                    'option_d' => 'গাছের ডালে',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Butterfly - My Friends & Animal Meeting',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'Butterfly Lesson 1 Notes, Summary & Vocabulary',
                            'description' => 'Detailed summary, comprehension exercises, nouns, and gender forms.',
                            'file_url' => 'https://vidyamission.com/materials/class3/english/butterfly_lesson1.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'Class 3 English: My Friends & Grammar Basics',
                            'description' => 'Reading unit line by line with clear English pronunciation and grammar explanations.',
                            'video_id' => '7e90gBu4pas',
                            'duration_seconds' => 1600,
                        ],
                        'live_class' => [
                            'title' => 'English Reading & Grammar Doubt Clearing Session',
                            'description' => 'Live practice on Nouns, Pronouns, and answering textbook questions.',
                        ],
                        'test' => [
                            'title' => 'Butterfly Lesson 1 Practice MCQ Test',
                            'questions' => [
                                [
                                    'question_text' => 'Which of the following is a Pet Animal?',
                                    'option_a' => 'Tiger',
                                    'option_b' => 'Dog',
                                    'option_c' => 'Lion',
                                    'option_d' => 'Elephant',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'What is the feminine gender of "Bull"?',
                                    'option_a' => 'Mare',
                                    'option_b' => 'Cow',
                                    'option_c' => 'Bitch',
                                    'option_d' => 'Lioness',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'আমার গণিত - তিন অঙ্কের সংখ্যা, গুণ ও নামতা (3-Digit Numbers & Multiplication)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'তিন অঙ্কের স্থানীয় মান, বিস্তার ও গুণের সূত্রাবলী',
                            'description' => 'একক, দশক, শতক ধারণা এবং নামতা ব্যবহার করে সহজ গুণের নিয়ম।',
                            'file_url' => 'https://vidyamission.com/materials/class3/math/3_digit_multiplication.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'শতক, দশক ও এককের স্থানীয় মান এবং গুণের কৌশল',
                            'description' => 'কার্ড ও কাঠির সাহায্যে সহজে তিন অঙ্কের সংখ্যা বোঝা ও গুণ করার নিয়ম।',
                            'video_id' => 'vEROUuXy_88',
                            'duration_seconds' => 1800,
                        ],
                        'live_class' => [
                            'title' => 'লাইভ ম্যাথ ওয়ার্কশপ: দ্রুত গুণ ও স্থানীয় মান',
                            'description' => 'নামতার ছড়া ও দ্রুত গুণ করার আকর্ষণীয় ট্রিকস।',
                        ],
                        'test' => [
                            'title' => 'তিন অঙ্কের সংখ্যা ও গুণ মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => '৪ শতক + ৫ দশক + ২ একক = কত?',
                                    'option_a' => '৪২৫',
                                    'option_b' => '৪৫২',
                                    'option_c' => '৫৪২',
                                    'option_d' => '২৪৫',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => '১৫ × ৪ = কত?',
                                    'option_a' => '৫০',
                                    'option_b' => '৬০',
                                    'option_c' => '৭০',
                                    'option_d' => '৪৫',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Environmental Studies' => [
                    [
                        'title' => 'আমাদের পরিবেশ - চারপাশের জীবজগত ও জলের গুরুত্ব (Living World & Water)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'পরিবেশের জীব ও জড়, পানির উৎস ও সংরক্ষণ নোটস',
                            'description' => 'পশু-পাখির বাসস্থান, পানির প্রয়োজনীয়তা এবং পানীয় জল দূষণ প্রতিরোধের উপায়।',
                            'file_url' => 'https://vidyamission.com/materials/class3/evs/living_world_water.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'জীবজগৎ ও জল সংরক্ষণ: কেন জল অপচয় রোধ জরুরি',
                            'description' => 'প্রাকৃতিক পরিবেশ ও জীবকূলের বেঁচে থাকার শর্তাদি বিশ্লেষণ।',
                            'video_id' => 'F2hJ7eS80uI',
                            'duration_seconds' => 1500,
                        ],
                        'live_class' => [
                            'title' => 'আমাদের পরিবেশ আলোচনা: জল দূষণ ও সমাধান',
                            'description' => 'চারপাশের পরিবেশের সমস্যা এবং কীভাবে আমরা সহায়তা করতে পারি।',
                        ],
                        'test' => [
                            'title' => 'জীবজগত ও পরিবেশ সচেতনতা কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'কোনটি জলের প্রাকৃতিক উৎস?',
                                    'option_a' => 'বৃষ্টি ও নদী',
                                    'option_b' => 'ট্যাপের কল',
                                    'option_c' => 'প্লাস্টিক বোতল',
                                    'option_d' => 'গাড়ি',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'উদ্ভিদ খাদ্য তৈরির জন্য কোন গ্যাস বাতাস থেকে গ্রহণ করে?',
                                    'option_a' => 'অক্সিজেন',
                                    'option_b' => 'কার্বন ডাই অক্সাইড',
                                    'option_c' => 'নাইট্রোজেন',
                                    'option_d' => 'হাইড্রোজেন',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 4
            // ==========================================
            4 => [
                'Bengali' => [
                    [
                        'title' => 'পাতাবাহার - সবার আমি ছাত্র (সুনির্মল বসু) ও নরহরি দাস',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'সুনির্মল বসুর "সবার আমি ছাত্র" কবিতার সম্পূর্ণ ভাবার্থ ও ব্যাকরণ',
                            'description' => 'প্রকৃতি থেকে শিক্ষা লাভ, ছন্দ বিশ্লেষণ এবং উপেন্দ্রকিশোর রায়চৌধুরীর নরহরি দাস গল্পের প্রশ্নোত্তর।',
                            'file_url' => 'https://vidyamission.com/materials/class4/bengali/sobar_ami_chhatro.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'কবিতা বিশ্লেষণ: সবার আমি ছাত্র - প্রকৃতির পাঠশালা',
                            'description' => 'আকাশ, বাতাস, পাহাড়, খোলা মাঠ থেকে মানুষ কীভাবে উদার ও কর্মঠ হওয়ার শিক্ষা পায়।',
                            'video_id' => 'J_8q0sK8z0A',
                            'duration_seconds' => 1700,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ৪ বাংলা ব্যাকরণ: পদ পরিবর্তন ও বিপরীত শব্দ',
                            'description' => 'লাইভ সেশনে ব্যাকরণ ও বোধপরীক্ষণের প্রশ্ন আলোচনা।',
                        ],
                        'test' => [
                            'title' => 'সবার আমি ছাত্র ও নরহরি দাস মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => '"আকাশ আমায় শিক্ষা দিল ___ হতে ভাই রে" — শূন্যস্থানে কী হবে?',
                                    'option_a' => 'উদার',
                                    'option_b' => 'কর্মী',
                                    'option_c' => 'মৌন',
                                    'option_d' => 'পাষাণ',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '"নরহরি দাস" গল্পে শিয়াল কাকে মামা বলে ডাকত?',
                                    'option_a' => 'বাঘকে',
                                    'option_b' => 'সিংহকে',
                                    'option_c' => 'ছাগলকে',
                                    'option_d' => 'ভালুককে',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Butterfly - A Taste of Bengal & Curious World',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'Butterfly Lesson: A Taste of Bengal Comprehension Notes',
                            'description' => 'West Bengal culture, traditional sweets (Rosogolla, Sandesh), verbs and tenses.',
                            'file_url' => 'https://vidyamission.com/materials/class4/english/taste_of_bengal.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'A Taste of Bengal: Exploring Food, Culture & Grammar',
                            'description' => 'Line-by-line reading, vocabulary building and Past Tense exercise discussions.',
                            'video_id' => 'L_Xf8qX9w7Q',
                            'duration_seconds' => 1650,
                        ],
                        'live_class' => [
                            'title' => 'English Past Tense & Paragraph Writing Workshop',
                            'description' => 'Live practice on writing short paragraphs on festivals and favorite sweets.',
                        ],
                        'test' => [
                            'title' => 'Butterfly Lesson Practice & Grammar Test',
                            'questions' => [
                                [
                                    'question_text' => 'Which sweet is world-famous and originated in Bengal?',
                                    'option_a' => 'Rasgulla (Rosogolla)',
                                    'option_b' => 'Laddoo',
                                    'option_c' => 'Jalebi',
                                    'option_d' => 'Kaju Katli',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'What is the past tense form of the verb "Go"?',
                                    'option_a' => 'Going',
                                    'option_b' => 'Went',
                                    'option_c' => 'Gone',
                                    'option_d' => 'Goes',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'আমার গণিত - সাধারণ ভগ্নাংশ ও ঐকিক নিয়ম (Fractions & Unitary Method)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'ভগ্নাংশের ধারণা (লব ও হর), সমমানের ভগ্নাংশ ও ঐকিক নিয়ম',
                            'description' => 'ছবি দিয়ে ভগ্নাংশ বোঝা, ছোট-বড় তুলনা ও বাস্তব জীবনের ঐকিক নিয়মের উদাহরণ।',
                            'file_url' => 'https://vidyamission.com/materials/class4/math/fractions_unitary.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'ভগ্নাংশের সহজ ধারণা: লব, হর ও ছবির মাধ্যমে সমাধান',
                            'description' => 'পিজ্জা ও রুটির টুকরো দিয়ে ভগ্নাংশের বাস্তব বোঝাপড়া ও যোগ-বিয়োগের নিয়ম।',
                            'video_id' => 'M_Y8q0zL7w1',
                            'duration_seconds' => 1800,
                        ],
                        'live_class' => [
                            'title' => 'গণিত সমাধান: ঐকিক নিয়ম ও ভগ্নাংশের সমস্যা',
                            'description' => 'লাইভ বোর্ডে পাঠ্যবইয়ের অনুশীলনী ধরে প্রতিটি অঙ্ক সমাধান।',
                        ],
                        'test' => [
                            'title' => 'সাধারণ ভগ্নাংশ ও ঐকিক নিয়ম পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => '৩/৫ ভগ্নাংশটিতে লব (Numerator) কোনটি?',
                                    'option_a' => '৫',
                                    'option_b' => '৩',
                                    'option_c' => '৮',
                                    'option_d' => '২',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => '১টি কলমের দাম ৫ টাকা হলে, ৬টি কলমের দাম কত?',
                                    'option_a' => '২৫ টাকা',
                                    'option_b' => '৩০ টাকা',
                                    'option_c' => '৩৫ টাকা',
                                    'option_d' => '২০ টাকা',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Environmental Studies' => [
                    [
                        'title' => 'আমাদের পরিবেশ - পরিবেশের উপাদান: মাটি, জল ও বাতাস (Soil, Water & Air)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মাটির প্রকারভেদ (এঁটেল, বেলে, দোআঁশ) ও বায়ুমণ্ডলের গুরুত্ব',
                            'description' => 'মাটি কীভাবে গঠিত হয়, মাটির ক্ষয়রোধের উপায় এবং বিশুদ্ধ পানির প্রয়োজনীয়তা।',
                            'file_url' => 'https://vidyamission.com/materials/class4/evs/soil_water_air.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'মাটির প্রকারভেদ ও পরিবেশের সুরক্ষা',
                            'description' => 'মাটির নমুনা দেখিয়ে এঁটেল, বেলে ও দোআঁশ মাটির গুণাগুণ বিশদ আলোচনা।',
                            'video_id' => 'N_8xq1zP9r3',
                            'duration_seconds' => 1600,
                        ],
                        'live_class' => [
                            'title' => 'পরিবেশ বিজ্ঞানী: মাটি ও জল পরীক্ষা লাইভ ক্লাস',
                            'description' => 'সহজ ঘরোয়া পরীক্ষা দিয়ে মাটির জল ধারণ ক্ষমতা পর্যবেক্ষণ।',
                        ],
                        'test' => [
                            'title' => 'মাটি, জল ও বাতাস পরিবেশ বিজ্ঞান কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'কোন মাটিতে বালি ও কাদার ভাগ প্রায় সমান থাকে?',
                                    'option_a' => 'বেলে মাটি',
                                    'option_b' => 'দোআঁশ মাটি',
                                    'option_c' => 'এঁটেল মাটি',
                                    'option_d' => 'পাথুরে মাটি',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'গাছপালা কীভাবে মাটির ক্ষয় রোধ করে?',
                                    'option_a' => 'শিকড় দিয়ে মাটিকে শক্ত করে আঁকড়ে ধরে',
                                    'option_b' => 'পাতা ঝরিয়ে',
                                    'option_c' => 'ছায়া দিয়ে',
                                    'option_d' => 'ফল তৈরি করে',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($curriculum as $classLevel => $subjects) {
            foreach ($subjects as $subjectName => $chapters) {
                foreach ($chapters as $chapterData) {
                    $this->seedCurriculumUnit($classLevel, $subjectName, $chapterData, $teacher);
                }
            }
        }

        $this->command->info('Class 1 to 4 primary educational materials seeded successfully.');
    }
}
