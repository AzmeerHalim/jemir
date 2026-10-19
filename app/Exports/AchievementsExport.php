<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AchievementsExport implements FromCollection, WithHeadings
{
    public function __construct(private $achievements) {}

    public function collection()
    {
        return $this->achievements->map(fn ($a) => [
            'Student' => $a->student->user->name ?? '',
            'Matric No' => $a->student->matric_no ?? '',
            'Programme' => $a->student->programme ?? '',
            'Achievement' => $a->title,
            'Category' => $a->category->name ?? '',
            'Level' => $a->level,
            'Points' => $a->score,
            'Status' => $a->status,
            'Date' => $a->created_at->format('Y-m-d'),
        ]);
    }

    public function headings(): array
    {
        return ['Student', 'Matric No', 'Programme', 'Achievement', 'Category', 'Level', 'Points', 'Status', 'Date'];
    }
}
