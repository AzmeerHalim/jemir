<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    public function definition(): array
    {
        $programmes = ['BIS', 'BIM', 'BIP', 'BIW', 'BIT'];

        return [
            'user_id' => User::factory()->student(),
            'student_name' => null,
            'matric_no' => 'A'.fake()->unique()->numerify('######'),
            'ic_number' => fake()->numerify('############'),
            'phone' => '01'.fake()->numerify('#########'),
            'date_of_birth' => fake()->date('Y-m-d', '2005-01-01'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'nationality' => 'Malaysian',
            'address' => fake()->address(),
            'programme' => fake()->randomElement($programmes),
            'year' => fake()->numberBetween(1, 4),
            'talent' => fake()->optional()->word(),
            'student_status' => 'active',
            'status' => 'active',
            'cgpa' => fake()->randomFloat(2, 2.5, 4.0),
            'faculty' => 'FSKTM',
            'department' => fake()->randomElement(['Software Engineering', 'Information Security', 'Multimedia', 'Web Technology']),
            'skills' => [fake()->word(), fake()->word(), fake()->word()],
            'interests' => [fake()->word(), fake()->word()],
        ];
    }
}
