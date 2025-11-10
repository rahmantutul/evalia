<?php

namespace App\Http\Controllers;

use App\Services\HamsaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
class HamsaController extends Controller
{
    protected HamsaService $hamsaService;

    public function __construct(HamsaService $hamsaService)
    {
        $this->hamsaService = $hamsaService;
    }

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

    public function transcribe(): View
    {
        return view('hamsa.transcribe');
    }


    public function sts(): View
    {
        return view('hamsa.sts');
    }

    public function aiGenerate(): View
    {
        return view('hamsa.ai-generate');
    }



    public function jobs(Request $request): View
    {
        $projectId = '605ee5e1-3e22-41df-aa95-5d705a359dbd';
        
        $queryParams = [
            'projectId' => $projectId
        ];

        $take = 30;
        $page = max(1, (int) $request->get('page', 1));
        
        $skip = $page;

        $requestBody = [
            'take' => $take,
            'skip' => $skip,
            'sort' => [
                'field' => 'createdAt',
                'direction' => 'desc'
            ]
        ];

        if ($request->filled('search')) {
            $requestBody['search'] = $request->get('search');
        }

        if ($request->filled('status')) {
            $requestBody['status'] = $request->get('status');
        }

        if ($request->filled('type')) {
            $requestBody['type'] = $request->get('type');
        }

        $endpoint = '/jobs/all?' . http_build_query($queryParams);
        $result = $this->hamsaService->makeRequest('POST', $endpoint, $requestBody);
        
        $jobs = [];
        $pagination = [];
        $error = null;
        
        if ($result['success']) {
            $apiData = $result['data']['data'] ?? [];
            $jobs = $apiData['jobs'] ?? [];
            $total = $apiData['total'] ?? 0;
            $filtered = count($jobs);

            $pagination = [
                'total' => $total,
                'filtered' => $filtered,
                'take' => $take,
                'skip' => ($page - 1) * $take,  // For display: showing X to Y
                'page' => $page,
                'totalPages' => $total > 0 ? ceil($total / $take) : 1,
            ];
        } else {
            $error = $result['error'] ?? 'Unknown error occurred';
        }

        $availableStatuses = ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED'];
        $availableTypes = ['TRANSCRIPTION', 'TTS', 'VOICE_AGENTS', 'AI_CONTENT'];
        
        return view('hamsa.jobs', [
            'jobs' => $jobs,
            'pagination' => $pagination,
            'filters' => $request->all(),
            'availableStatuses' => $availableStatuses,
            'availableTypes' => $availableTypes,
            'error' => $error,
        ]);
    }



    public function tts(): View
    {
         $result = $this->hamsaService->makeRequest('get', '/voice-agents');

        if ($result['success'] && isset($result['data']['data']['voiceAgents'])) {
            $agents = $result['data']['data']['voiceAgents'];
        } else {
            $agents = [];
        }
        return view('hamsa.tts',compact('agents'));
    }

