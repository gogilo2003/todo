<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'priority',
        'due_date',
        'completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'completed' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope a query to filter by completed status.
     */
    public function scopeCompleted(Builder $query, bool $completed = true): Builder
    {
        return $query->where('completed', $completed);
    }

    /**
     * Scope a query to filter by pending status.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('completed', false);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopePriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to filter by project.
     */
    public function scopeProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope a query to filter by due date.
     */
    public function scopeDueDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('due_date', $date);
    }

    /**
     * Scope a query to filter by due date before a given date.
     */
    public function scopeDueBefore(Builder $query, string $date): Builder
    {
        return $query->whereDate('due_date', '<', $date);
    }

    /**
     * Scope a query to filter by due date on or before today.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('completed', false)
            ->whereDate('due_date', '<', now()->toDateString());
    }

    /**
     * Scope a query to filter by due today.
     */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->where('completed', false)
            ->whereDate('due_date', now()->toDateString());
    }

    /**
     * Scope a query to search by title.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('title', 'like', "%{$search}%");
    }
}
