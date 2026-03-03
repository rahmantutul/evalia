<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form — redirect if already authenticated
     */
    public function showLoginForm()
    {
        if (session()->has('user')) {
            return redirect()->route('user.home');
        }

        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Look up user from the database
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['username' => 'Invalid username or password.'])
                ->withInput();
        }

        if (!$user->is_active) {
            return back()
                ->withErrors(['username' => 'Your account is deactivated. Please contact the administrator.'])
                ->withInput();
        }

        // Build session data using the convenience method
        $sessionUser = $user->toSessionArray();

        // Standard Laravel auth for @can etc and middleware
        Auth::login($user);

        // Put user in session for legacy parts of the app
        session(['user' => $sessionUser]);
        
        // Fetch real permissions for session-based checks
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        session(['permissions' => $permissions]);

        $request->session()->regenerate();

        return redirect()->route('user.home')->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}

