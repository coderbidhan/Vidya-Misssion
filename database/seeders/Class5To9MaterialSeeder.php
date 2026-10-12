<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsCurriculumContent;
use Illuminate\Database\Seeder;

class Class5To9MaterialSeeder extends Seeder
{
    use SeedsCurriculumContent;

    public function run(): void
    {
        $teacher = $this->getOrCreateTeacher();

        $curriculum = [
            // ==========================================
            // CLASS 5
            // ==========================================
            5 => [
                'Bengali' => [
                    [
                        'title' => 'গল্পবুড়ো (সুনির্মল বসু) ও বোকা কুমিরের কথা',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'গল্পবুড়ো কবিতার বিষয়বস্তু, ভাবার্থ ও ব্যাকরণ বিশ্লেষণ',
                            'description' => 'শীতের ভোরে গল্পবুড়োর ডাক, রূপকথার গল্প সম্ভার এবং উপেন্দ্রকিশোর রায়চৌধুরীর গল্পের প্রশ্নোত্তর।',
                            'file_url' => 'https://vidyamission.com/materials/class5/bengali/golpoburo_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'কবিতা পাঠ ও ব্যাখ্যা: সুনির্মল বসুর গল্পবুড়ো',
                            'description' => 'ছন্দ, অলঙ্কার এবং শীতের সকালে গল্পবুড়োর ঝুলির জাদুকরী রূপকথা।',
                            'video_id' => 'P_8q1wZ7x9Q',
                            'duration_seconds' => 1750,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ৫ বাংলা আলোচনা: গল্পবুড়োর ঝুলি ও রূপকথা',
                            'description' => 'শিক্ষার্থীদের অংশগ্রহণে আবৃত্তি ও ব্যাকরণের প্রশ্ন সমাধান।',
                        ],
                        'test' => [
                            'title' => 'গল্পবুড়ো অধ্যায় মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'গল্পবুড়ো কোন ঋতুতে ডাক পেড়ে ছুটে চলেছিল?',
                                    'option_a' => 'গ্রীষ্মকালে',
                                    'option_b' => 'শীতকালে',
                                    'option_c' => 'বর্ষাকালে',
                                    'option_d' => 'শরৎকালে',
                                    'correct_option' => 'b',
                                ],
                                [
                                    'question_text' => 'গল্পবুড়োর কাঁধের ঝোলায় কিসের গল্প ভরা ছিল?',
                                    'option_a' => 'বিজ্ঞান কথাসাহিত্য',
                                    'option_b' => 'রূপকথার গল্প',
                                    'option_c' => 'ইতিহাসের যুদ্ধ',
                                    'option_d' => 'ভূগোলের মানচিত্র',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'A Feat on Feet (Tenzing & Hillary) & Buildings to Remember',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'A Feat on Feet - Chapter Summary, Questions & Past Continuous Tense',
                            'description' => 'The heroic expedition of Tenzing Norgay and Edmund Hillary conquering Mt. Everest on 29th May 1953.',
                            'file_url' => 'https://vidyamission.com/materials/class5/english/feat_on_feet.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'Conquering Mount Everest: The Story of Tenzing and Hillary',
                            'description' => 'Engaging historical account of mountaineering bravery, map study and grammar analysis.',
                            'video_id' => 'Q_9r2xL8w1A',
                            'duration_seconds' => 1800,
                        ],
                        'live_class' => [
                            'title' => 'English Grammar Live: Tenses and Prepositions Practice',
                            'description' => 'Live practice exercises on forming sentences using Simple Past and Prepositions of place.',
                        ],
                        'test' => [
                            'title' => 'A Feat on Feet Comprehension and Grammar Test',
                            'questions' => [
                                [
                                    'question_text' => 'On which date did Tenzing Norgay and Edmund Hillary reach the summit of Mt. Everest?',
                                    'option_a' => '29th May, 1953',
                                    'option_b' => '15th August, 1947',
                                    'option_c' => '26th January, 1950',
                                    'option_d' => '2nd October, 1960',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'Who led the British Mount Everest Expedition in 1953?',
                                    'option_a' => 'Colonel John Hunt',
                                    'option_b' => 'Robert Falcon Scott',
                                    'option_c' => 'George Mallory',
                                    'option_d' => 'Ernest Shackleton',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'আমার গণিত - ভগ্নাংশের সহজ ধারণা ও ঐকিক নিয়ম (Fractions & Factors)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'প্রকৃত, অপ্রকৃত ও মিশ্র ভগ্নাংশ এবং লসাগু-গসাগুর প্রাথমিক রূপ',
                            'description' => 'ভগ্নাংশের রূপান্তর, সমহরবিশিষ্টকরণ এবং দৈনন্দিন জীবনে ঐকিক নিয়মের প্রয়োগ।',
                            'file_url' => 'https://vidyamission.com/materials/class5/math/fractions_unitary.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'প্রকৃত ও অপ্রকৃত ভগ্নাংশের যোগ-বিয়োগের সহজ নিয়ম',
                            'description' => 'ধাপে ধাপে লসাগু নির্ণয় এবং ভগ্নাংশের তুলনা ও বাস্তব সমস্যার সমাধান।',
                            'video_id' => 'R_1s2xT9z3Q',
                            'duration_seconds' => 1900,
                        ],
                        'live_class' => [
                            'title' => 'গণিত সমাধান সভা: ভগ্নাংশ ও ঐকিক নিয়ম প্র্যাকটিস',
                            'description' => 'লাইভ ক্লাসে ছাত্রদের সমস্যা চিহ্নিতকরণ ও শর্টকাট পদ্ধতিতে সমাধান।',
                        ],
                        'test' => [
                            'title' => 'ভগ্নাংশ ও মৌলিক সংখ্যা মূল্যায়ন কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'যে ভগ্নাংশের লব হর অপেক্ষা ছোট, তাকে কী ভগ্নাংশ বলে?',
                                    'option_a' => 'প্রকৃত ভগ্নাংশ',
                                    'option_b' => 'অপ্রকৃত ভগ্নাংশ',
                                    'option_c' => 'মিশ্র ভগ্নাংশ',
                                    'option_d' => 'দশমিক ভগ্নাংশ',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'ক্ষুদ্রতম মৌলিক সংখ্যাটি কোনটি?',
                                    'option_a' => '১',
                                    'option_b' => '২',
                                    'option_c' => '৩',
                                    'option_d' => '০',
                                    'correct_option' => 'b',
                                ],
                            ],
                        ],
                    ],
                ],
                'Environmental Studies' => [
                    [
                        'title' => 'আমাদের পরিবেশ - মানবদেহ: ত্বক, হাড় ও পেশি (Human Body & Health)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মানবদেহের কঙ্কালতন্ত্র, অস্থিসন্ধি ও ত্বকের সুরক্ষা নোটস',
                            'description' => 'মেলানিনের কাজ, বিভিন্ন অস্থির নাম (হিউমেরাস, ফিমার) এবং পেশির সঙ্কোচন-প্রসারণ।',
                            'file_url' => 'https://vidyamission.com/materials/class5/evs/human_body_systems.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'আমাদের মানবদেহ: ত্বক ও অস্থিসন্ধির গঠন ও কাজ',
                            'description' => 'থ্রি-ডি মডেল সহযোগে মানুষের কঙ্কাল ও চামড়ার সুরক্ষামূলক ভূমিকা ব্যাখ্যা।',
                            'video_id' => 'S_2t3yU0w4A',
                            'duration_seconds' => 1700,
                        ],
                        'live_class' => [
                            'title' => 'বিজ্ঞান চর্চা: রোগ প্রতিরোধ ও পুষ্টিকর খাদ্য তালিকা',
                            'description' => 'ভিটামিন ও খনিজের প্রয়োজনীয়তা এবং স্বাস্থ্যকর জীবনযাপন নিয়ে লাইভ আলোচনা।',
                        ],
                        'test' => [
                            'title' => 'মানবদেহ ও স্বাস্থ্য সচেতনতা পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'মানবদেহের সবচেয়ে বড় হাড় (অস্থি) কোনটি?',
                                    'option_a' => 'ফিমার (উরু বা থাইয়ের হাড়)',
                                    'option_b' => 'টিবিয়া',
                                    'option_c' => 'হিউমেরাস',
                                    'option_d' => 'স্টেপিস',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'ত্বকে কোন রঞ্জক পদার্থের উপস্থিতির কারণে চামড়ার রঙ কালো বা বাদামি হয়?',
                                    'option_a' => 'মেলানিন',
                                    'option_b' => 'হিমোগ্লোবিন',
                                    'option_c' => 'ক্লোরোফিল',
                                    'option_d' => 'কেরাটিন',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 6
            // ==========================================
            6 => [
                'Bengali' => [
                    [
                        'title' => 'সাহিত্যমেলা - ভরদুপুরে (নীরেন্দ্রনাথ চক্রবর্তী) ও সেনাপতি শঙ্কর',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'নীরেন্দ্রনাথ চক্রবর্তীর "ভরদুপুরে" কবিতার মূলভাব ও ছন্দ বিশ্লেষণ',
                            'description' => 'গ্রাম-বাংলার নিস্তব্ধ মধ্যাহ্ন, অসথ গাছের ছায়া ও উদাসী পথিকের চিত্রকল্প।',
                            'file_url' => 'https://vidyamission.com/materials/class6/bengali/bhordupure_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'কবিতা পাঠ ও আলোচনা: ভরদুপুরে — নিস্তব্ধ দুপুরের গান',
                            'description' => 'চিত্রকল্প ও অনুভূতির আলোকে পল্লীবাংলার ভরদুপুরের দৃশ্যপটের নিখুঁত পর্যালোচনা।',
                            'video_id' => 'T_3u4zV1w5B',
                            'duration_seconds' => 1800,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ৬ বাংলা: শ্যামল ঘোষের সেনাপতি শঙ্কর ও ব্যাকরণ',
                            'description' => 'কাল্পনিক জগৎ, পাখির উড়াল এবং সন্ধি ও সমোচ্চারিত ভিন্নার্থক শব্দ।',
                        ],
                        'test' => [
                            'title' => 'ভরদুপুরে ও সেনাপতি শঙ্কর অধ্যায় টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => '"ভরদুপুরে" কবিতায় অসথ গাছটিকে কার সাথে তুলনা করা হয়েছে?',
                                    'option_a' => 'পথিকজনের ছাতা',
                                    'option_b' => 'পাহারাদার',
                                    'option_c' => 'সন্ন্যাসী',
                                    'option_d' => 'রাজপ্রাসাদ',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'শঙ্কর ক্লাসে আনমনা হয়ে কোন পাখির ওড়ার কথা ভাবছিল?',
                                    'option_a' => 'এমু পাখি',
                                    'option_b' => 'শঙ্খচিল',
                                    'option_c' => 'দোয়েল',
                                    'option_d' => 'ময়ূর',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Blossoms - It All Began With Drip-Drip & Adventurous Clown',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'It All Began With Drip-Drip by Alka Shankar & Sharleen Mukundan',
                            'description' => 'Full summary, character analysis of Bholenath the potter, vocabulary and grammar.',
                            'file_url' => 'https://vidyamission.com/materials/class6/english/drip_drip_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'It All Began With Drip-Drip: Humorous Story of Bholenath',
                            'description' => 'Detailed line-by-line reading, irony explanation and exercise discussions.',
                            'video_id' => 'U_4v5aW2x6C',
                            'duration_seconds' => 1850,
                        ],
                        'live_class' => [
                            'title' => 'English Blossoms Live: Sentence Structure and Punctuation',
                            'description' => 'Subject-Verb agreement, punctuation marks and answering descriptive questions.',
                        ],
                        'test' => [
                            'title' => 'Blossoms Lesson 1 Comprehension Test',
                            'questions' => [
                                [
                                    'question_text' => 'What was Bholenath by profession in "It All Began With Drip-Drip"?',
                                    'option_a' => 'A potter',
                                    'option_b' => 'A farmer',
                                    'option_c' => 'A soldier',
                                    'option_d' => 'A weaver',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'What did the tiger mistake for the terrifying creature "Drip-Drip"?',
                                    'option_a' => 'The leaking rainwater dripping from the thatched roof',
                                    'option_b' => 'A furious elephant',
                                    'option_c' => 'A hunter with a gun',
                                    'option_d' => 'A thunderstorm',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'গণিতপ্রভা - লসাগু ও গসাগু এবং অনুপাত (HCF, LCM & Ratio)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মৌলিক উৎপাদক ও ভাগ প্রক্রিয়ায় লসাগু ও গসাগু নির্ণয়ের নিয়মাবলী',
                            'description' => 'গসাগু ও লসাগুর সম্পর্ক (দুটি সংখ্যার গুণফল = গসাগু × লসাগু) এবং বাস্তব সমস্যার সমাধান।',
                            'file_url' => 'https://vidyamission.com/materials/class6/math/hcf_lcm_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'লসাগু ও গসাগু: শর্টকাট কৌশল ও পাটিগণিতের বাস্তব প্রয়োগ',
                            'description' => 'ঘণ্টা বাজার সময় এবং মাপার ফিতার বাস্তব উদাহরণ দিয়ে গসাগু ও লসাগু শেখা।',
                            'video_id' => 'V_5w6bX3y7D',
                            'duration_seconds' => 1950,
                        ],
                        'live_class' => [
                            'title' => 'গণিতপ্রভা লাইভ ক্লাস: অনুপাত ও সমানুপাত ধারণা',
                            'description' => 'অনুপাতের লঘিষ্ঠ আকার এবং সমানুপাতের চারটি পদের সম্পর্ক সমাধান।',
                        ],
                        'test' => [
                            'title' => 'লসাগু ও গসাগু মূল্যায়ন কুইজ',
                            'questions' => [
                                [
                                    'question_text' => 'দুটি পরস্পর মৌলিক সংখ্যার গসাগু কত হয়?',
                                    'option_a' => '১',
                                    'option_b' => '০',
                                    'option_c' => '২',
                                    'option_d' => 'সংখ্যা দুটির গুণফল',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '১২ এবং ১৮-এর গসাগু (HCF) কত?',
                                    'option_a' => '৬',
                                    'option_b' => '৩',
                                    'option_c' => '৩৬',
                                    'option_d' => '২',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'History' => [
                    [
                        'title' => 'অতীত ও ঐতিহ্য - হরপ্পা সভ্যতা ও প্রাচীন ভারতীয় নগরজীবন (Harappa)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'হরপ্পা ও মহেঞ্জোদাড়ো নগর পরিকল্পনা ও সমাজব্যবস্থা নোটস',
                            'description' => 'পাকা ড্রেন, শস্যাগার, স্নানাগার, সিলমোহর এবং হরপ্পা সভ্যতার পতনের কারণসমূহ।',
                            'file_url' => 'https://vidyamission.com/materials/class6/history/harappa_civilization.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'হরপ্পা সভ্যতা: বিশ্বের প্রাচীনতম পরিকল্পিত নগর সংস্কৃতি',
                            'description' => 'প্রত্নতাত্ত্বিক নিদর্শন, রাখালদাস বন্দ্যোপাধ্যায় ও দয়ারাম সাহানির আবিষ্কারের ইতিহাস।',
                            'video_id' => 'W_6x7cY4z8E',
                            'duration_seconds' => 1900,
                        ],
                        'live_class' => [
                            'title' => 'ইতিহাস লাইভ: হরপ্পা সভ্যতার জীবনযাত্রা ও ব্যবসা-বাণিজ্য',
                            'description' => 'মেসোপটেমিয়ার সাথে বাণিজ্য এবং পোড়ামাটির শিল্পকর্ম নিয়ে লাইভ সেশন।',
                        ],
                        'test' => [
                            'title' => 'হরপ্পা সভ্যতা ইতিহাস মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'মহেঞ্জোদাড়োর বিখ্যাত প্রত্নতাত্ত্বিক স্থানটি কে আবিষ্কার করেন?',
                                    'option_a' => 'রাখালদাস বন্দ্যোপাধ্যায়',
                                    'option_b' => 'দয়ারাম সাহানি',
                                    'option_c' => 'আলেকজান্ডার কানিংহাম',
                                    'option_d' => 'জন মার্শাল',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'হরপ্পা সভ্যতার সবচেয়ে উল্লেখযোগ্য বৈশিষ্ট্য কোনটি ছিল?',
                                    'option_a' => 'উন্নত নগর পরিকল্পনা ও নিকাশি ব্যবস্থা',
                                    'option_b' => 'লোহার তৈরি অস্ত্রশস্ত্র',
                                    'option_c' => 'ঘোড়ার ব্যাপক ব্যবহার',
                                    'option_d' => 'মুদ্রার বহুল প্রচলন',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Geography' => [
                    [
                        'title' => 'আমাদের পৃথিবী - আকাশভরা সূর্য-তারা ও পৃথিবীর আবর্তন (Solar System)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'সৌরজগৎ, গ্রহের পরিচয় ও আহ্নিক গতির ফলাফল নোটস',
                            'description' => 'আলোকবর্ষ, ছায়াপথ, গ্রহ ও নক্ষত্রের পার্থক্য, দিন-রাত্রির কারণ ও অক্ষের হেলে থাকা।',
                            'file_url' => 'https://vidyamission.com/materials/class6/geography/solar_system_earth.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'সৌরজগৎ ও মহাবিশ্ব: আমাদের বাসস্থান পৃথিবী',
                            'description' => 'থ্রি-ডি স্পেস অ্যানিমেশনে সূর্য, আটটি গ্রহ ও চন্দ্রের পরিক্রমণ ব্যাখ্যা।',
                            'video_id' => 'X_7y8dZ5w9F',
                            'duration_seconds' => 1850,
                        ],
                        'live_class' => [
                            'title' => 'ভূগোল লাইভ: পৃথিবীর আবর্তন ও ঋতু পরিবর্তনের প্রাথমিক ধারণা',
                            'description' => 'গ্লোব ব্যবহার করে দিন-রাত্রি সংগঠন এবং কর্কটক্রান্তি রেখা আলোচনা।',
                        ],
                        'test' => [
                            'title' => 'সৌরজগৎ ও পৃথিবীর গতি মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'সৌরজগতের বৃহত্তম গ্রহ কোনটি?',
                                    'option_a' => 'বৃহস্পতি (Jupiter)',
                                    'option_b' => 'শনি (Saturn)',
                                    'option_c' => 'পৃথিবী (Earth)',
                                    'option_d' => 'মঙ্গল (Mars)',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'পৃথিবীর কোন গতির ফলে দিন এবং রাত্রি সংঘটিত হয়?',
                                    'option_a' => 'আহ্নিক গতি (Rotation)',
                                    'option_b' => 'বার্ষিক গতি (Revolution)',
                                    'option_c' => 'মহাকর্ষীয় গতি',
                                    'option_d' => 'অভিকর্ষীয় গতি',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Science & Environment' => [
                    [
                        'title' => 'পরিবেশ ও বিজ্ঞান - পদার্থের ভৌত ও রাসায়নিক পরিবর্তন (States of Matter)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'পদার্থের ভৌত অবস্থা, পরিবর্তন ও মিশ্রণ পৃথকীকরণ নোটস',
                            'description' => 'কঠিন, তরল, গ্যাসীয় অবস্থা, পরিশ্রাবণ, বাষ্পীভবন ও রাসায়নিক বিক্রিয়া চেনার উপায়।',
                            'file_url' => 'https://vidyamission.com/materials/class6/science/matter_changes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'ভৌত ও রাসায়নিক পরিবর্তনের পার্থক্য ও পরীক্ষামূলক প্রমাণ',
                            'description' => 'বরফ গলে জল হওয়া বনাম মোমবাতি জ্বালানো ও লোহার মরচে ধরা বিশ্লেষণ।',
                            'video_id' => 'Y_8z9eA6x0G',
                            'duration_seconds' => 1800,
                        ],
                        'live_class' => [
                            'title' => 'বিজ্ঞানের মজার পরীক্ষা: মিশ্রণ পৃথকীকরণ ও ফিল্ট্রেশন',
                            'description' => 'লাইভ ল্যাব ডেমোনেস্ট্রেশনে সাধারণ উপকরণ দিয়ে দ্রবণ ও অধঃক্ষেপণ।',
                        ],
                        'test' => [
                            'title' => 'পদার্থের পরিবর্তন বিজ্ঞান পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'লোহায় মরিচা ধরা কী ধরনের পরিবর্তন?',
                                    'option_a' => 'রাসায়নিক পরিবর্তন',
                                    'option_b' => 'ভৌত পরিবর্তন',
                                    'option_c' => 'উভমুখী পরিবর্তন',
                                    'option_d' => 'সাময়িক পরিবর্তন',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'জল জমে বরফে পরিণত হলে তার অবস্থার কী রূপ পরিবর্তন ঘটে?',
                                    'option_a' => 'ভৌত পরিবর্তন (তরল থেকে কঠিন)',
                                    'option_b' => 'রাসায়নিক পরিবর্তন',
                                    'option_c' => 'নতুন যৌগ উৎপন্ন হয়',
                                    'option_d' => 'কোনো পরিবর্তন ঘটে না',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 7
            // ==========================================
            7 => [
                'Bengali' => [
                    [
                        'title' => 'সাহিত্যমেলা - পাগলা দাশু (সুকুমার রায়) ও ছন্দে শুধু কান রাখো',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'সুকুমার রায়ের "পাগলা দাশু" গল্পের চরিত্র চিত্রণ ও হিউমার বিশ্লেষণ',
                            'description' => 'দাশুর আশ্চর্য বুদ্ধি, স্কুলের বন্ধুদের প্রতিক্রিয়া এবং অজিত দত্তের কবিতার মূলভাব।',
                            'file_url' => 'https://vidyamission.com/materials/class7/bengali/pagla_dashu_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'পাগলা দাশু: হাসির মোড়কে তীক্ষ্ণ বুদ্ধি ও স্কুল জীবনের গল্প',
                            'description' => 'সুকুমার রায়ের শিশু সাহিত্যের বিশেষত্ব এবং দাশুর অদ্ভুত সব কীর্তিকলাপের পাঠ।',
                            'video_id' => 'Z_9a0fB7y1H',
                            'duration_seconds' => 1900,
                        ],
                        'live_class' => [
                            'title' => 'বাংলা ব্যাকরণ লাইভ: কারক ও বিভক্তি নির্ণয়ের সহজ উপায়',
                            'description' => 'কর্তা, কর্ম, করণ, অপাদান ও অধিকরণ কারকের বাস্তব নিয়মাবলী চর্চা।',
                        ],
                        'test' => [
                            'title' => 'পাগলা দাশু ও ব্যাকরণ মূল্যায়ন কুইজ',
                            'questions' => [
                                [
                                    'question_text' => '"পাগলা দাশু" চরিত্রটির স্রষ্টা কে?',
                                    'option_a' => 'সুকুমার রায়',
                                    'option_b' => 'উপেন্দ্রকিশোর রায়চৌধুরী',
                                    'option_c' => 'সত্যজিৎ রায়',
                                    'option_d' => 'লীলা মজুমদার',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'দাশু তার কাণ্ডকারখানা নিয়ে বন্ধুদের কাছে কী প্রমাণ করতে ভালোবাসত?',
                                    'option_a' => 'তার অদ্ভুত খেয়ালি বুদ্ধি ও কৌতুকবোধ',
                                    'option_b' => 'সে ক্লাসের ফার্স্ট বয়',
                                    'option_c' => 'সে খুব ধনী',
                                    'option_d' => 'সে খুব রাগী',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Blossoms - The Book of Nature (Jawaharlal Nehru) & The Riddle',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'The Book of Nature - Letters from a Father to His Daughter',
                            'description' => 'How pebbles, mountains, rivers and fossils tell the magnificent story of the ancient Earth.',
                            'file_url' => 'https://vidyamission.com/materials/class7/english/book_of_nature.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'The Book of Nature: Reading the Earth\'s History in Rocks & Stones',
                            'description' => 'Philosophical and scientific understanding of Nehru’s letters to Indira Gandhi.',
                            'video_id' => 'A_0b1gC8z2I',
                            'duration_seconds' => 1950,
                        ],
                        'live_class' => [
                            'title' => 'English Writing Skills: Formal Letter and Descriptive Writing',
                            'description' => 'Live step-by-step guidance on writing letters and identifying Participles & Gerunds.',
                        ],
                        'test' => [
                            'title' => 'The Book of Nature Comprehension Test',
                            'questions' => [
                                [
                                    'question_text' => 'To whom did Jawaharlal Nehru write the letters comprising "The Book of Nature"?',
                                    'option_a' => 'His young daughter, Indira',
                                    'option_b' => 'Mahatma Gandhi',
                                    'option_c' => 'Rabindranath Tagore',
                                    'option_d' => 'Sarojini Naidu',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'According to Nehru, what helps us read the first pages of nature\'s book?',
                                    'option_a' => 'Rocks, pebbles, mountains and fossils',
                                    'option_b' => 'Printed textbooks only',
                                    'option_c' => 'Television news',
                                    'option_d' => 'Social media',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'গণিতপ্রভা - বীজগণিতীয় সূত্রাবলী (Algebraic Identities)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => '(a+b)², (a-b)², a²-b² সূত্রের জ্যামিতিক প্রমাণ ও অংকের কৌশল',
                            'description' => 'বর্গ নির্ণয়, মান নির্ণয়ের অংক ও উৎপাদকে বিশ্লেষণের প্রাথমিক সূত্রাবলীর বিশদ গাইড।',
                            'file_url' => 'https://vidyamission.com/materials/class7/math/algebraic_identities.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'বীজগণিতের মৌলিক সূত্র: (a+b)² ও (a-b)²-এর জ্যামিতিক ও বীজগাণিতিক প্রমাণ',
                            'description' => 'বর্গের ছবির সাহায্যে সূত্র প্রমাণ এবং ধাপে ধাপে কঠিন মান নির্ণয়ের সমাধান।',
                            'video_id' => 'B_1c2hD9w3J',
                            'duration_seconds' => 2100,
                        ],
                        'live_class' => [
                            'title' => 'বীজগণিত ডাউট ক্লিয়ারিং: সূত্রের সাহায্যে দ্রুত সমাধান',
                            'description' => 'লাইভ সেশনে পাঠ্যবইয়ের নিজে করি ও কোষে দেখি ১২.১, ১২.২ সমাধান।',
                        ],
                        'test' => [
                            'title' => 'বীজগণিতীয় সূত্রাবলী টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => '(a + b)² সূত্রের সঠিক রূপ কোনটি?',
                                    'option_a' => 'a² + 2ab + b²',
                                    'option_b' => 'a² - 2ab + b²',
                                    'option_c' => 'a² + b²',
                                    'option_d' => 'a² - b²',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'যদি a + b = 5 এবং ab = 6 হয়, তবে a² + b²-এর মান কত?',
                                    'option_a' => '১৩',
                                    'option_b' => '৩৭',
                                    'option_c' => '১৯',
                                    'option_d' => '২৫',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'History' => [
                    [
                        'title' => 'অতীত ও ঐতিহ্য - মুঘল সাম্রাজ্য ও আকবরের শাসনব্যবস্থা (Mughal Empire)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মনসবদারি প্রথা, সুলহ-ই-কুল, দীন-ই-ইলাহী ও আকবরের ধর্মনীতি নোটস',
                            'description' => 'পানিপথের দ্বিতীয় যুদ্ধ, বৈরাম খাঁ, রাজা তোডরমলের রাজস্ব নীতি এবং জাবতি প্রথা।',
                            'file_url' => 'https://vidyamission.com/materials/class7/history/mughal_empire_akbar.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'মহামতি আকবর: প্রশাসনিক সংস্কার ও জাতীয় ঐক্যের রাজনীতি',
                            'description' => 'মনসবদারি ব্যবস্থা ও ধর্মনিরপেক্ষ শাসনের বিস্তারিত পর্যালোচনা।',
                            'video_id' => 'C_2d3iE0x4K',
                            'duration_seconds' => 2050,
                        ],
                        'live_class' => [
                            'title' => 'ইতিহাস লাইভ: মুঘল স্থাপত্য, চিত্রকলা ও নবরত্ন সভা',
                            'description' => 'ফতেহপুর সিক্রি ও তানসেন-বীরবল-আবুল ফজলের ভূমিকা আলোচনা।',
                        ],
                        'test' => [
                            'title' => 'মুঘল সাম্রাজ্য ইতিহাস মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'মুঘল শাসনব্যবস্থায় "মনসব" কথাটির অর্থ কী?',
                                    'option_a' => 'পদমর্যাদা বা স্থান',
                                    'option_b' => 'জমির খতিয়ান',
                                    'option_c' => 'যুদ্ধের ঘোড়া',
                                    'option_d' => 'রাজকীয় তরবারি',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'আকবরের রাজসভায় রাজস্ব ব্যবস্থার প্রধান সংস্কারক কে ছিলেন?',
                                    'option_a' => 'রাজা তোডরমল',
                                    'option_b' => 'বীরবল',
                                    'option_c' => 'মানসিংহ',
                                    'option_d' => 'বৈরাম খাঁ',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Geography' => [
                    [
                        'title' => 'আমাদের পৃথিবী - বায়ুচাপ ও বায়ুপ্রবাহ এবং নদী (Air Pressure & Rivers)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'বায়ুচাপ বলয়, নিয়ত বায়ুপ্রবাহ ও নদীর বিভিন্ন গতি ও ভূমিরূপ',
                            'description' => 'উচ্চচাপ ও নিম্নচাপ, বাণিজ্য বায়ু, পশ্চিমা বায়ু এবং নদীর উচ্চগতিতে গঠিত আই ও ভি আকৃতির উপত্যকা।',
                            'file_url' => 'https://vidyamission.com/materials/class7/geography/air_pressure_rivers.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'বায়ুর চাপ ও বায়ুপ্রবাহ: পৃথিবী জোড়া চাপের সমীকরণ',
                            'description' => 'নিরক্ষীয় নিম্নচাপ বলয় এবং ফেরেলের সূত্র অনুযায়ী বায়ুর দিকবিক্ষেপ।',
                            'video_id' => 'D_3e4jF1y5L',
                            'duration_seconds' => 2000,
                        ],
                        'live_class' => [
                            'title' => 'ভূগোল মানচিত্র ও ডায়াগ্রাম ওয়ার্কশপ: নদীর ভূমিরূপ আঁকা',
                            'description' => 'অশ্বখুরাকৃতি হ্রদ, বদ্বীপ এবং জলপ্রপাতের চিত্রাঙ্কন অনুশীলন।',
                        ],
                        'test' => [
                            'title' => 'বায়ুচাপ ও নদী ভূমিরূপ পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'কোন সূত্রানুযায়ী উত্তর গোলার্ধে বায়ু ডানদিকে এবং দক্ষিণ গোলার্ধে বাঁদিকে বেঁকে যায়?',
                                    'option_a' => 'ফেরেলের সূত্র (Ferrel\'s Law)',
                                    'option_b' => 'বয়েলের সূত্র',
                                    'option_c' => 'নিউটনের গতিসূত্র',
                                    'option_d' => 'কেপলারের সূত্র',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'নদীর উচ্চগতিতে ক্ষয়কার্যের ফলে ইংরেজি কোন অক্ষরের মতো উপত্যকা সৃষ্টি হয়?',
                                    'option_a' => 'I বা V আকৃতির উপত্যকা',
                                    'option_b' => 'U আকৃতির উপত্যকা',
                                    'option_c' => 'O আকৃতির হ্রদ',
                                    'option_d' => 'S আকৃতির বদ্বীপ',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Science & Environment' => [
                    [
                        'title' => 'পরিবেশ ও বিজ্ঞান - পরমাণু, অণু ও রাসায়নিক বিক্রিয়া (Atoms & Reactions)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মৌলের প্রতীক, সংকেত, যোজ্যতা ও রাসায়নিক সমীকরণ সমতাবিধান',
                            'description' => 'প্রোটন, নিউট্রন ও ইলেকট্রন কণা, ক্যাটায়ন ও অ্যানায়ন এবং বিক্রিয়ার প্রকারভেদ।',
                            'file_url' => 'https://vidyamission.com/materials/class7/science/atoms_reactions.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'পরমাণুর অভ্যন্তরীণ গঠন ও রাসায়নিক সংকেত লেখার নিয়ম',
                            'description' => 'যোজ্যতার ক্রস মেথডে সংকেত লেখা এবং প্রত্যক্ষ সংযোগ বিক্রিয়া ব্যাখ্যা।',
                            'video_id' => 'E_4f5kG2z6M',
                            'duration_seconds' => 2100,
                        ],
                        'live_class' => [
                            'title' => 'বিজ্ঞান লাইভ: রাসায়নিক সমীকরণ সমতাবিধান (Balancing Equations)',
                            'description' => 'লাইভ বোর্ডে কঠিন সমীকরণ ধরে ব্যালেন্সিং করার ধাপে ধাপে পদ্ধতি।',
                        ],
                        'test' => [
                            'title' => 'পরমাণু, অণু ও রাসায়নিক বিক্রিয়া পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'পরমাণুর নিউক্লিয়াসে কোন কোন কণা অবস্থান করে?',
                                    'option_a' => 'প্রোটন ও নিউট্রন',
                                    'option_b' => 'শুধু ইলেকট্রন',
                                    'option_c' => 'প্রোটন ও ইলেকট্রন',
                                    'option_d' => 'শুধু নিউট্রন',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'জলের রাসায়নিক সংকেত কোনটি?',
                                    'option_a' => 'H₂O',
                                    'option_b' => 'CO₂',
                                    'option_c' => 'NaCl',
                                    'option_d' => 'O₂',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 8
            // ==========================================
            8 => [
                'Bengali' => [
                    [
                        'title' => 'সাহিত্যমেলা - বোঝাপড়া (রবীন্দ্রনাথ ঠাকুর) ও অদ্ভুত আতিথেয়তা',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'রবীন্দ্রনাথ ঠাকুরের "বোঝাপড়া" কবিতার জীবনদর্শন ও ভাবার্থ',
                            'description' => 'মনের সাথে বোঝাপড়া করা, আঘাত সয়ে এগিয়ে চলা ও ঈশ্বরচন্দ্র বিদ্যাসাগরের গল্পের আলোচনা।',
                            'file_url' => 'https://vidyamission.com/materials/class8/bengali/bojhapora_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'বোঝাপড়া: বিপদে মোরে রক্ষা করো নয়, বিপদ দেখে ভয় না পাওয়ার রবীন্দ্রনাথের দর্শন',
                            'description' => 'কবিতার প্রতিটি স্তবকের কাব্যিক সৌন্দর্য ও আধ্যাত্মিক মূল্যবোধের বিশদ বিশ্লেষণ।',
                            'video_id' => 'F_5g6lH3w7N',
                            'duration_seconds' => 2000,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ৮ বাংলা ব্যাকরণ: দল বিশ্লেষণ ও ণ-ত্ব ও ষ-ত্ব বিধান',
                            'description' => 'মুক্তদল ও রুদ্ধদল চেনা এবং বানান শুদ্ধির নিয়মাবলী।',
                        ],
                        'test' => [
                            'title' => 'বোঝাপড়া ও অদ্ভুত আতিথেয়তা টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => '"মনেরে আজ কহ যে, ভালো মন্দ যাহাই আসুক ___ লও সহজে" — শূন্যস্থানে কী হবে?',
                                    'option_a' => 'সত্যেরে',
                                    'option_b' => 'কষ্টেরে',
                                    'option_c' => 'জীবনেরে',
                                    'option_d' => 'শান্তিরে',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '"অদ্ভুত আতিথেয়তা" গল্পে কোন দুই জাতির যুদ্ধের পটভূমি বর্ণিত হয়েছে?',
                                    'option_a' => 'আরব ও মুর',
                                    'option_b' => 'গ্রিক ও রোমান',
                                    'option_c' => 'মুঘল ও পাঠান',
                                    'option_d' => 'ইংরেজ ও ফরাসি',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Blossoms - The Great Escape (Netaji Subhas Chandra Bose)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'The Great Escape - Sisir Bose\'s Account of Netaji\'s Historic Journey',
                            'description' => 'Netaji\'s secret departure from Elgin Road house disguised as Muhammad Ziauddin in 1941.',
                            'file_url' => 'https://vidyamission.com/materials/class8/english/great_escape_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'The Great Escape: The Midnight Journey of Freedom Fighter Subhas Bose',
                            'description' => 'Historical thrill of the midnight car drive to Gomoh railway station.',
                            'video_id' => 'G_6h7mI4x8O',
                            'duration_seconds' => 2050,
                        ],
                        'live_class' => [
                            'title' => 'English Grammar Live: Voice Change (Active to Passive Voice)',
                            'description' => 'Mastering rules of Voice Change across all Tenses with textbook examples.',
                        ],
                        'test' => [
                            'title' => 'The Great Escape & Voice Change Test',
                            'questions' => [
                                [
                                    'question_text' => 'What was the assumed name of Subhas Chandra Bose when he escaped in disguise?',
                                    'option_a' => 'Muhammad Ziauddin',
                                    'option_b' => 'Rash Behari Bose',
                                    'option_c' => 'Bhagat Ram Talwar',
                                    'option_d' => 'Akbar Shah',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'Which car was used by Sisir and Subhas Bose to escape from Calcutta?',
                                    'option_a' => 'Wanderer',
                                    'option_b' => 'Ambassador',
                                    'option_c' => 'Fiat',
                                    'option_d' => 'Ford',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'গণিতপ্রভা - মূলদ সংখ্যার ধারণা ও উৎপাদকে বিশ্লেষণ (Rational Numbers)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'মূলদ সংখ্যা (p/q আকার, q≠0), সংখ্যারেখায় স্থাপন ও বীজগণিতীয় উৎপাদক',
                            'description' => 'যোগ ও গুণের সংযোগ ও বিনিময় নিয়ম, মধ্যপদ সহগ বিশ্লেষণ (Middle Term Break)।',
                            'file_url' => 'https://vidyamission.com/materials/class8/math/rational_numbers.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'মূলদ সংখ্যা ও মিডিল টার্ম উৎপাদকে বিশ্লেষণের সেরা ট্রিকস',
                            'description' => 'সহজে দ্বিঘাত রাশিমালার মধ্যপদ ভেঙে উৎপাদকে বিশ্লেষণ করার কৌশল।',
                            'video_id' => 'H_7i8nJ5y9P',
                            'duration_seconds' => 2150,
                        ],
                        'live_class' => [
                            'title' => 'গণিতপ্রভা লাইভ: ঘনফল নির্ণয় (Cube Formulae: (a+b)³ & a³+b³)',
                            'description' => 'ঘনফলের সূত্রাবলীর মান নির্ণয় ও প্রমাণ সমাধান।',
                        ],
                        'test' => [
                            'title' => 'মূলদ সংখ্যা ও উৎপাদকে বিশ্লেষণ টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => 'যে সংখ্যাকে p/q আকারে প্রকাশ করা যায় (যেখানে p, q পূর্ণসংখ্যা এবং q ≠ 0), তাকে কী সংখ্যা বলে?',
                                    'option_a' => 'মূলদ সংখ্যা (Rational Number)',
                                    'option_b' => 'অমূলদ সংখ্যা',
                                    'option_c' => 'অবাস্তব সংখ্যা',
                                    'option_d' => 'অখণ্ড সংখ্যা নয় এমন কিছু',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'x² + 5x + 6-এর উৎপাদকে বিশ্লেষিত রূপ কোনটি?',
                                    'option_a' => '(x + 2)(x + 3)',
                                    'option_b' => '(x - 2)(x - 3)',
                                    'option_c' => '(x + 1)(x + 6)',
                                    'option_d' => '(x - 1)(x + 6)',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'History' => [
                    [
                        'title' => 'অতীত ও ঐতিহ্য - ১৮৫৭ সালের মহাবিদ্রোহ ও জাতীয়তাবাদের সূচনা (Revolt of 1857)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'সিপাহি বিদ্রোহের কারণ, বিস্তার ও ব্যর্থতার ফলাফল এবং মহারানির ঘোষণাপত্র',
                            'description' => 'মঙ্গল পাণ্ডে, ঝাঁসির রানি লক্ষ্মীবাঈ, নানাসাহেব এবং ব্রিটিশ ইস্ট ইন্ডিয়া কোম্পানির শাসনের অবসান।',
                            'file_url' => 'https://vidyamission.com/materials/class8/history/revolt_of_1857.pdf',
                        ],
                        'recorded_class' => [
                            'title' => '১৮৫৭ সালের মহাবিদ্রোহ: ভারতের প্রথম স্বাধীনতা সংগ্রাম',
                            'description' => 'এনফিল্ড রাইফেলের কার্তুজ থেকে শুরু করে ব্যারাকপুর ও মিরাটের আগ্নেয় বিস্ফোরণ।',
                            'video_id' => 'I_8j9oK6z0Q',
                            'duration_seconds' => 2100,
                        ],
                        'live_class' => [
                            'title' => 'ইতিহাস লাইভ: ১৮৫৮ সালের ভারত শাসন আইন ও মহারানির ঘোষণাপত্র',
                            'description' => 'কোম্পানি রাজের অবসান ও সরাসরি ব্রিটিশ রাজমুকুটের অধীনস্থ ভারত।',
                        ],
                        'test' => [
                            'title' => '১৮৫৭ মহাবিদ্রোহ ইতিহাস পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'ব্যারাকপুর সেনা ছাউনিতে ১৮৫৭ সালের বিদ্রোহের প্রথম সূচনা কে করেছিলেন?',
                                    'option_a' => 'মঙ্গল পাণ্ডে',
                                    'option_b' => 'তাঁতিয়া তোপি',
                                    'option_c' => 'দ্বিতীয় বাহাদুর শাহ',
                                    'option_d' => 'কুনওয়ার সিং',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '১৮৫৭ সালের বিদ্রোহের পর ব্রিটিশ পার্লামেন্ট কোন আইনের মাধ্যমে কোম্পানির শাসনের অবসান ঘটায়?',
                                    'option_a' => '১৮৫৮ সালের উন্নততর ভারত শাসন আইন',
                                    'option_b' => 'পিটের ভারত শাসন আইন',
                                    'option_c' => 'রেগুলেটিং অ্যাক্ট',
                                    'option_d' => 'চার্টার অ্যাক্ট ১৮১৩',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Geography' => [
                    [
                        'title' => 'আমাদের পৃথিবী - পৃথিবীর অন্দরমহল ও পাতসংস্থান তত্ত্ব (Earth\'s Interior)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'ভূত্বক (শিয়াল ও সিমা), গুরুমণ্ডল ও কেন্দ্রমণ্ডল (নিফে) নোটস',
                            'description' => 'মোহোরোভিচিস বিযুক্তি, গুটেনবার্গ বিযুক্তি, পাতের সীমানা এবং ভূমিকম্পের তরঙ্গের আচরণ।',
                            'file_url' => 'https://vidyamission.com/materials/class8/geography/earths_interior_plates.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'পৃথিবীর অন্দরমহল: ভূত্বক থেকে ভূকেন্দ্র পর্যন্ত রহস্য উন্মোচন',
                            'description' => 'ভূমিকম্পের P ও S তরঙ্গের গতিবিধি দিয়ে ভূ-অভ্যন্তরের স্তরবিন্যাস বোঝা।',
                            'video_id' => 'J_9k0pL7w1R',
                            'duration_seconds' => 2050,
                        ],
                        'live_class' => [
                            'title' => 'ভূগোল লাইভ: টেকটনিক পাত ও আগ্নেয়গিরি সৃষ্টি',
                            'description' => 'অভিসারী, প্রতিসারী ও নিরপেক্ষ পাত সীমানার ফলাফল।',
                        ],
                        'test' => [
                            'title' => 'পৃথিবীর অন্দরমহল ও পাতসংস্থান পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'মহাদেশীয় ভূত্বক প্রধানত কোন দুটি উপাদানে গঠিত?',
                                    'option_a' => 'সিলিকা ও অ্যালুমিনিয়াম (SIAL)',
                                    'option_b' => 'সিলিকা ও ম্যাগনেসিয়াম (SIMA)',
                                    'option_c' => 'নিকেল ও লোহা (NIFE)',
                                    'option_d' => 'ক্যালসিয়াম ও লোহা',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'ভূত্বক ও গুরুমণ্ডলের সংযোগকারী বিযুক্তি রেখাটির নাম কী?',
                                    'option_a' => 'মোহোরোভিচিস বিযুক্তি (Moho Discontinuity)',
                                    'option_b' => 'কনরাড বিযুক্তি',
                                    'option_c' => 'গুটেনবার্গ বিযুক্তি',
                                    'option_d' => 'লেহম্যান বিযুক্তি',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Science & Environment' => [
                    [
                        'title' => 'পরিবেশ ও বিজ্ঞান - বল ও চাপ এবং আলোর প্রতিফলন (Force, Pressure & Light)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'বল (F=ma), ঘর্ষণ বল, প্লবতা, আর্কিমিডিসের নীতি ও আলোর প্রতিফলন',
                            'description' => 'বায়ুমণ্ডলীয় চাপ (পাস্কাল), পারদ ব্যারোমিটার, গলীয় দর্পণ ও প্রতিবিম্ব গঠন।',
                            'file_url' => 'https://vidyamission.com/materials/class8/science/force_pressure_light.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'বল, চাপ ও প্লবতা: আর্কিমিডিসের সূত্র ও ভাসন-নিমজ্জনের শর্ত',
                            'description' => 'জাহাজ জলে ভাসে কিন্তু লোহার পেরেক কেন ডোবে — বাস্তব বিজ্ঞান বিশ্লেষণ।',
                            'video_id' => 'K_0l1qM8x2S',
                            'duration_seconds' => 2200,
                        ],
                        'live_class' => [
                            'title' => 'বিজ্ঞান সমাধান লাইভ: পাস্কালের সূত্র ও গলীয় দর্পণ',
                            'description' => 'অবতল ও উত্তল দর্পণে প্রতিবিম্ব অঙ্কন এবং সংখ্যাসূচক অংক সমাধান।',
                        ],
                        'test' => [
                            'title' => 'বল, চাপ ও আলো বিজ্ঞান পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'এস.আই (SI) পদ্ধতিতে চাপের একক কী?',
                                    'option_a' => 'পাস্কাল (বা নিউটন/বর্গমিটার)',
                                    'option_b' => 'ডাইন',
                                    'option_c' => 'জুল',
                                    'option_d' => 'ওয়াট',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'স্থির তরলে নিমজ্জিত কোনো বস্তুর ওপর প্রযুক্ত ঊর্ধ্বমুখী বলকে কী বলে?',
                                    'option_a' => 'প্লবতা (Buoyant Force)',
                                    'option_b' => 'ঘর্ষণ বল',
                                    'option_c' => 'মহাকর্ষ বল',
                                    'option_d' => 'তড়িৎ বল',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // CLASS 9
            // ==========================================
            9 => [
                'Bengali' => [
                    [
                        'title' => 'সাহিত্য সঞ্চয়ন - কলিঙ্গদেশে ঝড়-বৃষ্টি (মুকুন্দরাম চক্রবর্তী) ও ধীবর-বৃত্তান্ত',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'কবিকঙ্কণ মুকুন্দরাম চক্রবর্তীর "কলিঙ্গদেশে ঝড়-বৃষ্টি" কবিতার প্রেক্ষাপট ও অলঙ্কার',
                            'description' => 'চণ্ডীমঙ্গল কাব্যের কালকেতু উপাখ্যানের অংশ, প্রলয়ঙ্করী ঝড়ের চিত্র ও কালিদাসের ধীবর-বৃত্তান্ত নাট্যাংশ।',
                            'file_url' => 'https://vidyamission.com/materials/class9/bengali/kalingadeshe_jhor_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'কলিঙ্গদেশে ঝড়-বৃষ্টি: মধ্যযুগের কাব্যে প্রাকৃতিক বিপর্যয়ের অসাধারণ চিত্র',
                            'description' => 'মেঘের গর্জন, দেবী চণ্ডীর আদেশে হনুমানের তাণ্ডব ও সাত দিনের অবিশ্রান্ত বৃষ্টির বর্ণনা।',
                            'video_id' => 'L_1m2rN9y3T',
                            'duration_seconds' => 2200,
                        ],
                        'live_class' => [
                            'title' => 'ক্লাস ৯ বাংলা ব্যাকরণ: ধ্বনি পরিবর্তন ও শব্দ গঠনের নিয়ম',
                            'description' => 'স্বরভক্তি, অপিনিহিতি, অভিশ্রুতি ও প্রত্যয়ের নিয়মাবলী।',
                        ],
                        'test' => [
                            'title' => 'কলিঙ্গদেশে ঝড়-বৃষ্টি ও ব্যাকরণ টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => '"কলিঙ্গদেশে ঝড়-বৃষ্টি" কবিতাটি কোন মঙ্গলকাব্যের অন্তর্গত?',
                                    'option_a' => 'চণ্ডীমঙ্গল',
                                    'option_b' => 'মনসামঙ্গল',
                                    'option_c' => 'অন্নদামঙ্গল',
                                    'option_d' => 'ধর্মমঙ্গল',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'কলিঙ্গদেশে ঝড়-বৃষ্টি কতদিন ধরে অবিশ্রান্ত হয়েছিল?',
                                    'option_a' => 'সাত দিন',
                                    'option_b' => 'পাঁচ দিন',
                                    'option_c' => 'তিন দিন',
                                    'option_d' => 'দশ দিন',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'English' => [
                    [
                        'title' => 'Bliss - Tales of Bhola Grandpa (Manoj Das) & Autumn',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'Tales of Bhola Grandpa - Humorous Character Sketch and Themes',
                            'description' => 'The unforgettable forgetfulness of Bhola Grandpa, the tiger encounter in Sunderbans, and John Clare\'s Autumn.',
                            'file_url' => 'https://vidyamission.com/materials/class9/english/bhola_grandpa_notes.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'Tales of Bhola Grandpa: Humour and Nostalgia in Rural Bengal',
                            'description' => 'Literary analysis of Manoj Das’s classic story with vocabulary and comprehension.',
                            'video_id' => 'M_2n3sO0z4U',
                            'duration_seconds' => 2100,
                        ],
                        'live_class' => [
                            'title' => 'English Bliss Live: Narration Change (Direct to Indirect Speech)',
                            'description' => 'Direct and Indirect speech rules for Assertive, Interrogative, and Imperative sentences.',
                        ],
                        'test' => [
                            'title' => 'Tales of Bhola Grandpa & Narration Test',
                            'questions' => [
                                [
                                    'question_text' => 'Where had Bhola Grandpa forgotten his grandchild while returning from the festival?',
                                    'option_a' => 'In the village festival fair market',
                                    'option_b' => 'On the boat',
                                    'option_c' => 'Inside the temple',
                                    'option_d' => 'Near the river bank',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'What did Bhola Grandpa mistake the Royal Bengal Tiger for when climbing down the banyan tree?',
                                    'option_a' => 'He completely forgot about the tiger sitting beneath the tree',
                                    'option_b' => 'He thought it was his dog',
                                    'option_c' => 'He thought it was a large rock',
                                    'option_d' => 'He thought it was a sleeping cow',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Mathematics' => [
                    [
                        'title' => 'গণিত প্রকাশ - বাস্তব সংখ্যা ও সূচকের নিয়মাবলী (Real Numbers & Indices)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'অমূলদ সংখ্যা (√২, √৩ অমূলদতার প্রমাণ) ও সূচকের মৌলিক নিয়মাবলী',
                            'description' => 'সূচকের সূত্র (aᵐ × aⁿ = aᵐ⁺ⁿ), বাস্তব সংখ্যার বিস্তার ও সংখ্যারেখায় স্থাপন পদ্ধতি।',
                            'file_url' => 'https://vidyamission.com/materials/class9/math/real_numbers_indices.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'বাস্তব সংখ্যা ও সূচকের নিয়মাবলী: সূত্র ও গাণিতিক সমস্যার সমাধান',
                            'description' => 'কঠিন সূচক রাশি সরলীকরণ এবং অমূলদ সংখ্যার প্রমাণের সহজ গাণিতিক রূপ।',
                            'video_id' => 'N_3o4tP1w5V',
                            'duration_seconds' => 2250,
                        ],
                        'live_class' => [
                            'title' => 'গণিত প্রকাশ লাইভ: সহসমীকরণ সমাধান (বজ্রগুণন ও অপনয়ন পদ্ধতি)',
                            'description' => 'দুই চলবিশিষ্ট একঘাত সমীকরণ সমাধানের চারটি পদ্ধতির দ্রুত অনুশীলন।',
                        ],
                        'test' => [
                            'title' => 'বাস্তব সংখ্যা ও সূচক পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'যেকোনো অশূন্য সংখ্যা a-এর জন্য a⁰-এর মান কত?',
                                    'option_a' => '১',
                                    'option_b' => '০',
                                    'option_c' => 'a',
                                    'option_d' => 'অসীম',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '√২ সংখ্যাটি কী ধরনের সংখ্যা?',
                                    'option_a' => 'অমূলদ সংখ্যা (Irrational Number)',
                                    'option_b' => 'মূলদ সংখ্যা',
                                    'option_c' => 'পূর্ণসংখ্যা',
                                    'option_d' => 'স্বাভাবিক সংখ্যা',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'History' => [
                    [
                        'title' => 'ইতিহাস ও পরিবেশ - ফরাসি বিপ্লবের কয়েকটি দিক (French Revolution)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'ফরাসি সমাজের তিন সম্প্রদায় (Estate System), বাস্তিল দুর্গের পতন ও সন্ত্রাসের রাজত্ব',
                            'description' => 'ভলতেয়ার, রুশো ও মন্টেস্কুর দার্শনিক অবদান, বাস্তিল দুর্গ পতন (১৪ জুলাই ১৭৮৯) ও রোবসপিয়রের জ্যকোবিন শাসন।',
                            'file_url' => 'https://vidyamission.com/materials/class9/history/french_revolution.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'ফরাসি বিপ্লব ১৭৮৯: সাম্য, মৈত্রী ও স্বাধীনতার জয়গান',
                            'description' => 'ষোড়শ লুইয়ের স্বৈরতন্ত্র, বুর্জোয়া বিদ্রোহ এবং নতুন ইউরোপের জন্ম ইতিহাস।',
                            'video_id' => 'O_4p5uQ2x6W',
                            'duration_seconds' => 2200,
                        ],
                        'live_class' => [
                            'title' => 'ইতিহাস লাইভ: নেপোলিয়ন বোনাপার্ট ও তাঁর কোড নেপোলিয়ন',
                            'description' => 'বিপ্লবের সন্তান থেকে সম্রাট এবং আধুনিক আইনের ভিত্তি রচনা।',
                        ],
                        'test' => [
                            'title' => 'ফরাসি বিপ্লব ইতিহাস টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => 'ঐতিহাসিক বাস্তিল দুর্গের পতন কোন তারিখে ঘটেছিল?',
                                    'option_a' => '১৪ জুলাই, ১৭৮৯',
                                    'option_b' => '৪ জুলাই, ১৭৭৬',
                                    'option_c' => '৫ মে, ১৭৮৯',
                                    'option_d' => '২৬ আগস্ট, ১৭৮৯',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => '"সামাজিক চুক্তি" (The Social Contract) গ্রন্থের রচয়িতা কে ছিলেন?',
                                    'option_a' => 'জাঁ জ্যাক রুশো (Jean-Jacques Rousseau)',
                                    'option_b' => 'ভলতেয়ার',
                                    'option_c' => 'মন্টেস্কু',
                                    'option_d' => 'জন লক',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Geography' => [
                    [
                        'title' => 'ভূগোল ও পরিবেশ - গ্রহরূপে পৃথিবী ও পশ্চিমবঙ্গের ভূপ্রকৃতি (Earth & WB Geography)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'পৃথিবীর অভিগত গোলক আকার, প্রমাণ এবং পশ্চিমবঙ্গের প্রাকৃতিক বিভাগ নোটস',
                            'description' => 'এরাতোস্থেনিসের পরিধি নির্ণয়, জিয়ড ধারণা এবং উত্তরবঙ্গের পার্বত্য অঞ্চল, পশ্চিমের মালভূমি ও সমভূমি।',
                            'file_url' => 'https://vidyamission.com/materials/class9/geography/earth_wb_geography.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'গ্রহরূপে পৃথিবী: গোলকীয় আকারের বৈজ্ঞানিক প্রমাণাদি',
                            'description' => 'জাহাজের দূরবীক্ষণ দৃশ্য, ছায়া ও মহাশূন্য থেকে পৃথিবীর নীল গ্রহের দৃশ্য বিশ্লেষণ।',
                            'video_id' => 'P_5q6vR3y7X',
                            'duration_seconds' => 2150,
                        ],
                        'live_class' => [
                            'title' => 'ভূগোল লাইভ: পশ্চিমবঙ্গের নদ-নদী, মাটি ও জলবায়ুর মানচিত্র পাঠ',
                            'description' => 'তিস্তা, তোর্সা, গঙ্গা ও দামোদর নদ-নদীর গতিপথ মানচিত্রে চিহ্নিতকরণ।',
                        ],
                        'test' => [
                            'title' => 'গ্রহরূপে পৃথিবী ও পশ্চিমবঙ্গের ভূগোল পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'পৃথিবীর প্রকৃত আকৃতিকে কী বলা হয়?',
                                    'option_a' => 'জিয়ড (Geoid - পৃথিবীর সদৃশ)',
                                    'option_b' => 'সম্পূর্ণ গোলাকার',
                                    'option_c' => 'ডিম্বাকার',
                                    'option_d' => 'চ্যাপ্টা থালার মতো',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'পশ্চিমবঙ্গের সর্বোচ্চ শৃঙ্গ কোনটি?',
                                    'option_a' => 'সান্দাকফু (Sandakphu)',
                                    'option_b' => 'ফালুট',
                                    'option_c' => 'ঘুম',
                                    'option_d' => 'টাইগার হিল',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Physical Science' => [
                    [
                        'title' => 'ভৌত বিজ্ঞান - পরিমাপ, বল ও গতি এবং পরমাণুর গঠন (Measurement & Mechanics)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'ভৌত রাশি ও মাত্রা, নিউটনের গতিসূত্রসমূহ (F=ma) এবং বোরের পরমাণু মডেল',
                            'description' => 'রৈখিক ভরবেগ সংরক্ষণ সূত্র, জাড্য ধর্ম, বলের ঘাত ও পরমাণুর ইলেকট্রন বিন্যাস (2n²)।',
                            'file_url' => 'https://vidyamission.com/materials/class9/physci/measurement_mechanics.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'নিউটনের গতিসূত্র ও ভরবেগ সংরক্ষণ নীতি: গতিবিজ্ঞানের ভিত্তি',
                            'description' => 'ক্রিয়া ও প্রতিক্রিয়া বল, বন্দুক থেকে গুলি ছোঁড়া এবং রকেটের গতির বাস্তব প্রয়োগ।',
                            'video_id' => 'Q_6r7wS4z8Y',
                            'duration_seconds' => 2300,
                        ],
                        'live_class' => [
                            'title' => 'ভৌত বিজ্ঞান সমাধান লাইভ: গতির সমীকরণ (v=u+at, s=ut+½at²) সমাধান',
                            'description' => 'লেখচিত্রের সাহায্যে গতির সমীকরণ প্রতিষ্ঠা ও সংখ্যাসূচক অংক সমাধান।',
                        ],
                        'test' => [
                            'title' => 'পরিমাপ, বল ও গতি ভৌত বিজ্ঞান টেস্ট',
                            'questions' => [
                                [
                                    'question_text' => 'নিউটনের কোন গতিসূত্র থেকে বলের পরিমাপ ও সমীকরণ (F = ma) পাওয়া যায়?',
                                    'option_a' => 'দ্বিতীয় গতিসূত্র',
                                    'option_b' => 'প্রথম গতিসূত্র',
                                    'option_c' => 'তৃতীয় গতিসূত্র',
                                    'option_d' => 'মহাকর্ষ সূত্র',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'একটি বস্তুর ভর ৫ কেজি এবং ত্বরণ ২ মি/সে² হলে প্রযুক্ত বলের মান কত?',
                                    'option_a' => '১০ নিউটন',
                                    'option_b' => '২.৫ নিউটন',
                                    'option_c' => '৭ নিউটন',
                                    'option_d' => '২৫ নিউটন',
                                    'correct_option' => 'a',
                                ],
                            ],
                        ],
                    ],
                ],
                'Life Science' => [
                    [
                        'title' => 'জীবন বিজ্ঞান - জীবন সংগঠনের স্তর: কোষ ও কলা (Cell Structure & Tissues)',
                        'sort_order' => 1,
                        'notes' => [
                            'title' => 'ইউক্যারিওটিক উদ্ভিদ ও প্রাণী কোষের অঙ্গাণু এবং ভাজক ও স্থায়ী কলা নোটস',
                            'description' => 'মাইটোকনড্রিয়া (কোষের শক্তিঘর), রাইবোজোম, প্লাস্টিড, জাইলেম ও ফ্লোয়েম কলার গঠন ও কাজ।',
                            'file_url' => 'https://vidyamission.com/materials/class9/lifesci/cell_structure_tissues.pdf',
                        ],
                        'recorded_class' => [
                            'title' => 'কোষ ও কোষীয় অঙ্গাণু: জীবনের গঠনমূলক ও কার্যমূলক একক',
                            'description' => 'ইলেকট্রন অণুবীক্ষণ যন্ত্রে কোষের অভ্যন্তরীণ রূপ ও ডিএনএ-এর ভূমিকা বিশদ আলোচনা।',
                            'video_id' => 'R_7s8xT5w9Z',
                            'duration_seconds' => 2250,
                        ],
                        'live_class' => [
                            'title' => 'জীবন বিজ্ঞান লাইভ: জৈব অণু (কার্বোহাইড্রেট, প্রোটিন, লিপিড) ও ভিটামিন',
                            'description' => 'উৎসেচক ও ভিটামিনের অভাবজনিত রোগ এবং পুষ্টি সংক্রান্ত প্রশ্নোত্তর।',
                        ],
                        'test' => [
                            'title' => 'কোষ ও কলা জীবন বিজ্ঞান মূল্যায়ন পরীক্ষা',
                            'questions' => [
                                [
                                    'question_text' => 'কোন কোষীয় অঙ্গাণুকে "কোষের শক্তিঘর" (Powerhouse of the Cell) বলা হয়?',
                                    'option_a' => 'মাইটোকনড্রিয়া (Mitochondria)',
                                    'option_b' => 'গলগি বডি',
                                    'option_c' => 'লাইসোজোম',
                                    'option_d' => 'রাইবোজোম',
                                    'correct_option' => 'a',
                                ],
                                [
                                    'question_text' => 'উদ্ভিদের কোন জটিল স্থায়ী কলা জল ও খনিজ লবণ সংবহন করে?',
                                    'option_a' => 'জাইলেম (Xylem)',
                                    'option_b' => 'ফ্লোয়েম (Phloem)',
                                    'option_c' => 'প্যারেনকাইমা',
                                    'option_d' => 'কোলেনকাইমা',
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

        $this->command->info('Class 5 to 9 educational curriculum and materials seeded successfully.');
    }
}
