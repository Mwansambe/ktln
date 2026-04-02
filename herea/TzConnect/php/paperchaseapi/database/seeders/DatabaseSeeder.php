<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@paperchase.local',
            'password' => Hash::make('admin123'),
            'role' => 'ADMIN',
        ]);

        // Create Editor User
        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@paperchase.local',
            'password' => Hash::make('editor123'),
            'role' => 'EDITOR',
        ]);

        // Create Regular User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@paperchase.local',
            'password' => Hash::make('user123'),
            'role' => 'VIEWER',
        ]);

        // Create additional test users (optional)
        // User::factory(5)->create();
    }
}
