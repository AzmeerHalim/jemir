<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'title' => fake()->sentence(3),
            'level' => fake()->randomElement(['faculty', 'university', 'national', 'international']),
            'category_id' => Category::factory(),
            'certificate' => null,
            'status' => 'pending',
            'score' => 0,
        ];
    }

    public function approved(): static
    {
        $scoreMap = ['faculty' => 5, 'university' => 10, 'national' => 20, 'international' => 30];

        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'score' => $scoreMap[$attributes['level']] ?? 0,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'score' => 0,
        ]);
    }
}
