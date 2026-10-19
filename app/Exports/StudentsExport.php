<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function __construct(private $students) {}

    public function collection()
    {
        return $this->students->map(fn ($s) => [
            'Name' => $s->user->name ?? '',
            'Matric No' => $s->matric_no,
            'Programme' => $s->programme,
            'Year' => $s->year,
            'Cluster' => $s->cluster->cluster_name ?? '',
            'Total Points' => $s->getTotalScore(),
            'Status' => $s->student_status,
        ]);
    }

    public function headings(): array
    {
        return ['Name', 'Matric No', 'Programme', 'Year', 'Cluster', 'Total Points', 'Status'];
    }
}
