<?php

use App\Enums\UserRole;
use App\Models\AccompanyingLecturer;
use App\Models\Activity;
use App\Models\Cluster;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->cluster = Cluster::firstOrCreate(['cluster_name' => 'Tech Cluster'], ['description' => 'Test']);

    $this->studentUser = User::updateOrCreate(
        ['email' => 'test_student@strms.test'],
        [
            'name' => 'Test Student',
            'username' => 'teststudent',
            'password' => Hash::make('password'),
            'role' => UserRole::Student,
            'email_verified_at' => now(),
        ]
    );

    $this->studentProfile = StudentProfile::updateOrCreate(
        ['user_id' => $this->studentUser->id],
        [
            'student_name' => 'Test Student',
            'matric_no' => 'AI220001',
            'programme' => 'BIP',
            'year' => 2,
            'cgpa' => 3.70,
            'student_status' => 'active',
            'status' => 'active',
            'faculty' => 'FSKTM',
            'cluster_id' => $this->cluster->id,
        ]
    );

    $this->coordinatorUser = User::updateOrCreate(
        ['email' => 'test_coordinator@strms.test'],
        [
            'name' => 'Test Coordinator',
            'username' => 'testcoordinator',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator,
            'email_verified_at' => now(),
        ]
    );

    $this->lecturerUser = User::updateOrCreate(
        ['email' => 'test_lecturer@strms.test'],
        [
            'name' => 'Dr Test Lecturer',
            'username' => 'testlecturer',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer,
            'email_verified_at' => now(),
        ]
    );

    $this->accompanyingLecturer = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $this->lecturerUser->id],
        [
            'lecturer_name' => 'Dr Test Lecturer',
            'lecturer_email' => $this->lecturerUser->email,
            'department' => 'Software Engineering',
            'is_active' => true,
        ]
    );

    $this->itcAdminUser = User::updateOrCreate(
        ['email' => 'test_itcadmin@strms.test'],
        [
            'name' => 'Test ITC Admin',
            'username' => 'testitcadmin',
            'password' => Hash::make('password'),
            'role' => UserRole::ItcAdmin,
            'email_verified_at' => now(),
        ]
    );

    $this->committeeUser = User::updateOrCreate(
        ['email' => 'test_committee@strms.test'],
        [
            'name' => 'Test Committee',
            'username' => 'testcommittee',
            'password' => Hash::make('password'),
            'role' => UserRole::Committee,
            'email_verified_at' => now(),
        ]
    );

    $this->affairsUser = User::updateOrCreate(
        ['email' => 'test_affairs@strms.test'],
        [
            'name' => 'Test Affairs',
            'username' => 'testaffairs',
            'password' => Hash::make('password'),
            'role' => UserRole::StudentAffairs,
            'email_verified_at' => now(),
        ]
    );
});

