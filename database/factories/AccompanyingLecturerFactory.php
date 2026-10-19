<?php

namespace Database\Factories;

use App\Models\AccompanyingLecturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccompanyingLecturer>
 */
class AccompanyingLecturerFactory extends Factory
{
    protected $model = AccompanyingLecturer::class;

    public function definition(): array
    {
        return [
            'lecturer_name' => 'Dr. '.fake()->name(),
            'lecturer_email' => fake()->unique()->safeEmail(),
            'lecturer_phone' => '01'.fake()->numerify('#########'),
            'department' => 'FSKTM',
            'is_active' => true,
        ];
    }
}