       public function ttsSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
            'voice' => 'required',
        ]);

        try {
            $payload = [
                'voiceId' => $request->get('voice'), 
                'text' => $request->get('text'),
            ];

            if ($request->filled('model')) {
                $payload['model'] = $request->get('model');
            }

            if ($request->filled('speed')) {
                $payload['speed'] = (float) $request->get('speed');
            }

            if ($request->filled('response_format')) {
                $payload['responseFormat'] = $request->get('response_format');
            }

            $result = $this->hamsaService->makeRequest('post', '/jobs/text-to-speech', $payload);

            if ($result['success']) {
                $data = $result['data'];
                
                return back()
                    ->with('success', 'Text-to-speech job created successfully!')
                    ->with('job_id', $data['id'] ?? null)
                    ->with('status', $data['status'] ?? null)
                    ->with('media_url', $data['mediaUrl'] ?? null)
                    ->with('result_data', $data);
            }

            return back()
                ->with('error', 'TTS conversion failed: ' . ($result['error'] ?? $result['message'] ?? 'Unknown error'))
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function translate(): View
    {
        return view('hamsa.translate');
    }

    public function translateSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
        'mediaUrl' => 'required',
        'title' => 'nullable',
        'language' => 'required',
        'model' => 'required',
        'processingType' => 'required|string|in:async,sync',
        'webhookUrl' => 'nullable',
        'returnSrtFormat' => 'nullable',
        'srtOptions' => 'nullable',
        'srtOptions.maxLinesPerSubtitle' => 'nullable|integer|min:1|max:10',
        'srtOptions.singleSpeakerPerSubtitle' => 'nullable|boolean',
        'srtOptions.maxCharsPerLine' => 'nullable|integer|min:1|max:100',
        'srtOptions.maxMergeableGap' => 'nullable|numeric|min:0|max:10',
        'srtOptions.minDuration' => 'nullable|numeric|min:0|max:60',
        'srtOptions.maxDuration' => 'nullable|numeric|min:0|max:60',
        'srtOptions.minGap' => 'nullable|numeric|min:0|max:10',
    ]);

    try {
        // Prepare the request payload
        $payload = [
            'mediaUrl' => $validated['mediaUrl'],
            'processingType' => $validated['processingType'],
            'model' => $validated['model'],
            'language' => $validated['language'],
            'returnSrtFormat' => isset($validated['returnSrtFormat']) && $validated['returnSrtFormat'] == '1',
        ];

        // Add optional fields
        if (!empty($validated['title'])) {
            $payload['title'] = $validated['title'];
        }

        if (!empty($validated['webhookUrl'])) {
            $payload['webhookUrl'] = $validated['webhookUrl'];
        }

        // Add SRT options if returnSrtFormat is true
        if ($payload['returnSrtFormat'] && !empty($validated['srtOptions'])) {
            $srtOptions = [];
            
            if (isset($validated['srtOptions']['maxLinesPerSubtitle'])) {
                $srtOptions['maxLinesPerSubtitle'] = (int) $validated['srtOptions']['maxLinesPerSubtitle'];
            }
            
            if (isset($validated['srtOptions']['singleSpeakerPerSubtitle'])) {
                $srtOptions['singleSpeakerPerSubtitle'] = (bool) $validated['srtOptions']['singleSpeakerPerSubtitle'];
            }
            
            if (isset($validated['srtOptions']['maxCharsPerLine'])) {
                $srtOptions['maxCharsPerLine'] = (int) $validated['srtOptions']['maxCharsPerLine'];
            }
            
            if (isset($validated['srtOptions']['maxMergeableGap'])) {
                $srtOptions['maxMergeableGap'] = (float) $validated['srtOptions']['maxMergeableGap'];
            }
            
            if (isset($validated['srtOptions']['minDuration'])) {
                $srtOptions['minDuration'] = (float) $validated['srtOptions']['minDuration'];
            }
            
            if (isset($validated['srtOptions']['maxDuration'])) {
                $srtOptions['maxDuration'] = (float) $validated['srtOptions']['maxDuration'];
            }
            
            if (isset($validated['srtOptions']['minGap'])) {
                $srtOptions['minGap'] = (float) $validated['srtOptions']['minGap'];
            }

            if (!empty($srtOptions)) {
                $payload['srtOptions'] = $srtOptions;
            }
        }

        // Make the API request
        $result = $this->hamsaService->makeRequest('post', '/jobs/transcribe', $payload);

        if ($result['success']) {
            $data = $result['data'];
            
            // Store the transcription result if available
            $transcriptionText = $data['transcription'] ?? $data['text'] ?? '';
            
            return back()
                ->with('success', 'Transcription job submitted successfully!')
                ->with('transcription_text', $transcriptionText)
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

    public function voiceAgents(): View
    {
        $result = $this->hamsaService->makeRequest('get', '/voice-agents');

        if ($result['success'] && isset($result['data']['data']['voiceAgents'])) {
            $agents = $result['data']['data']['voiceAgents'];
        } else {
            $agents = [];
        }

        return view('hamsa.voice-agents', [
            'agents' => $agents,
            'error' => !$result['success'] ? ($result['error'] ?? 'Unknown error') : null,
        ]);
    }
    
    public function createVoiceAgent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'voice_id' => 'required|string',
            'language' => 'required|string|max:10',
            'prompt' => 'required|string|max:4000',
            'greeting_message' => 'nullable|string|max:500',
        ]);

        try {
            $apiData = [
                'agentName' => $validated['name'],
                'voiceId' => $validated['voice_id'],
                'lang' => $validated['language'],
                'preamble' => $validated['prompt'],
                'greetingMessage' => $validated['greeting_message'] ?? 'Hello, how can I help you today?',
                'interrupt' => $validated['allow_interruptions'] ?? true,
                'silenceTimeout' => 800,
                'maxDuration' => 600,
                'webhook' => '',
                'model' => 'gpt-4',
                'silenceThreshold' => 800,
                'realTime' => false,
                'pokeMessages' => [],
                'outcome' => null,
                'params' => new \stdClass(),
                'tools' => [
                    'genderDetection' => false,
                    'smartCallEnd' => false
                ]
            ];

            $result = $this->hamsaService->makeRequest('post', '/voice-agents', $apiData);
            
            if ($result['success']) {
                return redirect()
                    ->route('hamsa.voice-agents')
                    ->with('success', 'Voice agent created successfully!');
            }

            return back()
                ->with('error', 'Failed to create voice agent: ' . ($result['error'] ?? 'Unknown error'))
                ->withInput();

        } catch (Exception $e) {
            return back()
                ->with('error', 'Exception occurred: ' . $e->getMessage())
                ->withInput();
        }
    }














    














    public function conversations(): View
    {
        return view('hamsa.conversations');
    }

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

    public function project(): View
    {
        $result = $this->hamsaService->makeRequest('get', '/project');
        $project = $result['success'] ? $result['data'] : [];

        return view('hamsa.project', [
            'project' => $project,
            'error' => !$result['success'] ? $result['error'] : null,
        ]);
    }

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

    public function getJob(string $jobId)
    {
        $endpoint = "/jobs";
        
        // Pass jobId as query parameters for GET request
        $queryParams = [
            'jobId' => $jobId
        ];
        
        $result = $this->hamsaService->makeRequest('GET', $endpoint, $queryParams);
        
        if ($result['success']) {
            // dd($result['data']['data']['data']);
            // Extract the actual job data from the nested response
            $jobData = $result['data']['data']['data'] ?? $result['data']['data'] ?? [];
            
            return view('hamsa.job-details', [
                'job' => $jobData
            ]);
        }

        return view('hamsa.job-details', [
            'error' => $result['error'] ?? 'Failed to retrieve job'
        ]);
    }

    
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