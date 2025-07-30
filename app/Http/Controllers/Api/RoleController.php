<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request): JsonResponse
    {
        $roles = Role::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->sort_by, function ($query, $sortBy) use ($request) {
                $direction = $request->sort_direction === 'desc' ? 'desc' : 'asc';
                $query->orderBy($sortBy, $direction);
            }, function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->get();

        return response()->json([
            'roles' => $roles,
        ]);
    }

    /**
     * Display the specified role
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'role' => $role,
        ]);
    }
} 