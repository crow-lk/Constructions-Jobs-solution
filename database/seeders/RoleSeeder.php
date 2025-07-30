<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'permissions' => [
                    'create-users',
                    'edit-users',
                    'delete-users',
                    'view-users',
                    'create-roles',
                    'edit-roles',
                    'delete-roles',
                    'view-roles',
                    'manage-system',
                ],
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access with most permissions',
                'permissions' => [
                    'create-users',
                    'edit-users',
                    'view-users',
                    'create-roles',
                    'edit-roles',
                    'view-roles',
                ],
            ],
            [
                'name' => 'Manager',
                'description' => 'User management and team oversight',
                'permissions' => [
                    'view-users',
                    'manage-team',
                ],
            ],
            [
                'name' => 'Client',
                'description' => 'Basic access for clients',
                'permissions' => [
                    'view-own-profile',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
