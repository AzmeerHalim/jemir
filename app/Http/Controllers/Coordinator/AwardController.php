<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\StudentAward;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index()
    {
        $awards = StudentAward::with('student.user', 'recommender')
            ->latest('award_date')
            ->paginate(15);

        return view('coordinator.awards.index', compact('awards'));
    }

    public function create()
    {
        $students = StudentProfile::with('user')
            ->where('student_status', 'active')
            ->orderBy('student_name')
            ->get();

        return view('coordinator.awards.create', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:student_profiles,id'],
            'award_name' => ['required', 'string', 'max:255'],
            'award_category' => ['nullable', 'string', 'max:255'],
            'award_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $data['recommended_by'] = auth()->id();

        StudentAward::create($data);

        return redirect()->route('coordinator.awards.index')
            ->with('success', 'Award recorded.');
    }

    public function topPerformers()
    {
        $top = StudentProfile::with('user')
            ->get()
            ->sortByDesc(fn ($s) => $s->getTotalScore())
            ->take(10)
            ->values();

        return view('coordinator.awards.top', compact('top'));
    }
}
