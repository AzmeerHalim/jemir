<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $table = 'student_profiles';

    protected $fillable = [
        'user_id',
        'matric_no',
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

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'student_id');
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
