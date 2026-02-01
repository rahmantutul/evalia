<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class HamsaService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout = 60;
    protected int $fileTimeout = 180;

    public function __construct()
    {
        $this->baseUrl = 'https://api.hamsa.ai/v1';
        $this->apiKey = 'dummy-key';
    }

    public function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        // Mocked response for Hamsa API
        $responses = [
            '/usage/numbers' => [
                'success' => true,
                'data' => [
                    'transcriptions' => 1245,
                    'tts_jobs' => 432,
                    'ai_generations' => 2840,
                    'voice_agents' => 8,
                    'remaining' => 8755,
                    'used' => 6245,
                    'total' => 15000
                ]
            ],
            '/jobs' => [
                'success' => true,
                'data' => [
                    'results' => [
                        ['id' => 'job-882', 'type' => 'transcription', 'status' => 'completed', 'created_at' => now()->subMinutes(15)->toIso8601String()],
                        ['id' => 'job-881', 'type' => 'tts', 'status' => 'completed', 'created_at' => now()->subHours(1)->toIso8601String()],
                        ['id' => 'job-880', 'type' => 'transcription', 'status' => 'processing', 'created_at' => now()->subHours(2)->toIso8601String()],
                        ['id' => 'job-879', 'type' => 'ai_content', 'status' => 'completed', 'created_at' => now()->subDays(1)->toIso8601String()],
                    ]
                ]
            ],
            '/voice-agents' => [
                'success' => true,
                'data' => [
                    'data' => [
                        'voiceAgents' => [
                            ['id' => 'agent-ar-1', 'agentName' => 'حمزة - صوت ذكوري', 'voiceId' => 'arabic-male-1', 'lang' => 'ar'],
                            ['id' => 'agent-ar-2', 'agentName' => 'سلمى - صوت أنثوي', 'voiceId' => 'arabic-female-1', 'lang' => 'ar'],
                            ['id' => 'agent-jo-1', 'agentName' => 'زيد - لهجة أردنية', 'voiceId' => 'jordan-male-1', 'lang' => 'ar-JO'],
                        ]
                    ]
                ]
            ],
            '/project' => [
                'success' => true,
                'data' => [
                    'id' => 'proj-ssc-2024',
                    'name' => 'مشروع تحليل المكالمات - الضمان الاجتماعي',
                    'status' => 'ACTIVE'
                ]
            ],
            '/jobs/all' => [
                 'success' => true,
                 'data' => [
                     'data' => [
                         'jobs' => [
                             ['id' => 'j101', 'type' => 'transcription', 'status' => 'completed', 'created_at' => now()->subHours(1)->toIso8601String()],
                             ['id' => 'j102', 'type' => 'tts', 'status' => 'completed', 'created_at' => now()->subHours(2)->toIso8601String()],
                             ['id' => 'j103', 'type' => 'transcription', 'status' => 'completed', 'created_at' => now()->subHours(3)->toIso8601String()]
                         ],
                         'total' => 3
                     ]
                 ]
            ]
        ];

        // Check if we have a mocked response for this endpoint
        foreach ($responses as $path => $response) {
            if (str_contains($endpoint, $path)) {
                return $response;
            }
        }

        // Default success for other endpoints (like POST requests)
        return [
            'success' => true,
            'data' => ['id' => 'job-' . rand(100, 999), 'status' => 'COMPLETED', 'message' => 'Success (Mock)'],
            'status' => 200
        ];
    }

    public function makeFileRequest(string $method, string $endpoint, array $multipartData = []): array
    {
        return [
            'success' => true,
            'data' => ['job_id' => 'file-job-' . rand(1000, 9999), 'status' => 'PROCESSING'],
            'status' => 200
        ];
    }

    protected function parseErrorResponse($response): string
    {
        return 'Mocked error response';
    }

    protected function logRequest(string $method, string $endpoint, float $duration, int $status, array $data = []): void
    {
    }

    protected function logError(string $method, string $endpoint, string $error): void
    {
    }

    public function testConnection(): array
    {
        return ['success' => true];
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
        return true;
    }
}