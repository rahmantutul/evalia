<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class HamsaService
{
    protected string $baseUrl = 'https://api.tryhamsa.com';
    protected string $v1Url  = 'https://api.tryhamsa.com';
    protected string $apiKey = 'f8f7b582-ecb6-43fb-8921-0542f5169378';

    public function createTranscriptionJob(string $mediaUrl, string $title = 'Untitled', string $language = 'ar'): array
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $this->baseUrl . '/v2/jobs',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Token ' . $this->apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'type'           => 'TRANSCRIPTION',
                    'apiKey'         => $this->apiKey,
                    'mediaUrl'       => $mediaUrl,
                    'title'          => $title,
                    'language'       => $language,
                    'processingType' => 'async',
                    'sentiment'      => true,
                    'diarization'    => true,
                ]),
            ]);

            $body  = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('Hamsa Create Job cURL error: ' . $error);
                return ['success' => false, 'error' => $error];
            }

            $data = json_decode($body, true);
            Log::info('Hamsa Create Job response', ['body' => $data]);

            $jobId = $data['data']['jobId'] ?? null;

            if ($jobId) {
                return ['success' => true, 'jobId' => $jobId, 'data' => $data];
            }

            return ['success' => false, 'error' => $body];

        } catch (Exception $e) {
            Log::error('Hamsa Create Job Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getJobDetails(string $jobId): array
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $this->v1Url . '/v1/jobs?jobId=' . urlencode($jobId),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Token ' . $this->apiKey,
                ],
            ]);

            $body  = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('Hamsa Get Job cURL error: ' . $error);
                return ['success' => false, 'error' => $error];
            }

            $responseData = json_decode($body, true);

            $outer = $responseData['data'] ?? $responseData;
            $inner = isset($outer['data']) && is_array($outer['data']) ? $outer['data'] : $outer;

            $status = $inner['status'] ?? 'UNKNOWN';
            $result = $inner['jobResponse'] ?? $inner;

            return [
                'success'       => true,
                'status'        => $status,
                'result'        => $result,
                'data'          => $inner,
                'full_response' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Hamsa Get Job Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function waitForCompletion(string $jobId, int $maxWaitSeconds = 300, int $intervalSeconds = 5): array
    {
        $waited = 0;

        while ($waited < $maxWaitSeconds) {
            sleep($intervalSeconds);
            $waited += $intervalSeconds;

            $details = $this->getJobDetails($jobId);

            if (!$details['success']) {
                Log::warning('Could not poll job ' . $jobId . ', retrying...', $details);
                continue;
            }

            $status = strtoupper($details['status']);
            Log::info("Hamsa job {$jobId} status after {$waited}s: {$status}");

            if ($status === 'COMPLETED' || $status === 'SUCCESSFUL') {
                return $details;
            }

            if (in_array($status, ['FAILED', 'ERROR', 'REJECTED'])) {
                throw new Exception("Hamsa job failed with status: {$status}");
            }

        }

        throw new Exception("Hamsa job {$jobId} did not complete within {$maxWaitSeconds} seconds.");
    }

    public function extractConversation(array $data): array
    {
        $segments = [];
        // Check for common keys where Hamsa stores conversation turns/segments
        foreach (['conversation', 'diarization', 'fragments', 'segments', 'results'] as $key) {
            if (!empty($data[$key]) && is_array($data[$key])) {
                $segments = $data[$key];
                break;
            }
        }

        if (empty($segments) && (!empty($data['words']) || !empty($data['tokens']))) {
            $segments = $this->buildConversationFromWords($data['words'] ?? $data['tokens']);
        }

        // Normalize segments to a standard format for the application
        return array_map(function($s) {
            $start = $s['start'] ?? ($s['start_time'] ?? 0);
            $end = $s['end'] ?? ($s['end_time'] ?? 0);
            
            // Auto-detect milliseconds (Hamsa fragments often use ms)
            if ($start > 10000) $start /= 1000;
            if ($end > 10000) $end /= 1000;

            return [
                'speaker'    => $s['speaker'] ?? ($s['speakerId'] ?? 'Unknown'),
                'text'       => $s['text'] ?? ($s['transcript'] ?? ''),
                'start_time' => (float)$start,
                'end_time'   => (float)$end,
                'sentiment'  => $s['sentiment'] ?? 'Neutral'
            ];
        }, $segments);
    }

    private function buildConversationFromWords(array $words): array
    {
        $conversation    = [];
        $currentSpeaker  = null;
        $currentText     = [];

        foreach ($words as $word) {
            $speaker = $word['speaker'] ?? ($word['speaker_tag'] ?? 'Unknown');

            if ($speaker !== $currentSpeaker && $currentSpeaker !== null) {
                $conversation[] = ['speaker' => $currentSpeaker, 'text' => implode(' ', $currentText)];
                $currentText    = [];
            }

            $currentSpeaker  = $speaker;
            $currentText[]   = $word['text'] ?? ($word['word'] ?? '');
        }

        if (!empty($currentText)) {
            $conversation[] = ['speaker' => $currentSpeaker, 'text' => implode(' ', $currentText)];
        }

        return $conversation;
    }
}