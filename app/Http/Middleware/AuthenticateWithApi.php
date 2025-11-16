<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthenticateWithApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for login routes to prevent redirect loops
        if ($this->shouldSkipMiddleware($request)) {
            return $next($request);
        }

        // Check if user has access token in session
        if (!$this->hasValidToken()) {
            return $this->redirectToLogin('Your session has expired. Please login again.');
        }

        // Verify token with API on EVERY request (remove caching)
        if (!$this->verifyTokenWithApi()) {
            return $this->redirectToLogin('Your session has expired. Please login again.');
        }

        return $next($request);
    }

    /**
     * Check if middleware should be skipped for certain routes
     */
    private function shouldSkipMiddleware(Request $request): bool
    {
        $skipRoutes = [
            'login',
            'login.post',
            'logout',
            'auth.*',
            'password.*'
        ];

        return $request->routeIs($skipRoutes);
    }

    /**
     * Check if token exists and is valid locally
     */
    private function hasValidToken(): bool
    {
        $token = session('user_access_token');
        
        if (empty($token) || !is_string($token)) {
            Log::warning('Token missing or invalid type');
            return false;
        }

        // Basic token format validation
        if (strlen($token) < 20) {
            Log::warning('Invalid token format - too short');
            return false;
        }

        return true;
    }

    /**
     * Verify token with API - ALWAYS check on every request
     */
    private function verifyTokenWithApi(): bool
    {
        try {
            $token = session('user_access_token');
            
            $response = Http::timeout(10)
                ->retry(1, 100) // Only retry once to be faster
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ])
                ->get('http://35.153.178.201:8080/auth/me');

            if ($response->successful()) {
                Log::debug('Token verified successfully');
                return true;
            }

            // Token is invalid at API level
            if ($response->status() === 401 || $response->status() === 403) {
                Log::warning('Token rejected by API', [
                    'status' => $response->status(),
                    'token_prefix' => substr($token, 0, 10) . '...'
                ]);
                return false;
            }

            // For server errors, allow temporary access
            Log::warning('API server error during token verification', [
                'status' => $response->status()
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Token verification failed: ' . $e->getMessage());
            
            // If API is down, allow temporary access
            return true;
        }
    }

    /**
     * Secure redirect to login with session cleanup
     */
    private function redirectToLogin(string $message = null)
    {
        // Clear the invalid token from session
        session()->forget('user_access_token');
        session()->forget('token_expiry');
        session()->forget('token_last_verified');
        
        if ($message) {
            return redirect()->route('login')
                ->with('error', $message);
        }
        
        return redirect()->route('login');
    }
}