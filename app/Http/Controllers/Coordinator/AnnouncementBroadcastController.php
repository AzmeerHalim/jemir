<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\User;

class AnnouncementBroadcastController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index()
    {
        $announcements = Announcement::with('author')->latest()->paginate(15);
        return view('coordinator.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('coordinator.announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'announcement_title' => ['required', 'string', 'max:255'],
            'announcement_message' => ['required', 'string'],
            'announcement_date' => ['required', 'date'],
        ]);

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'announcement_title' => $data['announcement_title'],
            'announcement_message' => $data['announcement_message'],
            'announcement_date' => $data['announcement_date'],
            'is_published' => true,
        ]);

        foreach (User::where('role', \App\Enums\UserRole::Student->value)->get() as $student) {
            $this->notifications->newAnnouncement($student->id, $announcement->announcement_title);
        }

        return redirect()->route('coordinator.announcements.index')
            ->with('success', 'Announcement published to all students.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('coordinator.announcements.index')
            ->with('success', 'Announcement deleted.');
    }
}
