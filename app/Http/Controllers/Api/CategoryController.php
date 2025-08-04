<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Filter by status if provided
            if ($request->has('status')) {
                $status = $request->get('status');
                if (in_array($status, ['active', 'inactive'])) {
                    $query->where('status', $status);
                }
            }

            // Search by name if provided
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%");
            }

            // Include subcategories count if requested
            if ($request->boolean('with_subcategories_count')) {
                $query->withCount('subCategories');
            }

            // Include subcategories if requested
            if ($request->boolean('with_subcategories')) {
                $query->with(['subCategories' => function ($query) {
                    $query->where('status', 'active');
                }]);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $categories = $query->orderBy('name')->paginate($perPage);

            return $this->successResponse(
                CategoryResource::collection($categories),
                'Categories retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve categories: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Request $request, Category $category): JsonResponse
    {
        try {
            // Include subcategories if requested
            if ($request->boolean('with_subcategories')) {
                $category->load(['subCategories' => function ($query) {
                    $query->where('status', 'active');
                }]);
            }

            // Include subcategories count
            $category->loadCount('subCategories');

            return $this->successResponse(
                new CategoryResource($category),
                'Category retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve category: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get only active categories.
     */
    public function active(): JsonResponse
    {
        try {
            $categories = Category::active()
                ->withCount('subCategories')
                ->orderBy('name')
                ->get();

            return $this->successResponse(
                CategoryResource::collection($categories),
                'Active categories retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve active categories: ' . $e->getMessage(),
                500
            );
        }
    }
} 