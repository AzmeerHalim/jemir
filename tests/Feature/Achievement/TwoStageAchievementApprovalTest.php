<?php

use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->clusters = Cluster::firstOrCreate(['cluster_name' => 'Test Cluster'], ['description' => 't']);
    $this->category = Category::firstOrCreate(['name' => 'Achievement Test Category '.uniqid()]);

    $this->studentUser = User::updateOrCreate(
        ['email' => 'two_stage_ach_student@strms.test'],
        [
            'name' => 'Two Stage Ach Student',
            'username' => 'two_stage_ach_student',
            'password' => Hash::make('password'),
            'role' => UserRole::Student->value,
            'email_verified_at' => now(),
        ],
    );
    $this->studentProfile = StudentProfile::updateOrCreate(
        ['user_id' => $this->studentUser->id],
        [
            'student_name' => $this->studentUser->name,
            'matric_no' => 'A888001',
            'ic_number' => '888888888888',
            'phone' => '0128888888',
            'programme' => 'BIP',
            'year' => 2,
            'student_status' => 'active',
            'status' => 'active',
            'cgpa' => 3.5,
            'faculty' => 'FSKTM',
            'department' => 'Software Engineering',
            'cluster_id' => $this->clusters->id,
        ],
    );

    $this->lecturer = User::updateOrCreate(
        ['email' => 'two_stage_ach_lecturer@strms.test'],
        [
            'name' => 'Two Stage Ach Lecturer',
            'username' => 'two_stage_ach_lecturer',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $this->lecturerProfile = \App\Models\AccompanyingLecturer::updateOrCreate(
        ['user_id' => $this->lecturer->id],
        [
            'lecturer_name' => 'Two Stage Ach Lecturer',
            'lecturer_email' => $this->lecturer->email,
            'lecturer_phone' => '0127770001',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );

    $this->coordinator = User::updateOrCreate(
        ['email' => 'two_stage_ach_coordinator@strms.test'],
        [
            'name' => 'Two Stage Ach Coordinator',
            'username' => 'two_stage_ach_coordinator',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );
});

test('self-submitted achievement starts with both stages pending', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Achievement',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    expect($achievement->lecturer_status)->toBe('pending')
        ->and($achievement->coordinator_status)->toBe('pending')
        ->and($achievement->status)->toBe('pending');
});

test('lecturer can verify and forward self-submitted achievement', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Achievement',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'accompanying_id' => $this->lecturerProfile->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->lecturer)->post(
        route('lecturer.achievement-verifications.verify', $achievement),
        ['verification_notes' => 'Looks legitimate.']
    );

    $response->assertRedirect(route('lecturer.achievement-verifications.index'));
    $achievement->refresh();
    expect($achievement->lecturer_status)->toBe('verified')
        ->and($achievement->lecturer_reviewed_by)->toBe($this->lecturer->id)
        ->and($achievement->coordinator_status)->toBe('pending')
        ->and($achievement->status)->toBe('pending');
});

test('coordinator cannot final-approve before lecturer has verified', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Achievement',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->coordinator)->post(
        route('coordinator.achievements.approve', $achievement->id)
    );

    $response->assertStatus(403);
    $achievement->refresh();
    expect($achievement->status)->toBe('pending');
});

test('coordinator can final-approve after lecturer verified, awarding points and triggering merit recompute', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Achievement',
        'level' => 'national',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $achievement->update([
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $response = $this->actingAs($this->coordinator)->post(
        route('coordinator.achievements.approve', $achievement->id)
    );

    $response->assertRedirect();
    $achievement->refresh();
    expect($achievement->status)->toBe('approved')
        ->and($achievement->coordinator_status)->toBe('approved')
        ->and($achievement->coordinator_reviewed_by)->toBe($this->coordinator->id)
        ->and((int) $achievement->score)->toBe(config('achievements.scores.national'));
});

test('lecturer can reject self-submitted achievement, marking it rejected', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Achievement',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'accompanying_id' => $this->lecturerProfile->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->lecturer)->post(
        route('lecturer.achievement-verifications.reject', $achievement),
        ['rejection_reason' => 'Certificate not legible.']
    );

    $response->assertRedirect();
    $achievement->refresh();
    expect($achievement->lecturer_status)->toBe('rejected')
        ->and($achievement->status)->toBe('rejected')
        ->and($achievement->rejection_reason)->toBe('Certificate not legible.');
});

test('coordinator can reject at stage 2 after lecturer verified', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Achievement',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $achievement->update([
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $response = $this->actingAs($this->coordinator)->post(
        route('coordinator.achievements.reject', $achievement->id),
        ['rejection_reason' => 'Insufficient documentation.']
    );

    $response->assertRedirect();
    $achievement->refresh();
    expect($achievement->coordinator_status)->toBe('rejected')
        ->and($achievement->status)->toBe('rejected')
        ->and($achievement->rejection_reason)->toBe('Insufficient documentation.');
});
