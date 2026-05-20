<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Task Service - handles business logic for tasks.
 * Follows clean architecture principles.
 */
class TaskService
{
    /**
     * Get paginated tasks for a user with filters.
     */
    public function getTasks(User $user, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->buildFilteredQuery($user->tasks(), $filters);

        return $query->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all tasks for a user (non-paginated).
     */
    public function getAllTasks(User $user, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->buildFilteredQuery($user->tasks(), $filters);

        return $query->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get inbox tasks (tasks without project).
     */
    public function getInboxTasks(User $user, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $user->tasks()
            ->where(function ($q) {
                $q->whereNull('project_id')
                  ->orWhere('project_id', 0);
            })
            ->with('project');

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get today's dashboard data.
     */
    public function getTodayDashboard(User $user): array
    {
        $today = now()->startOfDay();

        $dueToday = $user->tasks()
            ->where('completed', false)
            ->whereDate('due_date', $today)
            ->with('project')
            ->orderBy('priority', 'desc')
            ->orderBy('due_date')
            ->get();

        $overdue = $user->tasks()
            ->where('completed', false)
            ->where('due_date', '<', $today)
            ->with('project')
            ->orderBy('due_date')
            ->get();

        $highPriority = $user->tasks()
            ->where('completed', false)
            ->where('priority', 'high')
            ->where(function ($q) use ($today) {
                $q->whereDate('due_date', '>', $today)
                  ->orWhereNull('due_date');
            })
            ->with('project')
            ->orderBy('due_date')
            ->limit(10)
            ->get();

return [
            'dueToday' => $dueToday,
            'overdue' => $overdue,
            'highPriority' => $highPriority,
        ];
    }

    /**
     * Get task statistics for a user.
     */
    public function getStats(User $user): array
    {
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->where('completed', true)->count();
        $pendingTasks = $user->tasks()->where('completed', false)->count();

        return [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'pending' => $pendingTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
        ];
    }

    /**
     * Create a new task.
     */
    public function createTask(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    /**
     * Toggle task completion status.
     */
    public function toggleTask(Task $task): Task
    {
        $task->update(['completed' => !$task->completed]);
        return $task->fresh();
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Build filtered query with eager loading.
     */
    protected function buildFilteredQuery(Builder $query, array $filters = []): Builder
    {
        $query->with('project');

        return $this->applyFilters($query, $filters);
    }

    /**
     * Apply filters to query.
     */
    protected function applyFilters(Builder $query, array $filters = []): Builder
    {
        // Status filter
        if (isset($filters['status'])) {
            if ($filters['status'] === 'completed') {
                $query->completed(true);
            } elseif ($filters['status'] === 'pending') {
                $query->pending();
            }
        }

        // Priority filter
        if (!empty($filters['priority']) && in_array($filters['priority'], ['low', 'medium', 'high'])) {
            $query->priority($filters['priority']);
        }

        // Project filter
        if (!empty($filters['project'])) {
            $query->project((int) $filters['project']);
        }

        // Search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Due date filter
        if (!empty($filters['due_date'])) {
            if ($filters['due_date'] === 'overdue') {
                $query->overdue();
            } elseif ($filters['due_date'] === 'today') {
                $query->dueToday();
            } else {
                $query->dueDate($filters['due_date']);
            }
        }

        return $query;
    }
}
