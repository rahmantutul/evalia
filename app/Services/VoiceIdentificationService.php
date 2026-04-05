<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VoicePrint;
use Exception;

class VoiceIdentificationService
{
    protected string $url = 'http://167.99.136.60:8000/identify';

    public function identifyVoice(string $audioPath)
    {
        try {
            // 1. Fetch all existing voice prints from database
            $history = VoicePrint::all()->map(function ($vp) {
                return [
                    'id' => $vp->internal_id ?? ('voice_' . $vp->id),
                    'embedding' => $vp->embedding
                ];
            })->toArray();

            // 2. Prepare the request
            Log::info('Voice Identification Request', [
                'url' => $this->url,
                'history_count' => count($history),
                'file_size' => file_exists($audioPath) ? filesize($audioPath) : 'NOT_FOUND'
            ]);

            $response = Http::asMultipart()
                ->timeout(120) // Increase timeout to 2 minutes
                ->attach('file', file_get_contents($audioPath), 'audio.wav', ['Content-Type' => 'audio/wav'])
                ->post($this->url, [
                    'history' => json_encode($history)
                ]);

            if ($response->failed()) {
                Log::error('Voice Identification API failed', ['body' => $response->body()]);
                throw new Exception('Voice identification service failed.');
            }

            $data = $response->json();

            // 3. Handle the result
            if (isset($data['action']) && $data['action'] === 'create_new' && !empty($data['embedding'])) {
                // If it's a new voice, save it to the database
                VoicePrint::create([
                    'internal_id' => 'voice_' . (VoicePrint::count() + 1),
                    'embedding' => $data['embedding']
                ]);
            }

            return $data;

        } catch (Exception $e) {
            Log::error('Voice Identification Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
