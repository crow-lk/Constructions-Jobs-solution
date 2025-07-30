<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create default admin user
        $adminRole = Role::where('name', 'Super Admin')->first();
        
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'role_id' => $adminRole ? $adminRole->id : null,
                'password' => bcrypt('password'),
            ]
        );

        // Create additional test users if they don't exist
        if (User::count() < 6) {
            User::factory(5)->create();
        }
    }
}
