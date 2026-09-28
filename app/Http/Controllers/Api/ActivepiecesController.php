<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TaskController;
use App\Models\Company;
use App\Models\Task;
use App\Models\User;
use App\Services\HamsaService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivepiecesController extends Controller
{
    public function token(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password) || !$user->is_active) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $expiresAt = now()->addHours(12);
        $token = Crypt::encryptString(json_encode([
            'user_id' => $user->id,
            'expires_at' => $expiresAt->timestamp,
        ]));

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 12 * 60 * 60,
        ]);
    }

    public function pricing(Request $request)
    {
        $auth = $this->activepiecesUser($request);
        if ($auth) {
            return $auth;
        }

        return response()->json([
            'currency' => 'USD',
            'evalia_cost_per_call' => (float) env('EVALIA_COST_PER_CALL', 0.50),
            'models' => [
                [
                    'name' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                    'provider' => 'openai',
                    'currency' => 'USD',
                    'note' => 'Pricing is configured from Evalia environment values.',
                ],
            ],
        ]);
    }

    public function upload(Request $request, HamsaService $hamsa)
    {
        $auth = $this->activepiecesUser($request);
        if ($auth) {
            return $auth;
        }

        $request->validate([
            'audio_file' => ['required', 'file', 'mimes:wav,mp3,m4a,ogg,webm', 'max:102400'],
            'company_id' => ['required', 'exists:companies,id'],
            'agent_id' => ['required', 'exists:users,id'],
        ]);

        set_time_limit(360);

        $audioFile = $request->file('audio_file');
        $filename = time() . '_' . Str::slug(pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $audioFile->getClientOriginalExtension();
        $path = $audioFile->storeAs('tasks/audios', $filename, 's3');

        if (!$path) {
            return response()->json(['message' => 'Audio upload failed'], 500);
        }

        $mediaUrl = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
        $jobResponse = $hamsa->createTranscriptionJob($mediaUrl, 'Task Audio: ' . $audioFile->getClientOriginalName(), 'ar');

        if (empty($jobResponse['success'])) {
            return response()->json([
                'message' => 'Hamsa job creation failed',
                'error' => $jobResponse['error'] ?? 'Unknown error',
            ], 422);
        }

        $task = Task::create([
            'company_id' => $request->company_id,
            'agent_id' => $request->agent_id,
            'audio_path' => $path,
            'transcription' => '',
            'analysis' => [
                'jobId' => $jobResponse['jobId'],
                'hamsa_create_data' => $jobResponse['data'] ?? null,
                'audio_path' => $path,
                'processing_started_at' => now()->toDateTimeString(),
            ],
            'status' => 'processing',
            'score' => 0,
            'sentiment' => 'Neutral',
            'risk_flag' => 'No',
            'source' => 'api',
            'channel' => 'Call',
            'lang' => 'ar',
            'duration' => '00:00',
        ]);

        return response()->json([
            'status_code' => 200,
            'work_id' => $task->id,
            'status' => 'processing',
            'message' => 'Audio uploaded and processing started.',
        ]);
    }

    public function result(Request $request, string $workId, HamsaService $hamsa)
    {
        $auth = $this->activepiecesUser($request);
        if ($auth) {
            return $auth;
        }

        $task = Task::find($workId);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        if ($task->status === 'processing') {
            $jobId = $task->analysis['jobId'] ?? null;
            if ($jobId) {
                $details = $hamsa->getJobDetails($jobId);
                $hamsaStatus = strtoupper($details['status'] ?? '');

                if (($details['success'] ?? false) && in_array($hamsaStatus, ['COMPLETED', 'SUCCESSFUL'], true)) {
                    $result = $details['result'] ?? [];
                    $task->update([
                        'transcription' => $result['transcription'] ?? ($result['text'] ?? ''),
                        'analysis' => array_merge($task->analysis ?? [], [
                            'jobResponse' => $result,
                            'hamsa_full_data' => $details['data'] ?? [],
                            'processed_at' => now()->toDateTimeString(),
                        ]),
                        'status' => 'completed',
                    ]);

                    try {
                        app(TaskController::class)->performEvaluation($task->id);
                    } catch (\Throwable $e) {
                        $task->refresh();
                        $task->update([
                            'status' => 'completed',
                            'analysis' => array_merge($task->analysis ?? [], [
                                'evaluation_error' => $e->getMessage(),
                            ]),
                        ]);
                    }
                }

                if (in_array($hamsaStatus, ['FAILED', 'ERROR', 'REJECTED'], true)) {
                    $task->update([
                        'status' => 'failed',
                        'analysis' => array_merge($task->analysis ?? [], [
                            'error_details' => $details['full_response'] ?? $details,
                        ]),
                    ]);
                }
            }
        }

        $task->refresh();

        return response()->json([
            'id' => $task->id,
            'status' => $task->status === 'evaluated' ? 'completed' : $task->status,
            'data' => [
                'task_id' => $task->id,
                'company_id' => $task->company_id,
                'agent_id' => $task->agent_id,
                'score' => $task->score,
                'sentiment' => $task->sentiment,
                'risk_flag' => $task->risk_flag,
                'transcription' => $task->transcription,
                'analysis' => $task->analysis,
                'created_at' => optional($task->created_at)->toDateTimeString(),
                'updated_at' => optional($task->updated_at)->toDateTimeString(),
            ],
        ]);
    }

    public function extractionOptions(Request $request)
    {
        $auth = $this->activepiecesUser($request);
        if ($auth) {
            return $auth;
        }

        $agents = User::where('user_type', User::TYPE_AGENT)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn ($agent) => [
                'id' => $agent->id,
                'name' => $agent->name ?: ($agent->email ?: 'Agent #' . $agent->id),
                'email' => $agent->email,
            ])
            ->values();

        $groupNames = [];
        $companies = Company::whereNotNull('data_extraction_config')->get(['data_extraction_config']);
        foreach ($companies as $company) {
            $groups = $company->data_extraction_config ?? [];
            if (!is_array($groups)) {
                continue;
            }
            foreach ($groups as $group) {
                $name = is_array($group) ? ($group['group_name'] ?? null) : ($group->group_name ?? null);
                if ($name) {
                    $groupNames[] = $name;
                }
            }
        }

        return response()->json([
            'agents' => $agents,
            'groups' => array_values(array_unique($groupNames)),
        ]);
    }

    public function extractionResults(Request $request)
    {
        $auth = $this->activepiecesUser($request);
        if ($auth) {
            return $auth;
        }

        $selectedAgentId = $request->query('agent_id', 'all');
        $selectedGroupName = trim((string) $request->query('group_name', '')) ?: 'all';
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $updatedSince = $request->query('updated_since');

        $allGroups = [];
        $companies = Company::whereNotNull('data_extraction_config')->get(['data_extraction_config']);
        foreach ($companies as $company) {
            $groups = $company->data_extraction_config ?? [];
            if (is_array($groups)) {
                $allGroups = array_merge($allGroups, $groups);
            }
        }

        $taskQuery = Task::where('status', 'evaluated')
            ->whereNotNull('analysis')
            ->where('analysis', 'like', '%extracted_data%')
            ->with('agent')
            ->orderByDesc('created_at');

        if ($selectedAgentId !== 'all') {
            $taskQuery->where('agent_id', $selectedAgentId);
        }

        if ($selectedGroupName !== 'all') {
            $matchingAgentIds = [];
            foreach ($allGroups as $group) {
                $name = is_array($group) ? ($group['group_name'] ?? null) : ($group->group_name ?? null);
                if ($name === $selectedGroupName) {
                    $ids = is_array($group) ? ($group['agent_ids'] ?? []) : ($group->agent_ids ?? []);
                    $matchingAgentIds = array_merge($matchingAgentIds, (array) $ids);
                }
            }
            $matchingAgentIds = array_values(array_unique($matchingAgentIds));
            $taskQuery->whereIn('agent_id', !empty($matchingAgentIds) ? $matchingAgentIds : [0]);
        }

        if ($startDate) {
            $taskQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $taskQuery->whereDate('created_at', '<=', $endDate);
        }
        if ($updatedSince) {
            $taskQuery->where('updated_at', '>', $updatedSince);
        }

        $tasks = $taskQuery->limit(100)->get();

        return response()->json([
            'data' => $tasks->map(function ($task) {
                $extractedData = $task->analysis['gpt_evaluation']['extracted_data']
                    ?? $task->analysis['extracted_data']
                    ?? [];

                return [
                    'id' => $task->id,
                    'workId' => $task->id,
                    'task_id' => $task->id,
                    'agent_id' => $task->agent_id,
                    'agent_name' => $task->agent?->name ?? 'Unknown',
                    'created_at' => optional($task->created_at)->toDateTimeString(),
                    'updated_at' => optional($task->updated_at)->toDateTimeString(),
                    'data' => $extractedData,
                    'duration' => $task->duration,
                    'sentiment' => $task->sentiment,
                    'score' => $task->score,
                    'risk_flag' => $task->risk_flag,
                    'transcription' => $task->transcription,
                ];
            })->values(),
        ]);
    }

    private function activepiecesUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Missing bearer token'], 401);
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException $e) {
            return response()->json(['message' => 'Invalid bearer token'], 401);
        }

        if (($payload['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json(['message' => 'Bearer token expired'], 401);
        }

        $user = User::find($payload['user_id'] ?? null);
        if (!$user || !$user->is_active) {
            return response()->json(['message' => 'Invalid bearer token'], 401);
        }

        return null;
    }
}
