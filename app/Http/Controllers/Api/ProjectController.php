<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreProjectRequest;
use App\Http\Requests\Api\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends BaseController
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->user_id, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($request->sort_by, function ($query, $sortBy) use ($request) {
                $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
                $query->orderBy($sortBy, $direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($request->per_page ?? 15);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return $this->createdResponse(
            new ProjectResource($project->load('user')),
            'Project created successfully'
        );
    }

    /**
     * Display the specified project
     */
    public function show(Project $project): JsonResponse
    {
        return $this->successResponse(
            new ProjectResource($project->load('user'))
        );
    }

    /**
     * Update the specified project
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return $this->updatedResponse(
            new ProjectResource($project->load('user')),
            'Project updated successfully'
        );
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return $this->deletedResponse('Project deleted successfully');
    }
} 