<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'award_name',
        'award_category',
        'award_date',
        'description',
        'recommended_by',
    ];

    protected function casts(): array
    {
        return [
            'award_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function recommender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }
}
