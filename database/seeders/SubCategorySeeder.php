<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the categories
        $consultingCategory = Category::where('name', 'Consulting')->first();
        $contractorsCategory = Category::where('name', 'Contractors')->first();

        if (!$consultingCategory || !$contractorsCategory) {
            $this->command->error('Categories not found. Please run CategorySeeder first.');
            return;
        }

        $subCategories = [
            // Consulting subcategories
            [
                'name' => 'Chartered Architect',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Project Architect',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Structural Engineer',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Draft man',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Quantity Surveyor',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => '3D modeler',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Site Engineer',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Technical Officer',
                'category_id' => $consultingCategory->id,
                'status' => 'active',
            ],
            
            // Contractors subcategories
            [
                'name' => 'Main Contractor',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Sub Contractor',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Service Engineer',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Plumbing Contractor',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Electrical Contractor',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Metal work company',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'Aluminum work company',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
            [
                'name' => 'AC Company',
                'category_id' => $contractorsCategory->id,
                'status' => 'active',
            ],
        ];

        foreach ($subCategories as $subCategory) {
            SubCategory::updateOrCreate(
                [
                    'name' => $subCategory['name'],
                    'category_id' => $subCategory['category_id']
                ],
                $subCategory
            );
        }

        $this->command->info('SubCategories seeded successfully!');
    }
}
