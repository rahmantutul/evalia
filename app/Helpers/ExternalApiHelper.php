<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class ExternalApiHelper
{
    protected static $baseUrl = 'http://13.218.100.190:8080/api/v1/auth';

    public static function getToken()
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user for external API token');
            return null;
        }
        
        try {
            $password = 'DefaultPassword123!';

            // Try login first
            $loginResponse = Http::post(self::$baseUrl . "/login", [
                "email" => $user->email,
                "password" => $password,
            ]);
            
            if ($loginResponse->successful()) {
                $token = $loginResponse->json('access_token');
                if ($token) {
                    return $token;
                }
                Log::warning('Login successful but no access_token in response');
            } else {
                Log::warning('Login failed: ' . $loginResponse->body());
            }

            // If login fails, try registration
            $registerPayload = [
                "email"      => $user->email,
                "role"       => "client",
                "full_name"  => $user->name ?? $user->email,
                "password"   => $password,
            ];

            $registerResponse = Http::post(self::$baseUrl . "/register", $registerPayload);

            if ($registerResponse->successful()) {
                // Try login again after registration
                $loginResponse = Http::post(self::$baseUrl . "/login", [
                    "email" => $user->email,
                    "password" => $password,
                ]);

                if ($loginResponse->successful()) {
                    $token = $loginResponse->json('access_token');
                    if ($token) {
                        return $token;
                    }
                    Log::warning('Post-registration login successful but no access_token');
                }
            } else {
                Log::warning('Registration failed: ' . $registerResponse->body());
            }

            return null;

        } catch (\Exception $e) {
            Log::error('External API exception: ' . $e->getMessage());
            return null;
        }
    }
}
