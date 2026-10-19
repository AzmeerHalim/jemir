<?php

use App\Enums\UserRole;
use App\Models\AccompanyingLecturer;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Participation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->clusters = Cluster::firstOrCreate(['cluster_name' => 'Test Cluster'], ['description' => 't']);
    $this->category = Category::firstOrCreate(['name' => 'Test Category '.uniqid()]);

    $this->studentUser = User::updateOrCreate(
        ['email' => 'index_student@strms.test'],
        [
            'name' => 'Index Student',
            'username' => 'index_student',
            'password' => Hash::make('password'),
            'role' => UserRole::Student->value,
            'email_verified_at' => now(),
        ],
    );
    $this->studentProfile = StudentProfile::updateOrCreate(
        ['user_id' => $this->studentUser->id],
        [
            'student_name' => $this->studentUser->name,
            'matric_no' => 'A555001',
            'ic_number' => '555555555555',
            'phone' => '0125555555',
            'programme' => 'BIP',
            'year' => 2,
            'talent' => 'Testing',
            'student_status' => 'active',
            'status' => 'active',
            'cgpa' => 3.5,
            'faculty' => 'FSKTM',
            'department' => 'Software Engineering',
            'cluster_id' => $this->clusters->id,
        ],
    );

    $this->lecturer = User::updateOrCreate(
        ['email' => 'index_lecturer@strms.test'],
        [
            'name' => 'Index Lecturer',
            'username' => 'index_lecturer',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $this->lecturerProfile = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $this->lecturer->id],
        [
            'lecturer_name' => 'Index Lecturer',
            'lecturer_email' => $this->lecturer->email,
            'lecturer_phone' => '0125000001',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );
});

test('student with no achievements sees empty state', function () {
    $response = $this->actingAs($this->studentUser)->get(route('achievements.index'));

    $response->assertStatus(200);
    $response->assertSee('All Achievements');
    $response->assertSee('No achievements to show');
    $response->assertSee('Add Achievement');
});

test('achievement index shows the unified list with source and status badges', function () {
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Hackathon',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $coordinator = User::updateOrCreate(
        ['email' => 'index_coordinator_a@strms.test'],
        [
            'name' => 'Index Coordinator A',
            'username' => 'index_coordinator_a',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );
    $activity = Activity::create([
        'activity_name' => 'Verified Activity',
        'activity_type' => 'competition',
        'activity_date' => now()->addWeek(),
        'organizer' => 'TalentSys Test',
        'activity_level' => 'national',
        'created_by' => $coordinator->id,
        'is_open' => true,
    ]);
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'verified',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'approved',
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
        'coordinator_reviewed_by' => $coordinator->id,
        'coordinator_reviewed_at' => now(),
        'verified_by' => $coordinator->id,
        'verified_at' => now(),
    ]);
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Participation: Verified Activity',
        'level' => 'national',
        'category_id' => null,
        'participation_id' => $participation->id,
        'status' => 'approved',
        'score' => 20,
    ]);

    $response = $this->actingAs($this->studentUser)->get(route('achievements.index'));

    $response->assertStatus(200);
    $response->assertSee('Self Hackathon');
    $response->assertSee('Self-submitted');
    $response->assertSee('Participation: Verified Activity');
    $response->assertSee('Participation');
});

test('status tab filter only shows matching achievements', function () {
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Pending Row',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Approved Row',
        'level' => 'national',
        'category_id' => $this->category->id,
        'status' => 'approved',
        'score' => 20,
    ]);

    $response = $this->actingAs($this->studentUser)
        ->get(route('achievements.index', ['status' => 'approved']));

    $response->assertStatus(200);
    $response->assertSee('Approved Row');
    $response->assertDontSee('Pending Row');
});

