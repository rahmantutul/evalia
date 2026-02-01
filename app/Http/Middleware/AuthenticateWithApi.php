<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

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

        // For static dummy site, ensure a dummy user is in session if not there
        if (!session()->has('user')) {
             return $this->redirectToLogin('Please login to access the dashboard.');
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
            'password.*',
            'register',
            'register.post'
        ];

        return $request->routeIs($skipRoutes) || $request->path() === 'login' || $request->path() === 'register';
    }

    /**
     * Secure redirect to login with session cleanup
     */
    private function redirectToLogin(string $message = null)
    {
        if ($message) {
            return redirect()->route('login')
                ->with('error', $message);
        }
        
        return redirect()->route('login');
    }
}