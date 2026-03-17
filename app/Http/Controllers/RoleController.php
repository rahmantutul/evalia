<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('user.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' created successfully!");
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'success' => true,
            'role'    => $role,
        ]);
    }

    public function edit($id)
    {
        $role            = Role::findOrFail($id);
        $groupedPerms    = $this->groupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('user.roles.edit', compact('role', 'groupedPerms', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' updated successfully!");
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Admin') {
            return redirect()->back()->with('error', 'The Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // JSON endpoint — all permissions grouped by category
    // ─────────────────────────────────────────────────────────────────────────

    public function permissions()
    {
        return response()->json([
            'success'  => true,
            'permissions' => Permission::all(),
            'grouped'  => $this->groupedPermissions(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper — group permissions by their first segment (before the dot)
    // ─────────────────────────────────────────────────────────────────────────

    public function groupedPermissions(): array
    {
        $categoryLabels = [
            'dashboard'     => ['label' => 'Dashboard',          'icon' => 'fas fa-tachometer-alt', 'color' => 'primary'],
            'roles'         => ['label' => 'Roles & Permissions', 'icon' => 'fas fa-lock',           'color' => 'danger'],
            'users'         => ['label' => 'Users',              'icon' => 'fas fa-users',           'color' => 'info'],
            'companies'     => ['label' => 'Companies',          'icon' => 'icofont-bank-alt',        'color' => 'warning'],
            'agents'        => ['label' => 'Agents',             'icon' => 'fas fa-headset',          'color' => 'success'],
            'knowledgebase' => ['label' => 'Knowledge Base',     'icon' => 'fas fa-brain',            'color' => 'primary'],
            'tasks'         => ['label' => 'Tasks / Calls',      'icon' => 'fas fa-phone-alt',        'color' => 'secondary'],
            'reports'       => ['label' => 'Reports',            'icon' => 'fas fa-chart-bar',        'color' => 'dark'],
        ];

        $grouped = [];

        foreach (Permission::all() as $permission) {
            $category = explode('.', $permission->name)[0];
            $meta     = $categoryLabels[$category] ?? ['label' => ucfirst($category), 'icon' => 'fas fa-key', 'color' => 'secondary'];

            if (!isset($grouped[$category])) {
                $grouped[$category] = array_merge($meta, ['permissions' => []]);
            }

            $grouped[$category]['permissions'][] = $permission;
        }

        return $grouped;
    }
}