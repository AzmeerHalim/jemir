<?php

namespace Database\Factories;

use App\Models\Cluster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cluster>
 */
class ClusterFactory extends Factory
{
    protected $model = Cluster::class;

    public function definition(): array
    {
        return [
            'cluster_name' => fake()->unique()->word().' Cluster',
            'description' => fake()->sentence(),
        ];
    }
}
