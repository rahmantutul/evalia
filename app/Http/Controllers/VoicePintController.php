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
        $groups = [];
        
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
            'audio' => 'required|array|max:5',
            'audio.*' => 'required|mimes:mp3,wav|max:8192', // 8MB per file
        ]);

        // Increase maximum execution time for batch processing
        set_time_limit(900);

        $files = $request->file('audio');
        $results = [];
        $errors = [];

        $tempDirectory = public_path('uploads/voice-pints');
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        foreach ($files as $file) {
            $timestamp = time();
            $originalFileName = $timestamp . '_' . $file->getClientOriginalName();
            $localFilePath = $tempDirectory . '/' . $originalFileName;

            // Save original upload locally for processing
            $file->move($tempDirectory, $originalFileName);

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

                // Reference Audio Logic
                $referenceFileUrl = null;
                if ($matchedId) {
                    $allFilesInDir = array_diff(scandir($tempDirectory), ['.', '..']);
                    $otherFiles = [];
                    foreach ($allFilesInDir as $f) {
                        if (str_starts_with($f, $matchedId . '_') && $f !== $finalFileName) {
                            $otherFiles[] = $f;
                        }
                    }
                    if (!empty($otherFiles)) {
                        usort($otherFiles, fn($a, $b) => filemtime($tempDirectory . '/' . $a) <=> filemtime($tempDirectory . '/' . $b));
                        $referenceFile = $otherFiles[0];
                        $referenceFileUrl = route('user.voice-pint.stream', $referenceFile);
                    }
                }

                // History grouping
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

                // Cleanup original
                if (file_exists($localFilePath)) {
                    unlink($localFilePath);
                }

                $results[] = [
                    'file_url'           => route('user.voice-pint.stream', $finalFileName),
                    'reference_file_url' => $referenceFileUrl,
                    'voice_info'         => $voiceInfo,
                    'related_files'      => $relatedFiles,
                    'filename'           => $finalFileName
                ];

            } catch (Exception $e) {
                Log::error('Voice Identification Error for ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
                if (file_exists($localFilePath)) unlink($localFilePath);
                if (isset($processedWavPath) && file_exists($processedWavPath)) unlink($processedWavPath);
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        if (empty($results)) {
            return back()->with('error', 'Failed to process any files. ' . implode(', ', $errors));
        }

        // Return with information from the last processed file for the summary card
        $lastResult = end($results);
        $message = count($results) . ' file(s) processed successfully.';
        if (!empty($errors)) {
            $message .= ' (' . count($errors) . ' failed)';
        }

        return back()->with('success', $message)
                     ->with('batch_results', $results)
                     ->with('file_url', $lastResult['file_url'])
                     ->with('reference_file_url', $lastResult['reference_file_url'])
                     ->with('voice_info', $lastResult['voice_info'])
                     ->with('related_files', $lastResult['related_files']);
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
