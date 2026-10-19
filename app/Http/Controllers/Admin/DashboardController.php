<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Category;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalAchievements = Achievement::count();
        $pending = Achievement::where('status', 'pending')->count();

        $categories = Category::all();

        $recentPending = Achievement::with('student.user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $programmeStats = Student::with('achievements')
            ->get()
            ->groupBy('course')
            ->map(function ($students, $course) {

                $totalScore = $students->sum(function ($s) {
                    return $s->achievements
                        ->where('status', 'approved')
                        ->sum('score');
                });

                return [
                    'programme' => $course,
                    'score' => $totalScore,
                ];
            })
            ->values();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalAchievements',
            'pending',
            'categories',
            'recentPending',
            'programmeStats',
        ));
    }
}
