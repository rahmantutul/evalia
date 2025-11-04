<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Exception;

/**
 * Hamsa API Service
 * 
 * Complete integration with Hamsa API (https://docs.tryhamsa.com)
 * Handles all API interactions including authentication, transcription,
 * text-to-speech, translation, voice agents, and more.
 * 
 * @package App\Services
 */
class HamsaService
{
    /**
     * @var string Base URL for Hamsa API
     */
    protected string $baseUrl;

    /**
     * @var string API Key for authentication
     */
    protected string $apiKey;

    /**
     * @var int Default timeout for requests (seconds)
     */
    protected int $timeout = 60;

    /**
     * @var int Timeout for file upload requests (seconds)
     */
    protected int $fileTimeout = 180;

    /**
     * Initialize the service with configuration
     * 
     * @throws Exception if API key is not configured
     */
    public function __construct()
    {
        $this->baseUrl = config('services.hamsa.base_url');
        $this->apiKey = config('services.hamsa.api_key');

        if (empty($this->apiKey)) {
            throw new Exception('Hamsa API key is not configured. Please set HAMSA_API_KEY in .env');
        }
    }

    /**
     * Make authenticated API request to Hamsa
     * 
     * @param string $method HTTP method (get, post, put, delete, patch)
     * @param string $endpoint API endpoint (e.g., '/transcribe')
     * @param array $data Request payload
     * @param array $headers Additional headers
     * @return array Response with success status, data/error, and HTTP status
     */
    public function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = $this->baseUrl . $endpoint;
        
        $defaultHeaders = [
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $fullHeaders = array_merge($defaultHeaders, $headers);

        try {
            $startTime = microtime(true);
            
            $response = Http::withHeaders($fullHeaders)
                ->timeout($this->timeout)
                ->{$method}($url, $data);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            // Log to custom channel if exists, otherwise use default
            $this->logRequest($method, $endpoint, $duration, $response->status(), $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ];
            }

            // Handle specific error responses
            $errorMessage = $this->parseErrorResponse($response);

            return [
                'success' => false,
                'error' => $errorMessage,
                'status' => $response->status(),
                'raw_response' => $response->body(),
            ];

        } catch (Exception $e) {
            $this->logError($method, $endpoint, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Make file upload request with multipart form data
     * 
     * @param string $method HTTP method (typically 'post')
     * @param string $endpoint API endpoint
     * @param array $multipartData Array of multipart form data
     * @return array Response with success status, data/error, and HTTP status
     */
    public function makeFileRequest(string $method, string $endpoint, array $multipartData = []): array
    {
        $url = $this->baseUrl . $endpoint;
        
        try {
            $startTime = microtime(true);

            $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $this->apiKey,
                ])
                ->timeout($this->fileTimeout)
                ->asMultipart()
                ->{$method}($url, $multipartData);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->logRequest($method, $endpoint, $duration, $response->status(), ['type' => 'file_upload']);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ];
            }

            $errorMessage = $this->parseErrorResponse($response);

            return [
                'success' => false,
                'error' => $errorMessage,
                'status' => $response->status(),
                'raw_response' => $response->body(),
            ];

        } catch (Exception $e) {
            $this->logError($method, $endpoint, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Parse error response from API
     * 
     * @param Response $response
     * @return string Formatted error message
     */
    protected function parseErrorResponse(Response $response): string
    {
        try {
            $body = $response->json();
            
            // Check common error formats
            if (isset($body['error'])) {
                return is_string($body['error']) ? $body['error'] : json_encode($body['error']);
            }
            
            if (isset($body['message'])) {
                return $body['message'];
            }
            
            if (isset($body['detail'])) {
                return $body['detail'];
            }

            return $response->body() ?: 'Unknown error occurred';
            
        } catch (Exception $e) {
            return $response->body() ?: 'Failed to parse error response';
        }
    }

    /**
     * Log API request
     * 
     * @param string $method
     * @param string $endpoint
     * @param float $duration
     * @param int $status
     * @param array $data
     */
    protected function logRequest(string $method, string $endpoint, float $duration, int $status, array $data = []): void
    {
        try {
            Log::channel('hamsa')->info('Hamsa API Request', [
                'method' => strtoupper($method),
                'endpoint' => $endpoint,
                'duration_ms' => $duration,
                'status' => $status,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Exception $e) {
            // Fallback to default channel if custom channel doesn't exist
            Log::info('Hamsa API Request', [
                'method' => strtoupper($method),
                'endpoint' => $endpoint,
                'duration_ms' => $duration,
                'status' => $status,
            ]);
        }
    }

    /**
     * Log API error
     * 
     * @param string $method
     * @param string $endpoint
     * @param string $error
     */
    protected function logError(string $method, string $endpoint, string $error): void
    {
        try {
            Log::channel('hamsa')->error('Hamsa API Exception', [
                'method' => strtoupper($method),
                'endpoint' => $endpoint,
                'error' => $error,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Exception $e) {
            // Fallback to default channel
            Log::error('Hamsa API Exception', [
                'method' => strtoupper($method),
                'endpoint' => $endpoint,
                'error' => $error,
            ]);
        }
    }

    /**
     * Test API connection and authentication
     * 
     * @return array Result with connection status
     */
    public function testConnection(): array
    {
        return $this->makeRequest('get', '/project');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Set custom timeout for requests
     * 
     * @param int $seconds
     * @return self
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Set custom timeout for file uploads
     * 
     * @param int $seconds
     * @return self
     */
    public function setFileTimeout(int $seconds): self
    {
        $this->fileTimeout = $seconds;
        return $this;
    }

    /**
     * Get the base URL
     * 
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Check if API key is configured
     * 
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}