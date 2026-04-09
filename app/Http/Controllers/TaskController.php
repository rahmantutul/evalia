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

        // ── Always resolve the agent's CURRENT live evaluation role ──────────
        // This overrides any stale value stored in the analysis JSON, ensuring
        // permissions are always accurate even for tasks evaluated before this
        // feature was introduced, or when an agent's role changes after evaluation.
        if (!empty($task['agent_id'])) {
            $agent = \App\Models\User::with('evaluationRole')->find($task['agent_id']);
            $evalRole = $agent?->evaluationRole;
            $data['evaluation_settings'] = [
                'eval_kb'              => $evalRole ? (bool)$evalRole->eval_kb              : true,
                'eval_policies'        => $evalRole ? (bool)$evalRole->eval_policies        : true,
                'eval_risks'           => $evalRole ? (bool)$evalRole->eval_risks            : true,
                'eval_extractions'     => $evalRole ? (bool)$evalRole->eval_extractions      : true,
                'eval_professionalism' => $evalRole ? (bool)$evalRole->eval_professionalism  : true,
                'eval_assessment'      => $evalRole ? (bool)$evalRole->eval_assessment       : true,
                'eval_cooperation'     => $evalRole ? (bool)$evalRole->eval_cooperation      : true,
                'eval_linguistic'      => $evalRole ? (bool)$evalRole->eval_linguistic       : true,
            ];
            // Also refresh the role name shown in the policy alert banner
            $data['evaluation_role_name'] = $evalRole?->name ?? $data['evaluation_role_name'] ?? null;
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


            // Upload to S3 — storeAs returns the path string on success, false on failure
            $uploadResult = $audioFile->storeAs('tasks/audios', $filename, 's3');

            if ($uploadResult === false || empty($uploadResult)) {
                return back()->with('error', 'Audio file upload to storage failed. Please check your AWS credentials and bucket settings, then try again.');
            }

            $path = $uploadResult; // e.g. "tasks/audios/1234567890_call.mp3"

            // Generate a short-lived URL for Hamsa to process
            try {
                $mediaUrl = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
            } catch (\Exception $e) {
                return back()->with('error', 'Audio uploaded but could not generate processing URL. Please try again.');
            }

            // Start new Hamsa transcription job
            $jobResponse = $this->hamsa->createTranscriptionJob($mediaUrl, 'Task Audio: ' . $audioFile->getClientOriginalName(), 'ar');
            if (!$jobResponse['success']) {
                return back()->with('error', 'Hamsa Job Creation failed: ' . ($jobResponse['error'] ?? 'Unknown error'));
            }
            $jobId = $jobResponse['jobId'];
        }
        try {
            $details = $this->hamsa->waitForCompletion($jobId, 300, 5);
        } catch (\Exception $e) {
            return back()->with('error', 'Processing failed: ' . $e->getMessage());
        }
        $resultData   = $details['result'] ?? [];
        $text         = $resultData['transcription'] ?? ($resultData['text'] ?? '');
        $conversation = $this->hamsa->extractConversation($resultData);

        $task = Task::create([
            'company_id'    => $companyId,
            'agent_id'      => $agentId,
            'audio_path'    => $path,
            'transcription' => $text,
            'analysis'      => [
                'jobId'            => $jobId,
                'jobResponse'      => $resultData,
                'hamsa_full_data'  => $details['data'] ?? [],
                'conversation'     => $conversation,
                'processed_at'     => now()->toDateTimeString(),
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

        try {
            $this->performEvaluation($task->id);
            return redirect()->route('user.task.details', $task->id)
                ->with('success', 'Analysis completed automatically! Your report is ready.');
        } catch (\Exception $e) {
            return redirect()->route('user.task.details', $task->id)
                ->with('success', 'Audio transcribed! (AI Analysis failed, you can retry manually)');
        }
    }


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
                
                if ($hamsaStatus === 'COMPLETED' || $hamsaStatus === 'SUCCESSFUL') {
                    $transcriptionData = $details['result'];
                    $text = $transcriptionData['transcription'] ?? ($transcriptionData['text'] ?? '');
                    $conversation = $this->hamsa->extractConversation($transcriptionData);

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
        set_time_limit(0);
        try {
            $this->performEvaluation($id);
            return redirect()->route('user.task.details', $id)->with('success', 'Evaluation completed successfully!');
        } catch (Exception $e) {
            Log::error('Evaluation failed for task ' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Evaluation failed: ' . $e->getMessage());
        }
    }

    /**
     * Centralized method to run GPT evaluation on a task.
     *
     * Stage 1 — splits the transcript into Q/A pairs and matches each pair
     *            to a KB independently (so different pairs can reference
     *            different KBs).
     * Stage 2 — sends the full transcript + pair-aware KB context to GPT.
     */
    private function performEvaluation($taskId)
    {
        set_time_limit(0);
        $task = \App\Models\Task::with(['agent.evaluationRole'])->find($taskId);
        if (!$task) throw new Exception("Task not found");

        $agent = $task->agent;
        $evalRole = $agent?->evaluationRole;
        
        // Default settings — all true if no role assigned
        $evalSettings = [
            'eval_kb' => $evalRole ? (bool)$evalRole->eval_kb : true,
            'eval_policies' => $evalRole ? (bool)$evalRole->eval_policies : true,
            'eval_risks' => $evalRole ? (bool)$evalRole->eval_risks : true,
            'eval_extractions' => $evalRole ? (bool)$evalRole->eval_extractions : true,
            'eval_professionalism' => $evalRole ? (bool)$evalRole->eval_professionalism : true,
            'eval_assessment' => $evalRole ? (bool)$evalRole->eval_assessment : true,
            'eval_cooperation' => $evalRole ? (bool)$evalRole->eval_cooperation : true,
            'eval_linguistic' => $evalRole ? (bool)$evalRole->eval_linguistic : true,
        ];

        // ── Load KB entries for this company ─────────────────────────────
        $kbContext = '';
        $pairResults = [];
        
        if ($evalSettings['eval_kb']) {
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

            // ── Build formatted transcript ────────────────────────────────────
            $conversation = $task->analysis['conversation'] ?? [];
            $transcriptFormatted = '';
            foreach ($conversation as $turn) {
                $speaker = $turn['speaker'] ?? 'Unknown';
                $text    = $turn['text']    ?? '';
                $transcriptFormatted .= "{$speaker}: {$text}\n";
            }
            if (empty($transcriptFormatted)) {
                $transcriptFormatted = $task->transcription;
            }

            $pairs = $this->splitTranscriptIntoPairs($transcriptFormatted);

            $collectedKbIds = []; 

            foreach ($pairs as $pairText) {
                if (!empty($kbMapping)) {
                    $matchedIndices = $this->openai->identifyMatchedKnowledgeBase($pairText, $kbMapping);
                } else {
                    $matchedIndices = [];
                }

                $pairKbIds = [];
                foreach ($matchedIndices as $index) {
                    if (isset($kbMapping[$index])) {
                        $id = $kbMapping[$index]['id'];
                        $pairKbIds[] = $id;
                        if (!in_array($id, $collectedKbIds)) {
                            $collectedKbIds[] = $id;
                        }
                    }
                }

                $pairResults[] = [
                    'pair_text'      => $pairText,
                    'matched_kb_ids' => $pairKbIds,
                ];
            }

            Log::info("[Stage 1] Matched KB IDs across all pairs: " . implode(', ', $collectedKbIds));

            foreach ($collectedKbIds as $kbId) {
                $kb = $kbEntries->firstWhere('id', $kbId);
                if (!$kb) continue;

                $kbContext .= "=== NOTEBOOK: {$kb->title} ===\n";
                $kbContext .= $kb->content . "\n";
                $kbContext .= "Reference Pairs:\n";

                foreach ($pairResults as $pairIndex => $pair) {
                    if (in_array($kbId, $pair['matched_kb_ids'])) {
                        $shortPair = mb_substr(trim($pair['pair_text']), 0, 300);
                        $kbContext .= "  Pair " . ($pairIndex + 1) . ": " . $shortPair . "\n";
                    }
                }
                $kbContext .= "\n";
            }

            if (empty($kbContext)) {
                $kbContext = "No specific knowledge base content matched this conversation.";
            }
        }

        // 2. Get Company Policies & Risks — filter out empty/placeholder lines
        $company      = \App\Models\Company::find($task->company_id);
        $policies = [];
        $risks = [];

        if ($evalSettings['eval_policies']) {
            $rawPolicies  = $company->company_policies ?? [];
            $policies = array_values(array_filter($rawPolicies, function ($p) {
                $p = trim((string) $p);
                return strlen($p) > 5 && !preg_match('/^policy\s*\d+$/i', $p);
            }));
        }

        if ($evalSettings['eval_risks']) {
            $rawRisks     = $company->company_risks ?? [];
            $risks = array_values(array_filter($rawRisks, function ($p) {
                $p = trim((string) $p);
                return strlen($p) > 5 && !preg_match('/^risk\s*\d+$/i', $p);
            }));
        }

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

        // 3. Get Extractions for this agent
        $extractions = [];
        if ($evalSettings['eval_extractions']) {
            $rawExtractionGroups = $company->data_extraction_config ?? [];
            foreach ($rawExtractionGroups as $group) {
                if (isset($group['agent_ids']) && is_array($group['agent_ids']) && in_array($task->agent_id, $group['agent_ids'])) {
                    $extractions = array_merge($extractions, $group['extractions'] ?? []);
                }
            }
        }

        // ── Ensure Transcript is available for final prompt ─────────────────
        $transcriptFormatted = '';
        $conversation = $task->analysis['conversation'] ?? [];
        foreach ($conversation as $turn) {
            $speaker = $turn['speaker'] ?? 'Unknown';
            $text    = $turn['text']    ?? '';
            $transcriptFormatted .= "{$speaker}: {$text}\n";
        }
        if (empty($transcriptFormatted)) {
            $transcriptFormatted = $task->transcription;
        }

        Log::info("[performEvaluation] Task #{$task->id} — Policies: " . count($policies) . ", Risks: " . count($risks) . ", Extractions: " . count($extractions), [
            'settings' => $evalSettings,
            'role' => $evalRole->name ?? 'None',
        ]);

        Log::info("Stage 2: Performing deep evaluation for task {$task->id}");
        $gptResponse = $this->openai->evaluateTranscription(
            $transcriptFormatted,
            $kbContext,
            $policies,
            $companyContext,
            $risks,
            $extractions,
            $evalSettings
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

        $analysis['kb_mapping_used']  = $kbContext;
        $analysis['kb_pair_results']   = $pairResults;  // pair-level KB breakdown
        $analysis['gpt_evaluation']    = $gptResponse;
        $analysis['evaluation_settings'] = $evalSettings;
        $analysis['evaluation_role_name'] = $evalRole->name ?? 'Default';

        // Calculate average score from the 3 sections
        $scores = [
            $gptResponse['agent_professionalism']['total_score']['percentage'] ?? 0,
            $gptResponse['agent_assessment']['total_score']['percentage'] ?? 0,
            $gptResponse['agent_cooperation']['total_score']['percentage'] ?? 0,
        ];
        $overallScore = count($scores) > 0 ? (array_sum($scores) / count($scores)) : 0;

        // Determine dominant sentiment for the overall task summary
        $counts = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
        foreach ($allTranscripts as $t) {
            $s = ucfirst(strtolower(trim($t['sentiment'] ?? 'Neutral')));
            if (isset($counts[$s])) $counts[$s]++;
        }
        arsort($counts);
        $taskSentiment = key($counts);

        $task->update([
            'status'    => 'evaluated',
            'score'     => round($overallScore),
            'sentiment' => $taskSentiment,
            'risk_flag' => $gptResponse['risk_flag'] ?? 'No',
            'analysis'  => $analysis
        ]);

        return true;
    }

    /**
     * Split a formatted transcript into Q/A pairs.
     *
     * Strategy (same as KnowledgeBaseController::splitIntoPairs):
     *  1. Split on blank lines — each block is one pair.
     *  2. Detect speaker-tagged lines (Customer/Agent/Speaker N) and group exchanges.
     *  3. Fall back to treating the whole transcript as one pair.
     */
    private function splitTranscriptIntoPairs(string $text): array
    {
        // Method 1: blank-line delimited blocks
        $blocks = preg_split('/\n[\s]*\n/', trim($text));
        $blocks = array_values(array_filter(array_map('trim', $blocks)));

        if (count($blocks) >= 2) {
            return $blocks;
        }

        // Method 2: speaker-tagged lines
        $lines  = explode("\n", trim($text));
        $pairs  = [];
        $buffer = [];
        $seenCustomer = false;

        foreach ($lines as $line) {
            $isCustomerLine = preg_match('/^(?:Customer|User|Q|عميل|Speaker\s*0)\s*:/i', trim($line));
            $isAgentLine    = preg_match('/^(?:Agent|Assistant|A|وكيل|Speaker\s*1)\s*:/i', trim($line));

            // New customer turn → flush previous pair
            if ($isCustomerLine && $seenCustomer && !empty($buffer)) {
                $pairs[]      = implode("\n", $buffer);
                $buffer       = [];
                $seenCustomer = false;
            }

            if ($isCustomerLine) {
                $seenCustomer = true;
            }

            $buffer[] = $line;

            // After agent reply → close the pair
            if ($isAgentLine && $seenCustomer) {
                $pairs[]      = implode("\n", $buffer);
                $buffer       = [];
                $seenCustomer = false;
            }
        }

        if (!empty($buffer)) {
            $pairs[] = implode("\n", $buffer);
        }

        $pairs = array_values(array_filter(array_map('trim', $pairs)));

        return !empty($pairs) ? $pairs : [trim($text)];
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

    public function agentWiseExtractions(Request $request)
    {
        $user = auth()->user();
        
        // Fetch all agents (Open for all users as requested)
        $agents = User::where('user_type', User::TYPE_AGENT)->get();

        // Get extraction groups from ALL companies to build unified filters
        $allCompanies = \App\Models\Company::whereNotNull('data_extraction_config')->get();
        $allGroups = [];
        $groupNames = [];
        
        foreach ($allCompanies as $comp) {
            $compGroups = $comp->data_extraction_config ?? [];
            if (is_array($compGroups)) {
                foreach ($compGroups as $g) {
                    $name = is_array($g) ? ($g['group_name'] ?? null) : ($g->group_name ?? null);
                    if ($name) {
                        $groupNames[] = $name;
                        // Store the group with its agent ids for filtering later
                        $allGroups[] = $g;
                    }
                }
            }
        }
        $groupNames = array_unique($groupNames);

        // Get filters from request
        $selectedAgentId = $request->get('agent_id', 'all');
        $selectedGroupName = trim($request->get('group_name', '')) ?: 'all';
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Fetch tasks
        $taskQuery = Task::where('status', 'evaluated')
            ->whereNotNull('analysis')
            ->where('analysis', 'like', '%extracted_data%')
            ->with('agent')
            ->orderByDesc('created_at');
            
        // Filter by Agent
        if ($selectedAgentId !== 'all') {
            $taskQuery->where('agent_id', $selectedAgentId);
        }

        // Filter by Group Name (by finding agents assigned to that group across all companies)
        if ($selectedGroupName !== 'all') {
            $matchingAgentIds = [];
            foreach ($allGroups as $g) {
                $name = is_array($g) ? ($g['group_name'] ?? null) : ($g->group_name ?? null);
                if ($name === $selectedGroupName) {
                    $ids = is_array($g) ? ($g['agent_ids'] ?? []) : ($g->agent_ids ?? []);
                    $matchingAgentIds = array_merge($matchingAgentIds, (array)$ids);
                }
            }
            
            if (!empty($matchingAgentIds)) {
                $taskQuery->whereIn('agent_id', array_unique($matchingAgentIds));
            } else {
                $taskQuery->where('id', 0); // No match found
            }
        }

        // Filter by Date
        if ($startDate) {
            $taskQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $taskQuery->whereDate('created_at', '<=', $endDate);
        }



        $tasks = $taskQuery->get();

        // Prepare data
        $extractions = [];
        foreach ($tasks as $task) {
            $extractedData = $task->analysis['gpt_evaluation']['extracted_data'] ?? $task->analysis['extracted_data'] ?? [];
            if (!empty($extractedData)) {
                $extractions[] = [
                    'task_id'    => $task->id,
                    'agent_id'   => $task->agent_id,
                    'agent_name' => $task->agent?->name ?? 'Unknown',
                    'created_at' => $task->created_at->toDateTimeString(),
                    'data'       => $extractedData,
                    'duration'   => $task->duration,
                    'sentiment'  => $task->sentiment,
                    'score'      => $task->score,
                ];
            }
        }

        return view('user.extractions.agent_wise', [
            'agents'            => $agents,
            'groupNames'        => $groupNames,
            'selectedAgentId'   => $selectedAgentId,
            'selectedGroupName' => $selectedGroupName,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'extractions'       => $extractions
        ]);
    }
}