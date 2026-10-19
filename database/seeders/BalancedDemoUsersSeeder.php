<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Cluster;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BalancedDemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $staffAccounts = [
            ['name' => 'Talent Coordinator',     'username' => 'coordinator', 'email' => 'coordinator@strms.test', 'role' => UserRole::Coordinator],
            ['name' => 'Accompanying Lecturer',  'username' => 'lecturer',    'email' => 'lecturer@strms.test',    'role' => UserRole::Lecturer],
            ['name' => 'ITC Admin',              'username' => 'itcadmin',    'email' => 'itcadmin@strms.test',    'role' => UserRole::ItcAdmin],
            ['name' => 'Committee Member',       'username' => 'committee',   'email' => 'committee@strms.test',   'role' => UserRole::Committee],
            ['name' => 'Student Affairs Officer', 'username' => 'affairs',     'email' => 'affairs@strms.test',     'role' => UserRole::StudentAffairs],
        ];

        foreach ($staffAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make('password'),
                    'role' => $account['role']->value,
                    'email_verified_at' => $now,
                ],
            );
        }

        $studentAccounts = [
            ['username' => 'student',   'email' => 'student@strms.test',   'matric' => 'A123456', 'name' => 'Student Demo',     'programme' => 'BIP', 'year' => 3, 'talent' => 'Web Development'],
        ];

        for ($i = 1; $i <= 11; $i++) {
            $studentAccounts[] = [
                'username' => 'student'.$i,
                'email' => 'student'.$i.'@strms.test',
                'matric' => 'A1'.str_pad((string) (10000 + $i), 5, '0', STR_PAD_LEFT),
                'name' => 'Student '.$i,
                'programme' => collect(['BIS', 'BIM', 'BIP', 'BIW', 'BIT'])->random(),
                'year' => (($i - 1) % 4) + 1,
                'talent' => collect(['Web Development', 'Data Analytics', 'UI/UX Design', 'Cybersecurity', 'Mobile Apps', 'Game Dev', 'AI/ML', 'Cloud Computing', 'IoT', 'Networking', 'Open Source', 'Hackathons'])->random(),
            ];
        }

        $clusters = Cluster::all();

        foreach ($studentAccounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Student->value,
                    'email_verified_at' => $now,
                ],
            );

            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_name' => $account['name'],
                    'matric_no' => $account['matric'],
                    'ic_number' => '0101010'.str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT),
                    'phone' => '012'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'programme' => $account['programme'],
                    'year' => $account['year'],
                    'talent' => $account['talent'],
                    'student_status' => 'active',
                    'status' => 'active',
                    'cgpa' => round(random_int(250, 400) / 100, 2),
                    'faculty' => 'FSKTM',
                    'department' => collect(['Software Engineering', 'Information Security', 'Multimedia', 'Web Technology'])->random(),
                    'cluster_id' => $clusters->isNotEmpty() ? $clusters->random()->id : null,
                    'skills' => ['PHP', 'Laravel', 'JavaScript'],
                    'interests' => ['Hackathons', 'Open Source'],
                ],
            );
        }

        $this->command->info('Seeded '.User::count().' users:');
        $rows = User::query()
            ->selectRaw('role, COUNT(*) as c')
            ->groupBy('role')
            ->orderBy('role')
            ->get();
        foreach ($rows as $row) {
            $roleValue = $row->role instanceof UserRole ? $row->role->value : (string) $row->role;
            $this->command->info("  {$roleValue}: {$row->c}");
        }
    }
}
