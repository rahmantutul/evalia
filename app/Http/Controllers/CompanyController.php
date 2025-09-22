<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function __construct()
    {
        return \App\Helpers\ExternalApiHelper::getToken();
    }

    private function getToken()
    {
        return \App\Helpers\ExternalApiHelper::getToken();
    }

    private function makeAuthenticatedRequest($method, $url, $data = [], $options = [])
    {
        $token = $this->getToken();
        
        if (!$token) {
            throw new \Exception('Could not authenticate with external API: No token available');
        }

        $defaultOptions = [
            'timeout' => 30,
            'retry' => [3, 100],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'withToken' => true
        ];

        $options = array_merge($defaultOptions, $options);

        $http = Http::timeout($options['timeout'])->retry($options['retry'][0], $options['retry'][1]);

        if ($options['withToken']) {
            $http->withToken($token);
        }

        foreach ($options['headers'] as $key => $value) {
            $http->withHeaders([$key => $value]);
        }

        switch (strtoupper($method)) {
            case 'GET':
                return $http->get($url, $data);
            case 'POST':
                return $http->post($url, $data);
            case 'PUT':
                return $http->put($url, $data);
            case 'DELETE':
                return $http->delete($url, $data);
            default:
                return $http->get($url, $data);
        }
    }
    private function fetchAgentsData()
    {
        $response = $this->makeAuthenticatedRequest(
            'GET',
            'http://13.218.100.190:8080/api/v1/company-agents/list-agents'
        );

        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData['data'] ?? [];
        }

        return [];
    }

    public function companyList(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 100);
        
        $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/list_of_companies', [
            'page' => $page,
            'limit' => $limit
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            $companies = $responseData['data'] ?? [];
            $total = $responseData['total'] ?? count($companies);
            $perPage = $responseData['per_page'] ?? $limit;
            $currentPage = $responseData['current_page'] ?? $page;
            
            $paginatedCompanies = new LengthAwarePaginator(
                $companies,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => $request->query()
                ]
            );
            return view('user.company.company_list', compact('paginatedCompanies'));
        } else {
            return back()->with('error', 'Failed to fetch companies list');
        }
       
    }

    public function companyCreate()
    {
        $page =  1;
        $limit = 100;
        $groups = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/list_groups', [
            'page' => $page,
            'limit' => $limit
        ]);
        $groups= $groups['data'];
        return view('user.company.company_create',compact('groups'));
    }

    public function companyDetails($id)
    {
        try {
            // Get company details
            $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/get_company_details', [
                'company_id' => $id
            ]);
            $company = $response->successful() ? ($response->json()['data'] ?? []) : [];

            // Get task list
            $response_audio = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/task_list', [
                'company_id' => $id
            ]);

            $taskList = $response_audio->successful() ? ($response_audio->json()['tasks'] ?? []) : [];

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
                ['path' => Paginator::resolveCurrentPath()] 
            );
            $agents = $this->fetchAgentsData();
            return view('user.company.company_details', [
                'company' => $company,
                'taskList' => $paginatedTasks,
                'agents' => $agents,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function companyDelete($id)
    {
        try {
            // Append company_id as query parameter
            $url = "http://13.218.100.190:8080/api/v1/delete_company?company_id={$id}";

            $response = $this->makeAuthenticatedRequest('DELETE', $url);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Company deleted successfully.');
            } else {
                $errorMessage = $response->json()['detail'][0]['msg'] ?? $response->body();
                return redirect()->back()->with('error', 'Failed to delete company: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }


    public function companyStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'group_id' => 'nullable|string|max:255',
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
            'company_name' => $data['company_name'] ?? '',
            'group_id' => $data['group_id'] ?? '8d5fc194-c8ff-4bd1-a78c-547f32649ec6',
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

            $response = $this->makeAuthenticatedRequest('POST', 'http://13.218.100.190:8080/api/v1/register_company', $payload, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Request-Verification' => (string) Str::uuid(),
                ]
            ]);

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

    public function companyEdit($id)
    {
        try {
            $page =  1;
            $limit = 100;
            $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/get_company_details', [
                'company_id' => $id
            ]);

            $company = $response->successful() ? ($response->json()['data'] ?? []) : [];
            $groups = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/list_groups', [
            'page' => $page,
            'limit' => $limit
        ]);
            $groups= $groups['data'];
            return view('user.company.company_edit', compact('company','groups'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function companyCopy($id)
    {
        try {
            $page =  1;
            $limit = 100;
            $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/get_company_details', [
                'company_id' => $id
            ]);

            $company = $response->successful() ? ($response->json()['data'] ?? []) : [];
            $groups = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/list_groups', [
            'page' => $page,
            'limit' => $limit
        ]);
            $groups= $groups['data'];
            return view('user.company.company_copy', compact('company','groups'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function companyUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'group_id' => 'nullable|string|max:255',
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
            'company_id' => $data['company_id'] ?? '',
            'company_name' => $data['company_name'] ?? '',
            'group_id' => $data['group_id'] ?? '8d5fc194-c8ff-4bd1-a78c-547f32649ec6',
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

            $response = $this->makeAuthenticatedRequest('PUT', 'http://13.218.100.190:8080/api/v1/update_company', $payload, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Request-Verification' => (string) Str::uuid(),
                ]
            ]);
             Log::error('API Update Error Response', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payload' => $payload
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'success' => true,
                    'message' => 'Company updated successfully',
                    'data' => $responseData,
                ]);
            }

           

            return response()->json([
                'success' => false,
                'message' => 'API update request failed: ' . $response->body(),
            ], $response->status());
        
    }
}