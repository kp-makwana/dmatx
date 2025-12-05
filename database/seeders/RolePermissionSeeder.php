<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $permissions = [
        'manage_users',
        'manage_roles',
        'manage_permissions',
        'view_dashboard',
      ];

      foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
      }

      // Create Roles
      $admin = Role::firstOrCreate(['name' => 'admin']);
      $user  = Role::firstOrCreate(['name' => 'user']);

      // Assign permissions to admin
      $admin->syncPermissions($permissions);
    }
}
