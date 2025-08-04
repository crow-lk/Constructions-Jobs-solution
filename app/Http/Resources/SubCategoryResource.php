<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'category_id' => $this->category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Include category information if loaded
        if ($this->relationLoaded('category')) {
            $data['category'] = [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'status' => $this->category->status,
            ];
        }

        // Include category name attribute if available
        if (isset($this->category_name)) {
            $data['category_name'] = $this->category_name;
        }

        return $data;
    }
} 