<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $clusters = \App\Models\Cluster::orderBy('cluster_name')->get();
        return view('auth.register', compact('clusters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_.-]+$/', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            'matric_no' => ['required', 'string', 'max:50', Rule::unique('student_profiles', 'matric_no')],
            'programme' => ['required', 'string', Rule::in([
                'Bachelor of Computer Science (Information Security)',
                'Bachelor of Computer Science (Multimedia Computing)',
                'Bachelor of Computer Science (Software Engineering)',
                'Bachelor of Computer Science (Web Technology)',
                'Bachelor of Information Technology',
            ])],
            'year' => ['required', 'integer', 'min:1', 'max:4'],
            'cluster_id' => ['nullable', 'integer', Rule::exists('clusters', 'id')],

            'gender' => ['nullable', 'string', Rule::in(['Male', 'Female', 'Prefer not to say'])],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'ic_number' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],

            'talent' => ['nullable', 'string', 'max:255'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'string', 'max:1000'],
        ]);

        $username = strtolower(trim($data['username']));

        $user = DB::transaction(function () use ($data, $username) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $username,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::Student->value,
            ]);

            StudentProfile::create([
                'user_id' => $user->id,
                'student_name' => $data['name'],
                'matric_no' => $data['matric_no'],
                'ic_number' => $data['ic_number'] ?? null,
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'nationality' => $data['nationality'] ?? 'Malaysian',
                'address' => $data['address'] ?? null,
                'programme' => $data['programme'],
                'year' => $data['year'],
                'talent' => $data['talent'] ?? null,
                'student_status' => 'active',
                'status' => 'active',
                'cgpa' => null,
                'faculty' => 'FSKTM (Faculty of Computer Science and Information Technology)',
                'cluster_id' => $data['cluster_id'] ?? null,
                'skills' => $this->csvToArray($data['skills'] ?? null),
                'interests' => $this->csvToArray($data['interests'] ?? null),
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }

    private function csvToArray(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $parts = array_filter(array_map('trim', explode(',', $value)), fn ($v) => $v !== '');

        return $parts === [] ? null : array_values($parts);
    }
}
