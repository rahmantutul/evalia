<?php

namespace App\Http\Controllers;

use App\Models\TelephonyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    private function getToken()
    {
        return session('user_access_token');
    }

    private function getAuthHeaders()
    {
        $token = $this->getToken();
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    private function groupList()
    {
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders($this->getAuthHeaders())
            ->get('http://35.153.178.201:8080/list_groups');
        
        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData['data'] ?? [];
        } else {
            return back()->with('error', 'Failed to fetch group list: ' . $response->body());
        }
    }

    public function companyList()
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->get('http://35.153.178.201:8080/list_of_companies');

        $companies = [];
        if ($response->successful()) {
            $companies = $response->json()['data'] ?? [];
        }
        return view('user.company.company_list', compact('companies'));
    }

    public function companyCreate()
    {
        $groups = $this->groupList();
        return view('user.company.company_create', compact('groups'));
    }

    public function companyDetails($id)
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->get('http://35.153.178.201:8080/get_company_details', [
                'company_id' => $id
            ]);
        
        $company = $response->successful() ? ($response->json()['data'] ?? []) : [];
        
        $response_audio = Http::withHeaders($this->getAuthHeaders())
            ->get("http://35.153.178.201:8080/task_list", [
                'company_id' => $id
            ]);

        $taskList = $response_audio->successful() ? ($response_audio->json()['tasks'] ?? []) : [];

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
        
        return view('user.company.company_details', [
            'company' => $company,
            'taskList' => $paginatedTasks,
        ]);
    }

    public function companyDelete($id)
    {
        try {
            $response = Http::withHeaders($this->getAuthHeaders())
                ->delete("http://35.153.178.201:8080/delete_company?company_id={$id}");

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

        if (empty($data['company_id'])) {
            $companyId = Str::uuid()->toString();
        } else {
            $companyId = $data['company_id'];
        }

        $payload = [
            'company_name' => $data['company_name'] ?? '',
            'group_id' => $data['group_id'] ?? 'hassan',
            'company_id' => $companyId,
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

        $headers = $this->getAuthHeaders();
        $headers['X-Request-Verification'] = (string) Str::uuid();

        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders($headers)
            ->post('http://35.153.178.201:8080/register_company', $payload);
        
        if ($response->successful()) {
            $message = $request->has('telephony_account') 
                ? 'Company registered on both platform successfully'
                : 'Company registered successfully';
            
            $responseData = $response->json();
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $responseData,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'API registration request failed: ' . $response->body(),
        ], $response->status());
    }

    public function companyEdit($id)
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->get('http://35.153.178.201:8080/get_company_details', [
                'company_id' => $id
            ]);
        
        $company = $response->successful() ? ($response->json()['data'] ?? []) : [];
        $groups = $this->groupList();
        
        return view('user.company.company_edit', compact('company', 'groups'));
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

        $headers = $this->getAuthHeaders();
        $headers['X-Request-Verification'] = (string) Str::uuid();

        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders($headers)
            ->put('http://35.153.178.201:8080/update_company', $payload);

        if ($response->successful()) {
            $message = $request->has('telephony_account')
                ? 'Company updated on both platform successfully'
                : 'Company updated successfully';
            
            $responseData = $response->json();
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $responseData,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'API update request failed: ' . $response->body(),
        ], $response->status());
    }
}