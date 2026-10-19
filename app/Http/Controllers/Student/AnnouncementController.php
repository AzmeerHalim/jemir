<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::published()->with('author')->paginate(10);
        return view('student.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('author');
        return view('student.announcements.show', compact('announcement'));
    }
}
