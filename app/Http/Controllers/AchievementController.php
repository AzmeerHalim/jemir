<?php

namespace App\Http\Controllers;

use App\Models\AccompanyingLecturer;
use App\Models\Achievement;
use App\Models\Category;
use App\Services\MeritService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    public function __construct(
        private MeritService $merit,
        private NotificationService $notifications
    ) {}

    /**
     * 🔐 GET CURRENT STUDENT (SAFE)
     */
    private function getStudent()
    {
        $student = auth()->user()->student;

        if (! $student) {
            abort(403, 'Access denied: Student profile not found');
        }

        return $student;
    }

    /**
     * STUDENT: List own achievements (single source of truth = achievements table).
     * Verified participations are already mirrored into achievements rows
     * by ParticipationApprovalController::createAchievementFromParticipation.
     */
    public function index(Request $request)
    {
        $student = $this->getStudent();

        $query = Achievement::with('category', 'participation.activity')
            ->where('student_id', $student->id);

        $currentStatus = $request->string('status')->toString() ?: 'all';
        if (in_array($currentStatus, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $currentStatus);
        }

        $source = $request->string('source')->toString() ?: 'all';
        if ($source === 'participation') {
            $query->whereNotNull('participation_id');
        } elseif ($source === 'self') {
            $query->whereNull('participation_id');
        }

        $search = $request->string('search')->toString();
        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        $achievements = $query->latest()->get();

        $statusCounts = Achievement::where('student_id', $student->id)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $sourceCounts = [
            'participation' => (int) Achievement::where('student_id', $student->id)->whereNotNull('participation_id')->count(),
            'self' => (int) Achievement::where('student_id', $student->id)->whereNull('participation_id')->count(),
        ];

        return view('achievements.index', [
            'achievements' => $achievements,
            'statusCounts' => $statusCounts + ['pending' => 0, 'approved' => 0, 'rejected' => 0],
            'sourceCounts' => $sourceCounts,
            'currentStatus' => $currentStatus,
            'currentSource' => $source,
            'search' => $search,
        ]);
    }

    /**
     * STUDENT: Create page
     */
    public function create()
    {
        $this->getStudent(); // 🔥 ensure only student access

        $categories = Category::orderBy('name')->get();
        $lecturers = AccompanyingLecturer::where('is_active', true)->orderBy('lecturer_name')->get();

        return view('achievements.create', compact('categories', 'lecturers'));
    }

    /**
     * STUDENT: Store achievement
     */
    public function store(Request $request)
    {
        $student = $this->getStudent();

        $request->validate([
            'title' => 'required|string',
            'level' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'accompanying_id' => ['nullable', 'integer', 'exists:accompanying_lecturers,id'],
            'certificate' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'event_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'description' => 'nullable|string|max:500',
            'event_name' => 'nullable|string',
            'organizer' => 'nullable|string',
            'location' => 'nullable|string',
            'date_achieved' => 'nullable|date',
        ]);

        $certPath = null;
        $photoPath = null;

        if ($request->hasFile('certificate')) {
            $certPath = $request->file('certificate')->store('certificates', 'public');
        }
        if ($request->hasFile('event_photo')) {
            $photoPath = $request->file('event_photo')->store('event_photos', 'public');
        }

        Achievement::create([
            'student_id' => $student->id,
            'title' => $request->title,
            'level' => $request->level,
            'category_id' => $request->category_id,
            'accompanying_id' => $request->accompanying_id ?: null,
            'description' => $request->description,
            'event_name' => $request->event_name,
            'organizer' => $request->organizer,
            'location' => $request->location,
            'date_achieved' => $request->date_achieved,
            'certificate' => $certPath,
            'event_photo_path' => $photoPath,
            'status' => 'pending',
            'score' => 0,
            'lecturer_status' => 'pending',
            'coordinator_status' => 'pending',
        ]);

        return redirect()->route('achievements.index')
            ->with('success', 'Achievement submitted for two-stage approval (Accompanying Lecturer → Talent Coordinator).');
    }

    /**
     * STUDENT: Show detail page.
     */
    public function show($id)
    {
        $achievement = Achievement::with('category', 'participation.activity', 'lecturerReviewer', 'coordinatorReviewer')
            ->findOrFail($id);

        if ($achievement->student_id != $this->getStudent()->id) {
            abort(403);
        }

        return view('achievements.show', compact('achievement'));
    }

    /**
     * STUDENT: Edit
     */
    public function edit($id)
    {
        $student = $this->getStudent();

        $achievement = Achievement::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $lecturers = AccompanyingLecturer::where('is_active', true)->orderBy('lecturer_name')->get();

        if ($achievement->student_id != $student->id) {
            abort(403);
        }

        return view('achievements.edit', compact('achievement', 'categories', 'lecturers'));
    }

    /**
     * STUDENT: Update
     */
    public function update(Request $request, $id)
    {
        $student = $this->getStudent();

        $achievement = Achievement::findOrFail($id);

        if ($achievement->student_id != $student->id) {
            abort(403);
        }

        if ($achievement->status == 'approved') {
            return back()->with('error', 'Cannot edit approved achievement');
        }

        $request->validate([
            'title' => 'required|string',
            'level' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'accompanying_id' => ['nullable', 'integer', 'exists:accompanying_lecturers,id'],
            'certificate' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'event_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'description' => 'nullable|string|max:500',
            'event_name' => 'nullable|string',
            'organizer' => 'nullable|string',
            'location' => 'nullable|string',
            'date_achieved' => 'nullable|date',
        ]);

        $certPath = $achievement->certificate;
        $photoPath = $achievement->event_photo_path;

        if ($request->hasFile('certificate')) {
            if ($certPath) {
                \Storage::disk('public')->delete($certPath);
            }
            $certPath = $request->file('certificate')->store('certificates', 'public');
        }
        if ($request->hasFile('event_photo')) {
            if ($photoPath) {
                \Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('event_photo')->store('event_photos', 'public');
        }

        $update = [
            'title' => $request->title,
            'level' => $request->level,
            'category_id' => $request->category_id,
            'accompanying_id' => $request->accompanying_id ?: null,
            'description' => $request->description,
            'event_name' => $request->event_name,
            'organizer' => $request->organizer,
            'location' => $request->location,
            'date_achieved' => $request->date_achieved,
            'certificate' => $certPath,
            'event_photo_path' => $photoPath,
        ];

        if ($achievement->lecturer_status !== 'pending' && $achievement->participation_id === null) {
            $update['lecturer_status'] = 'pending';
            $update['lecturer_reviewed_by'] = null;
            $update['lecturer_reviewed_at'] = null;
            $update['lecturer_notes'] = null;
            $update['coordinator_status'] = 'pending';
            $update['coordinator_reviewed_by'] = null;
            $update['coordinator_reviewed_at'] = null;
            $update['coordinator_notes'] = null;
            $update['status'] = 'pending';
            $update['score'] = 0;
        }

        $achievement->update($update);

        return redirect()->route('achievements.index')
            ->with('success', 'Achievement updated. Both review stages have been reset to pending.');
    }

    public function history()
    {
        $achievements = Achievement::with('student.user', 'category')
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->get();

        return view('admin.achievements-history', compact('achievements'));
    }

    /**
     * STUDENT: Delete
     */
    public function destroy($id)
    {
        $student = $this->getStudent();

        $achievement = Achievement::findOrFail($id);

        if ($achievement->student_id != $student->id) {
            abort(403);
        }

        if ($achievement->status !== 'pending') {
            return back()->with('error', 'Cannot delete this achievement');
        }

        if ($achievement->certificate) {
            \Storage::disk('public')->delete($achievement->certificate);
        }

        $achievement->delete();

        return back()->with('success', 'Achievement deleted');
    }

    /**
     * ADMIN: View all (approval page)
     */
    public function adminIndex()
    {
        $achievements = Achievement::with('student.user', 'category')
            ->where('lecturer_status', 'pending')
            ->whereNull('participation_id')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.approvals', compact('achievements'));
    }

    /**
     * LECTURER: Verify a self-submitted achievement (stage 1 of 2).
     */
    public function lecturerVerify(Request $request, Achievement $achievement)
    {
        abort_unless($achievement->lecturer_status === 'pending', 403, 'This achievement is not awaiting lecturer verification.');
        abort_if($achievement->participation_id !== null, 403, 'Auto-created achievements do not need lecturer verification.');

        $data = $request->validate([
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $achievement->update([
            'lecturer_status' => 'verified',
            'lecturer_notes' => $data['verification_notes'] ?? null,
            'lecturer_reviewed_by' => auth()->id(),
            'lecturer_reviewed_at' => now(),
        ]);

        return back()->with('success', 'Achievement forwarded to the talent coordinator for final approval.');
    }

    /**
     * LECTURER: Reject a self-submitted achievement at stage 1.
     */
    public function lecturerReject(Request $request, Achievement $achievement)
    {
        abort_unless($achievement->lecturer_status === 'pending', 403, 'This achievement is not awaiting lecturer verification.');
        abort_if($achievement->participation_id !== null, 403, 'Auto-created achievements cannot be rejected by the lecturer.');

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $achievement->update([
            'lecturer_status' => 'rejected',
            'lecturer_notes' => $data['rejection_reason'],
            'lecturer_reviewed_by' => auth()->id(),
            'lecturer_reviewed_at' => now(),
        ]);

        return back()->with('success', 'Achievement rejected at lecturer stage. The student can resubmit.');
    }

    /**
     * COORDINATOR: Final approval of a self-submitted achievement (stage 2 of 2).
     */
    public function approve($id)
    {
        $achievement = Achievement::findOrFail($id);

        abort_if($achievement->participation_id === null && $achievement->lecturer_status !== 'verified', 403, 'Achievement is not awaiting coordinator approval.');
        abort_if($achievement->coordinator_status !== 'pending', 403, 'Achievement has already been finalised.');

        $score = config('achievements.scores')[$achievement->level] ?? config('achievements.default_score');

        $achievement->update([
            'status' => 'approved',
            'score' => $score,
            'coordinator_status' => 'approved',
            'coordinator_reviewed_by' => auth()->id(),
            'coordinator_reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $this->merit->recomputeForStudent($achievement->student_id);

        if ($achievement->student?->user) {
            $this->notifications->achievementApproved(
                $achievement->student->user_id,
                $achievement->title
            );
        }

        return back()->with('success', 'Achievement approved');
    }

    /**
     * COORDINATOR: Reject a self-submitted achievement at stage 2.
     */
    public function reject($id)
    {
        $achievement = Achievement::findOrFail($id);

        abort_if($achievement->participation_id === null && $achievement->lecturer_status !== 'verified', 403, 'Achievement is not awaiting coordinator approval.');
        abort_if($achievement->coordinator_status !== 'pending', 403, 'Achievement has already been finalised.');

        $request = request();
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $achievement->update([
            'status' => 'rejected',
            'score' => 0,
            'coordinator_status' => 'rejected',
            'coordinator_notes' => $data['rejection_reason'],
            'coordinator_reviewed_by' => auth()->id(),
            'coordinator_reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        $this->merit->recomputeForStudent($achievement->student_id);

        if ($achievement->student?->user) {
            $this->notifications->achievementRejected(
                $achievement->student->user_id,
                $achievement->title,
                $data['rejection_reason']
            );
        }

        return back()->with('success', 'Achievement rejected');
    }

    public function bulkApprove(Request $request)
    {
        $ids = explode(',', $request->ids ?: '');
        $achievements = Achievement::whereIn('id', $ids)
            ->whereNull('participation_id')
            ->where('lecturer_status', 'verified')
            ->where('coordinator_status', 'pending')
            ->get();

        $studentIds = [];
        DB::transaction(function () use ($achievements, &$studentIds) {
            foreach ($achievements as $a) {
                $score = config('achievements.scores')[$a->level] ?? config('achievements.default_score');
                $a->update([
                    'status' => 'approved',
                    'score' => $score,
                    'coordinator_status' => 'approved',
                    'coordinator_reviewed_by' => auth()->id(),
                    'coordinator_reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
                $studentIds[$a->student_id] = true;
            }
        });

        foreach (array_keys($studentIds) as $sid) {
            $this->merit->recomputeForStudent($sid);
        }

        return back()->with('success', count($achievements).' achievements approved');
    }

    public function bulkReject(Request $request)
    {
        $ids = explode(',', $request->ids ?: '');
        $achievements = Achievement::whereIn('id', $ids)
            ->whereNull('participation_id')
            ->where('lecturer_status', 'verified')
            ->where('coordinator_status', 'pending')
            ->get();

        $studentIds = [];
        DB::transaction(function () use ($achievements, &$studentIds) {
            foreach ($achievements as $a) {
                $a->update([
                    'status' => 'rejected',
                    'score' => 0,
                    'coordinator_status' => 'rejected',
                    'coordinator_reviewed_by' => auth()->id(),
                    'coordinator_reviewed_at' => now(),
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
                $studentIds[$a->student_id] = true;
            }
        });

        foreach (array_keys($studentIds) as $sid) {
            $this->merit->recomputeForStudent($sid);
        }

        return back()->with('success', count($achievements).' achievements rejected');
    }
}
