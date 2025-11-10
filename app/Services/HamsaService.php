<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Exception;

class HamsaService
{

    protected string $baseUrl;

    protected string $apiKey;

    protected int $timeout = 60;

    protected int $fileTimeout = 180;

    public function __construct()
    {
        $this->baseUrl = config('services.hamsa.base_url');
        $this->apiKey = config('services.hamsa.api_key');

        if (empty($this->apiKey)) {
            throw new Exception('Hamsa API key is not configured. Please set HAMSA_API_KEY in .env');
        }
    }

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
            $httpClient = Http::withHeaders($fullHeaders)->timeout($this->timeout);

            // Handle GET requests differently - use query parameters
            if (strtolower($method) === 'get') {
                $response = $httpClient->get($url, $data); // This will add data as query params
            } else {
                // For POST/PUT/PATCH, send data as JSON body
                $response = $httpClient->{$method}($url, $data);
            }

            \Log::info('Hamsa API Response', [
                'url' => $url,
                'method' => $method,
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body()
            ]);

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

        } catch (\Exception $e) {
            \Log::error('Hamsa API Request Failed', [
                'url' => $url,
                'method' => $method,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'API request failed: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

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

    protected function parseErrorResponse(Response $response): string
    {
        try {
            $body = $response->json();
            
            // Check common error formats and ensure string output
            if (isset($body['error'])) {
                return is_string($body['error']) ? $body['error'] : json_encode($body['error']);
            }
            
            if (isset($body['message'])) {
                return is_string($body['message']) ? $body['message'] : json_encode($body['message']);
            }
            
            if (isset($body['detail'])) {
                return is_string($body['detail']) ? $body['detail'] : json_encode($body['detail']);
            }

            // Handle cases where the response body might be a simple string
            $responseBody = $response->body();
            return !empty($responseBody) ? $responseBody : 'Unknown error occurred';
            
        } catch (Exception $e) {
            $responseBody = $response->body();
            return !empty($responseBody) ? $responseBody : 'Failed to parse error response';
        }
    }

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

    public function testConnection(): array
    {
        return $this->makeRequest('get', '/project');
    }

    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function setFileTimeout(int $seconds): self
    {
        $this->fileTimeout = $seconds;
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}