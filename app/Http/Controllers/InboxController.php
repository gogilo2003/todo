<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display inbox with unorganized tasks.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'priority', 'search']);

        $tasks = $this->taskService->getInboxTasks($request->user(), $filters);
        $projects = $request->user()->projects()->orderBy('name')->get();

        return Inertia::render('Inbox/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'filters' => array_merge([
                'status' => '',
                'priority' => '',
                'search' => '',
            ], $filters),
        ]);
    }
}
