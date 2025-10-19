<?php

namespace App\Http\Controllers;

use App\Services\EvaliaApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Exception;

class EvaliaController extends Controller
{
    protected EvaliaApiService $evaliaService;

    public function __construct(EvaliaApiService $evaliaService)
    {
        $this->evaliaService = $evaliaService;
    }

    public function dashboard()
    {
        return view('user.dashboard');
    }


    public function groupCreate()
    {
        return view('user.group.group_create');
    }

    public function listGroups(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            
            $response = $this->evaliaService->listGroups($page, $limit);
            
            // If you have a collection, create paginator manually
            $groups = collect($response['data'] ?? []);
            $paginatedGroups = new \Illuminate\Pagination\LengthAwarePaginator(
                $groups,
                $response['pagination']['total'] ?? 0,
                $limit,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('user.group.group_list', compact('paginatedGroups'));
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get group details
     */
    public function getGroupDetails(Request $request, $group_id)
    {
        try {
            $response = $this->evaliaService->getGroupDetails($group_id);
            $group =$response['data'];
            return view('user.group.group_edit', compact('group'));
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|string|max:255',
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cleanTagifyInput = function ($input) {
            if (empty($input)) return [];

            if (is_array($input)) {
                return array_map(fn($item) => is_array($item) ? $item['value'] : $item, $input);
            }

            if (str_starts_with($input, '[') || str_starts_with($input, '{')) {
                $decoded = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($decoded['value'])) {
                        return [$decoded['value']];
                    }
                    return array_map(fn($item) => is_array($item) ? $item['value'] : $item, $decoded);
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

        $response = $this->evaliaService->updateGroup($groupId, $payload);
        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'data' => $response,
        ]);
    }

    /**
     * Delete group
     */
    public function deleteGroup(string $groupId): JsonResponse
    {
        try {
            $response = $this->evaliaService->deleteGroup($groupId);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Agent Data Start

    public function agentList()
    {
        $response = $this->evaliaService->listAgents();
        $agents = $response['data'];
        return view('user.agent.agent_list', compact('agents'));
    }

    public function agentCreate()
    {
        // 1. Fetch all agents
        $agentResponse = $this->evaliaService->listAgents();

        // 2. Fetch companies
        $companiesResponse = $this->evaliaService->listCompanies();

        $agents =  $agentResponse['data'];
        $companies =  $companiesResponse['data'];

        return view('user.agent.agent_create', compact('agents','companies'));
    }

    public function agentEdit($agentId)
    {
        try {
            $agentResponse = $this->evaliaService->getAgentDetails($agentId);

            if (!isset($agentResponse['data'])) {
                return redirect()->back()->with('error', 'Invalid agent data from API.');
            }

            $agentData = $agentResponse['data'];
            $status = $agentResponse['status'] ?? 'active';

            $agentResponse = $this->evaliaService->listAgents();

            $companiesResponse = $this->evaliaService->listCompanies();

            $agents =  $agentResponse['data'];
            $companies =  $companiesResponse['data'];

            return view('user.agent.agent_edit', [
                'agentData' => $agentData,
                'agentId'   => $agentId,
                'agents'    => $agents,
                'companies' => $companies,
                'status'    => $status
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load agent edit page: ' . $e->getMessage());
        }
    }


    public function agentDetails($agentId)
    {
        try {
            $response = $this->makeAuthenticatedRequest('GET', "http://13.218.100.190:8080/api/v1/company-agents/get-agent/{$agentId}");
            if ($response->successful()) {
                $result = $response->json();
                
                if (!isset($result['data'])) {
                    return redirect()->back()->with('error', 'Invalid response structure from API.');
                }

                $data = $result['data'];
                return view('user.agent.agent_details', [
                    'data' => $data,
                    'agentId' => $agentId,
                    'status' => $result['status'] ?? 'active'
                ]);
            } else {
                return redirect()->back()->with('error', 'Failed to fetch agent details. API returned status: ' . $response->status());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }

    public function agentDelete($agentId)
    {
        try {
            $response = $this->evaliaService->deleteAgent($agentId);
            return redirect()->back()->with('success', 'Agent deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete agent: ' . $e->getMessage());
        }
    }

    public function agentStore(Request $request)
    {
        $request->validate([
            'company_id'   => 'required|string',
            'agent_name'   => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'description'  => 'nullable|string',
            'supervisor_id'=> 'nullable|string',
        ]);

        try {
            $payload = [
                'company_id'    => $request->input('company_id'),
                'agent_name'    => $request->input('agent_name'),
                'description'   => $request->input('description'),
                'supervisor_id' => $request->input('supervisor_id'),
                'email'         => $request->input('email'),
                'phone_number'  => $request->input('phone_number'),
                'is_active'     => true,
                'is_supervisor' => empty($request->input('supervisor_id')) ? true : false,
            ];

            $response = $this->evaliaService->addAgent($payload);

            return redirect()->back()->with('success', 'Agent created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create agent: ' . $e->getMessage());
        }
    }

    public function agentUpdate(Request $request, $agentId)
    {
        $request->validate([
            'company_id'   => 'required|string',
            'agent_name'   => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'description'  => 'nullable|string',
            'supervisor_id'=> 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ]);

        try {
            $payload = [
                'company_id'    => $request->input('company_id'),
                'agent_name'    => $request->input('agent_name'),
                'description'   => $request->input('description'),
                'supervisor_id' => $request->input('supervisor_id'),
                'email'         => $request->input('email'),
                'phone_number'  => $request->input('phone_number'),
                'is_active'     => $request->input('is_active', true),
                'is_supervisor' => empty($request->input('supervisor_id')) ? true : false,
            ];

            $response = $this->evaliaService->updateAgent($agentId, $payload);

            return redirect()->back()->with('success', 'Agent updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update agent: ' . $e->getMessage());
        }
    }



    public function companyList(Request $request)
    {
        $response = $this->evaliaService->listAgents();
        $companies = $response['data'];
        return view('user.company.company_list', compact('companies'));
    }
    
    public function companyEdit($id)
    {
        try {
            $companyResponse = $this->evaliaService->getCompanyDetails($id);
            // dd($companyResponse['data']['company_info']);
            $company = $companyResponse['data']['company_info'] ?? [];
            $groupsResponse = $this->evaliaService->listGroups();
            $groups = $groupsResponse['data'] ?? [];
            return view('user.company.company_edit', compact('company', 'groups'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }



    public function companyCreate()
    {
        $page =  1;
        $limit = 100;
        $groupsResponse = $this->evaliaService->listGroups();
        $groups = $groupsResponse['data'] ?? [];

        $groups = $this->evaliaService->listGroups();
        $groups= $groups['data'];
        return view('user.company.company_create',compact('groups'));
    }


    public function companyDetails($id)
    {
        try {
            // Fetch company details via service
            $company = $this->evaliaService->getCompanyDetails($id)['data']['company_info'] ?? [];


            // Fetch task list via service
            $taskList = $this->evaliaService->getCompanyTasks($id)['tasks'] ?? [];

            // Paginate tasks
            $page = Paginator::resolveCurrentPage();
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            $pagedTasks = array_slice($taskList, $offset, $perPage);

            $paginatedTasks = new LengthAwarePaginator(
                $pagedTasks,
                count($taskList),
                $perPage,
                $page,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => request()->query()
                ]
            );

            $agents = $this->fetchAgentsData(); // or use a service method

            return view('user.company.company_details', [
                'company' => $company,
                'taskList' => $paginatedTasks,
                'agents' => $agents,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }






































    public function getAnalysisResult(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'work_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->getAnalysisResult($request->input('work_id'));
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get task list
     */
    public function getTaskList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string',
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->getTaskList(
                $request->input('company_id'),
                $request->input('page', 1),
                $request->input('limit', 10)
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete task
     */
    public function deleteTask(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'work_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->deleteTask($request->input('work_id'));
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Re-evaluate task
     */
    public function reEvaluateTask(string $taskUuid, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string',
            'group_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->reEvaluateTask(
                $taskUuid,
                $request->input('company_id'),
                $request->input('group_id')
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get task records for agent
     */
    public function getTaskRecords(string $agentId, Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->getTaskRecords($agentId, $page, $limit);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== LLM KEY MANAGEMENT ====================

    /**
     * Add LLM API key
     */
    public function addLlmKey(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'llm_name' => 'required|string|in:openai,gemini,anthropic,google',
            'company_id' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->addLlmKey(
                $request->input('api_key'),
                $request->input('llm_name'),
                $request->input('company_id')
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all LLM keys
     */
    public function getLlmKeys(Request $request): JsonResponse
    {
        try {
            $response = $this->evaliaService->getLlmKeys($request->input('company_id'));
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get active LLM key
     */
    public function getActiveLlmKey(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'llm_name' => 'required|string|in:openai,gemini,anthropic,google',
            'company_id' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->getActiveLlmKey(
                $request->input('llm_name'),
                $request->input('company_id')
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update key expiration status
     */
    public function updateKeyExpiration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'is_expired' => 'required|boolean',
            'llm_name' => 'required|string',
            'company_id' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('PUT', '/api/v1/update_key_expiration', $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete LLM key
     */
    public function deleteLlmKey(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
            'company_id' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('DELETE', '/api/v1/delete_llm_key', $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== CONFIGURATION PROFILES ====================

    /**
     * Create configuration profile
     */
    public function createConfigProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_name' => 'required|string|max:255',
            'config_type' => 'required|string',
            'config_data' => 'required|array',
            'description' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->createConfigProfile($request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get profiles for dropdown
     */
    public function getProfilesForDropdown(string $configType): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/api/v1/config-profiles/dropdown/{$configType}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get configuration profile
     */
    public function getConfigProfile(string $profileId): JsonResponse
    {
        try {
            $response = $this->evaliaService->getConfigProfile($profileId);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update configuration profile
     */
    public function updateConfigProfile(string $profileId, Request $request): JsonResponse
    {
        try {
            $response = $this->evaliaService->updateConfigProfile($profileId, $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete configuration profile
     */
    public function deleteConfigProfile(string $profileId): JsonResponse
    {
        try {
            $response = $this->evaliaService->deleteConfigProfile($profileId);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Assign profile to companies
     */
    public function assignProfileToCompanies(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_id' => 'required|string',
            'company_ids' => 'required|array',
            'config_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('POST', '/api/v1/config-profiles/assign', $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get company effective configuration
     */
    public function getCompanyEffectiveConfig(string $companyId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/api/v1/companies/{$companyId}/effective-config");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get companies using profile
     */
    public function getCompaniesUsingProfile(string $profileId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/api/v1/config-profiles/{$profileId}/companies");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== KNOWLEDGE BASE MANAGEMENT ====================

    public function createKnowledgeBase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string',
            'file' => 'required|file',
            'data' => 'required|string' // JSON string
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('POST', '/api/v1/create_knowledgebase', $request->except('file'), [
                ['file', fopen($request->file('file')->getRealPath(), 'r'), $request->file('file')->getClientOriginalName()]
            ]);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateKnowledgeBase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string',
            'notebook_id' => 'required|string',
            'file' => 'required|file',
            'data' => 'required|string' // JSON string
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('PATCH', '/api/v1/update_knowledgebase', $request->except('file'), [
                ['file', fopen($request->file('file')->getRealPath(), 'r'), $request->file('file')->getClientOriginalName()]
            ]);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCompanyKnowledgeBases(string $companyId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/api/v1/knowledgebase/{$companyId}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllKnowledgeBases(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->makeRequest('GET', '/api/v1/get_all', [
                'page' => $page,
                'limit' => $limit
            ]);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteAllCompanyKnowledgeBases(string $companyId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('DELETE', "/api/v1/knowledgebase/{$companyId}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteKnowledgeBaseNotebook(string $companyId, string $notebookId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('DELETE', "/api/v1/knowledgebase_notebook/{$companyId}/{$notebookId}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== PRICING MANAGEMENT ====================

    public function getAllLlmPricing(): JsonResponse
    {
        try {
            $response = $this->evaliaService->getAllLlmPricing();
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAvailableEngines(): JsonResponse
    {
        try {
            $response = $this->evaliaService->getAvailableEngines();
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateLlmPricing(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'engine' => 'required|string',
            'provider' => 'required|string',
            'prompt_price_per_token' => 'required|numeric',
            'completion_price_per_token' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->updateLlmPricing($request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getEnginePricing(string $engine): JsonResponse
    {
        try {
            $response = $this->evaliaService->getEnginePricing($engine);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listUsers(Request $request): JsonResponse
    {
        try {
            $skip = $request->input('skip', 0);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->listUsers($skip, $limit);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user by ID (Admin only)
     */
    public function getUserById(string $userId): JsonResponse
    {
        try {
            $response = $this->evaliaService->getUserById($userId);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update user (Admin only)
     */
    public function updateUser(string $userId, Request $request): JsonResponse
    {
        try {
            $response = $this->evaliaService->updateUser($userId, $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete user (Admin only)
     */
    //     public function deleteUser(string $userId): JsonResponse
    //     {
    //         try {
    //             $response = $this->evaliaService->deleteUser($userId);
    //             return response()->json(['success' => true, 'data' => $response]);
    //         } catch (Exception $e) {
    //             return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //         }
    //     }
    // }, 'data' => $response]);
    //         } catch (Exception $e) {
    //             return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //         }
    //     }

    public function profile(): JsonResponse
    {
        try {
            $response = $this->evaliaService->getCurrentUser();
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->updateCurrentUser($request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteProfile(): JsonResponse
    {
        try {
            $response = $this->evaliaService->deleteCurrentUser();
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function registerCompany(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'group_id' => 'required|string',
            'filler_words' => 'sometimes|array',
            'main_topics' => 'sometimes|array',
            'call_types' => 'sometimes|array',
            'delay_accept_limit' => 'sometimes|numeric',
            'pause_accept_limit' => 'sometimes|numeric',
            'loudness_threshold' => 'sometimes|numeric',
            'interactive_threshold' => 'sometimes|numeric',
            'company_policies' => 'sometimes|array',
            'common_words_threshold' => 'sometimes|integer',
            'llm_api_limit' => 'sometimes|integer',
            'transcription_api_limit' => 'sometimes|integer',
            'call_outcomes' => 'sometimes|array',
            'agent_cooperation_configs' => 'sometimes|array',
            'agent_assessments_configs' => 'sometimes|array',
            'agent_performance_configs' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->registerCompany($request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listCompanies(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->listCurrentUserCompanies($page, $limit);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listAllCompanies(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->listCompanies($page, $limit);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCompanyDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->getCompanyDetails($request->input('company_id'));
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCompany(Request $request): JsonResponse
    {
        try {
            $response = $this->evaliaService->updateCompany($request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteCompany(string $companyId): JsonResponse
    {
        try {
            $response = $this->evaliaService->deleteCompany($companyId);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCompanyTaskCounts(): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', '/companies/task-counts');
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCompanyTaskCount(string $companyId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/companies/{$companyId}/task-count");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // public function addAgent(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'agent_name' => 'required|string|max:255',
    //         'company_id' => 'required|string',
    //         'description' => 'sometimes|string',
    //         'supervisor_id' => 'sometimes|string',
    //         'email' => 'sometimes|email',
    //         'phone_number' => 'sometimes|string'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $response = $this->evaliaService->addAgent($request->all());
    //         return response()->json(['success' => true, 'data' => $response]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    // public function listAgents()
    // {
    //     try {
    //         $response = $this->evaliaService->listAgents();
    //         $agents = $response['data'];

    //         return view('user.agent.agent_list', compact('agents'));

    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    // public function getAgentDetails(string $agentId, Request $request)
    // {
    //     try {
    //         $period = $request->input('period', 'all');
    //         $response = $this->evaliaService->getAgentDetails($agentId, $period);
    //         $data = $response['data'];
    //         return view('user.agent.agent_details', [
    //                 'data' => $data,
    //                 'agentId' => $agentId,
    //                 'status' => $result['status'] ?? 'active'
    //             ]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

// public function agentEdit(string $agentId, Request $request)
// {
//     try {
//         // 1. Fetch agent details
//         $period = $request->input('period', 'all');
//         $agentResponse = $this->evaliaService->getAgentDetails($agentId, $period);

//         if (!isset($agentResponse['data'])) {
//             return redirect()->back()->with('error', 'Invalid agent data from API.');
//         }

//         $agentData = $agentResponse['data'];
//         $status = $agentResponse['status'] ?? 'active';

//         // 2. Fetch all agents (if needed for dropdown or references)
//         $agents = $this->evaliaService->listAgents();

//         // 3. Fetch companies (pagination optional)
//         $companies = $this->evaliaService->listCompanies(); // default page=1, limit=10

//         // 4. Return edit view
//         return view('user.agent.agent_edit', [
//             'agentData' => $agentData,
//             'agentId' => $agentId,
//             'agents' => $agents,
//             'companies' => $companies,
//             'status' => $status
//         ]);
//     } catch (\Exception $e) {
//         return redirect()->back()->with('error', 'Failed to load agent edit page: ' . $e->getMessage());
//     }
// }


    // public function agentEdit($agentId)
    // {
    //     $agents = $this->fetchAgentsData();
    //     $companies = $this->fetchCompanyData();

    //     $response = $response = $this->makeAuthenticatedRequest('GET', "http://13.218.100.190:8080/api/v1/company-agents/get-agent/{$agentId}");

    //     $agentData = $response->json();
    //     $agentData = $agentData['data'];
    //     return view('user.agent.agent_edit', compact('agents','companies','agentData'));
    // }

    // public function updateAgent(string $agentId, Request $request): JsonResponse
    // {
    //     try {
    //         $response = $this->evaliaService->updateAgent($agentId, $request->all());
    //         return response()->json(['success' => true, 'data' => $response]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    // public function deleteAgent(string $agentId): JsonResponse
    // {
    //     try {
    //         $response = $this->evaliaService->deleteAgent($agentId);
    //         return response()->json(['success' => true, 'data' => $response]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    public function listSupervisors(): JsonResponse
    {
        try {
            $response = $this->evaliaService->listSupervisors();
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSupervisorRating(string $supervisorId, Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'all');
            $response = $this->evaliaService->getSupervisorRating($supervisorId, $period);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSupervisorRankings(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'month');
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->getSupervisorRankings($period, $limit);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function assignUserToCompany(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'company_id' => 'required|string',
            'role' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('POST', '/assign-user-company', $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listCompanyUsers(string $companyId, Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->makeRequest('GET', "/list-company-users/{$companyId}", [
                'page' => $page,
                'limit' => $limit
            ]);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listUserCompanies(string $userId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/list-user-companies/{$userId}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateUserCompanyRole(string $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('PUT', "/update-user-company-role/{$id}", $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function removeUserFromCompany(string $id): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('DELETE', "/remove-user-company/{$id}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function registerGroup(Request $request)
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
            'llm_total_usage' => [
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'total_tokens' => 0,
            ],
            'llm_total_usage_price' => 0,
            'transcription_api_limit' => (int)($data['transcription_api_limit'] ?? 100),
            'transcription_api_rate' => (float)($data['transcription_api_rate'] ?? 0.025),
            'transcription_api_total_usage' => 0,
            'call_outcomes' => $cleanTagifyInput($data['call_outcomes'] ?? ''),
            'agent_assessments_configs' => $cleanTagifyInput($data['agent_assessments_configs'] ?? ''),
            'agent_cooperation_configs' => $cleanTagifyInput($data['agent_cooperation_configs'] ?? ''),
            'agent_performance_configs' => $cleanTagifyInput($data['agent_performance_configs'] ?? ''),
            'qna_pair_prompt' => $data['qna_pair_prompt'] ?? '',
            'gem_qna_pair_eval' => $data['gem_qna_pair_eval'] ?? '',
            'gpt_qna_pair_eval' => $data['gpt_qna_pair_eval'] ?? '',
            'spelling_correction_prompt' => $data['spelling_correction_prompt'] ?? '',
        ];

        $responseData = $this->evaliaService->registerGroup($payload);

           return response()->json([
            'success' => true,
            'message' => 'Group updated successfully',
            'data' => $responseData,
        ]);
    }

    // public function registerGroup(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'group_name' => 'required|string|max:255',
    //         'description' => 'sometimes|string',
    //         'filler_words' => 'sometimes|array',
    //         'main_topics' => 'sometimes|array',
    //         'call_types' => 'sometimes|array',
    //         'delay_accept_limit' => 'sometimes|numeric',
    //         'pause_accept_limit' => 'sometimes|numeric',
    //         'delay_classes' => 'sometimes|array',
    //         'pause_classes' => 'sometimes|array',
    //         'loudness_threshold' => 'sometimes|numeric',
    //         'interactive_threshold' => 'sometimes|numeric',
    //         'company_policies' => 'sometimes|array',
    //         'common_words_threshold' => 'sometimes|integer',
    //         'llm_api_limit' => 'sometimes|integer',
    //         'transcription_api_limit' => 'sometimes|integer',
    //         'transcription_api_rate' => 'sometimes|numeric',
    //         'qna_pair_prompt' => 'sometimes|string',
    //         'gem_qna_pair_eval' => 'sometimes|string',
    //         'gpt_qna_pair_eval' => 'sometimes|string',
    //         'call_outcomes' => 'sometimes|array',
    //         'agent_cooperation_configs' => 'sometimes|array',
    //         'agent_assessments_configs' => 'sometimes|array',
    //         'agent_performance_configs' => 'sometimes|array',
    //         'spelling_correction_prompt' => 'sometimes|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $response = $this->evaliaService->registerGroup($request->all());
    //         return response()->json(['success' => true, 'data' => $response]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }




    

    // ==================== USER-GROUP ASSOCIATIONS ====================

    /**
     * Assign user to group
     */
    public function assignUserToGroup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'group_id' => 'required|string',
            'role' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('POST', '/api/v1/assign-user-group', [], $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List group users
     */
    public function listGroupUsers(string $groupId, Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $response = $this->evaliaService->makeRequest('GET', "/api/v1/list-group-users/{$groupId}", [
                'page' => $page,
                'limit' => $limit
            ]);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List user groups
     */
    public function listUserGroups(string $userId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('GET', "/api/v1/list-user-groups/{$userId}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update user group role
     */
    public function updateUserGroupRole(string $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->makeRequest('PUT', "/api/v1/update-user-group-role/{$id}", [], $request->all());
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove user from group (by record ID)
     */
    public function removeUserFromGroup(string $id): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('DELETE', "/api/v1/remove-user-group/{$id}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove user from group (by user and group ID)
     */
    public function removeUserFromGroupByIds(string $userId, string $groupId): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('DELETE', "/api/v1/remove-user-from-group/{$userId}/{$groupId}");
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== PERFORMANCE MANAGEMENT ====================

    /**
     * Update agent performance (Admin)
     */
    public function updateAgentPerformance(): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('POST', '/admin/update-agent-performance');
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Trigger performance update cron (Admin)
     */
    public function triggerPerformanceUpdateCron(): JsonResponse
    {
        try {
            $response = $this->evaliaService->makeRequest('POST', '/admin/trigger-performance-update-cron');
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== COST ANALYTICS ====================

    /**
     * Get cost analytics
     */
    public function getCostAnalytics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'provider' => 'sometimes|string',
            'engine' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->getCostAnalytics(
                $request->input('company_id'),
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('provider'),
                $request->input('engine')
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get cost summary
     */
    public function getCostSummary(string $companyId, Request $request): JsonResponse
    {
        try {
            $days = $request->input('days', 30);
            $response = $this->evaliaService->getCostSummary($companyId, $days);
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== AUDIO PROCESSING ====================

    /**
     * Process chat transcription
     */
    public function processChatV2(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transcription' => 'required|string',
            'source_type' => 'required|string|in:chatbot,agent',
            'company_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = $this->evaliaService->processChatV2(
                $request->input('transcription'),
                $request->input('source_type'),
                $request->input('company_id')
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process background audio files
     */
    public function processBackgroundAudio(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string',
            'agent_id' => 'required|string',
            'agent_audio' => 'required|file|mimes:wav',
            'customer_audio' => 'required|file|mimes:wav',
            'combined_audio' => 'sometimes|file|mimes:wav,mp3'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $audioFiles = [];
            
            if ($request->hasFile('agent_audio')) {
                $audioFiles[] = [
                    'agent_audio',
                    fopen($request->file('agent_audio')->getRealPath(), 'r'),
                    $request->file('agent_audio')->getClientOriginalName()
                ];
            }

            if ($request->hasFile('customer_audio')) {
                $audioFiles[] = [
                    'customer_audio',
                    fopen($request->file('customer_audio')->getRealPath(), 'r'),
                    $request->file('customer_audio')->getClientOriginalName()
                ];
            }

            if ($request->hasFile('combined_audio')) {
                $audioFiles[] = [
                    'combined_audio',
                    fopen($request->file('combined_audio')->getRealPath(), 'r'),
                    $request->file('combined_audio')->getClientOriginalName()
                ];
            }

            $response = $this->evaliaService->processBackgroundAudio(
                $request->input('company_id'),
                $request->input('agent_id'),
                $audioFiles
            );
            return response()->json(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
    /**
     * Convert audio to base64
     */
    // public function convertAudioToBase64(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|file|mimes:wav'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $file = $request->file('file');
    //         $response = $this->evaliaService->convertAudioToBase64($file->getRealPath());
    //         return response()->json(['success' => true