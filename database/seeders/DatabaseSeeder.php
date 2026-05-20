<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create projects for the user
        $projects = Project::factory()
            ->count(3)
            ->forUser($user)
            ->create();

        // Create tasks for each project
        foreach ($projects as $project) {
            Task::factory()
                ->count(rand(3, 8))
                ->forProject($project)
                ->create();
        }

        // Create some additional random users with their projects and tasks
        // User::factory(10)->create();
    }
}
