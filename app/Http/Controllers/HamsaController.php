<?php

namespace App\Http\Controllers;

use App\Services\HamsaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

/**
 * Hamsa API Controller
 * 
 * Handles all web and API routes for Hamsa integration
 * 
 * @package App\Http\Controllers
 */
class HamsaController extends Controller
{
    /**
     * @var HamsaService
     */
    protected HamsaService $hamsaService;

    /**
     * Constructor with dependency injection
     * 
     * @param HamsaService $hamsaService
     */
    public function __construct(HamsaService $hamsaService)
    {
        $this->hamsaService = $hamsaService;
    }

    // ==================== WEB PAGES ====================

    /**
     * Show main dashboard with usage stats
     * 
     * @return View
     */
    public function dashboard(): View
    {
        $usage = $this->hamsaService->makeRequest('get', '/usage/numbers');
        $recentJobs = $this->hamsaService->makeRequest('get', '/jobs', ['limit' => 5]);
        
        return view('hamsa.dashboard', [
            'usage' => $usage['success'] ? $usage['data'] : [],
            'recentJobs' => $recentJobs['success'] ? ($recentJobs['data']['results'] ?? []) : [],
            'usageError' => !$usage['success'] ? $usage['error'] : null,
            'jobsError' => !$recentJobs['success'] ? $recentJobs['error'] : null,
        ]);
    }

    /**
     * Show transcription page
     * 
     * @return View
     */
    public function transcribe(): View
    {
        return view('hamsa.transcribe');
    }

    /**
     * Show text-to-speech page
     * 
     * @return View
     */
    public function tts(): View
    {
        return view('hamsa.tts');
    }

    /**
     * Show translation page
     * 
     * @return View
     */
    public function translate(): View
    {
        return view('hamsa.translate');
    }

    /**
     * Show speech-to-speech page
     * 
     * @return View
     */
    public function sts(): View
    {
        return view('hamsa.sts');
    }

    /**
     * Show AI generation page
     * 
     * @return View
     */
    public function aiGenerate(): View
    {
        return view('hamsa.ai-generate');
    }

    /**
     * Show voice agents list
     * 
     * @return View
     */
    public function voiceAgents(): View
    {
        $result = $this->hamsaService->makeRequest('get', '/voice-agents');
        $agents = $result['success'] ? ($result['data']['results'] ?? $result['data']) : [];

        return view('hamsa.voice-agents', [
            'agents' => $agents,
            'error' => !$result['success'] ? $result['error'] : null,
        ]);
    }

    /**
     * Show conversations page
     * 
     * @return View
     */
    public function conversations(): View
    {
        return view('hamsa.conversations');
    }

    /**
     * Show jobs list
     * 
     * @return View
     */
    public function jobs(): View
    {
        $result = $this->hamsaService->makeRequest('get', '/jobs', ['limit' => 50]);
        $jobs = $result['success'] ? ($result['data']['results'] ?? $result['data']) : [];

        return view('hamsa.jobs', [
            'jobs' => $jobs,
            'error' => !$result['success'] ? $result['error'] : null,
        ]);
    }

    /**
     * Show usage statistics
     * 
     * @return View
     */
    public function usage(): View
    {
        $numbers = $this->hamsaService->makeRequest('get', '/usage/numbers');
        $charts = $this->hamsaService->makeRequest('get', '/usage/charts', ['period' => 'week']);

        return view('hamsa.usage', [
            'numbers' => $numbers['success'] ? $numbers['data'] : [],
            'charts' => $charts['success'] ? $charts['data'] : [],
            'numbersError' => !$numbers['success'] ? $numbers['error'] : null,
            'chartsError' => !$charts['success'] ? $charts['error'] : null,
        ]);
    }

    /**
     * Show project details
     * 
     * @return View
     */
    public function project(): View
    {
        $result = $this->hamsaService->makeRequest('get', '/project');
        $project = $result['success'] ? $result['data'] : [];

        return view('hamsa.project', [
            'project' => $project,
            'error' => !$result['success'] ? $result['error'] : null,
        ]);
    }

    // ==================== FORM SUBMISSIONS ====================

