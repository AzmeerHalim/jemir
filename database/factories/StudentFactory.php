<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $programmes = ['BIS', 'BIM', 'BIP', 'BIW', 'BIT'];

        return [
            'user_id' => User::factory()->student(),
            'student_name' => null,
            'matric_no' => 'A' . fake()->unique()->numerify('######'),
            'programme' => fake()->randomElement($programmes),
            'year' => fake()->numberBetween(1, 4),
            'talent' => fake()->optional()->word(),
            'student_status' => 'active',
            'status' => 'active',
            'cgpa' => fake()->randomFloat(2, 2.5, 4.0),
            'skills' => [fake()->word(), fake()->word()],
            'interests' => [fake()->word()],
        ];
    }
}
