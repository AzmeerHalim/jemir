<?php

use App\Enums\UserRole;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new students can register with the comprehensive payload', function () {
    $payload = [
        'name' => 'Ahmad bin Ali',
        'username' => 'ahmad2024',
        'email' => 'ahmad.ali@strms.test',
        'password' => 'password',
        'password_confirmation' => 'password',

        'matric_no' => 'A200100',
        'programme' => 'Bachelor of Computer Science (Software Engineering)',
        'year' => 2,
        'cluster_id' => null,

        'gender' => 'Male',
        'date_of_birth' => '2003-05-12',
        'nationality' => 'Malaysian',
        'ic_number' => '030512101234',
        'phone' => '0123456789',
        'address' => 'Parit Raja, Johor',

        'talent' => 'Web Development',
        'skills' => 'PHP, Laravel, JavaScript',
        'interests' => 'Hackathons, Open Source',
    ];

    $response = $this->post('/register', $payload);

    $response->assertRedirect(route('student.dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', $payload['email'])->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe(UserRole::Student);
    expect($user->username)->toBe('ahmad2024');
    expect($user->name)->toBe('Ahmad bin Ali');
    expect(Hash::check('password', $user->password))->toBeTrue();

    $profile = StudentProfile::where('user_id', $user->id)->first();
    expect($profile)->not->toBeNull();
    expect($profile->matric_no)->toBe('A200100');
    expect($profile->programme)->toBe('Bachelor of Computer Science (Software Engineering)');
    expect((int) $profile->year)->toBe(2);
    expect($profile->faculty)->toBe('FSKTM (Faculty of Computer Science and Information Technology)');
    expect($profile->student_status)->toBe('active');
    expect($profile->status)->toBe('active');
    expect($profile->gender)->toBe('Male');
    expect($profile->ic_number)->toBe('030512101234');
    expect($profile->phone)->toBe('0123456789');
    expect($profile->skills)->toBe(['PHP', 'Laravel', 'JavaScript']);
    expect($profile->interests)->toBe(['Hackathons', 'Open Source']);
});

test('registration rejects duplicate username', function () {
    User::create([
        'name' => 'Existing User',
        'username' => 'takenname',
        'email' => 'taken@strms.test',
        'password' => Hash::make('password'),
        'role' => UserRole::Student->value,
    ]);

    $response = $this->post('/register', [
        'name' => 'New User',
        'username' => 'takenname',
        'email' => 'new@strms.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'matric_no' => 'A200200',
        'programme' => 'BIP',
        'year' => 1,
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('registration rejects duplicate matric number', function () {
    $existing = User::create([
        'name' => 'Existing Student',
        'username' => 'existingstud',
        'email' => 'existing@strms.test',
        'password' => Hash::make('password'),
        'role' => UserRole::Student->value,
    ]);
    StudentProfile::create([
        'user_id' => $existing->id,
        'matric_no' => 'A999999',
        'programme' => 'BIP',
        'year' => 1,
        'status' => 'active',
    ]);

    $response = $this->post('/register', [
        'name' => 'New Student',
        'username' => 'newstud',
        'email' => 'new@strms.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'matric_no' => 'A999999',
        'programme' => 'BIP',
        'year' => 1,
    ]);

    $response->assertSessionHasErrors('matric_no');
    $this->assertGuest();
});

test('register page exposes all student fields and no role selector', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);

    foreach (['name', 'username', 'email', 'password', 'password_confirmation',
              'matric_no', 'programme', 'year',
              'gender', 'date_of_birth', 'nationality', 'ic_number', 'phone', 'address',
              'talent', 'skills', 'interests'] as $field) {
        $response->assertSee('name="'.$field.'"', false);
    }

    foreach ([
        'Bachelor of Computer Science (Information Security)',
        'Bachelor of Computer Science (Multimedia Computing)',
        'Bachelor of Computer Science (Software Engineering)',
        'Bachelor of Computer Science (Web Technology)',
        'Bachelor of Information Technology',
    ] as $programme) {
        $response->assertSee('value="'.$programme.'"', false);
    }

    $response->assertSee('FSKTM', false);
    $response->assertSee('Faculty', false);
    $response->assertDontSee('Department', false);
    $response->assertDontSee('name="role"', false);
});
