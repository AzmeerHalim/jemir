<?php

use App\Enums\UserRole;
use App\Models\Cluster;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $cluster = Cluster::firstOrCreate(['cluster_name' => 'Test Cluster'], ['description' => 't']);

    $this->studentUser = User::updateOrCreate(
        ['email' => 'profile_student@strms.test'],
        [
            'name' => 'Profile Student',
            'username' => 'profile_student',
            'password' => Hash::make('password'),
            'role' => UserRole::Student->value,
            'email_verified_at' => now(),
        ],
    );
    $this->studentProfile = StudentProfile::updateOrCreate(
        ['user_id' => $this->studentUser->id],
        [
            'student_name' => 'Profile Student',
            'matric_no' => 'A600001',
            'programme' => 'BIP',
            'year' => 2,
            'student_status' => 'active',
            'status' => 'active',
            'cgpa' => 3.5,
            'faculty' => 'FSKTM',
            'cluster_id' => $cluster->id,
        ],
    );
});

test('profile page shows all relevant data sections', function () {
    $response = $this->actingAs($this->studentUser)->get(route('profile'));

    $response->assertStatus(200);
    $response->assertSee('My Profile');
    $response->assertSee('Personal Information');
    $response->assertSee('Academic');
    $response->assertSee('Emergency Contact');
    $response->assertSee('Talent Cluster');
    $response->assertSee('Points by Category');
    $response->assertSee('Skills');
    $response->assertSee('Interests');
    $response->assertSee('Recent Activity');
    $response->assertSee($this->studentProfile->matric_no);
});

test('edit profile form exposes all editable fields', function () {
    $response = $this->actingAs($this->studentUser)->get(route('profile.edit'));

    $response->assertStatus(200);
    foreach (['programme', 'year', 'cgpa', 'expected_graduation',
        'ic_number', 'date_of_birth', 'gender', 'nationality', 'phone', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
        'talent', 'skills', 'interests'] as $field) {
        $response->assertSee('name="'.$field.'"', false);
    }
    $response->assertSee('FSKTM', false);
});

test('student can update the full profile with the new fields', function () {
    $response = $this->actingAs($this->studentUser)->post(route('profile.update'), [
        'programme' => 'Bachelor of Computer Science (Software Engineering)',
        'year' => 3,
        'cgpa' => 3.85,
        'expected_graduation' => 'July 2027',
        'ic_number' => '030512101234',
        'date_of_birth' => '2003-05-12',
        'gender' => 'Male',
        'nationality' => 'Malaysian',
        'phone' => '0123456789',
        'address' => '12 Jalan Cempaka',
        'emergency_contact_name' => 'Parent Name',
        'emergency_contact_phone' => '0198765432',
        'talent' => 'Web Development',
        'skills' => 'PHP, Laravel, JavaScript',
        'interests' => 'Hackathons, Open Source',
    ]);

    $response->assertRedirect(route('profile'));

    $this->studentProfile->refresh();
    expect($this->studentProfile->programme)->toBe('Bachelor of Computer Science (Software Engineering)')
        ->and((int) $this->studentProfile->year)->toBe(3)
        ->and((string) $this->studentProfile->cgpa)->toBe('3.85')
        ->and($this->studentProfile->expected_graduation)->toBe('July 2027')
        ->and($this->studentProfile->ic_number)->toBe('030512101234')
        ->and($this->studentProfile->gender)->toBe('Male')
        ->and($this->studentProfile->phone)->toBe('0123456789')
        ->and($this->studentProfile->address)->toBe('12 Jalan Cempaka')
        ->and($this->studentProfile->emergency_contact_name)->toBe('Parent Name')
        ->and($this->studentProfile->talent)->toBe('Web Development')
        ->and($this->studentProfile->skills)->toBe(['PHP', 'Laravel', 'JavaScript'])
        ->and($this->studentProfile->interests)->toBe(['Hackathons', 'Open Source']);
});

test('profile update validates programme and year are required', function () {
    $response = $this->actingAs($this->studentUser)->post(route('profile.update'), [
        'programme' => '',
        'year' => 99,
    ]);

    $response->assertSessionHasErrors(['programme', 'year']);
});

test('profile update validates cgpa is within 0-4', function () {
    $response = $this->actingAs($this->studentUser)->post(route('profile.update'), [
        'programme' => 'BIP',
        'year' => 2,
        'cgpa' => 5.0,
    ]);

    $response->assertSessionHasErrors('cgpa');
});

test('profile update parses CSV skills and interests into arrays', function () {
    $this->actingAs($this->studentUser)->post(route('profile.update'), [
        'programme' => 'Bachelor of Computer Science (Software Engineering)',
        'year' => 2,
        'skills' => '  PHP , Laravel , ,  Vue ',
        'interests' => 'Hackathons,Open Source',
    ]);

    $this->studentProfile->refresh();
    expect($this->studentProfile->skills)->toBe(['PHP', 'Laravel', 'Vue'])
        ->and($this->studentProfile->interests)->toBe(['Hackathons', 'Open Source']);
});

test('name and matric number are not editable through the form', function () {
    $response = $this->actingAs($this->studentUser)->get(route('profile.edit'));
    $response->assertSee('value="'.$this->studentUser->name.'"', false);
    $response->assertSee('value="'.$this->studentProfile->matric_no.'"', false);
    $response->assertSee('cursor-not-allowed', false);
});
