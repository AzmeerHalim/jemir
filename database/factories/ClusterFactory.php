<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cluster>
 */
class ClusterFactory extends Factory
{
    protected $model = \App\Models\Cluster::class;

    public function definition(): array
    {
        return [
            'cluster_name' => fake()->unique()->word() . ' Cluster',
            'description' => fake()->sentence(),
        ];
    }
}
