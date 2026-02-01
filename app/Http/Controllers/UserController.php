<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->middleware('auth.api');
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = 1000;
        $skip = ($page - 1) * $limit;

        $result = $this->apiService->listUsers($skip, $limit);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['error']);
        }

        $users = $result['users'] ?? [];
        return view('user.users.index', compact('users', 'page'));
    }

    public function create()
    {
        $rolesResult = $this->apiService->getRoles();
        $roles = $rolesResult['success'] ? $rolesResult['roles'] : [];
        
        $companiesResult = $this->apiService->getCompanies();
        $companies = $companiesResult['success'] ? ($companiesResult['companies']['data'] ?? $companiesResult['companies']) : [];
        
        $supervisorsResult = $this->apiService->listUsers(0, 1000);
        $supervisors = $supervisorsResult['success'] ? $supervisorsResult['users'] : [];

        return view('user.users.create', compact('roles', 'companies', 'supervisors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string',
            'role_id' => 'required',
            'company_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return redirect()->route('users.index')->with('success', 'User created successfully (Mock)!');
    }

    public function show($id)
    {
        $result = $this->apiService->getUser($id);
        if (!$result['success']) {
            return redirect()->route('users.index')->with('error', $result['error']);
        }

        $user = $result['user'];
        return view('user.users.show', compact('user'));
    }

    public function edit($id)
    {
        $result = $this->apiService->getUser($id);
        if (!$result['success']) {
            return redirect()->route('users.index')->with('error', $result['error']);
        }

        $user = $result['user'];
        
        $rolesResult = $this->apiService->getRoles();
        $roles = $rolesResult['success'] ? $rolesResult['roles'] : [];
        
        $companiesResult = $this->apiService->getCompanies();
        $companies = $companiesResult['success'] ? ($companiesResult['companies']['data'] ?? $companiesResult['companies']) : [];
        
        $supervisorsResult = $this->apiService->listUsers(0, 1000);
        $supervisors = $supervisorsResult['success'] ? $supervisorsResult['users'] : [];

        return view('user.users.edit', compact('user', 'roles', 'companies', 'supervisors'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->back()->with('success', 'User updated successfully (Mock)!');
    }

    public function destroy($id)
    {
        return redirect()->route('users.index')->with('success', 'User deactivated successfully (Mock)!');
    }

    public function activate($id)
    {
        return redirect()->route('users.index')->with('success', 'User activated successfully (Mock)!');
    }

    public function showChangePasswordForm($id)
    {
        $result = $this->apiService->getUser($id);
        if (!$result['success']) {
            return redirect()->route('users.index')->with('error', $result['error']);
        }

        $user = $result['user'];
        return view('user.users.change-password', compact('user'));
    }

    public function changePassword(Request $request, $id)
    {
        return redirect()->route('users.show', $id)->with('success', 'Password changed successfully (Mock)!');
    }

    public function getRolesList()
    {
        $result = $this->apiService->getRoles();
        return response()->json($result['success'] ? $result['roles'] : []);
    }

    public function getCompaniesList()
    {
        $result = $this->apiService->getCompanies();
        return response()->json($result['success'] ? $result['companies'] : []);
    }

    public function getSupervisorsList()
    {
        $result = $this->apiService->listUsers(0, 1000);
        return response()->json($result['success'] ? $result['users'] : []);
    }
}