<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Participation;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function dashboard()
    {
        return view('lecturer.dashboard');
    }

    public function index()
    {
        $profileId = auth()->user()->accompanyingLecturer?->id;

        $query = Participation::with('student.user', 'activity', 'accompanyingLecturer')
            ->where('lecturer_status', 'pending')
            ->latest();

        if ($profileId) {
            $query->where('accompanying_id', $profileId);
        } else {
            $query->whereRaw('1 = 0');
        }

        $pending = $query->paginate(15);

        return view('lecturer.verifications.index', compact('pending'));
    }

    public function history()
    {
        $profileId = auth()->user()->accompanyingLecturer?->id;

        $query = Participation::with('student.user', 'activity', 'verifiedBy')
            ->whereIn('lecturer_status', ['verified', 'rejected'])
            ->latest('lecturer_reviewed_at');

        if ($profileId) {
            $query->where('accompanying_id', $profileId);
        } else {
            $query->whereRaw('1 = 0');
        }

        $reviewed = $query->paginate(15);

        return view('lecturer.verifications.history', compact('reviewed'));
    }

    public function show(Participation $participation)
    {
        $this->authorizeAccess($participation);

        $participation->load('student.user', 'activity', 'accompanyingLecturer');
        return view('lecturer.verifications.show', compact('participation'));
    }

    public function verify(Request $request, Participation $participation)
    {
        $this->authorizeAccess($participation);

        $data = $request->validate([
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $participation->update([
            'lecturer_status' => 'verified',
            'lecturer_notes' => $data['verification_notes'] ?? null,
            'lecturer_reviewed_by' => auth()->id(),
            'lecturer_reviewed_at' => now(),
        ]);

        return redirect()->route('lecturer.verifications.index')
            ->with('success', 'Participation forwarded to the coordinator for final approval.');
    }

    public function reject(Request $request, Participation $participation)
    {
        $this->authorizeAccess($participation);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $participation->update([
            'lecturer_status' => 'rejected',
            'lecturer_notes' => $data['rejection_reason'],
            'participation_status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
            'lecturer_reviewed_by' => auth()->id(),
            'lecturer_reviewed_at' => now(),
        ]);

        return redirect()->route('lecturer.verifications.index')
            ->with('success', 'Participation rejected. The student may resubmit with a different lecturer.');
    }

    private function authorizeAccess(Participation $participation): void
    {
        $profileId = auth()->user()->accompanyingLecturer?->id;

        if (! $profileId || (int) $participation->accompanying_id !== (int) $profileId) {
            abort(403, 'This participation is not assigned to you.');
        }

        if ($participation->lecturer_status !== 'pending') {
            abort(403, 'This participation has already been reviewed.');
        }
    }
}
