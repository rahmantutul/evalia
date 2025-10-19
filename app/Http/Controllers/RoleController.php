<?php

namespace App\Http\Controllers;

use App\Services\ExternalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    protected $apiService;
    protected $baseUrl;
    protected $timeout;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
        
        // Make sure this is correct
        $this->baseUrl = 'http://35.153.178.201:8080'; // Direct assignment
        $this->timeout = 30;
    }

    /**
     * Get authentication token with validation
     */
    private function getToken()
    {
        $token = session('user_access_token');
        
        if (!$token || empty(trim($token))) {
            Log::warning('No valid authentication token found in session');
            return null;
        }
        
        return trim($token);
    }

    /**
     * Check if user is authenticated and redirect if not
     */
    private function checkAuthentication()
    {
        if (!$this->getToken()) {
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please login again.')
                ->with('session_expired', true);
        }
        return null;
    }

    /**
     * Make API request with comprehensive error handling
     */
    private function makeRequest($method, $endpoint, $data = [])
    {
        $token = $this->getToken();
        if (!$token) {
            return [
                'success' => false, 
                'error' => 'Authentication required. Please login again.',
                'status' => 401,
                'redirect_login' => true
            ];
        }

        // FIX: Convert UUID to string
        $requestId = (string) Str::uuid();
        $startTime = microtime(true);

        try {
            Log::debug('API Request Initiated', [
                'request_id' => $requestId,
                'method' => strtoupper($method),
                'endpoint' => $endpoint,
                'base_url' => $this->baseUrl,
                'full_url' => $this->baseUrl . $endpoint,
                'data_keys' => array_keys($data)
            ]);

            $httpClient = Http::timeout($this->timeout)
                ->withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Request-ID' => $requestId // Now it's a string
                ]);

            $response = $httpClient->$method($this->baseUrl . $endpoint, $data);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::debug('API Response Received', [
                'request_id' => $requestId,
                'status' => $response->status(),
                'response_time_ms' => $responseTime,
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                    'request_id' => $requestId
                ];
            }

            // Handle HTTP errors
            return $this->handleHttpError($response, $requestId);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return $this->handleException($e, 'Connection', $requestId, 503);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return $this->handleException($e, 'Request', $requestId, 500);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Unexpected', $requestId, 500);
        }
    }

    /**
     * Handle HTTP error responses
     */
    private function handleHttpError($response, $requestId)
    {
        $status = $response->status();
        $body = $response->json();

        $errorData = [
            'success' => false,
            'status' => $status,
            'request_id' => $requestId
        ];

        // Handle authentication errors
        if ($status === 401) {
            $errorData['error'] = 'Your session has expired. Please login again.';
            $errorData['redirect_login'] = true;
            return $errorData;
        }

        if ($status === 403) {
            $errorData['error'] = 'You do not have permission to perform this action.';
            return $errorData;
        }

        // Handle validation errors
        if ($status === 422 && isset($body['detail'])) {
            $errors = collect($body['detail'])
                ->map(fn($error) => $error['msg'] ?? 'Validation error')
                ->implode(' ');
            $errorData['error'] = $errors ?: 'Please check your input data.';
            return $errorData;
        }

        // Handle other HTTP errors
        $errorMessages = [
            400 => 'Bad request. Please check your input.',
            404 => 'The requested resource was not found.',
            409 => 'A conflict occurred. The resource may already exist.',
            500 => 'Internal server error. Please try again later.',
            503 => 'Service temporarily unavailable.',
        ];

        $errorData['error'] = $errorMessages[$status] 
            ?? $body['message'] 
            ?? $body['error'] 
            ?? 'Request failed with status ' . $status;

        return $errorData;
    }

    /**
     * Handle exceptions
     */
    private function handleException($e, $type, $requestId, $status)
    {
        Log::error("API {$type} Exception", [
            'request_id' => $requestId, // This is now a string
            'error' => $e->getMessage(),
            'exception_type' => get_class($e)
        ]);

        $errorMessages = [
            'Connection' => 'Unable to connect to the server. Please check your internet connection.',
            'Request' => 'Request failed. Please try again.',
            'Unexpected' => 'An unexpected error occurred. Please try again later.'
        ];

        return [
            'success' => false,
            'error' => $errorMessages[$type] ?? 'An error occurred.',
            'status' => $status,
            'request_id' => $requestId // String, not object
        ];
    }

    /**
     * Display listing of roles
     */
    public function index()
    {
        // Check authentication
        if ($redirect = $this->checkAuthentication()) {
            return $redirect;
        }

        $result = $this->makeRequest('get', '/roles');

        if (!$result['success']) {
            if (isset($result['redirect_login']) && $result['redirect_login']) {
                return redirect()->route('login')
                    ->with('error', $result['error'])
                    ->with('session_expired', true);
            }

            return view('user.roles.index', [
                'roles' => [],
                'error' => $result['error'],
                'request_id' => $result['request_id'] ?? null
            ]);
        }

        $roles = $result['data'] ?? [];
        return view('user.roles.index', compact('roles'));
    }

    /**
     * Store a new role
     */
    public function store(Request $request)
    {
        // Check authentication
        if ($redirect = $this->checkAuthentication()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'description' => 'nullable|string|max:500',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'string'
        ], [
            'name.required' => 'Role name is required.',
            'name.min' => 'Role name must be at least 2 characters.',
            'name.max' => 'Role name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('roles.index')
                ->withErrors($validator)
                ->withInput()
                ->with('open_create_modal', true);
        }

        $roleData = [
            'name' => trim($request->name),
            'description' => $request->description ? trim($request->description) : null
        ];

        // Create the role
        $result = $this->makeRequest('post', '/roles', $roleData);

        if (!$result['success']) {
            $errorData = [
                'error' => $result['error'],
                'open_create_modal' => true
            ];

            if (isset($result['request_id'])) {
                $errorData['request_id'] = $result['request_id'];
            }

            return redirect()->route('roles.index')
                ->with($errorData)
                ->withInput();
        }

        $roleId = $result['data']['id'] ?? null;
        
        if (!$roleId) {
            return redirect()->route('roles.index')
                ->with('warning', 'Role created but ID not returned. Please refresh the page.')
                ->with('highlight_role', $roleId);
        }

        // Assign permissions if provided
        if ($request->filled('permission_ids') && is_array($request->permission_ids)) {
            $validPermissions = array_filter($request->permission_ids);
            
            if (!empty($validPermissions)) {
                $permissionData = ['permission_ids' => array_values($validPermissions)];
                $permissionResult = $this->makeRequest('put', "/roles/{$roleId}", $permissionData);
                
                if (!$permissionResult['success']) {
                    return redirect()->route('roles.index')
                        ->with('warning', "Role '{$roleData['name']}' created, but permissions assignment failed. You can assign them later.")
                        ->with('open_edit_modal', $roleId);
                }
            }
        }

        return redirect()->route('roles.index')
            ->with('success', "Role '{$roleData['name']}' created successfully!")
            ->with('highlight_role', $roleId);
    }

    /**
     * Show role details (AJAX only)
     */
    public function show($id)
    {
        // Check authentication for AJAX too
        $token = $this->getToken();
        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'Authentication required. Please refresh the page.',
                'redirect' => true
            ], 401);
        }

        // Validate ID format
        if (!Str::isUuid($id)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid role ID format.'
            ], 400);
        }

        $result = $this->makeRequest('get', "/roles/{$id}");

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'request_id' => $result['request_id'] ?? null,
                'redirect' => $result['redirect_login'] ?? false
            ], $result['status'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'role' => $result['data'],
            'request_id' => $result['request_id'] ?? null
        ]);
    }

    /**
     * Update role permissions
     */
    public function update(Request $request, $id)
    {
        // Check authentication
        if ($redirect = $this->checkAuthentication()) {
            return $redirect;
        }

        // Validate ID format
        if (!Str::isUuid($id)) {
            return redirect()->route('roles.index')
                ->with('error', 'Invalid role ID format.')
                ->with('open_edit_modal', $id);
        }

        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array|min:1',
            'permission_ids.*' => 'string'
        ], [
            'permission_ids.required' => 'Please select at least one permission.',
            'permission_ids.min' => 'Please select at least one permission.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('roles.index')
                ->withErrors($validator)
                ->with('open_edit_modal', $id);
        }

        $validPermissions = array_filter($request->permission_ids);
        
        if (empty($validPermissions)) {
            return redirect()->route('roles.index')
                ->with('error', 'No valid permissions selected.')
                ->with('open_edit_modal', $id);
        }

        $permissionData = ['permission_ids' => array_values($validPermissions)];
        $result = $this->makeRequest('put', "/roles/{$id}", $permissionData);

        if (!$result['success']) {
            $errorData = [
                'error' => $result['error'],
                'open_edit_modal' => $id
            ];

            if (isset($result['request_id'])) {
                $errorData['request_id'] = $result['request_id'];
            }

            return redirect()->route('roles.index')
                ->with($errorData);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully!');
    }

    /**
     * Delete role
     */
    public function destroy($id)
    {
        // Check authentication
        if ($redirect = $this->checkAuthentication()) {
            return $redirect;
        }

        // Validate ID format
        if (!Str::isUuid($id)) {
            return redirect()->route('roles.index')
                ->with('error', 'Invalid role ID format.');
        }

        $result = $this->makeRequest('delete', "/roles/{$id}");

        if (!$result['success']) {
            $errorData = ['error' => $result['error']];
            
            if (isset($result['request_id'])) {
                $errorData['request_id'] = $result['request_id'];
            }

            return redirect()->route('roles.index')
                ->with($errorData);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Get all permissions (AJAX only)
     */
    public function permissions()
    {
        // Check authentication for AJAX too
        $token = $this->getToken();
        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'Authentication required.',
                'redirect' => true
            ], 401);
        }

        $result = $this->makeRequest('get', '/permissions');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'request_id' => $result['request_id'] ?? null,
                'redirect' => $result['redirect_login'] ?? false
            ], $result['status'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'permissions' => $result['data'] ?? [],
            'count' => count($result['data'] ?? []),
            'request_id' => $result['request_id'] ?? null
        ]);
    }
}