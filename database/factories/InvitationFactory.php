<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Invitation create($attributes = [], ?Model $parent = null)
 * @method Invitation make($attributes = [], ?Model $parent = null)
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email'        => $this->faker->email,
            'expires_at'   => now()->addHour(),
            'activated_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state([
            'expires_at' => now()->subHour(),
        ]);
    }

    public function activated(): static
    {
        return $this->state([
            'activated_at' => now()->subHours(2),
        ]);
    }
}
