<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodayController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display today's tasks dashboard.
     */
    public function index(Request $request): Response
    {
        $dashboardData = $this->taskService->getTodayDashboard($request->user());
        $stats = $this->taskService->getStats($request->user());
        $projects = $request->user()->projects()->orderBy('name')->get();

        return Inertia::render('Today/Index', [
            'dueToday' => $dashboardData['dueToday'],
            'overdue' => $dashboardData['overdue'],
            'highPriority' => $dashboardData['highPriority'],
            'projects' => $projects,
            'stats' => $stats,
        ]);
    }
}
