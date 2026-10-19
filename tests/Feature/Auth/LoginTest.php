<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$accounts = [
    'student' => ['student@strms.test',     UserRole::Student,         'student.dashboard',     '/student/dashboard'],
    'coordinator' => ['coordinator@strms.test', UserRole::Coordinator,     'coordinator.dashboard', '/coordinator/dashboard'],
    'lecturer' => ['lecturer@strms.test',    UserRole::Lecturer,        'lecturer.dashboard',    '/lecturer/dashboard'],
    'itc_admin' => ['itcadmin@strms.test',    UserRole::ItcAdmin,        'itc.dashboard',         '/itc/dashboard'],
    'committee' => ['committee@strms.test',   UserRole::Committee,       'committee.dashboard',   '/committee/dashboard'],
    'student_affairs' => ['affairs@strms.test',     UserRole::StudentAffairs,  'affairs.dashboard',     '/affairs/dashboard'],
];

test('login form exposes all six roles', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);

    foreach (UserRole::cases() as $role) {
        $response->assertSee('value="'.$role->value.'"', false);
    }
});

dataset('roleAccounts', $accounts);

test('users can log in across every role and land on the correct dashboard', function (string $email, UserRole $role, string $routeName, string $expectedPath) {
    User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Test '.str_replace('_', ' ', $role->value),
            'username' => explode('@', $email)[0],
            'password' => Hash::make('password'),
            'role' => $role->value,
            'email_verified_at' => now(),
        ],
    );

    $response = $this->post('/login', [
        'email' => $email,
        'password' => 'password',
        'role' => $role->value,
    ]);

    $this->assertAuthenticatedAs(User::where('email', $email)->first());
    $response->assertRedirect(route($routeName, absolute: false));
    $response->assertRedirectContains($expectedPath);
})->with('roleAccounts');

test('login fails when the selected role does not match the user', function () use ($accounts) {
    [$email] = $accounts['coordinator'];
    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Coordinator',
            'username' => 'coordinator',
            'password' => Hash::make('password'),
            'role' => UserRole::Coordinator->value,
            'email_verified_at' => now(),
        ],
    );

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'role' => 'student',
    ]);

    $response->assertSessionHasErrors('role');
    $this->assertGuest();
});

test('login rejects an unknown role value', function () use ($accounts) {
    [$email] = $accounts['student'];
    User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Student',
            'username' => 'student',
            'password' => Hash::make('password'),
            'role' => UserRole::Student->value,
            'email_verified_at' => now(),
        ],
    );

    $response = $this->post('/login', [
        'email' => $email,
        'password' => 'password',
        'role' => 'superuser',
    ]);

    $response->assertSessionHasErrors('role');
    $this->assertGuest();
});
