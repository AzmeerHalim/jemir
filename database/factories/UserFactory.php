<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'username' => Str::slug($name) . '_' . Str::lower(Str::random(4)),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => UserRole::Student->value,
            'remember_token' => Str::random(10),
        ];
    }

    public function withRole(UserRole $role): static
    {
        return $this->state(fn () => ['role' => $role->value]);
    }

    public function student(): static
    {
        return $this->withRole(UserRole::Student);
    }

    public function coordinator(): static
    {
        return $this->withRole(UserRole::Coordinator);
    }

    public function lecturer(): static
    {
        return $this->withRole(UserRole::Lecturer);
    }

    public function itcAdmin(): static
    {
        return $this->withRole(UserRole::ItcAdmin);
    }

    public function committee(): static
    {
        return $this->withRole(UserRole::Committee);
    }

    public function studentAffairs(): static
    {
        return $this->withRole(UserRole::StudentAffairs);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
