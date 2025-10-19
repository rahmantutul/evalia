<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display listing of users
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = 100;
        $skip = ($page - 1) * $limit;

        $result = $this->apiService->listUsers($skip, $limit);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['error']);
        }

        $users = $result['users'] ?? [];
        
        return view('user.users.index', compact('users', 'page'));
    }

    /**
     * Show user creation form
     */
    public function create()
    {
        // Get roles
        $rolesResult = $this->apiService->getRoles();
        $roles = $rolesResult['success'] ? $rolesResult['roles'] : [];
        
        // Get companies
        $companiesResult = $this->apiService->getCompanies();
        
        $companies = $companiesResult['success'] ? $companiesResult['companies']['data'] : [];
        
        // Get supervisors
        $supervisorsResult = $this->apiService->listUsers(0, 1000);
        $supervisors = $supervisorsResult['success'] ? $supervisorsResult['users'] : [];

        return view('user.users.create', compact('roles', 'companies', 'supervisors'));
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string',
            'role_id' => 'required|uuid',
            'company_id' => 'nullable|string',
            'supervisor_id' => 'nullable|uuid',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate username from email
        $username = $this->generateUsernameFromEmail($request->email);
        
        $userData = [
            'username' => $username,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
            'full_name' => $request->full_name,
            'position' => $request->position,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
            'supervisor_id' => $request->supervisor_id,
            'is_active' => true,
        ];

        // Remove null values
        $userData = array_filter($userData, function($value) {
            return $value !== null && $value !== '';
        });

        $result = $this->apiService->createUser($userData);

        if ($result['success']) {
            return redirect()->route('users.index')
                ->with('success', 'User created successfully!');
        }

        return redirect()->back()
            ->with('error', $result['error'] ?? 'An error occurred while creating the user.')
            ->withInput();
    }

    private function generateUsernameFromEmail($email)
    {
        $username = strtolower(explode('@', $email)[0]);
        $username = preg_replace('/[^a-z0-9]/', '_', $username);
        $username = preg_replace('/_+/', '_', $username);
        $username = trim($username, '_');
        
        if (strlen($username) < 3) {
            $username .= '_user';
        }
        
        return $username;
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        $result = $this->apiService->getUser($id);

        if (!$result['success']) {
            return redirect()->route('users.index')
                ->with('error', $result['error']);
        }

        $user = $result['user'];
        return view('user.users.show', compact('user'));
    }

    /**
     * Show user edit form
     */
    public function edit($id)
    {
        $result = $this->apiService->getUser($id);

        if (!$result['success']) {
            return redirect()->route('users.index')
                ->with('error', $result['error']);
        }

        $user = $result['user'];
        
        // Get roles
        $rolesResult = $this->apiService->getRoles();
        $roles = $rolesResult['success'] ? $rolesResult['roles'] : [];
        
        // Get companies
        $companiesResult = $this->apiService->getCompanies();
        $companies = $companiesResult['success'] ? $companiesResult['companies']['data'] : [];
        
        // Get supervisors
        $supervisorsResult = $this->apiService->listUsers(0, 1000);
        $supervisors = $supervisorsResult['success'] ? $supervisorsResult['users'] : [];

        return view('user.users.edit', compact('user', 'roles', 'companies', 'supervisors'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'full_name' => 'required|string',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'role_id' => 'required|uuid',
            'company_id' => 'nullable|string',
            'supervisor_id' => 'nullable|uuid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->only([
            'email', 'full_name', 'position', 'phone',
            'role_id', 'company_id', 'supervisor_id'
        ]);

        // Add is_active status
        $userData['is_active'] = true;

        // Remove null values
        $userData = array_filter($userData, function($value) {
            return $value !== null && $value !== '';
        });

        $result = $this->apiService->updateUser($id, $userData);

        if ($result['success']) {
            return redirect()->route('users.show', $id)
                ->with('success', 'User updated successfully!');
        }

        return redirect()->back()
            ->with('error', $result['error'])
            ->withInput();
    }

    /**
     * Deactivate user
     */
    public function destroy($id)
    {
        $result = $this->apiService->deactivateUser($id);

        if ($result['success']) {
            return redirect()->route('users.index')
                ->with('success', 'User deactivated successfully!');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Activate user
     */
    public function activate($id)
    {
        $userData = ['is_active' => true];
        $result = $this->apiService->updateUser($id, $userData);

        if ($result['success']) {
            return redirect()->route('users.index')
                ->with('success', 'User activated successfully!');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Show change password form
     */
    public function showChangePasswordForm($id)
    {
        $result = $this->apiService->getUser($id);
        
        if (!$result['success']) {
            return redirect()->route('users.index')
                ->with('error', $result['error']);
        }

        $user = $result['user'];
        return view('user.users.change-password', compact('user'));
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $result = $this->apiService->changePassword($id, $request->new_password);

        if ($result['success']) {
            return redirect()->route('users.show', $id)
                ->with('success', 'Password changed successfully!');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    /**
     * Get roles from API (for AJAX calls)
     */
    public function getRolesList()
    {
        $result = $this->apiService->getRoles();
        
        if ($result['success']) {
            return response()->json($result['roles']);
        }
        
        return response()->json([], 500);
    }

    /**
     * Get companies from API (for AJAX calls)
     */
    public function getCompaniesList()
    {
        $result = $this->apiService->getCompanies();
        
        if ($result['success']) {
            return response()->json($result['companies']);
        }
        
        return response()->json([], 500);
    }

    /**
     * Get supervisors from API (for AJAX calls)
     */
    public function getSupervisorsList()
    {
        $result = $this->apiService->listUsers(0, 1000);
        
        if ($result['success']) {
            return response()->json($result['users']);
        }
        
        return response()->json([], 500);
    }
}