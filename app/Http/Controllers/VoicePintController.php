<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use falahati\PHPMP3\MpegAudio;
use App\Services\HamsaService;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\AudioProcessingService;
use App\Services\VoiceIdentificationService;
use App\Models\VoicePrint;

class VoicePintController extends Controller
{
    protected $voiceId;
    protected $audioProcessor;
    protected $hamsa;
    protected $openai;

    public function __construct(VoiceIdentificationService $voiceId, AudioProcessingService $audioProcessor, HamsaService $hamsa, OpenAIService $openai)
    {
        $this->voiceId = $voiceId;
        $this->audioProcessor = $audioProcessor;
        $this->hamsa = $hamsa;
        $this->openai = $openai;
    }

    public function index()
    {
        $directory = public_path('uploads/voice-pints');
        $files = [];
        $groups = []; // voice_id => [files...]
        
        if (file_exists($directory)) {
            $allFiles = array_diff(scandir($directory), ['.', '..']);
            foreach ($allFiles as $file) {
                if (str_ends_with($file, '.wav') || str_ends_with($file, '.mp3')) {
                    $voiceId = null;
                    if (str_starts_with($file, 'voice_')) {
                        $parts = explode('_', $file);
                        if (count($parts) >= 3) {
                            $voiceId = $parts[0] . '_' . $parts[1]; // voice_1
                        }
                    }

                    $entry = [
                        'filename' => $file,
                        'name'     => $voiceId ? ltrim(str_replace($voiceId . '_', '', $file), '_') : $file,
                        'url'      => route('user.voice-pint.stream', $file),
                        'time'     => filemtime($directory . '/' . $file),
                        'voice_id' => $voiceId,
                    ];

                    $files[] = $entry;

                    if ($voiceId) {
                        $groups[$voiceId][] = $entry;
                    }
                }
            }
            // Sort by latest first
            usort($files, fn($a, $b) => $b['time'] <=> $a['time']);
            // Sort each group by latest first
            foreach ($groups as &$g) {
                usort($g, fn($a, $b) => $b['time'] <=> $a['time']);
            }
        }

        return view('user.voice-pint.index', [
            'files'       => $files,
            'groups'      => $groups,       // Grouped by voice identity
            'voicePrints' => VoicePrint::latest()->get()
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:mp3,wav|max:102400', 
        ]);

        // Increase maximum execution time (Both pre-processing and identification can be slow)
        set_time_limit(600);

        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $timestamp = time();
            $originalFileName = $timestamp . '_' . $file->getClientOriginalName();
            
            $tempDirectory = public_path('uploads/voice-pints');
            if (!file_exists($tempDirectory)) {
                mkdir($tempDirectory, 0755, true);
            }

            // Save original upload locally for processing
            $file->move($tempDirectory, $originalFileName);
            $localFilePath = $tempDirectory . '/' . $originalFileName;

            try {
                // 1. Send to port 8001 (Pre-Processing API) - Trims the audio
                $processedWavPath = $this->audioProcessor->processAudio($localFilePath);
                $processedWavFileName = basename($processedWavPath);

                // 2. Identification on the PROCESSED WAV file (port 8000 Identification API)
                $voiceInfo = $this->voiceId->identifyVoice($processedWavPath);

                // 3. Rename result with prefix if found/created
                $matchedId = null;
                $finalFileName = $processedWavFileName;
                if ($voiceInfo && (isset($voiceInfo['matched_id']) || $voiceInfo['action'] === 'create_new')) {
                    $matchedId = $voiceInfo['matched_id'] ?? ('voice_' . VoicePrint::count());
                    $finalFileName = $matchedId . '_' . $processedWavFileName;
                    rename($processedWavPath, $tempDirectory . '/' . $finalFileName);
                }
                $finalPath = $tempDirectory . '/' . $finalFileName;

                // 4. TRANSCRIPTION LOGIC (Using Hamsa & DB for reference)
                $currentTranscription = null;
                $referenceTranscription = null;
                
                try {
                    // A. Transcribe CURRENT file
                    $currentTranscription = $this->getOrTranscribeFile($finalFileName, $finalPath, $matchedId);

                    // B. If MATCHED, find and ensure reference transcription exists
                    if ($matchedId) {
                        // Find the first/reference recording for this identity (excluding the one we just made)
                        $allFilesInDir = array_diff(scandir($tempDirectory), ['.', '..']);
                        $otherFiles = [];
                        foreach ($allFilesInDir as $f) {
                            if (str_starts_with($f, $matchedId . '_') && $f !== $finalFileName) {
                                $otherFiles[] = $f;
                            }
                        }

                        if (!empty($otherFiles)) {
                            // Sort by time ascending to get the EARLIEST (original) print for comparison
                            usort($otherFiles, fn($a, $b) => filemtime($tempDirectory . '/' . $a) <=> filemtime($tempDirectory . '/' . $b));
                            $referenceFile = $otherFiles[0];
                            $referencePath = $tempDirectory . '/' . $referenceFile;
                            
                            // Get or Transcribe the reference file
                            $referenceTranscription = $this->getOrTranscribeFile($referenceFile, $referencePath, $matchedId);
                        }
                    }

                    // C. GPT SCORING (Structure ready for prompt)
                    if ($currentTranscription && $referenceTranscription) {
                        // $gptMatch = $this->openai->compareTranscriptions($currentTranscription, $referenceTranscription);
                        // session(['gpt_match' => $gptMatch]);
                    }

                } catch (Exception $transEx) {
                    Log::warning('Transcription Pipeline Error: ' . $transEx->getMessage());
                }

                // 5. History grouping for matches
                $relatedFiles = [];
                if (isset($matchedId)) {
                    $allFilesInDir = array_diff(scandir($tempDirectory), ['.', '..']);
                    foreach ($allFilesInDir as $f) {
                        if (str_starts_with($f, $matchedId . '_') && $f !== $finalFileName) {
                            $relatedFiles[] = [
                                'name' => str_replace($matchedId . '_', '', $f),
                                'url'  => route('user.voice-pint.stream', $f),
                                'time' => filemtime($tempDirectory . '/' . $f)
                            ];
                        }
                    }
                }

                // Cleanup: Delete original upload as we now have the processed WAV
                if (file_exists($localFilePath)) {
                    unlink($localFilePath);
                }

                return back()->with('success', 'Processing and identification complete!')
                             ->with('file_url', route('user.voice-pint.stream', $finalFileName))
                             ->with('voice_info', $voiceInfo)
                             ->with('related_files', $relatedFiles)
                             ->with('hamsa_response', $currentTranscription) // Showing latest for UI
                             ->with('reference_response', $referenceTranscription); 

            } catch (Exception $e) {
                Log::error('Voice Identification Error: ' . $e->getMessage());
                // Clean up both the original upload and processed WAV so they don't appear in Recent Audios
                if (file_exists($localFilePath)) {
                    unlink($localFilePath);
                }
                if (isset($processedWavPath) && file_exists($processedWavPath)) {
                    unlink($processedWavPath);
                }
                return back()->with('error', 'Error: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Failed to upload audio.');
    }

    /**
     * Helper to get transcription from DB or Hamsa
     */
    private function getOrTranscribeFile($fileName, $filePath, $voiceId = null)
    {
        $existing = \App\Models\VoicePrintTranscription::where('file_name', $fileName)->first();
        if ($existing && $existing->transcription) {
            return $existing->raw_response ?: ['transcript' => $existing->transcription];
        }

        // Upload to S3
        $s3Path = Storage::disk('s3')->putFileAs('voice-pints', new \Illuminate\Http\File($filePath), $fileName);
        if (!$s3Path) throw new Exception("S3 Upload Failed for $fileName");
        
        $tempUrl = Storage::disk('s3')->temporaryUrl($s3Path, now()->addMinutes(60));
        
        // Hamsa Job
        $jobResponse = $this->hamsa->createTranscriptionJob($tempUrl, 'Voice Pint: ' . $fileName, 'en');
        if (!$jobResponse['success']) throw new Exception("Hamsa Job Creation Failed for $fileName");

        $details = $this->hamsa->waitForCompletion($jobResponse['jobId'], 300, 5);
        $resultData = $details['data'] ?? $details;

        // Extract transcription text
        $conversation = $this->hamsa->extractConversation($resultData);
        $fullText = "";
        foreach($conversation as $seg) {
            $fullText .= ($seg['speaker'] ?? 'Unknown') . ": " . ($seg['text'] ?? '') . "\n";
        }

        // Save to DB
        \App\Models\VoicePrintTranscription::updateOrCreate(
            ['file_name' => $fileName],
            [
                'voice_id'      => $voiceId,
                'transcription' => $fullText,
                'raw_response'  => $resultData
            ]
        );

        return $resultData;
    }

    public function delete($filename)
    {
        $path = public_path('uploads/voice-pints/' . $filename);
        if (file_exists($path)) {
            // Check if it has a voice ID
            $voiceId = null;
            if (str_starts_with($filename, 'voice_')) {
                $parts = explode('_', $filename);
                $voiceId = $parts[0] . '_' . $parts[1];
            }

            unlink($path);

            // Delete transcription from DB
            \App\Models\VoicePrintTranscription::where('file_name', $filename)->delete();

            // If it had a voice ID, check if any other files share it
            if ($voiceId) {
                $dir = public_path('uploads/voice-pints');
                $remaining = array_filter(scandir($dir), function($f) use ($voiceId) {
                    return str_starts_with($f, $voiceId . '_');
                });

                // If no files left for this ID, delete the voice print
                if (empty($remaining)) {
                    VoicePrint::where('internal_id', $voiceId)->delete();
                    \App\Models\VoicePrintTranscription::where('voice_id', $voiceId)->delete();
                }
            }

            return back()->with('message', 'File deleted.');
        }
        return back()->with('error', 'File not found.');
    }

    public function deleteAll()
    {
        $directory = public_path('uploads/voice-pints');
        if (file_exists($directory)) {
            $files = array_diff(scandir($directory), ['.', '..']);
            foreach ($files as $file) {
                unlink($directory . '/' . $file);
            }
        }
        VoicePrint::truncate();
        \App\Models\VoicePrintTranscription::truncate();
        return back()->with('message', 'All history, voice prints and transcriptions cleared.');
    }

    public function stream($filename)
    {
        // Serve audio with proper Range request support so browsers detect duration
        $path = public_path('uploads/voice-pints/' . $filename);
        abort_unless(file_exists($path), 404);
        $mime = str_ends_with($filename, '.wav') ? 'audio/wav' : 'audio/mpeg';
        return response()->file($path, ['Content-Type' => $mime]);
    }
}