    /**
     * Submit audio file for transcription
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function transcribeSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg,flac,webm|max:102400',
            'language' => 'nullable|string|max:10',
            'model' => 'nullable|string|in:whisper-1,whisper-large',
            'response_format' => 'nullable|string|in:json,text,srt,vtt',
        ]);

        try {
            $multipartData = [
                [
                    'name' => 'audio_file',
                    'contents' => fopen($request->file('audio_file')->getRealPath(), 'r'),
                    'filename' => $request->file('audio_file')->getClientOriginalName(),
                ]
            ];

            // Add optional fields
            if ($request->filled('language')) {
                $multipartData[] = [
                    'name' => 'language',
                    'contents' => $request->get('language'),
                ];
            }

            if ($request->filled('model')) {
                $multipartData[] = [
                    'name' => 'model',
                    'contents' => $request->get('model'),
                ];
            }

            if ($request->filled('response_format')) {
                $multipartData[] = [
                    'name' => 'response_format',
                    'contents' => $request->get('response_format'),
                ];
            }

            $result = $this->hamsaService->makeFileRequest('post', '/transcribe', $multipartData);

            if ($result['success']) {
                $data = $result['data'];
                return back()
                    ->with('success', 'Transcription started successfully!')
                    ->with('job_id', $data['job_id'] ?? null)
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'Transcription failed: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit text for text-to-speech conversion
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function ttsSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
            'voice' => 'required|string|in:alloy,echo,fable,onyx,nova,shimmer',
            'model' => 'nullable|string|in:tts-1,tts-1-hd',
            'speed' => 'nullable|numeric|between:0.25,4.0',
            'response_format' => 'nullable|string|in:mp3,opus,aac,flac,wav',
        ]);

        try {
            $payload = [
                'text' => $request->get('text'),
                'voice' => $request->get('voice'),
            ];

            if ($request->filled('model')) {
                $payload['model'] = $request->get('model');
            }

            if ($request->filled('speed')) {
                $payload['speed'] = (float) $request->get('speed');
            }

            if ($request->filled('response_format')) {
                $payload['response_format'] = $request->get('response_format');
            }

            $result = $this->hamsaService->makeRequest('post', '/tts', $payload);

            if ($result['success']) {
                $data = $result['data'];
                return back()
                    ->with('success', 'Text-to-speech conversion successful!')
                    ->with('audio_url', $data['audio_url'] ?? null)
                    ->with('job_id', $data['job_id'] ?? null)
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'TTS conversion failed: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit text for translation
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function translateSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
            'target_language' => 'required|string|max:10',
            'source_language' => 'nullable|string|max:10',
            'model' => 'nullable|string',
        ]);

        try {
            $result = $this->hamsaService->makeRequest('post', '/translate', $validated);

            if ($result['success']) {
                $data = $result['data'];
                return back()
                    ->with('success', 'Translation completed successfully!')
                    ->with('translated_text', $data['translated_text'] ?? $data['translation'] ?? '')
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'Translation failed: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit audio for speech-to-speech conversion
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function stsSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg,flac,webm|max:102400',
            'target_voice' => 'required|string|in:alloy,echo,fable,onyx,nova,shimmer',
            'target_language' => 'nullable|string|max:10',
            'source_language' => 'nullable|string|max:10',
        ]);

        try {
            $multipartData = [
                [
                    'name' => 'audio_file',
                    'contents' => fopen($request->file('audio_file')->getRealPath(), 'r'),
                    'filename' => $request->file('audio_file')->getClientOriginalName(),
                ],
                [
                    'name' => 'target_voice',
                    'contents' => $request->get('target_voice'),
                ],
            ];

            if ($request->filled('target_language')) {
                $multipartData[] = [
                    'name' => 'target_language',
                    'contents' => $request->get('target_language'),
                ];
            }

            if ($request->filled('source_language')) {
                $multipartData[] = [
                    'name' => 'source_language',
                    'contents' => $request->get('source_language'),
                ];
            }

            $result = $this->hamsaService->makeFileRequest('post', '/sts', $multipartData);

            if ($result['success']) {
                $data = $result['data'];
                return back()
                    ->with('success', 'Speech-to-speech conversion started!')
                    ->with('job_id', $data['job_id'] ?? null)
                    ->with('audio_url', $data['audio_url'] ?? null)
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'Voice conversion failed: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit prompt for AI content generation
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function aiGenerateSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:4000',
            'model' => 'nullable|string',
            'max_tokens' => 'nullable|integer|min:1|max:4096',
            'temperature' => 'nullable|numeric|between:0,2',
        ]);

        try {
            $result = $this->hamsaService->makeRequest('post', '/ai/generate', $validated);

            if ($result['success']) {
                $data = $result['data'];
                return back()
                    ->with('success', 'Content generated successfully!')
                    ->with('generated_content', $data['generated_text'] ?? $data['text'] ?? $data['content'] ?? '')
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'AI generation failed: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create a new voice agent
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function createVoiceAgent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'voice' => 'required|string|in:alloy,echo,fable,onyx,nova,shimmer',
            'language' => 'required|string|max:10',
            'prompt' => 'required|string|max:4000',
            'greeting_message' => 'nullable|string|max:500',
            'model' => 'nullable|string',
        ]);

        try {
            $result = $this->hamsaService->makeRequest('post', '/voice-agents', $validated);

            if ($result['success']) {
                return redirect()
                    ->route('hamsa.voice-agents')
                    ->with('success', 'Voice agent created successfully!');
            }

            return back()
                ->with('error', 'Failed to create voice agent: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing voice agent
     * 
     * @param Request $request
     * @param string $agentId
     * @return RedirectResponse
     */
    public function updateVoiceAgent(Request $request, string $agentId): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'voice' => 'sometimes|string|in:alloy,echo,fable,onyx,nova,shimmer',
            'language' => 'sometimes|string|max:10',
            'prompt' => 'sometimes|string|max:4000',
            'greeting_message' => 'nullable|string|max:500',
            'model' => 'nullable|string',
        ]);

        try {
            $result = $this->hamsaService->makeRequest('put', "/voice-agents/{$agentId}", $validated);

            if ($result['success']) {
                return back()->with('success', 'Voice agent updated successfully!');
            }

            return back()
                ->with('error', 'Failed to update voice agent: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a voice agent
     * 
     * @param string $agentId
     * @return RedirectResponse
     */
    public function deleteVoiceAgent(string $agentId): RedirectResponse
    {
        try {
            $result = $this->hamsaService->makeRequest('delete', "/voice-agents/{$agentId}");

            if ($result['success']) {
                return redirect()
                    ->route('hamsa.voice-agents')
                    ->with('success', 'Voice agent deleted successfully!');
            }

            return back()->with('error', 'Failed to delete voice agent: ' . $result['error']);

        } catch (Exception $e) {
            return back()->with('error', 'Exception occurred: ' . $e->getMessage());
        }
    }

    /**
     * Start a new conversation with voice agent
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function startConversation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'agent_id' => 'required|string',
            'user_phone_number' => 'required|string|max:20',
            'metadata' => 'nullable|array',
        ]);

        try {
            $result = $this->hamsaService->makeRequest('post', '/conversations/start', $validated);

            if ($result['success']) {
                $data = $result['data'];
                return back()
                    ->with('success', 'Conversation started! ID: ' . ($data['conversation_id'] ?? 'N/A'))
                    ->with('conversation_id', $data['conversation_id'] ?? null)
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'Failed to start conversation: ' . $result['error'])
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Stop an active conversation
     * 
     * @param string $conversationId
     * @return RedirectResponse
     */
    public function stopConversation(string $conversationId): RedirectResponse
    {
        try {
            $result = $this->hamsaService->makeRequest('post', "/conversations/{$conversationId}/stop");

            if ($result['success']) {
                return back()->with('success', 'Conversation stopped successfully!');
            }

            return back()->with('error', 'Failed to stop conversation: ' . $result['error']);

        } catch (Exception $e) {
            return back()->with('error', 'Exception occurred: ' . $e->getMessage());
        }
    }

    // ==================== API/AJAX ENDPOINTS ====================

    /**
     * Get transcription job details (API)
     * 
     * @param string $jobId
     * @return JsonResponse
     */
    public function getTranscriptionJob(string $jobId): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', "/transcribe/{$jobId}");
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve transcription job'
        ], $result['status']);
    }

    /**
     * Get TTS job details (API)
     * 
     * @param string $jobId
     * @return JsonResponse
     */
    public function getTtsJob(string $jobId): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', "/tts/{$jobId}");
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve TTS job'
        ], $result['status']);
    }

    /**
     * Get speech-to-speech job details (API)
     * 
     * @param string $jobId
     * @return JsonResponse
     */
    public function getStsJob(string $jobId): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', "/sts/{$jobId}");
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve STS job'
        ], $result['status']);
    }

    /**
     * Get general job details (API)
     * 
     * @param string $jobId
     * @return JsonResponse
     */
    public function getJob(string $jobId): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', "/jobs/{$jobId}");
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve job'
        ], $result['status']);
    }

    /**
     * Get all jobs (API)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getJobs(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        
        $result = $this->hamsaService->makeRequest('get', '/jobs', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve jobs'
        ], $result['status']);
    }

    /**
     * Get voice agent details (API)
     * 
     * @param string $agentId
     * @return JsonResponse
     */
    public function getVoiceAgent(string $agentId): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', "/voice-agents/{$agentId}");
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve voice agent'
        ], $result['status']);
    }

    /**
     * Get all voice agents (API)
     * 
     * @return JsonResponse
     */
    public function getVoiceAgents(): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', '/voice-agents');
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve voice agents'
        ], $result['status']);
    }

    /**
     * Get conversation details (API)
     * 
     * @param string $conversationId
     * @return JsonResponse
     */
    public function getConversation(string $conversationId): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', "/conversations/{$conversationId}");
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve conversation'
        ], $result['status']);
    }

    /**
     * Get usage statistics (API)
     * 
     * @return JsonResponse
     */
    public function getUsageNumbers(): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', '/usage/numbers');
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve usage statistics'
        ], $result['status']);
    }

    /**
     * Get usage charts (API)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsageCharts(Request $request): JsonResponse
    {
        $period = $request->get('period', 'week');
        
        $result = $this->hamsaService->makeRequest('get', '/usage/charts', [
            'period' => $period,
        ]);
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve usage charts'
        ], $result['status']);
    }

    /**
     * Get project details (API)
     * 
     * @return JsonResponse
     */
    public function getProject(): JsonResponse
    {
        $result = $this->hamsaService->makeRequest('get', '/project');
        
        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => $result['error'],
            'message' => 'Failed to retrieve project details'
        ], $result['status']);
    }

    /**
     * Test API connection
     * 
     * @return JsonResponse
     */
    public function testConnection(): JsonResponse
    {
        try {
            $result = $this->hamsaService->testConnection();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully connected to Hamsa API',
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to Hamsa API',
                'error' => $result['error'],
            ], $result['status']);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred while testing connection',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}