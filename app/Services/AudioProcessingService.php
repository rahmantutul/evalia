<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AudioProcessingService
{
    protected string $url = 'http://167.99.136.60:8001/process-audio/';

    /**
     * Sends audio to the pre-processing API and returns the local path to the processed WAV file.
     */
    public function processAudio(string $localFilePath)
    {
        try {
            Log::info('Audio Pre-Processing Start', ['file' => $localFilePath]);

            // 1. Send file to the processing API
            $response = Http::asMultipart()
                ->timeout(120) // Give 2 minutes for processing
                ->attach('file', file_get_contents($localFilePath), basename($localFilePath), [
                    'Content-Type' => 'audio/mpeg'
                ])
                ->post($this->url);

            if ($response->failed()) {
                Log::error('Audio Pre-Processing API failed', ['body' => $response->body()]);
                throw new Exception('Audio pre-processing failed.');
            }

            $data = $response->json();
            if (empty($data['download_url'])) {
                throw new Exception('Audio pre-processing did not return a download URL.');
            }

            $downloadUrl = $data['download_url'];
            Log::info('Audio Pre-Processing Download URL received', ['url' => $downloadUrl]);

            // 2. Download the processed file
            $processedContent = file_get_contents($downloadUrl);
            if ($processedContent === false) {
                throw new Exception('Failed to download processed audio from ' . $downloadUrl);
            }

            // 3. Save the processed file locally (as a WAV)
            $processedFileName = 'processed_' . pathinfo($localFilePath, PATHINFO_FILENAME) . '.wav';
            $processedLocalPath = public_path('uploads/voice-pints/' . $processedFileName);
            
            file_put_contents($processedLocalPath, $processedContent);

            return $processedLocalPath;

        } catch (Exception $e) {
            Log::error('AudioProcessingService Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
