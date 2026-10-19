<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function dashboard()
    {
        return view('committee.dashboard');
    }

    public function index(Request $request)
    {
        $query = StudentProfile::with(['user', 'cluster'])->where('student_status', 'active');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhere('matric_no', 'like', "%{$q}%")
                    ->orWhere('talent', 'like', "%{$q}%")
                    ->orWhere('skills', 'like', "%{$q}%")
                    ->orWhere('interests', 'like', "%{$q}%");
            });
        }

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('talents')) {
            $talents = is_array($request->talents) ? $request->talents : explode(',', $request->talents);
            $query->where(function ($qb) use ($talents) {
                foreach ($talents as $talent) {
                    $t = trim($talent);
                    if ($t !== '') {
                        $qb->orWhere('talent', 'like', "%{$t}%")
                           ->orWhere('skills', 'like', "%{$t}%")
                           ->orWhere('interests', 'like', "%{$t}%");
                    }
                }
            });
        }

        $students = $query->paginate(20)->withQueryString();
        $programmes = StudentProfile::select('programme')->distinct()->pluck('programme');

        $allTalents = StudentProfile::whereNotNull('talent')
            ->where('talent', '!=', '')
            ->where('student_status', 'active')
            ->select('talent')
            ->distinct()
            ->pluck('talent')
            ->filter()
            ->sort()
            ->values();

        $selectedTalents = $request->filled('talents')
            ? (is_array($request->talents) ? $request->talents : explode(',', $request->talents))
            : [];

        return view('committee.search', compact('students', 'programmes', 'allTalents', 'selectedTalents'));
    }

    public function notifySelected(Request $request)
    {
        if (!auth()->user()->isItcAdmin() && !auth()->user()->isManagement()) {
            abort(403);
        }

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:student_profiles,id'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $students = StudentProfile::with('user')->whereIn('id', $data['student_ids'])->get();
        $notified = 0;

        foreach ($students as $student) {
            if ($student->user) {
                $this->notifications->send(
                    $student->user->id,
                    $data['message'],
                    'staff_notification',
                    route('student.activities.index')
                );
                $notified++;
            }
        }

        return back()->with('success', "Notified {$notified} students.");
    }
}
