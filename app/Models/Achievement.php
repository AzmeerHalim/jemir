<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'level',
        'category_id',
        'participation_id',
        'accompanying_id',
        'date_achieved',
        'event_name',
        'organizer',
        'location',
        'description',
        'certificate',
        'event_photo_path',
        'status',
        'score',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'lecturer_status',
        'lecturer_notes',
        'lecturer_reviewed_by',
        'lecturer_reviewed_at',
        'coordinator_status',
        'coordinator_notes',
        'coordinator_reviewed_by',
        'coordinator_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_achieved' => 'date',
            'reviewed_at' => 'datetime',
            'lecturer_reviewed_at' => 'datetime',
            'coordinator_reviewed_at' => 'datetime',
        ];
    }

    public function student()
{
    return $this->belongsTo(Student::class, 'student_id');
}

public function category()
{
    return $this->belongsTo(Category::class);
}

public function participation()
{
    return $this->belongsTo(Participation::class);
}

public function accompanyingLecturer()
{
    return $this->belongsTo(AccompanyingLecturer::class, 'accompanying_id');
}

public function reviewedBy()
{
    return $this->belongsTo(User::class, 'reviewed_by');
}

public function lecturerReviewer()
{
    return $this->belongsTo(User::class, 'lecturer_reviewed_by');
}

public function coordinatorReviewer()
{
    return $this->belongsTo(User::class, 'coordinator_reviewed_by');
}

public function isSelfSubmitted(): bool
{
    return $this->participation_id === null;
}
}
