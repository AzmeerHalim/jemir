<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Activity;
use App\Models\AccompanyingLecturer;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedClusters();
        $this->seedDemoUsers();
        $this->seedLecturers();
        $this->seedStudentProfiles();
        $this->seedActivities();
        $this->seedAnnouncements();
    }

    /**
     * Reset and reseed users + student profiles with a balanced role distribution.
     * Use `php artisan users:reset --force` from the CLI instead of running this directly.
     */
    public function resetAndReseedUsers(): void
    {
        $this->call(BalancedDemoUsersSeeder::class);
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Academic'],
            ['name' => 'Sports'],
            ['name' => 'Leadership'],
            ['name' => 'Innovation'],
            ['name' => 'Volunteering'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate($cat);
        }
    }

    private function seedClusters(): void
    {
        $clusters = [
            ['cluster_name' => 'Academic Excellence', 'description' => 'Top academic performers'],
            ['cluster_name' => 'Sports & Athletics', 'description' => 'Sports and athletic talents'],
            ['cluster_name' => 'Leadership & Governance', 'description' => 'Student leaders and governance'],
            ['cluster_name' => 'Innovation & Technology', 'description' => 'Tech and innovation talents'],
            ['cluster_name' => 'Community Service', 'description' => 'Volunteers and community contributors'],
        ];

        foreach ($clusters as $cluster) {
            Cluster::firstOrCreate($cluster);
        }
    }

    private function seedDemoUsers(): void
    {
        $demoAccounts = [
            ['name' => 'Student Demo', 'username' => 'student', 'email' => 'student@strms.test', 'role' => UserRole::Student],
            ['name' => 'Talent Coordinator', 'username' => 'coordinator', 'email' => 'coordinator@strms.test', 'role' => UserRole::Coordinator],
            ['name' => 'Accompanying Lecturer', 'username' => 'lecturer', 'email' => 'lecturer@strms.test', 'role' => UserRole::Lecturer],
            ['name' => 'ITC Admin', 'username' => 'itcadmin', 'email' => 'itcadmin@strms.test', 'role' => UserRole::ItcAdmin],
            ['name' => 'Committee Member', 'username' => 'committee', 'email' => 'committee@strms.test', 'role' => UserRole::Committee],
            ['name' => 'Student Affairs Officer', 'username' => 'affairs', 'email' => 'affairs@strms.test', 'role' => UserRole::StudentAffairs],
        ];

        foreach ($demoAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'email' => $account['email'],
                    'password' => Hash::make('password'),
                    'role' => $account['role']->value,
                    'email_verified_at' => now(),
                ]
            );
        }
    }

    private function seedLecturers(): void
    {
        if (AccompanyingLecturer::count() === 0) {
            AccompanyingLecturer::factory()->count(5)->create();
        }
    }

    private function seedStudentProfiles(): void
    {
        $studentUser = User::where('email', 'student@strms.test')->first();

        if ($studentUser && !StudentProfile::where('user_id', $studentUser->id)->exists()) {
            $clusters = Cluster::all();

            StudentProfile::create([
                'user_id' => $studentUser->id,
                'student_name' => $studentUser->name,
                'matric_no' => 'A123456',
                'ic_number' => '010101010101',
                'phone' => '0123456789',
                'programme' => 'BIP',
                'year' => 3,
                'talent' => 'Web Development',
                'student_status' => 'active',
                'status' => 'active',
                'cgpa' => 3.75,
                'faculty' => 'FSKTM',
                'department' => 'Software Engineering',
                'cluster_id' => $clusters->random()?->id,
                'skills' => ['PHP', 'Laravel', 'JavaScript'],
                'interests' => ['Hackathons', 'Open Source'],
            ]);
        }
    }

    private function seedActivities(): void
    {
        if (Activity::count() === 0) {
            $itcAdmin = User::where('email', 'itcadmin@strms.test')->first();

            Activity::factory()
                ->count(10)
                ->create(['created_by' => $itcAdmin?->id]);
        }
    }

    private function seedAnnouncements(): void
    {
        if (Announcement::count() === 0) {
            $itcAdmin = User::where('email', 'itcadmin@strms.test')->first();

            if ($itcAdmin) {
                Announcement::factory()
                    ->count(5)
                    ->create(['user_id' => $itcAdmin->id]);
            }
        }
    }
}
