<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userManagement = Permission::firstOrCreate(
            ['name' => 'user-management', 'guard_name' => 'web']
        );

        $dataImport = Permission::firstOrCreate(
            ['name' => 'data-import', 'guard_name' => 'web']
        );

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $adminRole->syncPermissions([$userManagement, $dataImport]);

        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }

        $testUser = User::where('email', 'user@example.com')->first();
        if ($testUser) {
            $testUser->assignRole($userRole);
        }
    }
}
