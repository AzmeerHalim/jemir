<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->open()->upcoming()->registrationOpen();

        if ($request->filled('level')) {
            $query->where('activity_level', $request->level);
        }

        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }

        $activities = $query->withCount('participations')->paginate(12)->withQueryString();
        $joined = auth()->user()->studentProfile?->participations()->pluck('activity_id')->toArray() ?? [];

        return view('student.activities.index', compact('activities', 'joined'));
    }

    public function show(Activity $activity)
    {
        $student = auth()->user()->studentProfile;
        $alreadyJoined = $student?->participations()->where('activity_id', $activity->id)->exists();
        $deadlinePassed = $activity->registration_deadline && $activity->registration_deadline->isPast();
        $participantsCount = $activity->participantsCount();
        $spotsRemaining = $activity->spotsRemaining();
        $isFull = $activity->isFull();

        return view('student.activities.show', compact(
            'activity', 'alreadyJoined', 'deadlinePassed',
            'participantsCount', 'spotsRemaining', 'isFull'
        ));
    }
}