test('source tab filter splits participation from self-submitted', function () {
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Self Row',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $coordinator = User::updateOrCreate(
        ['email' => 'index_coordinator_b@strms.test'],
        [
            'name' => 'Index Coordinator B',
            'username' => 'index_coordinator_b',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );
    $activity = Activity::create([
        'activity_name' => 'Source Test Activity',
        'activity_type' => 'competition',
        'activity_date' => now()->addWeek(),
        'organizer' => 'TalentSys Test',
        'activity_level' => 'national',
        'created_by' => $coordinator->id,
        'is_open' => true,
    ]);
    $participation = Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'verified',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'approved',
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
        'coordinator_reviewed_by' => $coordinator->id,
        'coordinator_reviewed_at' => now(),
        'verified_by' => $coordinator->id,
        'verified_at' => now(),
    ]);
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Participation Row',
        'level' => 'national',
        'category_id' => null,
        'participation_id' => $participation->id,
        'status' => 'approved',
        'score' => 20,
    ]);

    $response = $this->actingAs($this->studentUser)
        ->get(route('achievements.index', ['source' => 'participation']));

    $response->assertStatus(200);
    $response->assertSee('Participation Row');
    $response->assertDontSee('Self Row');
});

test('search filter narrows the list to matching titles', function () {
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Hackathon Winner',
        'level' => 'national',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);
    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Sports Champion',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->studentUser)
        ->get(route('achievements.index', ['search' => 'Hackathon']));

    $response->assertStatus(200);
    $response->assertSee('Hackathon Winner');
    $response->assertDontSee('Sports Champion');
});

test('auto-created participation achievement shows participation source badge and total points', function () {
    $coordinator = User::updateOrCreate(
        ['email' => 'index_coordinator@strms.test'],
        [
            'name' => 'Index Coordinator',
            'username' => 'index_coordinator',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );

    $activity = Activity::create([
        'activity_name' => 'Unified Test Activity',
        'activity_type' => 'competition',
        'activity_date' => now()->addWeek(),
        'organizer' => 'TalentSys Test',
        'activity_level' => 'national',
        'created_by' => $coordinator->id,
        'is_open' => true,
    ]);

    Participation::create([
        'student_id' => $this->studentProfile->id,
        'activity_id' => $activity->id,
        'participation_role' => 'Participant',
        'accompanying_id' => $this->lecturerProfile->id,
        'participation_status' => 'verified',
        'lecturer_status' => 'verified',
        'coordinator_status' => 'approved',
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
        'coordinator_reviewed_by' => $coordinator->id,
        'coordinator_reviewed_at' => now(),
        'verified_by' => $coordinator->id,
        'verified_at' => now(),
    ]);

    Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Participation: Unified Test Activity',
        'level' => 'national',
        'category_id' => null,
        'status' => 'approved',
        'score' => 20,
        'reviewed_by' => $coordinator->id,
        'reviewed_at' => now(),
    ]);

    $response = $this->actingAs($this->studentUser)->get(route('achievements.index'));

    $response->assertStatus(200);
    $response->assertSee('Participation: Unified Test Activity');
    $response->assertSee('+20 pts');
    $response->assertSee('Total Points');
    $response->assertSee('Approved');
});

test('achievement create form exposes all required fields aligned with participation style', function () {
    $response = $this->actingAs($this->studentUser)->get(route('achievements.create'));

    $response->assertStatus(200);
    foreach (['title', 'category_id', 'level', 'event_name', 'organizer', 'date_achieved', 'location', 'description', 'certificate'] as $field) {
        $response->assertSee('name="'.$field.'"', false);
    }
    foreach (['faculty', 'university', 'national', 'international'] as $level) {
        $response->assertSee('value="'.$level.'"', false);
    }
});

test('achievement detail page shows source, status, and review panels', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Detail Test',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->studentUser)->get(route('achievements.show', $achievement));

    $response->assertStatus(200);
    $response->assertSee('Detail Test');
    $response->assertSee('Self-submitted');
    $response->assertSee('Lecturer Review');
    $response->assertSee('Coordinator Final Approval');
    $response->assertSee('Awaiting lecturer review');
});

test('editing a pending self-submitted achievement resets both stages', function () {
    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Resettable',
        'level' => 'national',
        'category_id' => $this->category->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $achievement->update([
        'lecturer_status' => 'verified',
        'lecturer_reviewed_by' => $this->lecturer->id,
        'lecturer_reviewed_at' => now(),
    ]);

    $response = $this->actingAs($this->studentUser)->put(
        route('achievements.update', $achievement),
        [
            'title' => 'Resettable renamed',
            'level' => 'national',
            'category_id' => $this->category->id,
            'description' => 'updated',
        ]
    );

    $response->assertRedirect(route('achievements.index'));
    $achievement->refresh();
    expect($achievement->title)->toBe('Resettable renamed')
        ->and($achievement->lecturer_status)->toBe('pending')
        ->and($achievement->coordinator_status)->toBe('pending')
        ->and($achievement->status)->toBe('pending');
});

