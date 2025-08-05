<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->sort_by, function ($query, $sortBy) use ($request) {
                $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
                $query->orderBy($sortBy, $direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($request->per_page ?? 15);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Handle file upload if present
        if ($request->hasFile('business_registration_document')) {
            $file = $request->file('business_registration_document');
            $filename = 'business_registration_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('business-registration-documents', $filename, 'public');
            $data['business_registration_document'] = $path;
        }
        
        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        
        // Handle file upload if present
        if ($request->hasFile('business_registration_document')) {
            // Delete old file if exists
            if ($user->business_registration_document && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->business_registration_document)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->business_registration_document);
            }
            
            $file = $request->file('business_registration_document');
            $filename = 'business_registration_' . $user->id . '_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('business-registration-documents', $filename, 'public');
            $data['business_registration_document'] = $path;
        }
        
        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): JsonResponse
    {
        // Delete associated business registration document if exists
        if ($user->business_registration_document && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->business_registration_document)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->business_registration_document);
        }
        
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
} 