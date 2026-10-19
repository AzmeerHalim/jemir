<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_name',
        'activity_type',
        'activity_date',
        'registration_deadline',
        'organizer',
        'category_id',
        'activity_level',
        'accompanying_id',
        'description',
        'location',
        'created_by',
        'is_open',
        'max_participants',
        'required_talents',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'registration_deadline' => 'date',
            'is_open' => 'boolean',
            'max_participants' => 'integer',
            'required_talents' => 'array',
        ];
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function accompanyingLecturer(): BelongsTo
    {
        return $this->belongsTo(AccompanyingLecturer::class, 'accompanying_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('activity_date', '>=', now()->toDateString());
    }

    public function scopeRegistrationOpen($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('registration_deadline')
              ->orWhere('registration_deadline', '>=', now()->toDateString());
        });
    }

    public function participantsCount(): int
    {
        return $this->participations()->count();
    }

    public function spotsRemaining(): ?int
    {
        if ($this->max_participants === null) {
            return null;
        }
        return max(0, $this->max_participants - $this->participantsCount());
    }

    public function isFull(): bool
    {
        if ($this->max_participants === null) {
            return false;
        }
        return $this->participantsCount() >= $this->max_participants;
    }

    public function getTalentsArray(): array
    {
        return $this->required_talents ?? [];
    }

    public function matchingStudentsCount(): int
    {
        if (empty($this->getTalentsArray())) {
            return 0;
        }
        return StudentProfile::where('student_status', 'active')
            ->where(function ($q) {
                foreach ($this->getTalentsArray() as $talent) {
                    $q->orWhere('talent', 'like', "%{$talent}%")
                      ->orWhere('skills', 'like', "%{$talent}%")
                      ->orWhere('interests', 'like', "%{$talent}%");
                }
            })
            ->count();
    }
}
