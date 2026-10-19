<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentProfile extends Model
{
    use HasFactory;

    protected $table = 'student_profiles';

    protected $fillable = [
        'user_id',
        'matric_no',
        'student_name',
        'ic_number',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'address',
        'programme',
        'year',
        'talent',
        'status',
        'student_status',
        'cluster_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'cgpa',
        'expected_graduation',
        'faculty',
        'department',
        'skills',
        'interests',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'cgpa' => 'decimal:2',
            'skills' => 'array',
            'interests' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class, 'student_id');
    }

    public function verifiedParticipations(): HasMany
    {
        return $this->hasMany(Participation::class, 'student_id')
            ->where('participation_status', 'verified')
            ->latest('verified_at');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'student_id');
    }

    public function merit(): HasOne
    {
        return $this->hasOne(Merit::class, 'student_id');
    }

    public function leaderboard(): HasOne
    {
        return $this->hasOne(Leaderboard::class, 'student_id');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(StudentAward::class, 'student_id');
    }

    public function getTotalScore(): int
    {
        return (int) $this->achievements()
            ->where('status', 'approved')
            ->sum('score');
    }

    public function getScoreByCategory($categoryId): int
    {
        return (int) $this->achievements()
            ->where('status', 'approved')
            ->where('category_id', $categoryId)
            ->sum('score');
    }
}
