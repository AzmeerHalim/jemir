<?php

namespace Database\Factories;

use App\Models\StudentAward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentAward>
 */
class StudentAwardFactory extends Factory
{
    protected $model = StudentAward::class;

    public function definition(): array
    {
        $awards = ['Dean\'s List', 'Best Talent Award', 'Excellence Award', 'Anugerah Pelajar Cemerlang'];
        $categories = ['Academic', 'Sports', 'Leadership', 'Innovation', 'Community Service'];

        return [
            'award_name' => fake()->randomElement($awards),
            'award_category' => fake()->randomElement($categories),
            'award_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'description' => fake()->sentence(),
        ];
    }
}
