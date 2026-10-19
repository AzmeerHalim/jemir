<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccompanyingLecturer>
 */
class AccompanyingLecturerFactory extends Factory
{
    protected $model = \App\Models\AccompanyingLecturer::class;

    public function definition(): array
    {
        return [
            'lecturer_name' => 'Dr. ' . fake()->name(),
            'lecturer_email' => fake()->unique()->safeEmail(),
            'lecturer_phone' => '01' . fake()->numerify('#########'),
            'department' => 'FSKTM',
            'is_active' => true,
        ];
    }
}
