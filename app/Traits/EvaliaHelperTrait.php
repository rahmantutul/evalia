<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait EvaliaHelperTrait
{
    /**
     * Handle API response with consistent error handling
     */
    protected function handleApiResponse(callable $apiCall, string $operation = 'API operation'): JsonResponse
    {
        try {
            $this->setAuthToken();
            $response = $apiCall();
            
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error("Evalia {$operation} failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $this->getErrorCode($e)
            ], $this->getStatusCode($e));
        }
    }

    /**
     * Get appropriate HTTP status code based on exception
     */
    private function getStatusCode(Exception $e): int
    {
        $message = $e->getMessage();
        
        if (str_contains($message, 'Authentication') || str_contains($message, 'Unauthorized')) {
            return 401;
        }
        
        if (str_contains($message, 'Forbidden') || str_contains($message, 'Access denied')) {
            return 403;
        }
        
        if (str_contains($message, 'Not found')) {
            return 404;
        }
        
        if (str_contains($message, 'Validation') || str_contains($message, 'Invalid')) {
            return 422;
        }
        
        return 500;
    }

    /**
     * Get error code for client-side handling
     */
    private function getErrorCode(Exception $e): string
    {
        $message = strtolower($e->getMessage());
        
        if (str_contains($message, 'authentication') || str_contains($message, 'unauthorized')) {
            return 'EVALIA_AUTH_ERROR';
        }
        
        if (str_contains($message, 'not found')) {
            return 'EVALIA_NOT_FOUND';
        }
        
        if (str_contains($message, 'validation') || str_contains($message, 'invalid')) {
            return 'EVALIA_VALIDATION_ERROR';
        }
        
        if (str_contains($message, 'rate limit') || str_contains($message, 'quota')) {
            return 'EVALIA_RATE_LIMIT';
        }
        
        return 'EVALIA_GENERAL_ERROR';
    }

    /**
     * Validate pagination parameters
     */
    protected function validatePagination(array $params): array
    {
        $page = max(1, (int) ($params['page'] ?? 1));
        $limit = min(100, max(1, (int) ($params['limit'] ?? 10)));
        
        return ['page' => $page, 'limit' => $limit];
    }

    /**
     * Validate date range
     */
    protected function validateDateRange(?string $startDate, ?string $endDate): array
    {
        $start = $startDate ? date('Y-m-d', strtotime($startDate)) : date('Y-m-d', strtotime('-30 days'));
        $end = $endDate ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d');
        
        if ($start > $end) {
            throw new Exception('Start date cannot be after end date');
        }
        
        return ['start_date' => $start, 'end_date' => $end];
    }

    /**
     * Format file upload for API
     */
    protected function formatFileForUpload($file, string $fieldName): array
    {
        if (!$file || !$file->isValid()) {
            throw new Exception("Invalid file for {$fieldName}");
        }
        
        return [
            $fieldName,
            $file->getRealPath(),
            $file->getClientOriginalName(),
            $file->getMimeType()
        ];
    }

    /**
     * Validate audio file
     */
    protected function validateAudioFile($file): void
    {
        if (!$file || !$file->isValid()) {
            throw new Exception('Invalid audio file');
        }
        
        $allowedMimes = ['audio/wav', 'audio/mpeg', 'audio/mp3'];
        $allowedExtensions = ['wav', 'mp3'];
        
        if (!in_array($file->getMimeType(), $allowedMimes) || 
            !in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            throw new Exception('Audio file must be WAV or MP3 format');
        }
        
        $maxSize = config('evalia.audio.max_file_size', 50 * 1024 * 1024); // 50MB
        if ($file->getSize() > $maxSize) {
            throw new Exception('Audio file size exceeds maximum allowed size');
        }
    }

    /**
     * Cache response with TTL
     */
    protected function cacheResponse(string $key, callable $callback, int $ttl = 300)
    {
        return cache()->remember($key, $ttl, $callback);
    }

    /**
     * Build cache key
     */
    protected function buildCacheKey(string $prefix, array $params = []): string
    {
        $userId = session('evalia_user_id', 'anonymous');
        $paramString = implode('_', array_map(function($key, $value) {
            return "{$key}-{$value}";
        }, array_keys($params), array_values($params)));
        
        return "evalia_{$prefix}_{$userId}_{$paramString}";
    }

    /**
     * Clear cache by pattern
     */
    protected function clearCachePattern(string $pattern): void
    {
        $userId = session('evalia_user_id', 'anonymous');
        $fullPattern = "evalia_{$pattern}_{$userId}_*";
        
        // This would depend on your cache driver
        // For Redis: cache()->getRedis()->eval("return redis.call('del', unpack(redis.call('keys', ARGV[1])))", 0, $fullPattern);
        // For file cache: you'd need a custom implementation
        
        Log::info("Clearing cache pattern: {$fullPattern}");
    }

    /**
     * Log API activity
     */
    protected function logApiActivity(string $action, array $data = []): void
    {
        Log::info("Evalia API: {$action}", array_merge([
            'user_id' => session('evalia_user_id'),
            'timestamp' => now()->toISOString()
        ], $data));
    }

    /**
     * Format response data for frontend
     */
    protected function formatResponseForFrontend(array $data): array
    {
        // Remove sensitive data
        $sensitiveKeys = ['api_key', 'access_token', 'password', 'secret'];
        
        return $this->removeKeysRecursive($data, $sensitiveKeys);
    }

    /**
     * Remove sensitive keys recursively
     */
    private function removeKeysRecursive(array $data, array $keysToRemove): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $keysToRemove)) {
                $data[$key] = '[HIDDEN]';
            } elseif (is_array($value)) {
                $data[$key] = $this->removeKeysRecursive($value, $keysToRemove);
            }
        }
        
        return $data;
    }
}