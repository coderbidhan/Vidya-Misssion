<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete West Bengal Board curriculum (Classes 5-12).
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SubjectSeeder::class,
            SubscriptionSeeder::class,
            UserAndProfileSeeder::class,
            ClassSubjectSeeder::class,
            WestBengalCurriculumSeeder::class,
            UserActivitySeeder::class,
        ]);
    }
}
