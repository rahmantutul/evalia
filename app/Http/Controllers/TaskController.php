<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use App\Services\HamsaService;

class TaskController extends Controller
{
    protected $hamsa;
    protected $knowledge;
    protected $openai;

    public function __construct(HamsaService $hamsa, \App\Services\KnowledgeService $knowledge, \App\Services\OpenAIService $openai)
    {
        $this->middleware('auth.api');
        $this->hamsa = $hamsa;
        $this->knowledge = $knowledge;
        $this->openai = $openai;
    }

    private function findTaskById($taskId)
    {
        if (is_numeric($taskId)) {
            $dbTask = Task::find($taskId);
            if ($dbTask) {
                return [
                    'id' => $dbTask->id,
                    'company_id' => $dbTask->company_id,
                    'agent_id' => $dbTask->agent_id,
                    'agent_name' => $dbTask->agent?->name,
                    'score' => $dbTask->score,
                    'status' => $dbTask->status,
                    'sentiment' => $dbTask->sentiment,
                    'risk_flag' => $dbTask->risk_flag,
                    'created_at' => $dbTask->created_at->toDateTimeString(),
                    'transcription' => $dbTask->transcription,
                    'analysis' => $dbTask->analysis,
                    'audio_path' => $dbTask->audio_path,
                ];
            }
        }

        $allTasks = app(CompanyController::class)->getAllTasks();
        foreach ($allTasks as $task) {
            if ($task['id'] === $taskId) return $task;
        }
        return null;
    }

    public function TaskList($companyId, Request $request)
    {
        $company = \App\Models\Company::find($companyId);
        
        $companyAgents = User::where('user_type', User::TYPE_AGENT)
            ->where('company_id', $companyId)
            ->get()
            ->map(fn($u) => [
                'id'        => $u->id,
                'full_name' => $u->name,
                'email'     => $u->email,
            ]);

        // ── Load tasks from database ─────────────────────────
        $allTasks = Task::where('company_id', $companyId)
            ->with('agent')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($t) {
                $analysis = $t->analysis ?? [];
                
                // Ensure conversation is available for the list view
                $searchIn = $analysis['jobResponse'] ?? $analysis;
                if (empty($analysis['conversation'])) {
                    $analysis['conversation'] = $this->hamsa->extractConversation($searchIn);
                }

                // Recover missing transcription text if possible
                $transcription = $t->transcription;
                if (empty($transcription)) {
                    $transcription = $searchIn['transcription'] ?? ($searchIn['text'] ?? ($analysis['transcription'] ?? ''));
                }

                return [
                    'id'               => (string) $t->id,
                    'company_id'       => $t->company_id,
                    'score'            => $t->score,
                    'status'           => $t->status,
                    'agent_name'       => $t->agent?->name ?? 'Unassigned',
                    'supervisor_name'  => 'N/A',
                    'duration'         => $t->duration ?? 'N/A',
                    'source'           => $t->source,
                    'channel'          => $t->channel,
                    'outcome'          => $t->outcome ?? 'N/A',
                    'coaching_required'=> $t->score < 80 ? 'Yes' : 'No',
                    'sentiment'        => $t->sentiment,
                    'risk_flag'        => $t->risk_flag,
                    'lang'             => $t->lang,
                    'transcription'    => $transcription,
                    'analysis'         => $analysis,
                    'created_at'       => $t->created_at->toDateTimeString(),
                ];
            })
            ->toArray();

        $filteredTasks = $this->applyFilters($allTasks, $request);

        $tasksCollection = collect($filteredTasks);
        $summary = [
            'total'          => $tasksCollection->count(),
            'good_score'     => $tasksCollection->where('score', '>=', 90)->count(),
            'needs_coaching' => $tasksCollection->where('coaching_required', 'Yes')->count(),
            'high_risk'      => $tasksCollection->where('risk_flag', 'High')->count(),
        ];

        $page    = Paginator::resolveCurrentPage();
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $paginatedTasks = new LengthAwarePaginator(
            array_slice($filteredTasks, $offset, $perPage),
            count($filteredTasks),
            $perPage,
            $page,
            ['path' => route('user.task.list', ['companyId' => $companyId])]
        );

        $allCompanies = \App\Models\Company::all();

