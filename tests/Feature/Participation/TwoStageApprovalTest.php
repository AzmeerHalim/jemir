<?php

use App\Enums\UserRole;
use App\Models\Activity;
use App\Models\AccompanyingLecturer;
use App\Models\Cluster;
use App\Models\Notification;
use App\Models\Participation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->clusters = Cluster::firstOrCreate(['cluster_name' => 'Test Cluster'], ['description' => 't']);

    $this->studentUser = User::updateOrCreate(
        ['email' => 'two_stage_student@strms.test'],
        [
            'name' => 'Two Stage Student',
            'username' => 'two_stage_student',
            'password' => Hash::make('password'),
            'role' => UserRole::Student->value,
            'email_verified_at' => now(),
        ],
    );
    $this->studentProfile = StudentProfile::updateOrCreate(
        ['user_id' => $this->studentUser->id],
        [
            'student_name' => $this->studentUser->name,
            'matric_no' => 'A999001',
            'ic_number' => '999999999999',
            'phone' => '0129999999',
            'programme' => 'BIP',
            'year' => 2,
            'talent' => 'Testing',
            'student_status' => 'active',
            'status' => 'active',
            'cgpa' => 3.5,
            'faculty' => 'FSKTM',
            'department' => 'Software Engineering',
            'cluster_id' => $this->clusters->id,
            'skills' => ['Testing'],
            'interests' => ['QA'],
        ],
    );

    $this->lecturer1 = User::updateOrCreate(
        ['email' => 'two_stage_lecturer1@strms.test'],
        [
            'name' => 'Lecturer One',
            'username' => 'two_stage_lecturer1',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $this->lecturer1Profile = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $this->lecturer1->id],
        [
            'lecturer_name' => 'Lecturer One',
            'lecturer_email' => $this->lecturer1->email,
            'lecturer_phone' => '0120000001',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );

    $this->lecturer2 = User::updateOrCreate(
        ['email' => 'two_stage_lecturer2@strms.test'],
        [
            'name' => 'Lecturer Two',
            'username' => 'two_stage_lecturer2',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $this->lecturer2Profile = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $this->lecturer2->id],
        [
            'lecturer_name' => 'Lecturer Two',
            'lecturer_email' => $this->lecturer2->email,
            'lecturer_phone' => '0120000002',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );

    $this->coordinator = User::updateOrCreate(
        ['email' => 'two_stage_coordinator@strms.test'],
        [
            'name' => 'Test Coordinator',
            'username' => 'two_stage_coordinator',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );

    $this->itcAdmin = User::updateOrCreate(
        ['email' => 'two_stage_itc@strms.test'],
        [
            'name' => 'Test ITC',
            'username' => 'two_stage_itc',
            'password' => Hash::make('password'),
            'role' => UserRole::ItcAdmin->value,
            'email_verified_at' => now(),
        ],
    );

    $this->activity = Activity::create([
        'activity_name' => 'Two-Stage Test Activity',
        'activity_type' => 'competition',
        'activity_date' => now()->addWeek(),
        'organizer' => 'TalentSys Test',
        'activity_level' => 'faculty',
        'description' => 'Activity for two-stage approval tests',
        'location' => 'FSKTM',
        'created_by' => $this->coordinator->id,
        'is_open' => true,
    ]);
});

test('student submits a participation; both stages start as pending', function () {
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $this->activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturer1Profile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $participation->refresh();

    expect($participation->lecturer_status)->toBe('pending')
        ->and($participation->coordinator_status)->toBe('pending')
        ->and($participation->participation_status)->toBe('pending')
        ->and($participation->accompanying_id)->toBe($this->lecturer1Profile->id);
});

test('lecturer1 verifies the participation; coordinator stage stays pending and no student notification is sent yet', function () {
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $this->activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturer1Profile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->lecturer1)->post(route('lecturer.verifications.verify', $participation), [
        'verification_notes' => 'Looks legitimate.',
    ]);

    $response->assertRedirect(route('lecturer.verifications.index'));

    $participation->refresh();

    expect($participation->lecturer_status)->toBe('verified')
        ->and($participation->lecturer_reviewed_by)->toBe($this->lecturer1->id)
        ->and($participation->lecturer_notes)->toBe('Looks legitimate.')
        ->and($participation->coordinator_status)->toBe('pending')
        ->and($participation->participation_status)->toBe('pending');

    expect(Notification::where('user_id', $this->studentUser->id)->where('type', 'participation_verified')->count())->toBe(0);
});

test('lecturer2 cannot see or verify lecturer1 participation (per-lecturer scoping)', function () {
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $this->activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturer1Profile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $index = $this->actingAs($this->lecturer2)->get(route('lecturer.verifications.index'));
    $index->assertStatus(200);
    $index->assertDontSee('Two-Stage Test Activity');

    $verify = $this->actingAs($this->lecturer2)->post(route('lecturer.verifications.verify', $participation));
    $verify->assertStatus(403);

    $participation->refresh();
    expect($participation->lecturer_status)->toBe('pending');
});

test('coordinator approves a lecturer-verified participation and the student gets the verified notification', function () {
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $this->activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturer1Profile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_status' => 'verified',
        'lecturer_reviewed_by' => $this->lecturer1->id,
        'lecturer_reviewed_at' => now(),
        'lecturer_notes' => 'Confirmed by lecturer',
    ]);

    $response = $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.approve', $participation),
        ['coordinator_notes' => 'Final sign-off.']
    );

    $response->assertRedirect(route('coordinator.participation-approvals.index'));

    $participation->refresh();

    expect($participation->coordinator_status)->toBe('approved')
        ->and($participation->coordinator_reviewed_by)->toBe($this->coordinator->id)
        ->and($participation->participation_status)->toBe('verified')
        ->and($participation->verified_by)->toBe($this->coordinator->id);

    $note = Notification::where('user_id', $this->studentUser->id)
        ->where('type', 'participation_verified')
        ->first();
    expect($note)->not->toBeNull();
    expect($note->notification_message)->toContain('Two-Stage Test Activity');
});

test('coordinator rejects a lecturer-verified participation with a reason and the student gets the rejected notification', function () {
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $this->activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturer1Profile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_reviewed_by' => $this->lecturer1->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $response = $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.reject', $participation),
        ['coordinator_notes' => 'Documentation insufficient.']
    );

    $response->assertRedirect(route('coordinator.participation-approvals.index'));

    $participation->refresh();

    expect($participation->coordinator_status)->toBe('rejected')
        ->and($participation->participation_status)->toBe('rejected')
        ->and($participation->rejection_reason)->toBe('Documentation insufficient.');

    $note = Notification::where('user_id', $this->studentUser->id)
        ->where('type', 'participation_rejected')
        ->first();
    expect($note)->not->toBeNull();
    expect($note->notification_message)->toContain('Documentation insufficient');
});

test('non-coordinator cannot access the coordinator participation approval index', function () {
    $response = $this->actingAs($this->itcAdmin)->get(route('coordinator.participation-approvals.index'));
    $response->assertStatus(403);
});
