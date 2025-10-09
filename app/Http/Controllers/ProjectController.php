<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::withCount('paymentRequests')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection($projects),
            'can' => [
                'create' => auth()->user()->can('create', Project::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        return Inertia::render('Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $project = Project::create($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load('paymentRequests.user');

        return Inertia::render('Projects/Show', [
            'project' => new ProjectResource($project),
            'payment_requests' => $project->paymentRequests,
            'can' => [
                'update' => auth()->user()->can('update', $project),
                'delete' => auth()->user()->can('delete', $project),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // Check if project has payment requests
        if ($project->paymentRequests()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Không thể xóa dự án có phiếu đề xuất');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Dự án đã được xóa thành công');
    }
}
