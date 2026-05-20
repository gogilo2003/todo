<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = [
            '#ef4444', // red
            '#f97316', // orange
            '#eab308', // yellow
            '#22c55e', // green
            '#14b8a6', // teal
            '#3b82f6', // blue
            '#6366f1', // indigo
            '#a855f7', // purple
            '#ec4899', // pink
        ];

        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'color' => fake()->randomElement($colors),
        ];
    }

    /**
     * Indicate that the project belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
