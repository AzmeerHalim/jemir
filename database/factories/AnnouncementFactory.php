<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

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
