<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class ExternalApiHelper
{
    protected static $baseUrl = 'http://52.22.157.186:8080/api/v1/auth';

    public static function getToken(string $password): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        try {
            // 1. Try Login
            $loginResponse = Http::post(self::$baseUrl . "/login", [
                "username" => $user->email, // using email as username
                "password" => $password,
            ]);

            if ($loginResponse->successful()) {
                return $loginResponse->json('token');
            }

            // 2. Try Register if login failed
            $registerPayload = [
                "email"      => $user->email,
                "username"   => $user->email,
                "role"       => "client",
                "full_name"  => $user->name ?? $user->email,
                "password"   => $password,
            ];

            $registerResponse = Http::post(self::$baseUrl . "/register", $registerPayload);

            if ($registerResponse->successful()) {
                // Login again after successful registration
                $loginResponse = Http::post(self::$baseUrl . "/login", [
                    "username" => $user->email,
                    "password" => $password,
                ]);

                if ($loginResponse->successful()) {
                    return $loginResponse->json('token');
                }
            }

            // If still failed
            Log::error('External API login/register failed', [
                'login_response'    => $loginResponse->body(),
                'register_response' => $registerResponse->body() ?? null,
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('External API exception: ' . $e->getMessage());
            return null;
        }
    }
}
