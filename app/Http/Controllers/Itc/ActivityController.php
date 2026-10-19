<?php

namespace App\Http\Controllers\Itc;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function dashboard()
    {
        return view('itc.dashboard');
    }

    public function index()
    {
        $activities = Activity::with('creator')->latest('activity_date')->paginate(15);
        return view('itc.activities.index', compact('activities'));
    }

    public function create()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $lecturers = \App\Models\AccompanyingLecturer::where('is_active', true)->orderBy('lecturer_name')->get();
        return view('itc.activities.create', compact('categories', 'lecturers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'activity_name' => ['required', 'string', 'max:255'],
            'activity_type' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'organizer' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'activity_level' => ['required', 'in:faculty,university,state,national,international'],
            'accompanying_id' => ['nullable', 'integer', 'exists:accompanying_lecturers,id'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_open' => ['boolean'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'required_talents' => ['nullable', 'string', 'max:500'],
        ]);

        $data['created_by'] = auth()->id();
        $data['is_open'] = $request->boolean('is_open', true);
        $data['required_talents'] = $this->parseTalents($data['required_talents'] ?? null);

        Activity::create($data);

        return redirect()->route('itc.activities.index')
            ->with('success', 'Activity created.');
    }

    public function edit(Activity $activity)
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $lecturers = \App\Models\AccompanyingLecturer::where('is_active', true)->orderBy('lecturer_name')->get();
        return view('itc.activities.edit', compact('activity', 'categories', 'lecturers'));
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'activity_name' => ['required', 'string', 'max:255'],
            'activity_type' => ['required', 'string', 'max:255'],
            'activity_date' => ['required', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'organizer' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'activity_level' => ['required', 'in:faculty,university,state,national,international'],
            'accompanying_id' => ['nullable', 'integer', 'exists:accompanying_lecturers,id'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_open' => ['boolean'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'required_talents' => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_open'] = $request->boolean('is_open');
        $data['required_talents'] = $this->parseTalents($data['required_talents'] ?? null);

        $activity->update($data);

        return redirect()->route('itc.activities.index')
            ->with('success', 'Activity updated.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('itc.activities.index')
            ->with('success', 'Activity deleted.');
    }

    public function inviteMatchedStudents(Activity $activity)
    {
        $talents = $activity->getTalentsArray();

        if (empty($talents)) {
            return back()->with('error', 'This activity has no required talents set.');
        }

        $query = \App\Models\StudentProfile::where('student_status', 'active')
            ->where(function ($q) use ($talents) {
                foreach ($talents as $talent) {
                    $q->orWhere('talent', 'like', "%{$talent}%")
                      ->orWhere('skills', 'like', "%{$talent}%")
                      ->orWhere('interests', 'like', "%{$talent}%");
                }
            });

        $students = $query->get();
        $notified = 0;
        $notificationService = app(\App\Services\NotificationService::class);
        $activityUrl = route('student.activities.show', $activity);

        foreach ($students as $student) {
            if ($student->user) {
                $notificationService->activityInvitation(
                    $student->user->id,
                    $activity->activity_name,
                    $activityUrl
                );
                $notified++;
            }
        }

        return redirect()->route('itc.activities.index')
            ->with('success', "Invited {$notified} students to \"{$activity->activity_name}\".");
    }

    private function parseTalents(?string $value): ?array
    {
        if (blank($value)) {
            return null;
        }
        $parts = array_filter(array_map('trim', explode(',', $value)), fn ($v) => $v !== '');
        return $parts === [] ? null : array_values($parts);
    }
}
