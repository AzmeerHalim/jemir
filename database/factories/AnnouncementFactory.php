<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = \App\Models\Announcement::class;

    public function definition(): array
    {
        return [
            'announcement_title' => fake()->sentence(4),
            'announcement_message' => fake()->paragraph(),
            'announcement_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'is_published' => true,
        ];
    }
}
