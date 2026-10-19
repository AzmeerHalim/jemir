<?php

use App\Enums\UserRole;
use App\Models\AccompanyingLecturer;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Cluster;
use App\Models\Leaderboard;
use App\Models\Merit;
use App\Models\Participation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->clusters = Cluster::firstOrCreate(['cluster_name' => 'Test Cluster'], ['description' => 't']);

    $this->studentUser = User::updateOrCreate(
        ['email' => 'credit_student@strms.test'],
        [
            'name' => 'Credit Student',
            'username' => 'credit_student',
            'password' => Hash::make('password'),
            'role' => UserRole::Student->value,
            'email_verified_at' => now(),
        ],
    );
    $this->studentProfile = StudentProfile::updateOrCreate(
        ['user_id' => $this->studentUser->id],
        [
            'student_name' => $this->studentUser->name,
            'matric_no' => 'A777001',
            'ic_number' => '777777777777',
            'phone' => '0127777777',
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

    $this->lecturer = User::updateOrCreate(
        ['email' => 'credit_lecturer@strms.test'],
        [
            'name' => 'Credit Lecturer',
            'username' => 'credit_lecturer',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $this->lecturerProfile = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $this->lecturer->id],
        [
            'lecturer_name' => 'Credit Lecturer',
            'lecturer_email' => $this->lecturer->email,
            'lecturer_phone' => '0127000001',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );

    $this->coordinator = User::updateOrCreate(
        ['email' => 'credit_coordinator@strms.test'],
        [
            'name' => 'Credit Coordinator',
            'username' => 'credit_coordinator',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );
});

function makeActivity(User $coordinator, string $level = 'national'): Activity
{
    return Activity::create([
        'activity_name' => 'Credit Test Activity '.uniqid(),
        'activity_type' => 'competition',
        'activity_date' => now()->addWeek(),
        'organizer' => 'TalentSys Test',
        'activity_level' => $level,
        'description' => 'Test',
        'location' => 'FSKTM',
        'created_by' => $coordinator->id,
        'is_open' => true,
    ]);
}

test('coordinator approval of a participation auto-creates an approved achievement with score from config', function () {
    $activity = makeActivity($this->coordinator, 'national');

    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_status' => 'verified',
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.approve', $participation)
    );

    $achievement = Achievement::where('participation_id', $participation->id)->first();
    expect($achievement)->not->toBeNull();
    expect($achievement->student_id)->toBe($this->studentProfile->id);
    expect($achievement->title)->toBe('Participation: '.$activity->activity_name);
    expect($achievement->level)->toBe('national');
    expect($achievement->status)->toBe('approved');
    expect((int) $achievement->score)->toBe(config('achievements.scores.national'));
    expect($achievement->reviewed_by)->toBe($this->coordinator->id);
    expect($achievement->category_id)->toBeNull();
});

test('coordinator approval updates the student merit and leaderboard totals', function () {
    $activity = makeActivity($this->coordinator, 'international');

    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.approve', $participation)
    );

    $merit = Merit::where('student_id', $this->studentProfile->id)->first();
    expect($merit)->not->toBeNull();
    expect((int) $merit->total_score)->toBe(config('achievements.scores.international'));

    $leaderboard = Leaderboard::where('student_id', $this->studentProfile->id)->first();
    expect($leaderboard)->not->toBeNull();
    expect((int) $leaderboard->total_points)->toBe(config('achievements.scores.international'));
});

test('coordinator rejection does NOT create an achievement or update merit', function () {
    $activity = makeActivity($this->coordinator);

    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.reject', $participation),
        ['coordinator_notes' => 'Not eligible.']
    );

    expect(Achievement::where('participation_id', $participation->id)->count())->toBe(0);
    expect(Merit::where('student_id', $this->studentProfile->id)->count())->toBe(0);
});

test('approving the same participation twice does not create duplicate achievements', function () {
    $activity = makeActivity($this->coordinator, 'faculty');

    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.approve', $participation)
    );

    expect(Achievement::where('participation_id', $participation->id)->count())->toBe(1);

    $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.approve', $participation)
    );

    expect(Achievement::where('participation_id', $participation->id)->count())->toBe(1);
});

test('state activity level maps to university achievement level', function () {
    $activity = makeActivity($this->coordinator, 'state');

    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'pending',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'pending',
    ]);
    $participation->update([
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $this->actingAs($this->coordinator)->post(
        route('coordinator.participation-approvals.approve', $participation)
    );

    $achievement = Achievement::where('participation_id', $participation->id)->first();
    expect($achievement->level)->toBe('university');
    expect((int) $achievement->score)->toBe(config('achievements.scores.university'));
});