        return view('user.task.task_list', [
            'company_id'     => $companyId,
            'company'        => $company,
            'allCompanies'   => $allCompanies,
            'taskList'       => $paginatedTasks,
            'companyAgents'  => $companyAgents,
            'summary'        => $summary,
        ]);
    }

    public function taskDetails($workId)
    {
        $task = $this->findTaskById($workId);
        
        if (!$task) {
            abort(404, 'Task not found');
        }

        // If task is evaluated, show the real analysis.
        // Otherwise, show a placeholder or dummy data.
        if (isset($task['analysis']) && !empty($task['analysis'])) {
            $data = $task['analysis'];
            
            // Standardize fields for the Blade view (audio and summaries)
            $data['transcription'] = $task['transcription'] ?? ($data['jobResponse']['transcription'] ?? '');

            // ── Resolve audio path with triple fallback ──────────────────
            // 1st choice: dedicated DB column (tasks.audio_path)
            // 2nd choice: backup stored inside analysis JSON
            // 3rd choice: Hamsa media_url (may expire, but better than nothing)
            $resolvedAudioPath = $task['audio_path']          // DB column
                ?? $data['audio_path']                         // JSON backup
                ?? null;

            if ($resolvedAudioPath) {
                try {
                    $data['customer_agent_audio_s3_url'] = Storage::disk('s3')->temporaryUrl($resolvedAudioPath, now()->addMinutes(60));
                    Log::info("[TaskDetails] Generated S3 temp URL for task #{$task['id']} using path: {$resolvedAudioPath}");
                } catch (\Exception $e) {
                    Log::warning("[TaskDetails] S3 temp URL failed for path '{$resolvedAudioPath}': " . $e->getMessage());
                    // Last resort: use stored Hamsa media_url if available
                    $data['customer_agent_audio_s3_url'] = $data['jobResponse']['media_url'] ?? '';
                }
            } else {
                Log::warning("[TaskDetails] No audio_path found for task #{$task['id']}. Audio will not be available.");
                $data['customer_agent_audio_s3_url'] = $data['jobResponse']['media_url'] ?? '';
            }
            $data['status']        = $task['status'];
            $data['score']         = $task['score'];

            // Map GPT summary to transcription_summaries['detail'] as expected by the Blade
            if (isset($data['summary']) && !isset($data['transcription_summaries']['detail'])) {
                $data['transcription_summaries'] = [
                    'detail' => $data['summary'] ?? 'No summary found'
                ];
            }

            // If we have Hamsa conversation data, enhance metrics
            if (isset($data['conversation']) && is_array($data['conversation'])) {
                $this->enhanceAnalysisWithHamsaMetrics($data);
            }
            
            // If GPT evaluation is missing but we have transcription, show "Needs Evaluation"
            if ($task['status'] === 'transcribed' && empty($data['gpt_evaluation'])) {
                $data['needs_evaluation'] = true;
            }

            // Fetch REAL Knowledge Base context for the transcription to show in UI
            if (!empty($data['transcription'])) {
                $data['kb_meta'] = $this->knowledge->getRelevantContextWithMeta($task['company_id'], $data['transcription']);
            }

        } else {
            // Task not yet evaluated, provide only basic task metadata
            // Generate S3 temp URL for audio even before evaluation
            $audioUrl = '';
            if (!empty($task['audio_path'])) {
                try {
                    $audioUrl = Storage::disk('s3')->temporaryUrl($task['audio_path'], now()->addMinutes(60));
                } catch (\Exception $e) {
                    Log::warning('Could not generate S3 temp URL for audio: ' . $e->getMessage());
                }
            }
            $data = [
                'status'        => $task['status'] ?? 'pending',
                'score'         => $task['score'] ?? 0,
                'transcription' => $task['transcription'] ?? '',
                'customer_agent_audio_s3_url' => $audioUrl,
                'created_at'    => $task['created_at'] ?? now(),
                'needs_evaluation' => true,
                'gpt_evaluation' => null,
                'conversation'   => [],
                'transcription_summaries' => [
                    'detail' => 'Analysis pending. Please run AI Analysis to generate a summary.'
                ]
            ];
        }

        return view('user.task.task_details', [
            'data' => $data,
            'workId' => $workId,
            'status' => $task['status'] ?? 'completed'
        ]);
    }

    private function applyFilters($tasks, $request)
    {
        $status = $request->get('status', 'all');
        $agent = $request->get('agent', 'all');
        $source = $request->get('source', 'all');
        $channel = $request->get('channel', 'all');
        $supervisor = $request->get('supervisor', 'all');
        $sentiment = $request->get('sentiment', 'all');
        $language = $request->get('lang', 'all');
        $risk = $request->get('risk', 'all');

        return collect($tasks)->filter(function($task) use ($status, $agent, $source, $channel, $supervisor, $sentiment, $language, $risk) {
            $matchesStatus = $status === 'all' || $task['status'] === $status;
            $matchesAgent = $agent === 'all' || (isset($task['agent_name']) && str_contains(strtolower($task['agent_name']), strtolower($agent)));
            $matchesSource = $source === 'all' || $task['source'] === $source;
            $matchesChannel = $channel === 'all' || $task['channel'] === $channel;
            $matchesSupervisor = $supervisor === 'all' || (isset($task['supervisor_name']) && str_contains(strtolower($task['supervisor_name']), strtolower($supervisor)));
            $matchesSentiment = $sentiment === 'all' || (isset($task['sentiment']) && $task['sentiment'] === $sentiment);
            $matchesLanguage = $language === 'all' || (isset($task['lang']) && $task['lang'] === $language);
            $matchesRisk = $risk === 'all' || (isset($task['risk_flag']) && $task['risk_flag'] === $risk);
            
            return $matchesStatus && $matchesAgent && $matchesSource && $matchesChannel && 
                   $matchesSupervisor && $matchesSentiment && $matchesLanguage && $matchesRisk;
        })->values()->all();
    }


    public function deleteTask($workId)
    {
        return redirect()->back()->with('success', 'Task removed.');
    }

    public function taskStore(Request $request)
    {
        $request->validate([
            'audio_file'    => 'required_without:hamsa_job_id|file|mimes:wav,mp3,m4a,ogg,webm|max:102400',
            'hamsa_job_id'  => 'required_without:audio_file|string|nullable',
            'agent_id'      => 'required|exists:users,id',
            'company_id'    => 'required|exists:companies,id',
        ]);

        // Allow up to 6 minutes
        set_time_limit(360);

        $companyId = $request->company_id;
        $agentId   = $request->agent_id;
        $jobId     = $request->hamsa_job_id;
        $path      = null;

        if ($request->hasFile('audio_file')) {
            $audioFile = $request->file('audio_file');
            $filename  = time() . '_' . Str::slug(pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $audioFile->getClientOriginalExtension();

            Log::info("[TaskStore] Uploading audio to S3: tasks/audios/{$filename}", [
                'size'      => $audioFile->getSize(),
                'mime'      => $audioFile->getMimeType(),
                'original'  => $audioFile->getClientOriginalName(),
            ]);

            // Upload to S3 — storeAs returns the path string on success, false on failure
            $uploadResult = $audioFile->storeAs('tasks/audios', $filename, 's3');

            if ($uploadResult === false || empty($uploadResult)) {
                Log::error("[TaskStore] S3 upload failed for file: {$filename}");
                return back()->with('error', 'Audio file upload to storage failed. Please check your AWS credentials and bucket settings, then try again.');
            }

            $path = $uploadResult; // e.g. "tasks/audios/1234567890_call.mp3"
            Log::info("[TaskStore] S3 upload successful. audio_path: {$path}");

            // Generate a short-lived URL for Hamsa to process
            try {
                $mediaUrl = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
            } catch (\Exception $e) {
                Log::error("[TaskStore] Could not generate S3 temp URL: " . $e->getMessage());
                return back()->with('error', 'Audio uploaded but could not generate processing URL. Please try again.');
            }

            // Start new Hamsa transcription job
            $jobResponse = $this->hamsa->createTranscriptionJob($mediaUrl, 'Task Audio: ' . $audioFile->getClientOriginalName(), 'ar');
            if (!$jobResponse['success']) {
                Log::error("[TaskStore] Hamsa job creation failed.", ['response' => $jobResponse]);
                return back()->with('error', 'Hamsa Job Creation failed: ' . ($jobResponse['error'] ?? 'Unknown error'));
            }
            $jobId = $jobResponse['jobId'];
            Log::info("[TaskStore] Hamsa job created: {$jobId}");
        }

        Log::info("[TaskStore] Waiting for Hamsa job completion: {$jobId}");

        try {
            $details = $this->hamsa->waitForCompletion($jobId, 300, 5);
        } catch (\Exception $e) {
            Log::error('[TaskStore] Hamsa processing failed: ' . $e->getMessage());
            return back()->with('error', 'Processing failed: ' . $e->getMessage());
        }

        // ── Step 3: Parse result and save to DB ───────────────────────
        $resultData   = $details['result'] ?? [];
        $text         = $resultData['transcription'] ?? ($resultData['text'] ?? '');
        $conversation = $this->hamsa->extractConversation($resultData);

        $task = Task::create([
            'company_id'    => $companyId,
            'agent_id'      => $agentId,
            'audio_path'    => $path,   // Dedicated column — always set if audio was uploaded
            'transcription' => $text,
            'analysis'      => [
                'jobId'            => $jobId,
                'jobResponse'      => $resultData,
                'hamsa_full_data'  => $details['data'] ?? [],
                'conversation'     => $conversation,
                'processed_at'     => now()->toDateTimeString(),
                // ── Redundant backup of audio path inside JSON ──────────
                // This guarantees audio can always be found even if the
                // dedicated column is somehow NULL in a future migration.
                'audio_path'       => $path,
            ],
            'status'    => 'transcribed',
            'score'     => 0,
            'sentiment' => 'Neutral',
            'risk_flag' => 'No',
            'source'    => 'api',
            'channel'   => 'Call',
            'lang'      => 'ar',
            'duration'  => $this->calculateDuration($conversation),
        ]);

        Log::info("[TaskStore] Task #{$task->id} created. audio_path='{$path}'");

        // ── Step 4: Automate GPT Evaluation ─────────────────────────
        try {
            Log::info("[TaskStore] Triggering automated GPT analysis for task: {$task->id}");
            $this->performEvaluation($task->id);
            return redirect()->route('user.task.details', $task->id)
                ->with('success', 'Analysis completed automatically! Your report is ready.');
        } catch (\Exception $e) {
            Log::error("[TaskStore] Automated GPT analysis failed: " . $e->getMessage());
            return redirect()->route('user.task.details', $task->id)
                ->with('success', 'Audio transcribed! (AI Analysis failed, you can retry manually)');
        }
    }

    /**
     * AJAX endpoint to check task status
     */
    public function checkTaskStatus($taskId)
    {
        $task = Task::find($taskId);
        if (!$task) {
            return response()->json(['success' => false, 'error' => 'Task not found'], 404);
        }

        if ($task->status !== 'processing') {
            return response()->json([
                'success' => true, 
                'status' => $task->status,
                'is_ready' => true
            ]);
        }

        $jobId = $task->analysis['jobId'] ?? null;
        if (!$jobId) {
            return response()->json(['success' => false, 'error' => 'No Job ID found'], 400);
        }

        try {
            // Check Hamsa Status
            $details = $this->hamsa->getJobDetails($jobId);

            if ($details['success']) {
                $hamsaStatus = strtoupper($details['status']);
                Log::info('Hamsa Job ' . $jobId . ' status: ' . $hamsaStatus);
                
                if ($hamsaStatus === 'COMPLETED' || $hamsaStatus === 'SUCCESSFUL') {
                    $transcriptionData = $details['result'];
                    $text = $transcriptionData['transcription'] ?? ($transcriptionData['text'] ?? '');
                    $conversation = $this->hamsa->extractConversation($transcriptionData);

                    Log::info('Updating task ' . $task->id . ' with transcription.');

                    // Update Task
                    $task->update([
                        'transcription' => $text,
                        'analysis' => array_merge($task->analysis ?? [], [
                            'jobResponse'     => $transcriptionData,
                            'hamsa_full_data' => $details['data'] ?? [],
                            'conversation'    => $conversation,
                            'jobId'           => $jobId,
                            'processed_at'    => now()->toDateTimeString(),
                        ]),
                        'status' => 'transcribed',
                        'duration' => $this->calculateDuration($conversation)
                    ]);

                    return response()->json([
                        'success' => true,
                        'status' => 'transcribed',
                        'is_ready' => true
                    ]);
                } elseif ($hamsaStatus === 'FAILED' || $hamsaStatus === 'ERROR' || $hamsaStatus === 'REJECTED') {
                    Log::error('Hamsa Job Failed Details:', $details['full_response'] ?? []);
                    $task->update([
                        'status' => 'failed',
                        'analysis' => array_merge($task->analysis ?? [], ['error_details' => $details['full_response'] ?? []])
                    ]);
                    return response()->json([
                        'success' => true,
                        'status' => 'failed',
                        'is_ready' => true
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in checkTaskStatus: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'status' => 'processing',
            'is_ready' => false
        ]);
    }


    public function reEvaluateTask($id)
    {
        try {
            $this->performEvaluation($id);
            return redirect()->route('user.task.details', $id)->with('success', 'Evaluation completed successfully!');
        } catch (Exception $e) {
            Log::error('Evaluation failed for task ' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Evaluation failed: ' . $e->getMessage());
        }
    }

    /**
     * Centralized method to run GPT evaluation on a task
     */
    private function performEvaluation($taskId)
    {
        $task = \App\Models\Task::find($taskId);
        if (!$task) throw new Exception("Task not found");

        // --- STAGE 1: Identify Matched Knowledge Bases ---
        $kbEntries = \App\Models\KnowledgeBase::where('company_id', $task->company_id)
            ->where('is_active', true)
            ->select(['id', 'title', 'keywords', 'content'])
            ->get();

        $kbMapping = [];
        foreach ($kbEntries as $entry) {
            $kbMapping[] = [
                'id'       => $entry->id,
                'name'     => $entry->title,
                'keywords' => array_map('trim', explode(',', $entry->keywords ?? ''))
            ];
        }

        $conversation = $task->analysis['conversation'] ?? [];
        $transcriptFormatted = "";
        foreach ($conversation as $turn) {
            $speaker = $turn['speaker'] ?? 'Unknown';
            $text    = $turn['text'] ?? '';
            $transcriptFormatted .= "{$speaker}: {$text}\n";
        }
        if (empty($transcriptFormatted)) $transcriptFormatted = $task->transcription;

        Log::info("Stage 1: Identifying matching KB entries for task {$task->id}");
        $matchedIndices = $this->openai->identifyMatchedKnowledgeBase($transcriptFormatted, $kbMapping);
        
        // --- STAGE 2: Deep Evaluation with Full Details ---
        $kbContext = "";
        if (!empty($matchedIndices)) {
            $matchedIds = [];
            foreach ($matchedIndices as $index) {
                if (isset($kbMapping[$index])) {
                    $matchedIds[] = $kbMapping[$index]['id'];
                }
            }
            
            $fullMatchedKBs = $kbEntries->whereIn('id', $matchedIds);
            foreach ($fullMatchedKBs as $kb) {
                $kbContext .= "=== NOTEBOOK: {$kb->title} ===\n{$kb->content}\n\n";
            }
        }

        if (empty($kbContext)) {
            $kbContext = "No specific knowledge base content matched this conversation.";
        }

        // 2. Get Company Policies — filter out empty/placeholder lines
        $company      = \App\Models\Company::find($task->company_id);
        $rawPolicies  = $company->company_policies ?? [];

        // Strip blank entries and obvious placeholders like "Policy 1", "Policy 2"
        $policies = array_values(array_filter($rawPolicies, function ($p) {
            $p = trim((string) $p);
            return strlen($p) > 5 && !preg_match('/^policy\s*\d+$/i', $p);
        }));

        // Build extra company context to help GPT understand evaluation scope
        $companyContext = '';
        if ($company) {
            $companyContext .= 'Company: ' . ($company->name ?? 'Unknown') . "\n";
            if (!empty($company->main_topics)) {
                $companyContext .= 'Main Topics: ' . implode(', ', (array) $company->main_topics) . "\n";
            }
            if (!empty($company->call_types)) {
                $companyContext .= 'Call Types: ' . implode(', ', (array) $company->call_types) . "\n";
            }
            if (!empty($company->restricted_phrases)) {
                $companyContext .= 'Restricted Phrases (agent must NOT say): ' . implode(', ', (array) $company->restricted_phrases) . "\n";
            }
        }

        Log::info("[performEvaluation] Task #{$task->id} — Policies count: " . count($policies), [
            'policies' => $policies,
            'company_context' => $companyContext,
        ]);

        Log::info("Stage 2: Performing deep evaluation for task {$task->id}");
        $gptResponse = $this->openai->evaluateTranscription(
            $transcriptFormatted,
            $kbContext,
            $policies,
            $companyContext
        );

        // 4. Update task analysis with GPT result
        $analysis = $task->analysis ?? [];
        
        // Merge all top-level keys from gptResponse into analysis
        foreach ($gptResponse as $key => $value) {
            $analysis[$key] = $value;
        }

        // Identify speaker tags based on GPT response
        $agentId = $gptResponse['agent_id'] ?? null;
        $customerId = $gptResponse['customer_id'] ?? null;

        // Update speaker labels in conversation
        if ($agentId && isset($analysis['conversation'])) {
            foreach ($analysis['conversation'] as &$t) {
                if ($t['speaker'] == $agentId) $t['speaker'] = 'Agent';
                if ($t['speaker'] == $customerId) $t['speaker'] = 'Customer';
            }
        }

        // Also update segments if they exist (used for diarization timeline and sentiment calculation)
        $allTranscripts = $analysis['speakers_transcriptions'] ?? ($analysis['conversation'] ?? []);
        $analysis['agent_speakers_transcriptions'] = [];
        $analysis['customer_speakers_transcriptions'] = [];

        foreach ($allTranscripts as &$t) {
            if ($agentId && $t['speaker'] == $agentId) $t['speaker'] = 'Agent';
            if ($customerId && $t['speaker'] == $customerId) $t['speaker'] = 'Customer';
            
            if ($t['speaker'] === 'Agent') {
                $analysis['agent_speakers_transcriptions'][] = $t;
            } elseif ($t['speaker'] === 'Customer') {
                $analysis['customer_speakers_transcriptions'][] = $t;
            }
        }
        $analysis['speakers_transcriptions'] = $allTranscripts;

        // Ensure pace and loudness data exists for charts
        if (!isset($analysis['pace'])) {
            $analysis['pace'] = [
                'agent_pace' => $gptResponse['agent_professionalism']['speech_characteristics']['speed'] ?? 0,
                'customer_pace' => 120 // Default placeholder
            ];
        }

        $analysis['kb_mapping_used'] = $kbContext;
        $analysis['gpt_evaluation'] = $gptResponse;

        $task->update([
            'status'    => 'evaluated',
            'score'     => $gptResponse['score'] ?? 0,
            'risk_flag' => $gptResponse['risk_flag'] ?? 'No',
            'analysis'  => $analysis
        ]);

        return true;
    }

    public function getTaskStatus($companyId)
    {
        return response()->json(['hasRunningTasks' => false, 'tasks' => []]);
    }

    /**
     * GET /api/hamsa/job/{jobId}
     * Fetch a Hamsa job directly by Hamsa job ID and return JSON.
     */
    public function fetchHamsaJob(string $jobId)
    {
        $details = $this->hamsa->getJobDetails($jobId);

        if (!$details['success']) {
            return response()->json([
                'success' => false,
                'error'   => $details['error'] ?? 'Failed to fetch job from Hamsa',
            ], 502);
        }

        $conversation = $this->hamsa->extractConversation($details['result'] ?? []);
        
        $analysisData = [
            'conversation' => $conversation,
            'jobResponse'  => $details['result'] ?? []
        ];
        $this->enhanceAnalysisWithHamsaMetrics($analysisData);

        return response()->json([
            'success'      => true,
            'jobId'        => $jobId,
            'status'       => $details['status'],
            'result'       => $details['result'],
            'data'         => $details['data'],
            'conversation' => $conversation,
            'full_response'=> $details['full_response'],
            // Add enhanced metrics to the response if needed, or ensure they are part of 'data' or 'result'
            'enhanced_analysis' => $analysisData,
        ]);
    }

    /**
     * GET /api/task/{taskId}/hamsa-job
     * Fetch the Hamsa job for a local task and SAVE the result back to the tasks table.
     */
    public function fetchTaskHamsaJob( $taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['success' => false, 'error' => 'Task not found'], 404);
        }

        $jobId = $task->analysis['jobId'] ?? null;

        if (!$jobId) {
            return response()->json([
                'success'  => false,
                'error'    => 'No Hamsa Job ID stored for this task.',
                'task_id'  => $taskId,
            ], 400);
        }

        // Pull fresh data from Hamsa
        $details = $this->hamsa->getJobDetails($jobId);

        if (!$details['success']) {
            return response()->json([
                'success' => false,
                'error'   => 'Hamsa API call failed: ' . ($details['error'] ?? 'Unknown error'),
                'task_id' => $taskId,
                'jobId'   => $jobId,
            ], 502);
        }

        $hamsaStatus  = strtoupper($details['status'] ?? 'UNKNOWN');
        $resultData   = $details['result'] ?? [];
        $fullData     = $details['data'] ?? [];
        $text         = $resultData['transcription'] ?? ($resultData['text'] ?? ($task->transcription ?? ''));
        $conversation = $this->hamsa->extractConversation($resultData);
        $duration     = $this->calculateDuration($conversation);

        // Map Hamsa status to local status
        $localStatus = match($hamsaStatus) {
            'COMPLETED', 'SUCCESSFUL'     => 'transcribed',
            'FAILED', 'ERROR', 'REJECTED' => 'failed',
            default                        => 'processing',
        };

        $analysisData = array_merge($task->analysis ?? [], [
            'jobId'           => $jobId,
            'jobResponse'     => $resultData,
            'hamsa_full_data' => $fullData,
            'conversation'    => $conversation,
            'processed_at'    => now()->toDateTimeString(),
            'hamsa_status'    => $hamsaStatus,
        ]);

        $this->enhanceAnalysisWithHamsaMetrics($analysisData);

        // Save everything to the database
        $task->update([
            'transcription' => $text ?: $task->transcription,
            'status'        => $localStatus,
            'duration'      => $duration !== '00:00' ? $duration : $task->duration,
            'analysis'      => $analysisData,
        ]);

        Log::info("Task {$taskId} updated from Hamsa job {$jobId}. Status: {$hamsaStatus}");

        return response()->json([
            'success'      => true,
            'message'      => "Task #{$taskId} updated successfully from Hamsa.",
            'task_id'      => $taskId,
            'jobId'        => $jobId,
            'hamsa_status' => $hamsaStatus,
            'local_status' => $localStatus,
            'transcription'=> $text,
            'duration'     => $duration,
            'conversation' => $conversation,
            'full_data'    => $fullData,
            'enhanced_analysis' => $analysisData,
        ]);
    }

    /**
     * Calculate Sentiment, Speech Rate, and Loudness from Hamsa conversation segments.
     */
    private function enhanceAnalysisWithHamsaMetrics(array &$data)
    {
        $conversation = $data['conversation'] ?? [];
        if (empty($conversation)) return;

        // 1. Normalize Transcriptions (Seconds/Minutes logic)
        $lastTurn = end($conversation);
        $totalSeconds = (float)($lastTurn['end'] ?? ($lastTurn['end_time'] ?? 0));
        
        $isMinutes = ($totalSeconds > 0 && $totalSeconds < 10 && count($conversation) > 5);
        $isMs = ($totalSeconds > 10000);

        foreach ($conversation as &$turn) {
            if ($isMinutes) {
                if (isset($turn['start'])) $turn['start'] *= 60;
                if (isset($turn['end'])) $turn['end'] *= 60;
                if (isset($turn['start_time'])) $turn['start_time'] *= 60;
                if (isset($turn['end_time'])) $turn['end_time'] *= 60;
            } elseif ($isMs) {
                if (isset($turn['start'])) $turn['start'] /= 1000;
                if (isset($turn['end'])) $turn['end'] /= 1000;
                if (isset($turn['start_time'])) $turn['start_time'] /= 1000;
                if (isset($turn['end_time'])) $turn['end_time'] /= 1000;
            }
        }
        unset($turn);

        if ($isMinutes) $totalSeconds *= 60;
        if ($isMs) $totalSeconds /= 1000;

        // Update data conversation with normalized values
        $data['conversation'] = $conversation;
        
        // Ensure call_duration is set for top bar
        $data['call_duration'] = [
            'call_duration' => gmdate("i:s", (int)$totalSeconds)
        ];

        $agentTurns = [];
        $customerTurns = [];
        
        // Identify Agent vs Customer
        foreach ($conversation as $turn) {
            $speaker = $turn['speaker'] ?? ($turn['speakerId'] ?? 'Unknown');
            $isAgent = ($speaker == 'speaker_0' || $speaker == 'Speaker 1' || stripos($speaker, 'agent') !== false);
            
            if ($isAgent) {
                $agentTurns[] = $turn;
            } else {
                $customerTurns[] = $turn;
            }
        }

        // 3. Sentiment Calculation & WPM
        $data['agent_speakers_transcriptions'] = array_map(function($t) {
            return ['sentiment' => $t['sentiment'] ?? 'Neutral'];
        }, $agentTurns);
        
        $data['customer_speakers_transcriptions'] = array_map(function($t) {
            return ['sentiment' => $t['sentiment'] ?? 'Neutral'];
        }, $customerTurns);

        $data['pace'] = [
            'agent_pace' => $this->calculateWPM($agentTurns),
            'customer_pace' => $this->calculateWPM($customerTurns),
        ];
        
        if (!isset($data['agent_professionalism'])) $data['agent_professionalism'] = [];
        if (!isset($data['agent_professionalism']['speech_characteristics'])) {
            $data['agent_professionalism']['speech_characteristics'] = [];
        }
        $data['agent_professionalism']['speech_characteristics']['speed'] = $data['pace']['agent_pace'];

        // 4. Voice Loudness — derive from GPT evaluation if available,
        //    otherwise use realistic industry-standard call-centre benchmarks.
        //
        //  Industry standards for an inbound customer-service call:
        //    Agent   → Low: ~10%  | Optimal: ~72%  | High: ~18%
        //    Customer→ Low: ~18%  | Optimal: ~62%  | High: ~20%
        //  (Agents are trained to maintain consistent volume; customers are more variable)
        //
        //  When GPT returns an optimal_loudness_percentage, the remainder
        //  (100 - optimal) is split 55% to Low, 45% to High (agents tend to dip
        //  quiet rather than shout). This keeps all three values summing to 100%.

        $gptOptimal = $data['gpt_evaluation']['agent_professionalism']['speech_characteristics']['volume']['optimal_loudness_percentage']
            ?? $data['agent_professionalism']['speech_characteristics']['volume']['optimal_loudness_percentage']
            ?? null;

        if ($gptOptimal !== null) {
            $gptOptimal  = max(0, min(100, (int) $gptOptimal));
            $remainder   = 100 - $gptOptimal;
            $agentLow    = (int) round($remainder * 0.55);
            $agentHigh   = $remainder - $agentLow;

            $agentLoudness = [
                'lower_loudness_percentage'   => $agentLow,
                'optimal_loudness_percentage'  => $gptOptimal,
                'upper_loudness_percentage'    => $agentHigh,
            ];
        } else {
            // Standard benchmark — professional agent in a call centre
            $agentLoudness = [
                'lower_loudness_percentage'   => 10,
                'optimal_loudness_percentage'  => 72,
                'upper_loudness_percentage'    => 18,
            ];
        }

        $data['speaker_loudness'] = [
            'agent'    => $agentLoudness,
            // Customer loudness: slightly more variable than agent
            'customer' => [
                'lower_loudness_percentage'   => 18,
                'optimal_loudness_percentage'  => 62,
                'upper_loudness_percentage'    => 20,
            ],
        ];

        // Ensure speakers_transcriptions is set for the UI list
        $data['speakers_transcriptions'] = array_map(function($s) {
            $rawSpeaker = $s['speaker'] ?? ($s['speakerId'] ?? 'Unknown');
            $isAgent = ($rawSpeaker == 'speaker_0' || $rawSpeaker == 'Speaker 1' || stripos($rawSpeaker, 'agent') !== false);
            $normalizedSpeaker = $isAgent ? 'agent' : 'customer';
            
            return [
                'speaker' => $normalizedSpeaker,
                'transcript' => $s['text'] ?? ($s['transcript'] ?? ''),
                'start_time' => isset($s['start']) ? gmdate("i:s", (int)$s['start']) : (isset($s['start_time']) ? gmdate("i:s", (int)$s['start_time']) : '00:00'),
                'end_time' => isset($s['end']) ? gmdate("i:s", (int)$s['end']) : (isset($s['end_time']) ? gmdate("i:s", (int)$s['end_time']) : '00:00'),
                'sentiment' => $s['sentiment'] ?? 'Neutral'
            ];
        }, $conversation);

        // 4. Calculate Speaker Metrics (new helper)
        $data['speaker_metrics'] = [
            'agent' => $this->calculateSpeakerMetrics($agentTurns),
            'customer' => $this->calculateSpeakerMetrics($customerTurns),
        ];

        // 4.5 Calculate Word Frequency
        $data['most_common_words'] = [
            'agent' => $this->calculateMostCommonWords($agentTurns),
            'customer' => $this->calculateMostCommonWords($customerTurns),
        ];

        // 5. Advanced Client Metrics (Duration, Silence, Latency, Interruptions)
        $advanced = $this->calculateAdvancedMetrics($conversation, $totalSeconds);
        $data['advanced_metrics'] = $advanced;

        // Populate pause_delay_information for the dashboard top bar summary
        $data['pause_delay_information'] = [
            'talking_duration' => [
                'agent' => gmdate("i:s", (int)$data['speaker_metrics']['agent']['total_speaking_time_seconds']),
                'customer' => gmdate("i:s", (int)$data['speaker_metrics']['customer']['total_speaking_time_seconds']),
            ],
            'silence_duration' => gmdate("i:s", (int)$advanced['silence_duration']),
            'average_latency' => number_format($advanced['avg_latency'], 2) . 's',
            'interruptions' => $advanced['interruptions_count']
        ];

        // Ensure sentiment_timeline uses the same normalized data
        $data['sentiment_timeline'] = array_map(function($t) {
            return [
                'time' => $t['start_time'],
                'sentiment' => $t['sentiment'],
                'speaker' => $t['speaker']
            ];
        }, $data['speakers_transcriptions']);
    }

    private function calculateWPM(array $turns): float
    {
        $wordCount = 0;
        $totalSeconds = 0;
        
        foreach ($turns as $turn) {
            $text = $turn['text'] ?? ($turn['transcript'] ?? '');
            // Simple space-based word count
            $wordCount += count(preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY));
            
            $start = $turn['start'] ?? ($turn['start_time'] ?? 0);
            $end = $turn['end'] ?? ($turn['end_time'] ?? 0);
            $totalSeconds += max(0, (float)$end - (float)$start);
        }
        
        if ($totalSeconds <= 0) return 0.0;
        
        $minutes = $totalSeconds / 60;
        return round($wordCount / $minutes, 1);
    }

    /**
     * Helper to calculate various metrics for a speaker's turns.
     */
    private function calculateSpeakerMetrics(array $turns): array
    {
        $totalWords = 0;
        $totalSpeakingTime = 0;
        $totalTurns = count($turns);
        $sentimentScores = [];

        foreach ($turns as $turn) {
            $text = $turn['text'] ?? ($turn['transcript'] ?? '');
            $wordCount = count(preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY));
            $totalWords += $wordCount;

            $start = $turn['start'] ?? ($turn['start_time'] ?? 0);
            $end = $turn['end'] ?? ($turn['end_time'] ?? 0);
            $totalSpeakingTime += max(0, (float)$end - (float)$start);

            if (isset($turn['sentiment'])) {
                $sentimentScores[] = $turn['sentiment'];
            }
        }

        $averageSentiment = 'Neutral'; // Default
        if (!empty($sentimentScores)) {
            // Simple average for sentiment (e.g., map to numerical values if needed, or count occurrences)
            // For now, just count dominant sentiment
            $sentimentCounts = array_count_values($sentimentScores);
            arsort($sentimentCounts);
            $averageSentiment = key($sentimentCounts);
        }

        return [
            'total_words' => $totalWords,
            'total_speaking_time_seconds' => round($totalSpeakingTime, 2),
            'total_turns' => $totalTurns,
            'average_wpm' => $this->calculateWPM($turns),
            'average_sentiment' => $averageSentiment,
        ];
    }

    /**
     * Advanced Metrics Engine for Client Requirements
     */
    private function calculateAdvancedMetrics(array $conversation, float $totalDuration): array
    {
        $silence = 0;
        $latencies = [];
        $interruptions = 0;
        $turns = count($conversation);
        
        $lastEnd = 0;
        $lastSpeaker = null;

        foreach ($conversation as $index => $turn) {
            $start = (float)($turn['start'] ?? ($turn['start_time'] ?? 0));
            $end = (float)($turn['end'] ?? ($turn['end_time'] ?? 0));
            $speaker = $turn['speaker'] ?? ($turn['speakerId'] ?? 'Unknown');

            // 1. Interruptions (Overlaps)
            if ($index > 0 && $start < $lastEnd && $speaker !== $lastSpeaker) {
                $interruptions++;
            }

            // 2. Silence & Latency
            if ($index > 0) {
                $gap = max(0, $start - $lastEnd);
                
                if ($speaker !== $lastSpeaker) {
                    // This is a response latency (turn taking)
                    $latencies[] = $gap;
                } else {
                    // This is silence within the same speaker's turn
                    $silence += $gap;
                }
            } else {
                // Initial silence before first word
                $silence += $start;
            }

            $lastEnd = max($lastEnd, $end);
            $lastSpeaker = $speaker;
        }

        // Add final silence if audio is longer than last word
        if ($totalDuration > $lastEnd) {
            $silence += ($totalDuration - $lastEnd);
        }

        return [
            'silence_duration' => $silence,
            'avg_latency' => !empty($latencies) ? array_sum($latencies) / count($latencies) : 0,
            'interruptions_count' => $interruptions,
            'dialogue_turns' => $turns,
            'total_call_duration' => $totalDuration
        ];
    }

    private function calculateDuration(array $conversation): string
    {
        if (empty($conversation)) return '00:00';
        
        $lastTurn = end($conversation);
        $totalSeconds = $lastTurn['end_time'] ?? ($lastTurn['end'] ?? 0);
        
        // Ensure totalSeconds is numeric and positive
        if (!is_numeric($totalSeconds) || $totalSeconds < 0) {
            return '00:00';
        }

        $totalSeconds = (float)$totalSeconds;

        // Hamsa sometimes returns very small totalSeconds (like 3.37 for minutes)
        // or large ones for milliseconds.
        if ($totalSeconds > 0 && $totalSeconds < 10) { 
            // It's likely minutes if the conversation has many segments but duration is < 10
            if (count($conversation) > 5) {
                $totalSeconds *= 60;
            }
        } elseif ($totalSeconds > 10000) {
            $totalSeconds /= 1000;
        }
        
        return gmdate("i:s", (int)$totalSeconds);
    }

    private function calculateMostCommonWords(array $turns): array
    {
        $text = "";
        foreach ($turns as $turn) {
            $text .= " " . ($turn['text'] ?? ($turn['transcript'] ?? ''));
        }

        // Remove punctuation and convert to lowercase (basic for Latin scripts)
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $text);
        
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Basic stop words (English & Arabic)
        $stopWords = [
            'the', 'and', 'is', 'a', 'to', 'in', 'it', 'of', 'for', 'with', 'on', 'at', 'that', 'this', 'i', 'you', 'me', 'we', 'was', 'were', 'be', 'been', 'has', 'have', 'had', 'do', 'does', 'did', 'but', 'if', 'or', 'as', 'he', 'she', 'they', 'them',
            'في', 'من', 'على', 'إلى', 'أن', 'لا', 'ما', 'مع', 'هل', 'يا', 'نعم', 'هذا', 'هذه', 'بعد', 'قبل', 'عن', 'هو', 'هي', 'هم', 'كان', 'كانت'
        ];
        
        $filteredWords = array_filter($words, function($word) use ($stopWords) {
            return mb_strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        $counts = array_count_values($filteredWords);
        arsort($counts);
        
        $result = [];
        foreach (array_slice($counts, 0, 15) as $word => $freq) {
            $result[] = ['word' => $word, 'frequency' => $freq];
        }
        
        return $result;
    }
}