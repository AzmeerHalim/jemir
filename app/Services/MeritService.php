<?php

namespace App\Services;

use App\Models\Leaderboard;
use App\Models\Merit;
use App\Models\StudentProfile;

class MeritService
{
    public function recomputeAll(): void
    {
        $students = StudentProfile::with('achievements')->get();

        $ranked = $students->sortByDesc(function ($student) {
            return $student->getTotalScore();
        })->values();

        $ranking = 1;
        foreach ($ranked as $student) {
            $total = $student->getTotalScore();

            Merit::updateOrCreate(
                ['student_id' => $student->id],
                [
                    'total_score' => $total,
                    'ranking' => $total > 0 ? $ranking : null,
                    'last_recalculated_at' => now(),
                ]
            );

            Leaderboard::updateOrCreate(
                ['student_id' => $student->id],
                [
                    'total_points' => $total,
                    'position' => $total > 0 ? $ranking : null,
                    'snapshot_at' => now(),
                ]
            );

            if ($total > 0) {
                $ranking++;
            }
        }
    }

    public function recomputeForStudent(int $studentId): void
    {
        $student = StudentProfile::with('achievements')->find($studentId);

        if (! $student) {
            return;
        }

        $total = $student->getTotalScore();

        Merit::updateOrCreate(
            ['student_id' => $student->id],
            [
                'total_score' => $total,
                'last_recalculated_at' => now(),
            ]
        );

        Leaderboard::updateOrCreate(
            ['student_id' => $student->id],
            [
                'total_points' => $total,
                'snapshot_at' => now(),
            ]
        );

        $this->recomputeAll();
    }
}
