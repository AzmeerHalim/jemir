<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\Category;


class StudentController extends Controller
{
    public function profile()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect('/dashboard')->with('error', 'Profile not found');
        }

        return view('students.profile', compact('student'));
    }

    public function editProfile()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect('/dashboard');
        }

        return view('students.edit-profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect('/dashboard');
        }

        $data = $request->validate([
            'programme' => ['required', 'string', Rule::in([
                'Bachelor of Computer Science (Information Security)',
                'Bachelor of Computer Science (Multimedia Computing)',
                'Bachelor of Computer Science (Software Engineering)',
                'Bachelor of Computer Science (Web Technology)',
                'Bachelor of Information Technology',
            ])],
            'year' => ['required', 'integer', 'min:1', 'max:4'],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'expected_graduation' => ['nullable', 'string', 'max:100'],

            'ic_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Prefer not to say'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],

            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],

            'talent' => ['nullable', 'string', 'max:255'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'string', 'max:1000'],
        ]);

        $student->update([
            'programme' => $data['programme'],
            'year' => $data['year'],
            'cgpa' => $data['cgpa'] ?? null,
            'expected_graduation' => $data['expected_graduation'] ?? null,

            'ic_number' => $data['ic_number'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'nationality' => $data['nationality'] ?? 'Malaysian',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,

            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,

            'talent' => $data['talent'] ?? null,
            'skills' => $this->csvToArray($data['skills'] ?? null),
            'interests' => $this->csvToArray($data['interests'] ?? null),
        ]);

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    private function csvToArray(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }
        $parts = array_filter(array_map('trim', explode(',', $value)), fn ($v) => $v !== '');
        return $parts === [] ? null : array_values($parts);
    }


    public function categoryLeaderboard(Request $request, $id)
{
    $category = \App\Models\Category::findOrFail($id);

    $programme = $request->programme;

    $students = \App\Models\Student::with('user')
        ->when($programme, function ($query) use ($programme) {
            $query->where('course', $programme);
        })
        ->get()
        ->map(function ($s) use ($id) {

            $s->score = $s->achievements()
                ->where('status','approved')
                ->where('category_id', $id)
                ->sum('score');

            return $s;
        })
        ->sortByDesc('score')
        ->values();

    $programmes = \App\Models\Student::select('course')->distinct()->pluck('course');

    return view('admin.category-leaderboard', compact(
        'students',
        'category',
        'programmes',
        'programme'
    ));
}

    public function history()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect('/dashboard');
        }

        return view('students.history');
    }

    public function leaderboard(Request $request)
{
    $categoryId = $request->category_id;

    $students = \App\Models\Student::with(['user', 'achievements'])
        ->get()
        ->map(function ($student) use ($categoryId) {

            $achievements = $student->achievements()
                ->where('status', 'approved');

            // 🔥 filter by category
            if ($categoryId) {
                $achievements->where('category_id', $categoryId);
            }

            $student->total_score = $achievements->sum('score');

            return $student;
        })
        ->sortByDesc('total_score')
        ->values();

    $categories = \App\Models\Category::all();

    return view('students.leaderboard', compact('students', 'categories', 'categoryId'));
}


    
}