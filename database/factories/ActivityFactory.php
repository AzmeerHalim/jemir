<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = \App\Models\Activity::class;

    public function definition(): array
    {
        $types = ['Competition', 'Workshop', 'Seminar', 'Conference', 'Hackathon', 'Sports'];
        $levels = ['faculty', 'university', 'state', 'national', 'international'];

        return [
            'activity_name' => fake()->sentence(3),
            'activity_type' => fake()->randomElement($types),
            'activity_date' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'registration_deadline' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'organizer' => fake()->company(),
            'activity_level' => fake()->randomElement($levels),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'is_open' => true,
        ];
    }
}
