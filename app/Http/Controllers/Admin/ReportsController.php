<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function dashboard()
    {
        return view('affairs.dashboard');
    }

    public function index()
    {
        return view('admin.reports');
    }

    public function export()
    {
        // CSV export placeholder
        $achievements = \App\Models\Achievement::with('student.user', 'category')
            ->where('status', 'approved')
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="achievements-report.csv"',
        ];

        $callback = function () use ($achievements) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student', 'Matric No', 'Programme', 'Achievement', 'Category', 'Level', 'Points', 'Date']);

            foreach ($achievements as $a) {
                fputcsv($file, [
                    $a->student->user->name,
                    $a->student->matric_no,
                    $a->student->course,
                    $a->title,
                    $a->category?->name,
                    $a->level,
                    $a->score,
                    $a->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
