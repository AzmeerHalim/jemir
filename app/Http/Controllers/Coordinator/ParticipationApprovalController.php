<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Participation;
use App\Services\MeritService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ParticipationApprovalController extends Controller
{
    public function __construct(
        private NotificationService $notifications,
        private MeritService $merit,
    ) {}

    public function index(Request $request)
    {
        $query = Participation::with(['student.user', 'activity', 'accompanyingLecturer.user', 'lecturerReviewer'])
            ->where('lecturer_status', 'verified')
            ->where('coordinator_status', 'pending');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('activity', fn ($q2) => $q2->where('activity_name', 'like', "%{$search}%"));
            });
        }

        $participations = $query->latest('lecturer_reviewed_at')->paginate(15)->withQueryString();

        $pendingCount = Participation::where('lecturer_status', 'verified')
            ->where('coordinator_status', 'pending')
            ->count();

        return view('coordinator.participation-approvals.index', [
            'participations' => $participations,
            'pendingCount' => $pendingCount,
            'search' => $search,
        ]);
    }

    public function show(Participation $participation)
    {
        $participation->load([
            'student.user',
            'activity',
            'accompanyingLecturer.user',
            'lecturerReviewer',
        ]);

        return view('coordinator.participation-approvals.show', [
            'participation' => $participation,
        ]);
    }

    public function approve(Request $request, Participation $participation)
    {
        abort_unless(
            $participation->lecturer_status === 'verified' && $participation->coordinator_status === 'pending',
            403,
            'This participation is not awaiting coordinator approval.'
        );

        $data = $request->validate([
            'coordinator_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $participation->update([
            'coordinator_status' => 'approved',
            'coordinator_notes' => $data['coordinator_notes'] ?? null,
            'coordinator_reviewed_by' => auth()->id(),
            'coordinator_reviewed_at' => now(),
            'participation_status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $this->createAchievementFromParticipation($participation);

        $this->merit->recomputeForStudent($participation->student_id);

        $this->notifications->participationVerified(
            $participation->student->user_id,
            $participation->activity->activity_name
        );

        return redirect()->route('coordinator.participation-approvals.index')
            ->with('success', 'Participation approved and credited to the student.');
    }

    public function reject(Request $request, Participation $participation)
    {
        abort_unless(
            $participation->lecturer_status === 'verified' && $participation->coordinator_status === 'pending',
            403,
            'This participation is not awaiting coordinator approval.'
        );

        $data = $request->validate([
            'coordinator_notes' => ['required', 'string', 'max:1000'],
        ]);

        $participation->update([
            'coordinator_status' => 'rejected',
            'coordinator_notes' => $data['coordinator_notes'],
            'coordinator_reviewed_by' => auth()->id(),
            'coordinator_reviewed_at' => now(),
            'participation_status' => 'rejected',
            'rejection_reason' => $data['coordinator_notes'],
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $this->notifications->participationRejected(
            $participation->student->user_id,
            $participation->activity->activity_name,
            $data['coordinator_notes']
        );

        return redirect()->route('coordinator.participation-approvals.index')
            ->with('success', 'Participation rejected.');
    }

    public function history(Request $request)
    {
        $query = Participation::with(['student.user', 'activity', 'accompanyingLecturer.user', 'coordinatorReviewer'])
            ->whereIn('coordinator_status', ['approved', 'rejected']);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('activity', fn ($q2) => $q2->where('activity_name', 'like', "%{$search}%"));
            });
        }

        $reviewed = $query->latest('coordinator_reviewed_at')->paginate(15)->withQueryString();

        return view('coordinator.participation-approvals.history', [
            'reviewed' => $reviewed,
            'search' => $search,
        ]);
    }

    private function createAchievementFromParticipation(Participation $participation): void
    {
        if (Achievement::where('participation_id', $participation->id)->exists()) {
            return;
        }

        $activity = $participation->activity;
        $levelMap = config('achievements.activity_to_achievement_level');
        $scoreMap = config('achievements.scores');

        $achievementLevel = $levelMap[$activity->activity_level ?? ''] ?? null;
        $score = $achievementLevel !== null
            ? ($scoreMap[$achievementLevel] ?? config('achievements.default_score'))
            : config('achievements.default_score');

        $lecturerName = $participation->lecturerReviewer?->name ?? '—';
        $coordinatorName = auth()->user()?->name ?? '—';

        Achievement::create([
            'student_id' => $participation->student_id,
            'title' => 'Participation: '.$activity->activity_name,
            'level' => $achievementLevel,
            'category_id' => $activity->category_id,
            'participation_id' => $participation->id,
            'accompanying_id' => $participation->accompanying_id,
            'event_name' => $activity->activity_name,
            'organizer' => $activity->organizer,
            'location' => $activity->location,
            'date_achieved' => $activity->activity_date,
            'description' => "Auto-credited from verified participation. Lecturer: {$lecturerName}. Coordinator: {$coordinatorName}.",
            'certificate' => $participation->certificate_path,
            'event_photo_path' => $participation->event_photo_path,
            'status' => 'approved',
            'score' => $score,
            'lecturer_status' => $participation->lecturer_status,
            'lecturer_reviewed_by' => $participation->lecturer_reviewed_by,
            'lecturer_reviewed_at' => $participation->lecturer_reviewed_at,
            'lecturer_notes' => $participation->lecturer_notes,
            'coordinator_status' => $participation->coordinator_status,
            'coordinator_reviewed_by' => $participation->coordinator_reviewed_by,
            'coordinator_reviewed_at' => $participation->coordinator_reviewed_at,
            'coordinator_notes' => $participation->coordinator_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
    }
}
