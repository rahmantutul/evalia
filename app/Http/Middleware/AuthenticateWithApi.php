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

        // Verify token with API (with better error handling)
        if (!$this->verifyTokenWithApi()) {
            return $this->redirectToLogin('Invalid session. Please login again.');
        }

        // Add security headers
        $response = $next($request);
        return $this->addSecurityHeaders($response);
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
     * Check if token exists and is valid
     */
    private function hasValidToken(): bool
    {
        $token = session('user_access_token');
        
        if (empty($token) || !is_string($token)) {
            Log::warning('Token missing or invalid type');
            return false;
        }

        // More robust token validation
        if (strlen($token) < 20 || !preg_match('/^[a-zA-Z0-9\.\-_]+$/', $token)) {
            Log::warning('Invalid token format');
            return false;
        }

        // Check token expiration
        $tokenExpiry = session('token_expiry');
        if ($tokenExpiry) {
            try {
                $expiry = Carbon::parse($tokenExpiry);
                if (now()->greaterThan($expiry)) {
                    Log::info('Token expired');
                    return false;
                }
            } catch (\Exception $e) {
                Log::warning('Invalid token expiry format');
                return false;
            }
        }

        return true;
    }

    /**
     * Verify token with API with better security
     */
    private function verifyTokenWithApi(): bool
    {
        // Only verify periodically to reduce API calls
        $lastVerified = session('token_last_verified');
        
        if ($lastVerified && now()->diffInMinutes($lastVerified) < 10) {
            return true;
        }

        try {
            $token = session('user_access_token');
            
            $response = Http::timeout(8)
                ->retry(2, 100) // Retry twice with 100ms delay
                ->withHeaders([
                    'User-Agent' => 'Laravel-App/1.0',
                    'Accept' => 'application/json',
                ])
                ->withToken($token)
                ->get('http://35.153.178.201:8080/auth/me');

            if ($response->successful()) {
                session(['token_last_verified' => now()]);
                Log::info('Token verified successfully');
                return true;
            }

            // Log the specific error
            Log::warning('Token verification failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return false;

        } catch (\Exception $e) {
            Log::error('Token verification exception: ' . $e->getMessage(), [
                'exception' => get_class($e)
            ]);
            
            // If API is temporarily down, allow access but log the issue
            // Only allow this for a short period
            $lastSuccess = session('last_successful_verification');
            if ($lastSuccess && now()->diffInHours($lastSuccess) < 2) {
                return true;
            }
            
            return false;
        }
    }

    /**
     * Secure redirect to login with session cleanup
     */
    private function redirectToLogin(string $message = null)
    {
        $this->clearSession();
        
        if ($message) {
            return redirect()->route('login')
                ->with('error', $message)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
        }
        
        return redirect()->route('login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
    }

    /**
     * Clear all session data securely
     */
    private function clearSession(): void
    {
        try {
            $allSession = session()->all();
            session()->flush();
            
            Log::info('Session cleared for security', [
                'session_keys' => array_keys($allSession)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to clear session: ' . $e->getMessage());
        }
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders(Response $response): Response
    {
        $headers = [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}