<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentAward>
 */
class StudentAwardFactory extends Factory
{
    protected $model = \App\Models\StudentAward::class;

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
