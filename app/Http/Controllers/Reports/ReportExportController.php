<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Category;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportExportController extends Controller
{
    public function achievements(Request $request): Response
    {
        $achievements = Achievement::with('student.user', 'category')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->latest()
            ->get();

        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.achievements', compact('achievements'));
            return $pdf->download('achievements-report.pdf');
        }

        if ($format === 'xlsx') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AchievementsExport($achievements), 'achievements-report.xlsx');
        }

        return $this->csvResponse($achievements->map(fn ($a) => [
            $a->student->user->name ?? '',
            $a->student->matric_no ?? '',
            $a->student->programme ?? '',
            $a->title,
            $a->category->name ?? '',
            $a->level,
            $a->score,
            $a->status,
            $a->created_at->format('Y-m-d'),
        ]), ['Student', 'Matric', 'Programme', 'Title', 'Category', 'Level', 'Points', 'Status', 'Date'], 'achievements-report.csv');
    }

    public function students(Request $request): Response
    {
        $students = StudentProfile::with('user', 'cluster')->get();
        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.students', compact('students'));
            return $pdf->download('students-report.pdf');
        }

        if ($format === 'xlsx') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StudentsExport($students), 'students-report.xlsx');
        }

        return $this->csvResponse($students->map(fn ($s) => [
            $s->user->name ?? '',
            $s->matric_no,
            $s->programme,
            $s->year,
            $s->cluster->cluster_name ?? '',
            $s->getTotalScore(),
            $s->student_status,
        ]), ['Name', 'Matric', 'Programme', 'Year', 'Cluster', 'Total Points', 'Status'], 'students-report.csv');
    }

    private function csvResponse($rows, array $headers, string $filename): Response
    {
        $callback = function () use ($rows, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
