<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = 'http://35.153.178.201:8080';
        $this->timeout =  30;
    }

    /**
     * Get current user token from session
     */
    private function getToken()
    {
        return session('user_access_token');
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        
        try {
            
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/auth/token', [
                    'username' => $email,
                    'password' => $password
                ]);

            if ($response->successful()) {
                $token = $response->json('access_token');
                
                session(['user_access_token' => $token]);
                
                return [
                    'success' => true,
                    'access_token' => $token,
                    'token_type' => $response->json('token_type')
                ];
            }

            $error = $this->parseError($response);
            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Get current user profile
     */
    public function getCurrentUser()
    {
        $token = $this->getToken();
        
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . '/auth/me');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'user' => $response->json()
                ];
            }

            // If token is invalid, clear session
            if ($response->status() === 401) {
                session()->forget('user_access_token');
                session()->forget('user');
            }

            return [
                'success' => false,
                'error' => 'Failed to get user profile'
            ];

        } catch (\Exception $e) {
            Log::error('Get user profile failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Register new user
     */
    public function register($userData)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/users', $userData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'user' => $response->json()
                ];
            }

            $error = $this->parseError($response);
            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * List users
     */
    public function listUsers($skip = 0, $limit = 100)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . '/users', [
                    'skip' => $skip,
                    'limit' => $limit
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'users' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch users'
            ];

        } catch (\Exception $e) {
            Log::error('List users failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Get user by ID
     */
    public function getUser($userId)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . "/users/{$userId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'user' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'User not found'
            ];

        } catch (\Exception $e) {
            Log::error('Get user failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Create new user
     */
    public function createUser($userData)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->post($this->baseUrl . '/users', $userData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'user' => $response->json()
                ];
            }

            $error = $this->parseError($response);
            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error('Create user failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Update user
     */
    public function updateUser($userId, $userData)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->put($this->baseUrl . "/users/{$userId}", $userData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'user' => $response->json()
                ];
            }

            $error = $this->parseError($response);
            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error('Update user failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Deactivate user
     */
    public function deactivateUser($userId)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->delete($this->baseUrl . "/users/{$userId}");

            if ($response->status() === 204) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'error' => 'Failed to deactivate user'
            ];

        } catch (\Exception $e) {
            Log::error('Deactivate user failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $newPassword)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->post($this->baseUrl . "/users/{$userId}/change-password", [
                    'new_password' => $newPassword
                ]);

            if ($response->successful()) {
                return ['success' => true];
            }

            $error = $this->parseError($response);
            return [
                'success' => false,
                'error' => $error
            ];

        } catch (\Exception $e) {
            Log::error('Change password failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Parse error response from API
     */
    private function parseError($response)
    {
        $errors = $response->json('detail');
        
        if (is_array($errors) && count($errors) > 0) {
            return $errors[0]['msg'] ?? 'Validation failed';
        }
        
        return $response->json('message', 'Request failed');
    }
    public function getRoles()
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . '/roles');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'roles' => $response->json()
                ];
            }
        
    }

    /**
     * Get all companies for dropdown
     */
    public function getCompanies()
    {

        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . '/list_of_companies');
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'companies' => $response->json()
                ];
            }
            return [
                'success' => false,
                'error' => 'Failed to fetch companies'
            ];

    }

    public function getAgentDashboardSummary()
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . '/agents/dashboard-summary');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch dashboard summary'
            ];

        } catch (\Exception $e) {
            Log::error('Get agent dashboard summary failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Get agents list
     */
    public function getAgentsList()
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . '/agents/list');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'agents' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch agents list'
            ];

        } catch (\Exception $e) {
            Log::error('Get agents list failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }

    /**
     * Get agent performance history
     */
    public function getAgentPerformanceHistory($agentId)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Not authenticated'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->get($this->baseUrl . "/agents/{$agentId}/performance-history");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch agent performance history'
            ];

        } catch (\Exception $e) {
            Log::error('Get agent performance history failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable'
            ];
        }
    }
}