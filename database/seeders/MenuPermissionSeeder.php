<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class MenuPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear caches
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // TRUNCATE to avoid the weird "roles.viewreate" corruption I saw earlier
        // Turning off foreign key checks for a clean wipe
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Companies
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',

            // Agents
            'agents.view',
            'agents.create',
            'agents.edit',
            'agents.delete',

            // Knowledgebase (ensure singular/consistent naming)
            'knowledgebase.view',
            'knowledgebase.create',
            'knowledgebase.edit',
            'knowledgebase.delete',

            // Tasks
            'tasks.view',
            'tasks.create',
            'tasks.upload',
            'tasks.evaluate',
            'tasks.delete',

            // Reports
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        // Re-assign to Admin
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
        }

        $this->command->info('✅ Permissions completely reset and re-seeded.');
    }
}
