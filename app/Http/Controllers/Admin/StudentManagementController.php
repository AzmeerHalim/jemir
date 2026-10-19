<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = Student::with('user')->get();
        return view('admin.students.index', compact('students'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'course' => 'required|string',
            'year' => 'required|integer|min:1|max:4',
            'talent' => 'nullable|string',
        ]);

        $student->update([
            'course' => $request->course,
            'year' => $request->year,
            'talent' => $request->talent,
        ]);

        return redirect()->route('admin.students')
            ->with('success', 'Student updated successfully');
    }
}