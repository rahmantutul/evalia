<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

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

        if (!Auth::check()) {
             return $this->redirectToLogin('Please login to access the dashboard.');
        }

        // Repopulate session if it's missing (e.g. after a session timeout but remembered login)
        if (!session()->has('user')) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user) {
                session(['user' => $user->toSessionArray()]);
                session(['permissions' => $user->getAllPermissions()->pluck('name')->toArray()]);
            }
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