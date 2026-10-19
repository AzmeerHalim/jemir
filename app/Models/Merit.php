<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Merit extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_score',
        'ranking',
        'last_recalculated_at',
    ];

    protected function casts(): array
    {
        return [
            'last_recalculated_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }
}
