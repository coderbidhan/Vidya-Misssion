<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Seed the three subscription plans.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Monthly',
                'description' => 'Full access to all subjects, classes, notes and tests for 30 days.',
                'price' => 199.00,
                'duration_days' => 30,
                'features' => [
                    'All 7 subjects',
                    'Recorded video classes',
                    'Live classes',
                    'PDF notes & study material',
                    'Chapter tests & instant results',
                    'Progress tracking',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Quarterly',
                'description' => 'Best value — 3 months of uninterrupted learning.',
                'price' => 499.00,
                'duration_days' => 90,
                'features' => [
                    'All Monthly plan features',
                    'Save ₹98 vs monthly',
                    'Priority support',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Annual',
                'description' => 'Maximum savings — a full year of learning excellence.',
                'price' => 1499.00,
                'duration_days' => 365,
                'features' => [
                    'All Quarterly plan features',
                    'Save ₹889 vs monthly',
                    'Exam preparation guides',
                    'Direct teacher Q&A access',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Subscription::firstOrCreate(['name' => $plan['name']], $plan);
        }

        $this->command->info('3 subscription plans seeded (Monthly ₹199, Quarterly ₹499, Annual ₹1499).');
    }
}