test('student can access student portal pages', function () {
    $this->actingAs($this->studentUser)->get(route('student.dashboard'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('student.activities.index'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('student.participations.index'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('achievements.index'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('leaderboard'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('history'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('student.announcements.index'))->assertOk();
    $this->actingAs($this->studentUser)->get(route('student.notifications.index'))->assertOk();
});

test('coordinator can access coordinator portal pages', function () {
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.dashboard'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.approvals'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.students'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.users.index'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.categories'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.awards.index'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.reports'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.announcements.index'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.settings'))->assertOk();
    $this->actingAs($this->coordinatorUser)->get(route('coordinator.participation-approvals.index'))->assertOk();
});

test('lecturer can access lecturer portal pages', function () {
    $this->actingAs($this->lecturerUser)->get(route('lecturer.dashboard'))->assertOk();
    $this->actingAs($this->lecturerUser)->get(route('lecturer.verifications.index'))->assertOk();
    $this->actingAs($this->lecturerUser)->get(route('lecturer.achievement-verifications.index'))->assertOk();
    $this->actingAs($this->lecturerUser)->get(route('lecturer.verifications.history'))->assertOk();
});

test('itc admin can access itc portal pages', function () {
    $this->actingAs($this->itcAdminUser)->get(route('itc.dashboard'))->assertOk();
    $this->actingAs($this->itcAdminUser)->get(route('itc.activities.index'))->assertOk();
    $this->actingAs($this->itcAdminUser)->get(route('itc.clusters.index'))->assertOk();
    $this->actingAs($this->itcAdminUser)->get(route('itc.talent-search'))->assertOk();
});

test('committee member can access committee portal pages', function () {
    $this->actingAs($this->committeeUser)->get(route('committee.dashboard'))->assertOk();
    $this->actingAs($this->committeeUser)->get(route('committee.search'))->assertOk();
    $this->actingAs($this->committeeUser)->get(route('leaderboard'))->assertOk();
});

test('student affairs can access affairs portal pages', function () {
    $this->actingAs($this->affairsUser)->get(route('affairs.dashboard'))->assertOk();
    $this->actingAs($this->affairsUser)->get(route('leaderboard'))->assertOk();
});

test('management and coordinator can export reports in csv, pdf and excel formats', function () {
    // Achievements CSV and PDF
    $csvResponse = $this->actingAs($this->affairsUser)->get(route('management.reports.achievements', ['format' => 'csv']));
    $csvResponse->assertOk();
    $csvResponse->assertHeader('Content-Disposition', 'attachment; filename="achievements-report.csv"');

    $pdfResponse = $this->actingAs($this->affairsUser)->get(route('management.reports.achievements', ['format' => 'pdf']));
    $pdfResponse->assertOk();

    // Students CSV and PDF
    $studentCsv = $this->actingAs($this->affairsUser)->get(route('management.reports.students', ['format' => 'csv']));
    $studentCsv->assertOk();
    $studentCsv->assertHeader('Content-Disposition', 'attachment; filename="students-report.csv"');

    $studentPdf = $this->actingAs($this->affairsUser)->get(route('management.reports.students', ['format' => 'pdf']));
    $studentPdf->assertOk();
});

test('itc admin can create activity and invite matching students', function () {
    $response = $this->actingAs($this->itcAdminUser)->post(route('itc.activities.store'), [
        'activity_name' => 'National Hackathon 2026',
        'activity_type' => 'Competition',
        'activity_date' => now()->addDays(14)->toDateString(),
        'registration_deadline' => now()->addDays(7)->toDateString(),
        'organizer' => 'FSKTM UTHM',
        'location' => 'Main Hall',
        'activity_level' => 'national',
        'accompanying_id' => $this->accompanyingLecturer->id,
        'max_participants' => 50,
        'required_talents' => 'Web Development, PHP',
    ]);

    $response->assertRedirect(route('itc.activities.index'));

    $activity = Activity::where('activity_name', 'National Hackathon 2026')->first();
    expect($activity)->not->toBeNull();

    // Invite matching students
    $inviteResponse = $this->actingAs($this->itcAdminUser)->post(route('itc.activities.invite', $activity));
    $inviteResponse->assertRedirect();
});

test('coordinator can create announcement and awards', function () {
    $announcementResponse = $this->actingAs($this->coordinatorUser)->post(route('coordinator.announcements.store'), [
        'announcement_title' => 'Important Faculty Notice',
        'announcement_message' => 'All students must submit achievements before end of semester.',
        'announcement_date' => now()->toDateString(),
    ]);
    $announcementResponse->assertRedirect(route('coordinator.announcements.index'));

    $awardResponse = $this->actingAs($this->coordinatorUser)->post(route('coordinator.awards.store'), [
        'student_id' => $this->studentProfile->id,
        'award_name' => 'Dean List Talent Award',
        'award_category' => 'Excellence',
        'award_date' => now()->toDateString(),
        'description' => 'Outstanding contribution in software engineering competitions.',
    ]);
    $awardResponse->assertRedirect(route('coordinator.awards.index'));
});
