<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AccompanyingLecturer;
use App\Models\Activity;
use App\Models\Participation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticipationController extends Controller
{
    public function index()
    {
        $student = auth()->user()->studentProfile;

        $participations = Participation::with('activity', 'accompanyingLecturer', 'verifiedBy')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return view('student.participations.index', compact('participations'));
    }

    public function create(Activity $activity)
    {
        $lecturers = AccompanyingLecturer::with('user')
            ->where('is_active', true)
            ->orderBy('lecturer_name')
            ->get();

        return view('student.participations.create', compact('activity', 'lecturers'));
    }

    public function store(Request $request, Activity $activity)
    {
        $student = auth()->user()->studentProfile;

        if ($activity->isFull()) {
            return back()->with('error', 'This activity is already full. No spots remaining.');
        }

        if (! $activity->is_open) {
            return back()->with('error', 'Registration for this activity is closed.');
        }

        if ($activity->registration_deadline && $activity->registration_deadline->isPast()) {
            return back()->with('error', 'The registration deadline has passed.');
        }

        if ($student->participations()->where('activity_id', $activity->id)->exists()) {
            return back()->with('error', 'You have already joined this activity.');
        }

        if ($activity->accompanying_id && ! $request->filled('accompanying_id')) {
            $request->merge(['accompanying_id' => $activity->accompanying_id]);
        }

        $data = $request->validate([
            'participation_role' => ['nullable', 'string', 'max:255'],
            'accompanying_id' => ['required', 'exists:accompanying_lecturers,id'],
        ]);

        Participation::create([
            'student_id' => $student->id,
            'activity_id' => $activity->id,
            'participation_role' => $data['participation_role'] ?? null,
            'accompanying_id' => $data['accompanying_id'],
            'participation_status' => 'pending',
        ]);

        return redirect()->route('student.participations.index')
            ->with('success', 'You have joined the activity. You can upload your certificate and event photo later.');
    }

    public function show(Participation $participation)
    {
        if ($participation->student_id !== auth()->user()->studentProfile?->id) {
            abort(403);
        }

        $participation->load('activity', 'accompanyingLecturer', 'verifiedBy');

        return view('student.participations.show', compact('participation'));
    }

    public function edit(Participation $participation)
    {
        if ($participation->student_id !== auth()->user()->studentProfile?->id) {
            abort(403);
        }

        if (! in_array($participation->participation_status, ['pending', 'rejected'])) {
            return back()->with('error', 'You can only upload proof for pending or rejected participations.');
        }

        $participation->load('activity');

        return view('student.participations.edit', compact('participation'));
    }

    public function update(Request $request, Participation $participation)
    {
        if ($participation->student_id !== auth()->user()->studentProfile?->id) {
            abort(403);
        }

        if (! in_array($participation->participation_status, ['pending', 'rejected'])) {
            return back()->with('error', 'You can only upload proof for pending or rejected participations.');
        }

        $data = $request->validate([
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'event_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        if ($request->hasFile('certificate')) {
            if ($participation->certificate_path) {
                Storage::disk('public')->delete($participation->certificate_path);
            }
            $participation->certificate_path = $request->file('certificate')->store('certificates', 'public');
        }

        if ($request->hasFile('event_photo')) {
            if ($participation->event_photo_path) {
                Storage::disk('public')->delete($participation->event_photo_path);
            }
            $participation->event_photo_path = $request->file('event_photo')->store('event_photos', 'public');
        }

        $wasRejected = $participation->participation_status === 'rejected';

        if ($wasRejected) {
            $participation->participation_status = 'pending';
            $participation->lecturer_status = 'pending';
            $participation->rejection_reason = null;
            $participation->lecturer_notes = null;
            $participation->lecturer_reviewed_by = null;
            $participation->lecturer_reviewed_at = null;
            $participation->verified_by = null;
            $participation->verified_at = null;
            $participation->coordinator_status = 'pending';
            $participation->coordinator_notes = null;
            $participation->coordinator_reviewed_by = null;
            $participation->coordinator_reviewed_at = null;
        }

        $participation->save();

        $message = $wasRejected
            ? 'Proof uploaded and sent back to the lecturer for re-verification.'
            : 'Proof documents uploaded successfully.';

        return redirect()->route('student.participations.show', $participation)
            ->with('success', $message);
    }
}
