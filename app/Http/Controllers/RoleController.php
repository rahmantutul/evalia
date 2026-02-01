<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->middleware('auth.api');
        $this->apiService = $apiService;
    }

    public function index()
    {
        $roles = [
            ['id' => 'ecedd3ec-6b66-45e1-9c1b-6cc3ee772762', 'name' => 'Admin', 'description' => 'System administrator with full access.'],
            ['id' => 'role-2', 'name' => 'Manager', 'description' => 'Can manage company data and agents.'],
            ['id' => 'role-3', 'name' => 'Agent', 'description' => 'Dedicated agent role for task execution.']
        ];
        
        return view('user.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        return redirect()->route('roles.index')->with('success', "Role created successfully (Mock)!");
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'role' => [
                'id' => $id,
                'name' => 'Sample Role',
                'description' => 'Sample description',
                'permissions' => ['dashboard.view', 'users.view']
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('roles.index')->with('success', 'Role updated successfully (Mock)!');
    }

    public function destroy($id)
    {
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully (Mock)!');
    }

    public function permissions()
    {
        return response()->json([
            'success' => true,
            'permissions' => [
                ['id' => 'p1', 'name' => 'dashboard.view'],
                ['id' => 'p2', 'name' => 'users.view'],
                ['id' => 'p3', 'name' => 'users.create'],
                ['id' => 'p4', 'name' => 'companies.view']
            ]
        ]);
    }
}