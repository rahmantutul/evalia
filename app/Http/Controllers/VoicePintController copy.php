<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use falahati\PHPMP3\MpegAudio;
use App\Services\HamsaService;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

use App\Services\VoiceIdentificationService;
use App\Models\VoicePrint;

class VoicePintController extends Controller
{
    protected $hamsa;
    protected $openai;
    protected $voiceId;

    public function __construct(HamsaService $hamsa, OpenAIService $openai, VoiceIdentificationService $voiceId)
    {
        $this->hamsa = $hamsa;
        $this->openai = $openai;
        $this->voiceId = $voiceId;
    }

    public function index()
    {
        $directory = public_path('uploads/voice-pints');
        $files = [];
        
        if (file_exists($directory)) {
            $allFiles = array_diff(scandir($directory), ['.', '..']);
            foreach ($allFiles as $file) {
                // ONLY show files that start with 'cut_'
                if (str_ends_with($file, '.mp3') && str_starts_with($file, 'cut_')) {
                    $files[] = [
                        'name' => str_replace('cut_', '', $file), // Show original name to user
                        'url' => asset('uploads/voice-pints/' . $file),
                        'time' => filemtime($directory . '/' . $file),
                    ];
                }
            }
            // Sort by latest first
            usort($files, fn($a, $b) => $b['time'] <=> $a['time']);
        }

        return view('user.voice-pint.index', [
            'files' => $files,
            'voicePrints' => VoicePrint::latest()->get()
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:mp3|max:102400', 
        ]);

        // Increase maximum execution time (Hamsa transcription can take a few minutes)
        set_time_limit(360);

        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $originalName = time() . '_' . $file->getClientOriginalName();
            
            // 1. Move file to upload directory
            $file->move(public_path('uploads/voice-pints'), $originalName);
            $filePath = public_path('uploads/voice-pints/' . $originalName);

            try {
                // 2. Upload to S3 so Hamsa can access it via temporary URL
                $uploadResult = Storage::disk('s3')->putFileAs('voice-pints', new \Illuminate\Http\File($filePath), $originalName);
                
                if (!$uploadResult) {
                    throw new Exception('Failed to upload audio to S3 for processing.');
                }

                $mediaUrl = Storage::disk('s3')->temporaryUrl($uploadResult, now()->addMinutes(60));

                // 3. Start Hamsa Transcription Job
                $jobResponse = $this->hamsa->createTranscriptionJob($mediaUrl, 'Voice Pint: ' . $originalName, 'ar');
                if (!$jobResponse['success']) {
                    throw new Exception('Hamsa Job Creation failed: ' . ($jobResponse['error'] ?? 'Unknown error'));
                }
                
                $jobId = $jobResponse['jobId'];
                $details = $this->hamsa->waitForCompletion($jobId, 600, 5);
                
                if (!$details['success']) {
                    throw new Exception('Hamsa Processing failed.');
                }

                $resultData = $details['result'] ?? [];
                $conversation = $this->hamsa->extractConversation($resultData);

                // 4. Prepare transcription with timestamps for GPT
                $transcriptionWithTimestamps = "";
                foreach ($conversation as $turn) {
                    $transcriptionWithTimestamps .= "[" . $turn['start_time'] . " - " . $turn['end_time'] . "] " . $turn['speaker'] . ": " . $turn['text'] . "\n";
                }

                // 5. Call GPT to identify client segments
                $segments = $this->openai->identifyClientSegments($transcriptionWithTimestamps);

                if (empty($segments)) {
                    throw new Exception('GPT failed to identify client segments.');
                }

                // 6. Cut and Merge ALL identified segments
                $segmentsList = isset($segments['segments']) ? $segments['segments'] : $segments;
                $cutFileName = 'cut_' . $originalName;
                $cutFilePath = public_path('uploads/voice-pints/' . $cutFileName);
                
                $finalAudio = null;
                $totalExtractedDuration = 0;

                foreach ($segmentsList as $segment) {
                    if (isset($segment['from']) && isset($segment['duration']) && $segment['duration'] > 0) {
                        try {
                            $part = MpegAudio::fromFile($filePath)->trim($segment['from'], $segment['duration']);
                            if ($finalAudio === null) {
                                $finalAudio = $part;
                            } else {
                                $finalAudio->append($part);
                            }
                            $totalExtractedDuration += $segment['duration'];
                        } catch (Exception $trimEx) {
                            Log::warning('Failed to trim segment', ['segment' => $segment, 'error' => $trimEx->getMessage()]);
                        }
                    }
                }

                if ($finalAudio === null) {
                    throw new Exception('No valid client segments could be extracted.');
                }

                $finalAudio->saveFile($cutFilePath);

                // 7. VOICE IDENTIFICATION
                // Send the trimmed audio to the identification API
                $voiceInfo = null;
                try {
                    $voiceInfo = $this->voiceId->identifyVoice($cutFilePath);
                } catch (Exception $voiceEx) {
                    Log::warning('Voice Identification failed, but continuing: ' . $voiceEx->getMessage());
                    // We continue so the user at least gets their cut audio
                }

                // Cleanup: Delete the original full-length file to save space
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                return back()->with('success', 'Voice Pint processed! All client segments merged (' . round($totalExtractedDuration, 2) . 's extracted).')
                             ->with('file_url', asset('uploads/voice-pints/' . $cutFileName))
                             ->with('duration', $totalExtractedDuration)
                             ->with('voice_info', $voiceInfo);

            } catch (Exception $e) {
                Log::error('Voice Pint Error: ' . $e->getMessage());
                return back()->with('error', 'Error: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Failed to upload audio.');
    }
}

