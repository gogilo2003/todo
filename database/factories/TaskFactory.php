<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priorities = ['low', 'medium', 'high'];
        $titles = [
            'Complete project documentation',
            'Review pull request',
            'Fix bug in authentication',
            'Write unit tests',
            'Update dependencies',
            'Refactor database queries',
            'Add user profile page',
            'Implement search functionality',
            'Optimize API response time',
            'Create dashboard analytics',
            'Setup CI/CD pipeline',
            'Review code changes',
            'Add new feature',
            'Fix styling issues',
            'Update README',
        ];

        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'title' => fake()->randomElement($titles),
            'description' => fake()->optional()->paragraph(),
            'priority' => fake()->randomElement($priorities),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'completed' => false,
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed' => true,
        ]);
    }

    /**
     * Indicate that the task is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed' => false,
        ]);
    }

    /**
     * Indicate that the task has a specific priority.
     */
    public function priority(string $priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }

    /**
     * Indicate that the task belongs to a specific project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
            'user_id' => $project->user_id,
        ]);
    }
}
