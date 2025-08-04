<?php

namespace App\Http\Controllers\Api;

use App\Models\SubCategory;
use App\Models\Category;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubCategoryController extends BaseController
{
    /**
     * Display a listing of subcategories.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SubCategory::with('category');

            // Filter by status if provided
            if ($request->has('status')) {
                $status = $request->get('status');
                if (in_array($status, ['active', 'inactive'])) {
                    $query->where('status', $status);
                }
            }

            // Filter by category if provided
            if ($request->has('category_id')) {
                $categoryId = $request->get('category_id');
                $query->byCategory($categoryId);
            }

            // Search by name if provided
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%");
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $subCategories = $query->orderBy('name')->paginate($perPage);

            return $this->successResponse(
                SubCategoryResource::collection($subCategories),
                'Subcategories retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve subcategories: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified subcategory.
     */
    public function show(SubCategory $subCategory): JsonResponse
    {
        try {
            $subCategory->load('category');

            return $this->successResponse(
                new SubCategoryResource($subCategory),
                'Subcategory retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve subcategory: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get subcategories by category.
     */
    public function byCategory(Request $request, Category $category): JsonResponse
    {
        try {
            $query = $category->subCategories();

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

            // Pagination
            $perPage = $request->get('per_page', 15);
            $subCategories = $query->orderBy('name')->paginate($perPage);

            return $this->successResponse(
                SubCategoryResource::collection($subCategories),
                "Subcategories for category '{$category->name}' retrieved successfully"
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve subcategories: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get only active subcategories.
     */
    public function active(): JsonResponse
    {
        try {
            $subCategories = SubCategory::active()
                ->with('category')
                ->orderBy('name')
                ->get();

            return $this->successResponse(
                SubCategoryResource::collection($subCategories),
                'Active subcategories retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve active subcategories: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get active subcategories by category.
     */
    public function activeByCategory(Category $category): JsonResponse
    {
        try {
            $subCategories = $category->subCategories()
                ->active()
                ->orderBy('name')
                ->get();

            return $this->successResponse(
                SubCategoryResource::collection($subCategories),
                "Active subcategories for category '{$category->name}' retrieved successfully"
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve active subcategories: ' . $e->getMessage(),
                500
            );
        }
    }
} 