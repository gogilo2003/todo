<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'priority', 'project', 'due_date', 'search']);

        $tasks = $this->taskService->getTasks($request->user(), $filters);
        $projects = $request->user()->projects()->orderBy('name')->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks->items(),
            'projects' => $projects,
            'filters' => array_merge([
                'status' => '',
                'priority' => '',
                'project' => '',
                'due_date' => '',
                'search' => '',
            ], $filters),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $this->taskService->createTask($request->user(), $validated);

        return back();
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();
        $this->taskService->updateTask($task, $validated);

        return back();
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Request $request, Task $task)
    {
        $this->authorize('toggle', $task);

        $this->taskService->toggleTask($task);

        return back();
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return back();
    }
}