test('achievement create form exposes accompanying_id and event_photo fields', function () {
    $response = $this->actingAs($this->studentUser)->get(route('achievements.create'));

    $response->assertStatus(200);
    $response->assertSee('name="accompanying_id"', false);
    $response->assertSee('name="event_photo"', false);
    $response->assertSee('name="certificate"', false);
    $response->assertSee('Accompanying Lecturer');
});

test('student can store achievement with accompanying_id and both uploads', function () {
    Storage::fake('public');

    $cert = UploadedFile::fake()->createWithContent('cert.pdf', '%PDF-1.4 fake');
    $photo = UploadedFile::fake()->createWithContent('event.jpg', 'fake-jpeg-bytes');

    $response = $this->actingAs($this->studentUser)->post(
        route('achievements.store'),
        [
            'title' => 'Self with uploads',
            'level' => 'faculty',
            'category_id' => $this->category->id,
            'accompanying_id' => $this->lecturerProfile->id,
            'description' => 'with attachments',
            'certificate' => $cert,
            'event_photo' => $photo,
        ]
    );

    $response->assertRedirect(route('achievements.index'));

    $achievement = Achievement::where('title', 'Self with uploads')->first();
    expect($achievement)->not->toBeNull();
    expect((int) $achievement->accompanying_id)->toBe((int) $this->lecturerProfile->id);
    expect($achievement->certificate)->not->toBeNull();
    expect($achievement->event_photo_path)->not->toBeNull();
});

test('lecturer sees only self-submitted achievements assigned to them', function () {
    $otherLecturer = User::updateOrCreate(
        ['email' => 'other_lecturer@strms.test'],
        [
            'name' => 'Other Lecturer',
            'username' => 'other_lecturer',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $otherLecturerProfile = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $otherLecturer->id],
        [
            'lecturer_name' => 'Other Lecturer',
            'lecturer_email' => $otherLecturer->email,
            'lecturer_phone' => '0120000000',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );

    $mine = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Assigned to me',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'accompanying_id' => $this->lecturerProfile->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);
    $theirs = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Assigned to other',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'accompanying_id' => $otherLecturerProfile->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->lecturer)->get(route('lecturer.achievement-verifications.index'));
    $response->assertStatus(200);
    $response->assertSee('Assigned to me');
    $response->assertDontSee('Assigned to other');

    $response = $this->actingAs($this->lecturer)->get(route('lecturer.achievement-verifications.show', $theirs));
    $response->assertStatus(403);
});

test('lecturer cannot verify achievement assigned to a different lecturer', function () {
    $otherLecturer = User::updateOrCreate(
        ['email' => 'other_lecturer_b@strms.test'],
        [
            'name' => 'Other Lecturer B',
            'username' => 'other_lecturer_b',
            'password' => Hash::make('password'),
            'role' => UserRole::Lecturer->value,
            'email_verified_at' => now(),
        ],
    );
    $otherLecturerProfile = AccompanyingLecturer::updateOrCreate(
        ['user_id' => $otherLecturer->id],
        [
            'lecturer_name' => 'Other Lecturer B',
            'lecturer_email' => $otherLecturer->email,
            'lecturer_phone' => '0120000099',
            'department' => 'FSKTM',
            'is_active' => true,
        ],
    );

    $achievement = Achievement::create([
        'student_id' => $this->studentProfile->id,
        'title' => 'Assigned to other B',
        'level' => 'faculty',
        'category_id' => $this->category->id,
        'accompanying_id' => $otherLecturerProfile->id,
        'status' => 'pending',
        'score' => 0,
        'lecturer_status' => 'pending',
        'coordinator_status' => 'pending',
    ]);

    $response = $this->actingAs($this->lecturer)->post(
        route('lecturer.achievement-verifications.verify', $achievement),
        ['verification_notes' => 'Trying to verify not mine.']
    );

    $response->assertStatus(403);
    $achievement->refresh();
    expect($achievement->lecturer_status)->toBe('pending');
});
