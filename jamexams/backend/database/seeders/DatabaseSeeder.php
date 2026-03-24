<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $viewerRole = Role::create(['name' => 'viewer', 'guard_name' => 'web']);

        // Create permissions
        $permissions = [
            'manage-users', 'activate-users', 'view-users',
            'manage-exams', 'upload-exams', 'view-exams', 'delete-exams',
            'manage-subjects', 'create-subjects', 'view-subjects',
            'send-notifications', 'view-reports', 'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole->givePermissionTo(Permission::all());
        $editorRole->givePermissionTo(['upload-exams', 'manage-exams', 'view-exams', 'create-subjects', 'view-subjects', 'view-users']);
        $viewerRole->givePermissionTo(['view-exams']);

        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@jamexams.com',
            'password' => Hash::make('Admin@123'),
            'is_active' => true,
            'activated_at' => now(),
            'expires_at' => now()->addYears(10),
        ]);
        $admin->assignRole('admin');

        // Create subjects
        $subjects = [
            ['name' => 'Physics', 'code' => 'PHY', 'description' => 'Physics past papers', 'color' => '#3B82F6'],
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Mathematics past papers', 'color' => '#10B981'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'description' => 'Chemistry past papers', 'color' => '#F59E0B'],
            ['name' => 'Biology', 'code' => 'BIO', 'description' => 'Biology past papers', 'color' => '#8B5CF6'],
            ['name' => 'English', 'code' => 'ENG', 'description' => 'English language papers', 'color' => '#EF4444'],
            ['name' => 'Kiswahili', 'code' => 'KIS', 'description' => 'Kiswahili papers', 'color' => '#F97316'],
            ['name' => 'History', 'code' => 'HIST', 'description' => 'History past papers', 'color' => '#6B7280'],
            ['name' => 'Geography', 'code' => 'GEO', 'description' => 'Geography papers', 'color' => '#14B8A6'],
            ['name' => 'Commerce', 'code' => 'COM', 'description' => 'Commerce papers', 'color' => '#EC4899'],
            ['name' => 'Book Keeping', 'code' => 'BK', 'description' => 'Book Keeping papers', 'color' => '#84CC16'],
            ['name' => 'Bible Knowledge', 'code' => 'BIK', 'description' => 'Bible Knowledge papers', 'color' => '#A78BFA'],
            ['name' => 'Business Studies', 'code' => 'BS', 'description' => 'Business Studies papers', 'color' => '#FB923C'],
            ['name' => 'Civics', 'code' => 'CIV', 'description' => 'Civics papers', 'color' => '#34D399'],
            ['name' => 'Agriculture', 'code' => 'AGR', 'description' => 'Agriculture papers', 'color' => '#4ADE80'],
        ];

        foreach ($subjects as $data) {
            Subject::create(array_merge($data, ['is_active' => true, 'created_by' => $admin->id]));
        }
    }
}
