<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GroupDataController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('app.api_base_url', 'http://35.153.178.201:8080');
    }

    public function groupList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 100);
        
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->get($this->baseUrl . '/list_groups', [
                'page' => $page,
                'limit' => $limit
            ]);
        
        if ($response->successful()) {
            $responseData = $response->json();
            $groups = $responseData['data'] ?? [];
            $total = $responseData['total'] ?? count($groups);
            $perPage = $responseData['per_page'] ?? $limit;
            $currentPage = $responseData['current_page'] ?? $page;
            
            $paginatedGroups = new LengthAwarePaginator(
                $groups,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => $request->query()
                ]
            );
            return view('user.group.group_list', compact('paginatedGroups'));
        } else {
            return back()->with('error', 'Failed to fetch group list: ' . $response->body());
        }
    }

    public function groupCreate()
    {
        return view('user.group.group_create');
    }

    public function getGroupDetails(Request $request, $group_id)
    {
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->get($this->baseUrl . '/get_group_details', [
                'group_id' => $group_id
            ]);
            
        $group = $response['data'];
        return view('user.group.group_edit', compact('group'));
    }

    public function groupDelete($id)
    {
        try {
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->delete($this->baseUrl . "/delete_group?group_id={$id}");

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Group deleted successfully.');
            } else {
                $errorMessage = $response->json()['detail'][0]['msg'] ?? $response->body();
                return redirect()->back()->with('error', 'Failed to delete Group: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }

    public function groupStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'filler_words' => 'nullable|string',
            'main_topics' => 'nullable|string',
            'call_types' => 'nullable|string',
            'delay_accept_limit' => 'nullable|numeric',
            'pause_accept_limit' => 'nullable|numeric',
            'delay_classes_medium' => 'nullable|numeric',
            'delay_classes_short' => 'nullable|numeric',
            'pause_classes_medium' => 'nullable|numeric',
            'pause_classes_short' => 'nullable|numeric',
            'loudness_threshold' => 'nullable|numeric',
            'interactive_threshold' => 'nullable|numeric',
            'company_policies' => 'nullable|string',
            'common_words_threshold' => 'nullable|numeric',
            'llm_api_limit' => 'nullable|integer',
            'transcription_api_limit' => 'nullable|integer',
            'transcription_api_rate' => 'nullable|numeric',
            'call_outcomes' => 'nullable|string',
            'agent_assessments_configs' => 'nullable|string',
            'agent_cooperation_configs' => 'nullable|string',
            'agent_performance_configs' => 'nullable|string',
            'qna_pair_prompt' => 'nullable|string',
            'gem_qna_pair_eval' => 'nullable|string',
            'gpt_qna_pair_eval' => 'nullable|string',
            'spelling_correction_prompt' => 'nullable|string',
        ]);

        $cleanTagifyInput = function ($input) {
            if (empty($input)) return [];
            
            if (is_array($input)) {
                return array_map(function ($item) {
                    return is_array($item) ? $item['value'] : $item;
                }, $input);
            }
            
            if (str_starts_with($input, '[') || str_starts_with($input, '{')) {
                $decoded = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($decoded['value'])) {
                        return [$decoded['value']];
                    }
                    return array_map(function ($item) {
                        return is_array($item) ? $item['value'] : $item;
                    }, $decoded);
                }
            }
            
            return array_filter(array_map('trim', explode(',', $input)));
        };

        $data = $validator->validated();

        $payload = [
            'group_name' => $data['group_name'] ?? '',
            'description' => $data['description'] ?? '',
            'filler_words' => $cleanTagifyInput($data['filler_words'] ?? ''),
            'main_topics' => $cleanTagifyInput($data['main_topics'] ?? ''),
            'call_types' => $cleanTagifyInput($data['call_types'] ?? ''),
            'delay_accept_limit' => (float)($data['delay_accept_limit'] ?? 0),
            'pause_accept_limit' => (float)($data['pause_accept_limit'] ?? 0),
            'delay_classes' => [
                'medium' => (float)($data['delay_classes_medium'] ?? 2.4),
                'short' => (float)($data['delay_classes_short'] ?? 1.2),
            ],
            'pause_classes' => [
                'medium' => (float)($data['pause_classes_medium'] ?? 2.4),
                'short' => (float)($data['pause_classes_short'] ?? 1.2),
            ],
            'loudness_threshold' => (float)($data['loudness_threshold'] ?? 0),
            'interactive_threshold' => (float)($data['interactive_threshold'] ?? 0),
            'company_policies' => $cleanTagifyInput($data['company_policies'] ?? ''),
            'common_words_threshold' => (int)($data['common_words_threshold'] ?? 0),
            'llm_api_limit' => (int)($data['llm_api_limit'] ?? 100),
            'transcription_api_limit' => (int)($data['transcription_api_limit'] ?? 100),
            'transcription_api_rate' => (float)($data['transcription_api_rate'] ?? 0.025),
            'call_outcomes' => $cleanTagifyInput($data['call_outcomes'] ?? ''),
            'agent_assessments_configs' => $cleanTagifyInput($data['agent_assessments_configs'] ?? ''),
            'agent_cooperation_configs' => $cleanTagifyInput($data['agent_cooperation_configs'] ?? ''),
            'agent_performance_configs' => $cleanTagifyInput($data['agent_performance_configs'] ?? ''),
            'qna_pair_prompt' => $data['qna_pair_prompt'] ?? '',
            'gem_qna_pair_eval' => $data['gem_qna_pair_eval'] ?? '',
            'gpt_qna_pair_eval' => $data['gpt_qna_pair_eval'] ?? '',
            'spelling_correction_prompt' => $data['spelling_correction_prompt'] ?? '',
        ];

        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Request-Verification' => (string) Str::uuid(),
            ])
            ->post($this->baseUrl . '/register_group', $payload);

        if ($response->successful()) {
            $responseData = $response->json();

            return response()->json([
                'success' => true,
                'message' => 'Company registered successfully',
                'data' => $responseData,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'API request failed: ' . $response->body(),
        ], $response->status());
    }

    public function groupEdit($id)
    {
        try {
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get($this->baseUrl . '/get_group_details', [
                    'group_id' => $id
                ]);

            $group = $response->successful() ? ($response->json()['data'] ?? []) : [];
            return view('user.group.group_edit', compact('group'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function groupUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|string|max:255',
            'group_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'group_id' => 'required|string|max:255',
            'filler_words' => 'nullable|string',
            'main_topics' => 'nullable|string',
            'call_types' => 'nullable|string',
            'delay_accept_limit' => 'nullable|numeric',
            'pause_accept_limit' => 'nullable|numeric',
            'delay_classes_medium' => 'nullable|numeric',
            'delay_classes_short' => 'nullable|numeric',
            'pause_classes_medium' => 'nullable|numeric',
            'pause_classes_short' => 'nullable|numeric',
            'loudness_threshold' => 'nullable|numeric',
            'interactive_threshold' => 'nullable|numeric',
            'company_policies' => 'nullable|string',
            'common_words_threshold' => 'nullable|numeric',
            'llm_api_limit' => 'nullable|integer',
            'transcription_api_limit' => 'nullable|integer',
            'transcription_api_rate' => 'nullable|numeric',
            'call_outcomes' => 'nullable|string',
            'agent_assessments_configs' => 'nullable|string',
            'agent_cooperation_configs' => 'nullable|string',
            'agent_performance_configs' => 'nullable|string',
            'qna_pair_prompt' => 'nullable|string',
            'gem_qna_pair_eval' => 'nullable|string',
            'gpt_qna_pair_eval' => 'nullable|string',
            'spelling_correction_prompt' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cleanTagifyInput = function ($input) {
            if (empty($input)) return [];
            
            if (is_array($input)) {
                return array_map(function ($item) {
                    return is_array($item) ? $item['value'] : $item;
                }, $input);
            }
            
            if (str_starts_with($input, '[') || str_starts_with($input, '{')) {
                $decoded = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($decoded['value'])) {
                        return [$decoded['value']];
                    }
                    return array_map(function ($item) {
                        return is_array($item) ? $item['value'] : $item;
                    }, $decoded);
                }
            }
            
            return array_filter(array_map('trim', explode(',', $input)));
        };

        $data = $validator->validated();

        $payload = [
            'group_id' => $data['group_id'] ?? '',
            'group_name' => $data['group_name'] ?? '',
            'description' => $data['description'] ?? '',
            'filler_words' => $cleanTagifyInput($data['filler_words'] ?? ''),
            'main_topics' => $cleanTagifyInput($data['main_topics'] ?? ''),
            'call_types' => $cleanTagifyInput($data['call_types'] ?? ''),
            'delay_accept_limit' => (float)($data['delay_accept_limit'] ?? 0),
            'pause_accept_limit' => (float)($data['pause_accept_limit'] ?? 0),
            'delay_classes' => [
                'medium' => (float)($data['delay_classes_medium'] ?? 2.4),
                'short' => (float)($data['delay_classes_short'] ?? 1.2),
            ],
            'pause_classes' => [
                'medium' => (float)($data['pause_classes_medium'] ?? 2.4),
                'short' => (float)($data['pause_classes_short'] ?? 1.2),
            ],
            'loudness_threshold' => (float)($data['loudness_threshold'] ?? 0),
            'interactive_threshold' => (float)($data['interactive_threshold'] ?? 0),
            'company_policies' => $cleanTagifyInput($data['company_policies'] ?? ''),
            'common_words_threshold' => (int)($data['common_words_threshold'] ?? 0),
            'llm_api_limit' => (int)($data['llm_api_limit'] ?? 100),
            'transcription_api_limit' => (int)($data['transcription_api_limit'] ?? 100),
            'transcription_api_rate' => (float)($data['transcription_api_rate'] ?? 0.025),
            'call_outcomes' => $cleanTagifyInput($data['call_outcomes'] ?? ''),
            'agent_assessments_configs' => $cleanTagifyInput($data['agent_assessments_configs'] ?? ''),
            'agent_cooperation_configs' => $cleanTagifyInput($data['agent_cooperation_configs'] ?? ''),
            'agent_performance_configs' => $cleanTagifyInput($data['agent_performance_configs'] ?? ''),
            'qna_pair_prompt' => $data['qna_pair_prompt'] ?? '',
            'gem_qna_pair_eval' => $data['gem_qna_pair_eval'] ?? '',
            'gpt_qna_pair_eval' => $data['gpt_qna_pair_eval'] ?? '',
            'spelling_correction_prompt' => $data['spelling_correction_prompt'] ?? '',
        ];
        
        $groupId = $data['group_id'];

        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Request-Verification' => (string) Str::uuid(),
            ])
            ->put($this->baseUrl . "/update_group?group_id={$groupId}", $payload);
        
        if ($response->successful()) {
            $responseData = $response->json();

            return response()->json([
                'success' => true,
                'message' => 'Group updated successfully',
                'data' => $responseData,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'API update request failed: ' . $response->body(),
        ], $response->status());
    }
}