<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->email)->first();
            
            if(!$user) {
                // Download and store the avatar
                $avatarUrl = $this->storeGoogleAvatar($googleUser);
                
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $avatarUrl, // Store the avatar path
                    'password' => encrypt('randomencryptedpassword')
                ]);
            } else {
                // Update avatar if it's not set or if you want to refresh it
                if (empty($user->avatar)) {
                    $avatarUrl = $this->storeGoogleAvatar($googleUser);
                    $user->update(['avatar' => $avatarUrl]);
                }
            }
            
            Auth::login($user);
            return redirect()->intended('/user-dashboard');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google authentication failed: '.$e->getMessage());
        }
    }

    protected function storeGoogleAvatar($googleUser)
    {
        try {
            if ($googleUser->avatar) {
                $contents = file_get_contents($googleUser->avatar);
                $filename = 'avatars/google_' . $googleUser->id . '.jpg';
                Storage::disk('public')->put($filename, $contents);
                return $filename;
            }
        } catch (\Exception $e) {
            Log::error('Failed to store Google avatar: '.$e->getMessage());
        }
        
        return null;
    }
}