<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Include subcategories count if loaded
        if ($this->sub_categories_count !== null) {
            $data['sub_categories_count'] = $this->sub_categories_count;
        }

        // Include subcategories if loaded
        if ($this->relationLoaded('subCategories')) {
            $data['sub_categories'] = SubCategoryResource::collection($this->subCategories);
        }

        return $data;
    }
} 