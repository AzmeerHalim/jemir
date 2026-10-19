<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'activity_id',
        'participation_role',
        'participation_status',
        'accompanying_id',
        'certificate_path',
        'event_photo_path',
        'verification_notes',
        'rejection_reason',
        'verified_by',
        'verified_at',
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
            'verified_at' => 'datetime',
            'lecturer_reviewed_at' => 'datetime',
            'coordinator_reviewed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function accompanyingLecturer(): BelongsTo
    {
        return $this->belongsTo(AccompanyingLecturer::class, 'accompanying_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function lecturerReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_reviewed_by');
    }

    public function coordinatorReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_reviewed_by');
    }

    public function isVerified(): bool
    {
        return $this->participation_status === 'verified';
    }

    public function scopeVerified($query)
    {
        return $query->where('participation_status', 'verified');
    }
}
