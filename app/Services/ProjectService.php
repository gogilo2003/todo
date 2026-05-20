<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Project Service - handles business logic for projects.
 * Follows clean architecture principles.
 */
class ProjectService
{
    /**
     * Get all projects for a user.
     */
    public function getAllProjects(User $user): Collection
    {
        return $user->projects()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get paginated projects for a user.
     */
    public function getProjects(User $user, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $user->projects()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get a specific project for a user.
     */
    public function getProject(User $user, int $projectId): ?Project
    {
        return $user->projects()
            ->with('tasks')
            ->find($projectId);
    }

    /**
     * Create a new project.
     */
    public function createProject(User $user, array $data): Project
    {
        return $user->projects()->create($data);
    }

    /**
     * Update an existing project.
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);
        return $project->fresh();
    }

    /**
     * Delete a project.
     */
    public function deleteProject(Project $project): bool
    {
        return $project->delete();
    }

    /**
     * Get project with task counts.
     */
    public function getProjectsWithCounts(User $user): Collection
    {
        return $user->projects()
            ->withCount('tasks')
            ->orderBy('name')
            ->get();
    }
}
