<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ExternalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->apiService->login($request->username, $request->password);
    
        if ($result['success']) {
            $userResult = $this->apiService->getCurrentUser();
        
            if ($userResult['success']) {
                session(['user' => $userResult['user']]);
                return redirect('/user-dashboard')->with('success', 'Login successful!');
            }
        }

        return back()->withErrors(['username' => $result['error'] ?? 'Login failed'])->withInput();
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('user.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate username from email
        $username = $this->generateUsernameFromEmail($request->email);

        $userData = [
            'username'  => $username,
            'email'     => $request->email,
            'password'  => $request->password,
            'full_name' => $request->full_name,
            'position'  => $request->position,
            'phone'     => $request->phone,
            'is_active' => true,
            'role_id'   => $request->role_id ?: 'c03c4820-9d03-4a56-9b17-997f641dc3bc',
            'company_id' => $request->company_id ?: 'hassan',
        ];

        $result = $this->apiService->register($userData);
        if ($result['success']) {
            $loginResult = $this->apiService->login($username, $request->password);
            
            if ($loginResult['success']) {
                $userResult = $this->apiService->getCurrentUser();
                if ($userResult['success']) {
                    session(['user' => $userResult['user']]);
                    return redirect('/dashboard')->with('success', 'Registration successful! Welcome to Evalia.');
                }
            }

            return redirect()->route('login')->with('success', 'Registration successful! Please login.');
        }

        return back()->withErrors(['registration' => $result['error']])->withInput();
    }

    private function generateUsernameFromEmail($email)
    {
        // Extract the part before @
        $username = strtolower(explode('@', $email)[0]);
        
        // Remove special characters and replace with underscores
        $username = preg_replace('/[^a-z0-9]/', '_', $username);
        
        // Remove multiple consecutive underscores
        $username = preg_replace('/_+/', '_', $username);
        
        // Remove leading/trailing underscores
        $username = trim($username, '_');
        
        // Ensure username is at least 3 characters
        if (strlen($username) < 3) {
            $username = $username . '_user';
        }
        
        return $username;
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}