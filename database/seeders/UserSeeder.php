<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * 9 fake users — all share the same password: "password"
     *
     * Roles: 1 Admin · 2 Supervisors · 6 Agents
     */
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'companies.view',
            'companies.create',
            'tasks.view',
            'agents.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        $supervisorRole = Role::firstOrCreate(['name' => 'Accounts']);
        $supervisorRole->syncPermissions([
            'dashboard.view',
            'users.view',
            'companies.view',
            'tasks.view',
            'agents.view',
        ]);

        $agentRole = Role::firstOrCreate(['name' => 'HR']);
        $agentRole->syncPermissions([
            'dashboard.view',
            'tasks.view',
        ]);

        $password = Hash::make('password');

        $users = [
            // ── Admin ──────────────────────────────────────────────────────
            [
                'name'      => 'Ahmad Hassan',
                'username'  => 'admin',
                'email'     => 'admin@evalia.com',
                'password'  => $password,
                'position'  => 'System Administrator',
                'phone'     => '+962 79 123 4567',
                'role_name' => 'Admin',
                'user_type' => User::TYPE_ADMIN,
                'company_id' => 1,
            ],

            // ── Supervisors (Accounts) ────────────────────────────────────────────────
            [
                'name'      => 'Sara Nasser',
                'username'  => 'supervisor1',
                'email'     => 'sara.nasser@evalia.com',
                'password'  => $password,
                'position'  => 'Senior Supervisor',
                'phone'     => '+962 79 234 5678',
                'role_name' => 'Accounts',
                'user_type' => User::TYPE_SUPERVISOR,
                'company_id' => 1,
            ],
            [
                'name'      => 'Mahmoud Ali',
                'username'  => 'supervisor2',
                'email'     => 'mahmoud.ali@evalia.com',
                'password'  => $password,
                'position'  => 'Team Supervisor',
                'phone'     => '+962 79 345 6789',
                'role_name' => 'Accounts',
                'user_type' => User::TYPE_SUPERVISOR,
                'company_id' => 2,
            ],

            // ── Agents (HR) ─────────────────────────────────────────────────────
            [
                'name'      => 'Nour Haddad',
                'username'  => 'agent1',
                'email'     => 'nour.haddad@evalia.com',
                'password'  => $password,
                'position'  => 'Customer Support Agent',
                'phone'     => '+962 79 456 7890',
                'role_name' => 'HR',
                'user_type' => User::TYPE_AGENT,
                'company_id' => 1,
            ],
            [
                'name'      => 'Omar Al-Khouri',
                'username'  => 'agent2',
                'email'     => 'omar.khouri@evalia.com',
                'password'  => $password,
                'position'  => 'Customer Support Agent',
                'phone'     => '+962 79 567 8901',
                'role_name' => 'HR',
                'user_type' => User::TYPE_AGENT,
                'company_id' => 1,
            ],
            [
                'name'      => 'Layla Sayegh',
                'username'  => 'agent3',
                'email'     => 'layla.sayegh@evalia.com',
                'password'  => $password,
                'position'  => 'Quality Agent',
                'phone'     => '+962 79 678 9012',
                'role_name' => 'HR',
                'user_type' => User::TYPE_AGENT,
                'company_id' => 2,
            ],
            [
                'name'      => 'Fadi Jaber',
                'username'  => 'agent4',
                'email'     => 'fadi.jaber@evalia.com',
                'password'  => $password,
                'position'  => 'Customer Support Agent',
                'phone'     => '+962 79 789 0123',
                'role_name' => 'HR',
                'user_type' => User::TYPE_AGENT,
                'company_id' => 2,
            ],
            [
                'name'      => 'Rania Hamdan',
                'username'  => 'agent5',
                'email'     => 'rania.hamdan@evalia.com',
                'password'  => $password,
                'position'  => 'Customer Support Agent',
                'phone'     => '+962 79 890 1234',
                'role_name' => 'HR',
                'user_type' => User::TYPE_AGENT,
                'company_id' => 3,
            ],
            [
                'name'      => 'Dana Hijazi',
                'username'  => 'agent6',
                'email'     => 'dana.hijazi@evalia.com',
                'password'  => $password,
                'position'  => 'Quality Agent',
                'phone'     => '+962 79 901 2345',
                'role_name' => 'HR',
                'user_type' => User::TYPE_AGENT,
                'company_id' => 3,
            ],
        ];

        foreach ($users as $data) {
            $roleName = $data['role_name'];
            unset($data['role_name']);

            $user = User::updateOrCreate(
                ['username' => $data['username']],
                $data
            );

            if ($roleName) {
                $user->assignRole($roleName);
            }
        }

        $this->command->info('✅ 9 fake users and dynamic roles seeded.');
    }
}
