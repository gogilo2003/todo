<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    /**
     * Display a listing of the user's projects.
     */
    public function index(Request $request): Response
    {
        $projects = $this->projectService->getAllProjects($request->user());

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();
        $this->projectService->createProject($request->user(), $validated);

        return to_route('projects.index');
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validated();
        $this->projectService->updateProject($project, $validated);

        return to_route('projects.index');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Request $request, Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->deleteProject($project);

        return to_route('projects.index');
    }
}
