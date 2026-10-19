<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AccompanyingLecturer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccompanyingLecturerSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Dr. Lim Wei Ming',  'email' => 'lecturer1@strms.test'],
            ['name' => 'Dr. Tan Soo Lian',  'email' => 'lecturer2@strms.test'],
            ['name' => 'Dr. Rajesh Kumar',  'email' => 'lecturer3@strms.test'],
            ['name' => 'Dr. Nurul Ain',     'email' => 'lecturer4@strms.test'],
            ['name' => 'Dr. Chong Mei Fong','email' => 'lecturer5@strms.test'],
        ];

        foreach ($accounts as $i => $account) {
            $username = 'lecturer'.($i + 1);

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'role' => UserRole::Lecturer->value,
                    'email_verified_at' => now(),
                ],
            );

            AccompanyingLecturer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'lecturer_name' => $account['name'],
                    'lecturer_email' => $account['email'],
                    'lecturer_phone' => '012'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'department' => 'FSKTM',
                    'is_active' => true,
                ],
            );
        }

        $legacy = User::where('email', 'lecturer@strms.test')->first();
        if ($legacy) {
            AccompanyingLecturer::updateOrCreate(
                ['user_id' => $legacy->id],
                [
                    'lecturer_name' => $legacy->name,
                    'lecturer_email' => $legacy->email,
                    'lecturer_phone' => '012'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'department' => 'FSKTM',
                    'is_active' => true,
                ],
            );
        }

        $this->command->info('Seeded '.AccompanyingLecturer::count().' accompanying lecturers (linked to lecturer users).');
    }
}
