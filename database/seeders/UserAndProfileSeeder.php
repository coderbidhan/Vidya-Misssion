<?php

namespace Database\Seeders;

use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndProfileSeeder extends Seeder
{
    /**
     * Run the database seeds for Users, Teacher Profiles, and Student Profiles.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Vidya@2026');

        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@vidyamission.com'],
            [
                'name' => 'VidyaMission Admin',
                'mobile' => '9876500000',
                'password' => Hash::make('VidyaAdmin@2024'),
                'role' => 'admin',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        // 2. Teachers with Profiles
        $teachers = [
            [
                'name' => 'Dr. Sourav Sen',
                'email' => 'teacher@vidyamission.com',
                'mobile' => '9876543220',
                'bio' => 'Senior Physics and Physical Science Educator with over 14 years of teaching experience for Madhyamik and Higher Secondary students.',
                'qualification' => 'Ph.D in Physics (Calcutta University), B.Ed',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&q=80',
            ],
            [
                'name' => 'Ananya Roy',
                'email' => 'ananya.roy@vidyamission.com',
                'mobile' => '9876543221',
                'bio' => 'Passionate Bengali literature specialist helping students appreciate poetry, prose, and grammatical excellence.',
                'qualification' => 'M.A in Bengali Literature (Jadavpur University), Gold Medalist',
                'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&q=80',
            ],
            [
                'name' => 'Subhash Das',
                'email' => 'subhash.das@vidyamission.com',
                'mobile' => '9876543222',
                'bio' => 'Renowned Mathematics instructor known for simplifying complex algebraic formulas and geometric theorems.',
                'qualification' => 'M.Sc in Applied Mathematics (Presidency University)',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=80',
            ],
            [
                'name' => 'Priya Banerjee',
                'email' => 'priya.banerjee@vidyamission.com',
                'mobile' => '9876543223',
                'bio' => 'Expert in English grammar, comprehension, and communicative English for WB Board students.',
                'qualification' => 'M.A in English Literature, CELTA Certified',
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&q=80',
            ],
            [
                'name' => 'Amitabha Mukherjee',
                'email' => 'amitabha.mukherjee@vidyamission.com',
                'mobile' => '9876543224',
                'bio' => 'Dedicated History and Social Studies educator, bringing ancient and modern historical stories to life.',
                'qualification' => 'M.A in Modern Indian History (Calcutta University)',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&q=80',
            ],
            [
                'name' => 'Ruma Bose',
                'email' => 'ruma.bose@vidyamission.com',
                'mobile' => '9876543225',
                'bio' => 'Geography specialist with in-depth knowledge of geomorphology, climate systems, and map pointing skills.',
                'qualification' => 'M.Sc in Geography and Environment Management',
                'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&q=80',
            ],
            [
                'name' => 'Debashis Ghosh',
                'email' => 'debashis.ghosh@vidyamission.com',
                'mobile' => '9876543226',
                'bio' => 'Life Science and Biological Sciences educator focusing on conceptual clarity, diagram practice, and genetics.',
                'qualification' => 'M.Sc in Zoology, CSIR-NET Qualified',
                'avatar_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=200&q=80',
            ],
            [
                'name' => 'Tanmoy Chatterjee',
                'email' => 'tanmoy.chatterjee@vidyamission.com',
                'mobile' => '9876543227',
                'bio' => 'Chemistry mentor helping students master organic mechanisms, physical chemistry problems, and inorganic trends.',
                'qualification' => 'M.Sc in Organic Chemistry (Calcutta University)',
                'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&q=80',
            ],
            [
                'name' => 'Sneha Dutta',
                'email' => 'sneha.dutta@vidyamission.com',
                'mobile' => '9876543228',
                'bio' => 'Computer Application trainer specializing in programming foundations, database design, and web technology.',
                'qualification' => 'MCA, B.Sc in Computer Science',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&q=80',
            ],
        ];

        foreach ($teachers as $tData) {
            $user = User::updateOrCreate(
                ['email' => $tData['email']],
                [
                    'name' => $tData['name'],
                    'mobile' => $tData['mobile'],
                    'password' => $defaultPassword,
                    'role' => 'teacher',
                    'is_verified' => true,
                    'is_active' => true,
                ]
            );

            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $tData['bio'],
                    'qualification' => $tData['qualification'],
                    'avatar_url' => $tData['avatar_url'],
                ]
            );
        }

        // 3. Students from Class 5 to Class 12 with Profiles
        $students = [
            [
                'name' => 'Souvik Das',
                'email' => 'student5@vidyamission.com',
                'mobile' => '9876510005',
                'class' => '5',
                'school_name' => 'Ballygunge Government High School',
                'district' => 'Kolkata',
                'avatar_url' => 'https://images.unsplash.com/photo-1543610892-0b1f7e6d8ac1?w=200&q=80',
            ],
            [
                'name' => 'Riya Mondal',
                'email' => 'student6@vidyamission.com',
                'mobile' => '9876510006',
                'class' => '6',
                'school_name' => 'Howrah Zilla School',
                'district' => 'Howrah',
                'avatar_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=200&q=80',
            ],
            [
                'name' => 'Abhirup Ghosh',
                'email' => 'student7@vidyamission.com',
                'mobile' => '9876510007',
                'class' => '7',
                'school_name' => 'Hooghly Collegiate School',
                'district' => 'Hooghly',
                'avatar_url' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=200&q=80',
            ],
            [
                'name' => 'Tanusree Paul',
                'email' => 'student8@vidyamission.com',
                'mobile' => '9876510008',
                'class' => '8',
                'school_name' => 'Burdwan Municipal High School',
                'district' => 'Purba Bardhaman',
                'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80',
            ],
            [
                'name' => 'Debanjan Roy',
                'email' => 'student9@vidyamission.com',
                'mobile' => '9876510009',
                'class' => '9',
                'school_name' => 'Siliguri Boys High School',
                'district' => 'Darjeeling',
                'avatar_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&q=80',
            ],
            [
                'name' => 'Rahul Barman',
                'email' => 'student10@vidyamission.com',
                'mobile' => '9876510010',
                'class' => '10',
                'school_name' => 'Barasat PCS Government High School',
                'district' => 'North 24 Parganas',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&q=80',
            ],
            [
                'name' => 'Sneha Mukherjee',
                'email' => 'student11@vidyamission.com',
                'mobile' => '9876510011',
                'class' => '11',
                'school_name' => 'Jadavpur Vidyapith',
                'district' => 'Kolkata',
                'avatar_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=200&q=80',
            ],
            [
                'name' => 'Pritam Chakraborty',
                'email' => 'student12@vidyamission.com',
                'mobile' => '9876510012',
                'class' => '12',
                'school_name' => 'Midnapore Collegiate School',
                'district' => 'Paschim Medinipur',
                'avatar_url' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=200&q=80',
            ],
        ];

        foreach ($students as $sData) {
            $user = User::updateOrCreate(
                ['email' => $sData['email']],
                [
                    'name' => $sData['name'],
                    'mobile' => $sData['mobile'],
                    'password' => $defaultPassword,
                    'role' => 'student',
                    'is_verified' => true,
                    'is_active' => true,
                    'trial_starts_at' => now()->subDays(1),
                    'trial_ends_at' => now()->addDays(2),
                ]
            );

            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $sData['name'],
                    'class' => $sData['class'],
                    'school_name' => $sData['school_name'],
                    'district' => $sData['district'],
                    'avatar_url' => $sData['avatar_url'],
                ]
            );
        }

        $this->command->info('Admin, '.count($teachers).' teachers, and '.count($students).' students with profiles seeded.');
    }
}
