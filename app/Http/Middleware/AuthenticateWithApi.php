<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AuthenticateWithApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has access token in session
        if (!$this->hasValidToken()) {
            $this->clearSession();
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        // Verify token with API (optional - for extra security)
        if (!$this->verifyTokenWithApi()) {
            $this->clearSession();
            return redirect()->route('login')->with('error', 'Invalid session. Please login again.');
        }

        // Add security headers
        $response = $next($request);
        return $this->addSecurityHeaders($response);
    }

    /**
     * Check if token exists and is valid
     */
    private function hasValidToken(): bool
    {
        $token = session('user_access_token');
        
        if (empty($token)) {
            return false;
        }

        // Check token format (basic validation)
        if (!is_string($token) || strlen($token) < 10) {
            return false;
        }

        // Check token expiration if stored in session
        $tokenExpiry = session('token_expiry');
        if ($tokenExpiry && now()->greaterThan($tokenExpiry)) {
            return false;
        }

        return true;
    }

    /**
     * Verify token with API (optional - for maximum security)
     */
    private function verifyTokenWithApi(): bool
    {
        // Only verify periodically to reduce API calls
        $lastVerified = session('token_last_verified');
        
        if ($lastVerified && now()->diffInMinutes($lastVerified) < 5) {
            return true; // Skip verification if recently verified
        }

        try {
            $token = session('user_access_token');
            
            $response = Http::timeout(10)
                ->withToken($token)
                ->get('http://35.153.178.201:8080/auth/me');

            if ($response->successful()) {
                session(['token_last_verified' => now()]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::warning('Token verification failed: ' . $e->getMessage());
            // If API is down, allow access but log the issue
            return true;
        }
    }

    /**
     * Clear all session data
     */
    private function clearSession(): void
    {
        session()->forget([
            'user_access_token',
            'token_expiry',
            'token_last_verified',
            'user',
            'api_token'
        ]);
        session()->flush();
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders(Response $response): Response
    {
        return $response
            ->header('X-Frame-Options', 'DENY')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-XSS-Protection', '1; mode=block')
            ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }
}