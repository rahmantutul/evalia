<?php

namespace App\Http\Controllers;

use App\Services\HamsaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HamsaWebController extends Controller
{
    protected HamsaService $hamsaService;

    public function __construct(HamsaService $hamsaService)
    {
        $this->hamsaService = $hamsaService;
    }

    public function dashboard()
    {
        // Get usage stats for dashboard
        $usage = $this->hamsaService->makeRequest('get', '/usage/numbers');
        $recentJobs = $this->hamsaService->makeRequest('get', '/jobs?limit=5');
        
        return view('hamsa.dashboard', [
            'usage' => $usage['success'] ? $usage['data'] : [],
            'recentJobs' => $recentJobs['success'] ? $recentJobs['data'] : []
        ]);
    }

    public function transcribe()
    {
        return view('hamsa.transcribe');
    }

    public function transcribeSubmit(Request $request)
    {
        $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg,flac|max:102400',
            'language' => 'sometimes|string',
            'model' => 'sometimes|string',
        ]);

        $result = $this->hamsaService->makeFileRequest('post', '/transcribe', [
            [
                'name' => 'audio_file',
                'contents' => fopen($request->file('audio_file')->getRealPath(), 'r'),
                'filename' => $request->file('audio_file')->getClientOriginalName(),
            ],
            [
                'name' => 'language',
                'contents' => $request->get('language', 'en'),
            ],
            [
                'name' => 'model',
                'contents' => $request->get('model', 'whisper-1'),
            ]
        ]);

        if ($result['success']) {
            return back()->with('success', 'Transcription started! Job ID: ' . $result['data']['job_id'])
                        ->with('job_id', $result['data']['job_id']);
        }

        return back()->with('error', 'Transcription failed: ' . $result['error']);
    }

    public function tts()
    {
        return view('hamsa.tts');
    }

    public function ttsSubmit(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'voice' => 'required|string|in:alloy,echo,fable,onyx,nova,shimmer',
            'model' => 'sometimes|string',
            'speed' => 'sometimes|numeric|between:0.25,4.0',
        ]);

        $result = $this->hamsaService->makeRequest('post', '/tts', $request->all());

        if ($result['success']) {
            return back()->with('success', 'TTS conversion started!')
                        ->with('audio_url', $result['data']['audio_url'] ?? null)
                        ->with('job_id', $result['data']['job_id'] ?? null);
        }

        return back()->with('error', 'TTS conversion failed: ' . $result['error']);
    }

    public function translate()
    {
        return view('hamsa.translate');
    }

    public function translateSubmit(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:5000',
            'target_language' => 'required|string|max:10',
            'source_language' => 'sometimes|string|max:10',
        ]);

        $result = $this->hamsaService->makeRequest('post', '/translate', $request->all());

        if ($result['success']) {
            return back()->with('success', 'Translation completed!')
                        ->with('translated_text', $result['data']['translated_text'] ?? '');
        }

        return back()->with('error', 'Translation failed: ' . $result['error']);
    }

    public function sts()
    {
        return view('hamsa.sts');
    }

    public function stsSubmit(Request $request)
    {
        $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg,flac|max:102400',
            'target_voice' => 'required|string|in:alloy,echo,fable,onyx,nova,shimmer',
        ]);

        $result = $this->hamsaService->makeFileRequest('post', '/sts', [
            [
                'name' => 'audio_file',
                'contents' => fopen($request->file('audio_file')->getRealPath(), 'r'),
                'filename' => $request->file('audio_file')->getClientOriginalName(),
            ],
            [
                'name' => 'target_voice',
                'contents' => $request->get('target_voice'),
            ]
        ]);

        if ($result['success']) {
            return back()->with('success', 'Voice conversion started! Job ID: ' . $result['data']['job_id'])
                        ->with('job_id', $result['data']['job_id']);
        }

        return back()->with('error', 'Voice conversion failed: ' . $result['error']);
    }

    public function aiGenerate()
    {
        return view('hamsa.ai-generate');
    }

    public function aiGenerateSubmit(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:4000',
            'model' => 'sometimes|string',
            'max_tokens' => 'sometimes|integer',
        ]);

        $result = $this->hamsaService->makeRequest('post', '/ai/generate', $request->all());

        if ($result['success']) {
            return back()->with('success', 'Content generated successfully!')
                        ->with('generated_content', $result['data']['generated_text'] ?? '');
        }

        return back()->with('error', 'AI generation failed: ' . $result['error']);
    }

    public function voiceAgents()
    {
        $result = $this->hamsaService->makeRequest('get', '/voice-agents');
        $agents = $result['success'] ? $result['data'] : [];

        return view('hamsa.voice-agents', compact('agents'));
    }

    public function createVoiceAgent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'voice' => 'required|string|in:alloy,echo,fable,onyx,nova,shimmer',
            'language' => 'required|string|max:10',
            'prompt' => 'required|string|max:4000',
        ]);

        $result = $this->hamsaService->makeRequest('post', '/voice-agents', $request->all());

        if ($result['success']) {
            return back()->with('success', 'Voice agent created successfully!');
        }

        return back()->with('error', 'Failed to create voice agent: ' . $result['error']);
    }

    public function conversations()
    {
        return view('hamsa.conversations');
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|string',
            'user_phone_number' => 'required|string|max:20',
        ]);

        $result = $this->hamsaService->makeRequest('post', '/conversations/start', $request->all());

        if ($result['success']) {
            return back()->with('success', 'Conversation started! ID: ' . $result['data']['conversation_id']);
        }

        return back()->with('error', 'Failed to start conversation: ' . $result['error']);
    }

    public function jobs()
    {
        $result = $this->hamsaService->makeRequest('get', '/jobs?limit=20');
        $jobs = $result['success'] ? $result['data'] : [];

        return view('hamsa.jobs', compact('jobs'));
    }

    public function usage()
    {
        $numbers = $this->hamsaService->makeRequest('get', '/usage/numbers');
        $charts = $this->hamsaService->makeRequest('get', '/usage/charts?period=week');

        return view('hamsa.usage', [
            'numbers' => $numbers['success'] ? $numbers['data'] : [],
            'charts' => $charts['success'] ? $charts['data'] : []
        ]);
    }

    public function project()
    {
        $result = $this->hamsaService->makeRequest('get', '/project');
        $project = $result['success'] ? $result['data'] : [];

        return view('hamsa.project', compact('project'));
    }
}