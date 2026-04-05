<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function index(Request $request)
    {
        // Now it shows anyone who is NOT an agent (Admins and Supervisors)
        $users = User::where('user_type', '!=', User::TYPE_AGENT)->with('roles')->get()->map(function($user) {
            return $user->toSessionArray();
        })->toArray();
        return view('user.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $companies = Company::all();
        $type = $request->get('type', 'user');
        
        if ($type === 'agent') {
            // No roles needed for agent creation UI as it's auto-assigned
            $roles = collect([]); 
            $supervisors = User::where('user_type', User::TYPE_AGENT)->get()->map->toSessionArray();
        } else {
            // All roles are available for staff creation
            $roles = Role::all();
            $supervisors = collect([]);
        }

        return view('user.users.create', compact('roles', 'companies', 'supervisors', 'type'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string',
            'role_id' => $request->type === 'agent' ? 'nullable' : 'required',
            'company_id' => 'nullable',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'supervisor_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userType = User::TYPE_STAFF;
        if ($request->type === 'agent') {
            $userType = User::TYPE_AGENT;
        } else if ($request->role_id) {
            $role = Role::findById($request->role_id);
            if ($role) {
                if (strtolower($role->name) === 'admin') $userType = User::TYPE_ADMIN;
                else if (strtolower($role->name) === 'supervisor') $userType = User::TYPE_SUPERVISOR;
            }
        }

        try {
            $user = User::create([
                'name' => $request->full_name,
                'username' => $request->username,
                'email' => $request->email,
                'user_type' => $userType,
                'password' => Hash::make($request->password),
                'position' => $request->position,
                'phone' => $request->phone,
                'is_active' => true,
                'company_id' => $request->company_id,
                'supervisor_id' => $request->supervisor_id,
            ]);

            if ($request->type === 'agent') {
                if (!$request->supervisor_id) {
                    $user->update(['supervisor_id' => $user->id]);
                }
            } elseif ($request->role_id) {
                $role = Role::findById($request->role_id);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }

            return redirect()->route($request->type === 'agent' ? 'user.agents.index' : 'users.index')
                             ->with('success', ($request->type === 'agent' ? 'Agent' : 'User') . ' created successfully!');
        } catch (\Exception $e) {
            Log::error('User Creation Error: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to create ' . ($request->type === 'agent' ? 'agent' : 'user') . '. ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        // Normalize for the view which expects an array or specific keys
        $userData = $user->toSessionArray();
        return view('user.users.show', ['user' => $userData]);
    }

    public function edit($id)
    {
        $userModel = User::with('roles')->findOrFail($id);
        $user = $userModel->toSessionArray();
        $companies = Company::all();
        
        $type = $userModel->user_type;
        
        if ($type === User::TYPE_AGENT) {
            $roles = collect([]); 
            $supervisors = User::where('user_type', User::TYPE_AGENT)->get()->map->toSessionArray();
        } else {
            $roles = Role::all();
            $supervisors = collect([]);
        }

        return view('user.users.edit', compact('user', 'roles', 'companies', 'supervisors', 'type'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => $request->type === 'agent' ? 'nullable' : 'required',
            'company_id' => 'nullable',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'supervisor_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userType = User::TYPE_STAFF;
        if ($request->type === 'agent') {
            $userType = User::TYPE_AGENT;
        } else if ($request->role_id) {
            $role = Role::findById($request->role_id);
            if ($role) {
                if (strtolower($role->name) === 'admin') $userType = User::TYPE_ADMIN;
                else if (strtolower($role->name) === 'supervisor') $userType = User::TYPE_SUPERVISOR;
            }
        }

        try {
            $updateData = [
                'name' => $request->full_name,
                'email' => $request->email,
                'user_type' => $userType,
                'position' => $request->position,
                'phone' => $request->phone,
                'company_id' => $request->company_id,
                'supervisor_id' => $request->supervisor_id,
            ];
            
            if ($request->type === 'agent' && !$request->supervisor_id) {
                $updateData['supervisor_id'] = $user->id;
            }

            // Handle password update if provided
            if ($request->filled('new_password')) {
                $updateData['password'] = Hash::make($request->new_password);
            }

            $user->update($updateData);

            if ($request->type === 'agent') {
                // No role required for agents, we use user_type
            } elseif ($request->role_id) {
                $role = Role::findById($request->role_id);
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
            }

            return redirect()->route($request->type === 'agent' ? 'user.agents.index' : 'users.index')
                             ->with('success', ($request->type === 'agent' ? 'Agent' : 'User') . ' updated successfully!');
        } catch (\Exception $e) {
            Log::error('User Update Error: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to update ' . ($request->type === 'agent' ? 'agent' : 'user') . '. ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);
        return redirect()->route('users.index')->with('success', 'User deactivated successfully!');
    }

    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);
        return redirect()->route('users.index')->with('success', 'User activated successfully!');
    }

    public function showChangePasswordForm($id)
    {
        $user = User::findOrFail($id);
        return view('user.users.change-password', compact('user'));
    }

    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('users.show', $id)->with('success', 'Password changed successfully!');
    }

    public function getRolesList()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function getCompaniesList()
    {
        $companies = Company::all();
        return response()->json($companies);
    }

    public function getSupervisorsList()
    {
        $supervisorRole = Role::where('name', 'Supervisor')->first();
        $supervisors = $supervisorRole ? User::role('Supervisor')->get() : collect([]);
        return response()->json($supervisors);
    }
}
