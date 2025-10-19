<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EvaliaAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has Evalia token in session
        if (!session()->has('evalia_token')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please login to Evalia first.',
                    'error_code' => 'EVALIA_AUTH_REQUIRED'
                ], 401);
            }

            return redirect()->route('evalia.login')->with('error', 'Please login to access this feature.');
        }

        return $next($request);
    }
}