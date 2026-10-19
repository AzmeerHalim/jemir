<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\AccompanyingLecturer;
use Illuminate\Http\Request;

class AchievementVerificationController extends Controller
{
    public function index()
    {
        $profileId = auth()->user()->accompanyingLecturer?->id;

        $query = Achievement::with('student.user', 'category', 'accompanyingLecturer')
            ->whereNull('participation_id')
            ->whereNotNull('accompanying_id')
            ->where('lecturer_status', 'pending')
            ->latest();

        if ($profileId) {
            $query->where('accompanying_id', $profileId);
        } else {
            $query->whereRaw('1 = 0');
        }

        $pending = $query->paginate(15);

        return view('lecturer.achievement-verifications.index', compact('pending'));
    }

    public function history()
    {
        $profileId = auth()->user()->accompanyingLecturer?->id;

        $query = Achievement::with('student.user', 'category', 'accompanyingLecturer', 'lecturerReviewer')
            ->whereNull('participation_id')
            ->whereNotNull('accompanying_id')
            ->whereIn('lecturer_status', ['verified', 'rejected'])
            ->latest('lecturer_reviewed_at');

        if ($profileId) {
            $query->where('accompanying_id', $profileId);
        } else {
            $query->whereRaw('1 = 0');
        }

        $reviewed = $query->paginate(15);

        return view('lecturer.achievement-verifications.history', compact('reviewed'));
    }

    public function show(Achievement $achievement)
    {
        $this->authorize($achievement);

        $achievement->load('student.user', 'category', 'accompanyingLecturer.user');

        return view('lecturer.achievement-verifications.show', compact('achievement'));
    }

    public function verify(Request $request, Achievement $achievement)
    {
        $this->authorize($achievement);

        $data = $request->validate([
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $achievement->update([
            'lecturer_status' => 'verified',
            'lecturer_notes' => $data['verification_notes'] ?? null,
            'lecturer_reviewed_by' => auth()->id(),
            'lecturer_reviewed_at' => now(),
        ]);

        return redirect()->route('lecturer.achievement-verifications.index')
            ->with('success', 'Achievement forwarded to the talent coordinator for final approval.');
    }

    public function reject(Request $request, Achievement $achievement)
    {
        $this->authorize($achievement);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $achievement->update([
            'lecturer_status' => 'rejected',
            'lecturer_notes' => $data['rejection_reason'],
            'lecturer_reviewed_by' => auth()->id(),
            'lecturer_reviewed_at' => now(),
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('lecturer.achievement-verifications.index')
            ->with('success', 'Achievement rejected at lecturer stage. The student can resubmit.');
    }

    private function authorize(Achievement $achievement): void
    {
        abort_if($achievement->participation_id !== null, 403, 'Auto-created achievements do not need lecturer verification.');

        $profileId = auth()->user()->accompanyingLecturer?->id;
        abort_unless($profileId && (int) $achievement->accompanying_id === (int) $profileId, 403, 'This achievement was not assigned to you.');

        abort_unless($achievement->lecturer_status === 'pending', 403, 'This achievement is not awaiting lecturer verification.');
    }
}
