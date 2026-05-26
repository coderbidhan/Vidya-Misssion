<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the default admin account.
     */
    public function run(): void
    {
        $defaultPassword = 'VidyaAdmin@2024';

        User::updateOrCreate(
            ['email' => 'admin@vidyamission.com'],
            [
                'name'        => 'VidyaMission Admin',
                'password'    => \Illuminate\Support\Facades\Hash::make($defaultPassword),
                'role'        => 'admin',
                'is_verified' => true,
                'is_active'   => true,
            ]
        );

        $this->command->info('Admin user created: admin@vidyamission.com');
        $this->command->warn("Default password: {$defaultPassword}  ← Change this immediately!");
    }
}
