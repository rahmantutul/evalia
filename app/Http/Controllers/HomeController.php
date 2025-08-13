<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('user.dashboard');
    }
    public function profile()
    {
        return view('user.profile');
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,'.$user->id,
                'phone' => 'required|string|max:20',
                'company' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'current_password' => 'required_with:new_password',
                'new_password' => 'nullable|min:8|confirmed|different:current_password',
            ]);

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validatedData['avatar'] = $avatarPath;
            }

            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => ['The current password is incorrect']
                    ]);
                }
                $validatedData['password'] = Hash::make($request->new_password);
            }

            $user->update($validatedData);

            return back()->with('success', 'Profile updated successfully!');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: '.$e->getMessage());
        }
    }

    

    public function support()
    {
        return view('user.support');
    }





















    public function subscription()
    {
        return view('user.subscription');
    }

    public function bots()
    {
        return view('user.bots');
    }
    public function bot_create()
    {
        return view('user.bot_create');
    }
    
    public function bot_store()
    {
        return view('user.maintenance');
    }
    public function overview()
    {
        return view('user.overview');
    }
    public function inbox()
    {
        return view('user.inbox');
    }


}
