<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function index()
    {
        $roles = Role::all();
        return view('user.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('user.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' created successfully!");
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'success' => true,
            'role' => $role
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('user.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'Admin') {
            return redirect()->back()->with('error', 'The Admin role cannot be deleted.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }

    public function permissions()
    {
        $permissions = Permission::all();
        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }
}