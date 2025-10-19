<?php

namespace App\Services;

use App\Helpers\ExternalApiHelper;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EvaliaApiService
{
    private string $baseUrl;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->baseUrl = config('evalia.base_url', 'http://13.218.100.190:8080');
    }

    private function getAuthToken(): ?string
    {
        if (!$this->accessToken) {
            $this->accessToken = ExternalApiHelper::getToken();
        }
        
        return $this->accessToken;
    }

    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;
        return $this;
    }

    public function makeRequest(string $method, string $endpoint, array $data = [], array $files = []): array
    {
        try {
            $token = $this->getAuthToken();
            
            if (!$token) {
                throw new Exception("Unable to obtain authentication token");
            }

            $http = Http::timeout(60)->withToken($token);

            $response = match (strtoupper($method)) {
                'GET' => $http->get($this->baseUrl . $endpoint, $data),
                'POST' => empty($files) 
                    ? $http->post($this->baseUrl . $endpoint, $data)
                    : $http->attach(...$files)->post($this->baseUrl . $endpoint, $data),
                'PUT' => $http->put($this->baseUrl . $endpoint, $data),
                'DELETE' => $http->delete($this->baseUrl . $endpoint, $data),
                'PATCH' => $http->patch($this->baseUrl . $endpoint, $data),
                default => throw new Exception("Unsupported HTTP method: {$method}")
            };

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 401) {
                Log::warning('EvaliaApiService: Token expired, retrying with fresh token');
                $this->accessToken = null; 
                $newToken = $this->getAuthToken();
                
                if ($newToken && $newToken !== $token) {
                    $retryResponse = Http::timeout(60)
                        ->withToken($newToken)
                        ->{strtolower($method)}($this->baseUrl . $endpoint, $data);
                    
                    if ($retryResponse->successful()) {
                        return $retryResponse->json();
                    }
                }
            }

            throw new Exception("API request failed: " . $response->body(), $response->status());
        } catch (Exception $e) {
            Log::error('Evalia API Request Failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getCurrentUser(): array
    {
        return $this->makeRequest('GET', '/auth/me');
    }

    public function updateCurrentUser(array $userData): array
    {
        return $this->makeRequest('PUT', '/auth/me', $userData);
    }

    public function deleteCurrentUser(): array
    {
        return $this->makeRequest('DELETE', '/auth/me');
    }

    public function listUsers(int $skip = 0, int $limit = 10): array
    {
        return $this->makeRequest('GET', '/admin/users', [
            'skip' => $skip,
            'limit' => $limit
        ]);
    }

    public function getUserById(string $userId): array
    {
        return $this->makeRequest('GET', "/admin/users/{$userId}");
    }

    public function updateUser(string $userId, array $userData): array
    {
        return $this->makeRequest('PUT', "/admin/users/{$userId}", $userData);
    }

    public function deleteUser(string $userId): array
    {
        return $this->makeRequest('DELETE', "/admin/users/{$userId}");
    }

    public function registerCompany(array $companyData): array
    {
        return $this->makeRequest('POST', '/api/v1/register_company', $companyData);
    }

    public function updateCompany(array $companyData): array
    {
        return $this->makeRequest('PUT', '/api/v1/update_company', $companyData);
    }

    public function listCompanies(int $page = 1, int $limit = 10): array
    {
        return $this->makeRequest('GET', '/api/v1/list_of_companies', [
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function getCompanyDetails(string $companyId): array
    {
        return $this->makeRequest('GET', '/api/v1/get_company_details', [
            'company_id' => $companyId
        ]);
    }

    public function deleteCompany(string $companyId): array
    {
        return $this->makeRequest('DELETE', '/delete_company', [
            'company_id' => $companyId
        ]);
    }

    public function listCurrentUserCompanies(int $page = 1, int $limit = 10): array
    {
        return $this->makeRequest('GET', '/list-companies-current-user', [
            'page' => $page,
            'limit' => $limit
        ]);
    }

    // public function addAgent(array $agentData): array
    // {
    //     // Convert payload to query string
    //     $queryString = http_build_query($agentData);

    //     return $this->makeRequest(
    //         'POST',
    //         "/company-agents/add-agent?{$queryString}",
    //     );
    // }

    public function updateAgent(string $agentId, array $agentData): array
    {
        $queryString = http_build_query($agentData);

        return $this->makeRequest(
            'PUT',
            "/api/v1/company-agents/update-agent/{$agentId}?{$queryString}",
        );
    }


    public function deleteAgent(string $agentId): array
    {
        return $this->makeRequest('DELETE', "/api/v1/company-agents/delete-agent/{$agentId}");
    }

    public function getSupervisorRating(string $supervisorId, string $period = 'all'): array
    {
        return $this->makeRequest('GET', "/supervisors/rating/{$supervisorId}", [
            'period' => $period
        ]);
    }

    public function addAgent( array $agentData): array
    {
        $queryString = http_build_query($agentData);

        return $this->makeRequest(
            'POST',
            "/api/v1/company-agents/add-agent?{$queryString}",
        );
    }

    public function listAgents(): array
    {
        
        return $this->makeRequest('GET', '/api/v1/company-agents/list-agents');
    }

    public function listSupervisors(): array
    {
        return $this->makeRequest('GET', '/company-agents/supervisors');
    }

    public function getAgentDetails(string $agentId, string $period = 'all'): array
    {
        return $this->makeRequest(
            'GET',
            "/api/v1/company-agents/get-agent/{$agentId}?period={$period}"
        );
    }



    public function getSupervisorRankings(string $period = 'month', int $limit = 10): array
    {
        return $this->makeRequest('GET', '/supervisors/rankings', [
            'period' => $period,
            'limit' => $limit
        ]);
    }

    public function getAnalysisResult(string $workId): array
    {
        return $this->makeRequest('GET', '/api/v1/analysis_result', [
            'work_id' => $workId
        ]);
    }

    public function getTaskList(string $companyId, int $page = 1, int $limit = 10): array
    {
        return $this->makeRequest('GET', '/api/v1/task_list', [
            'company_id' => $companyId,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function deleteTask(string $workId): array
    {
        return $this->makeRequest('DELETE', '/api/v1/delete_task', [
            'work_id' => $workId
        ]);
    }

    public function reEvaluateTask(string $taskUuid, string $companyId, string $groupId): array
    {
        return $this->makeRequest('POST', "/api/v1/re-evaluation/{$taskUuid}", [
            'company_id' => $companyId,
            'group_id' => $groupId
        ]);
    }

    public function getTaskRecords(string $agentId, int $page = 1, int $limit = 10): array
    {
        return $this->makeRequest('GET', "/api/v1/task-record/{$agentId}", [
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function processChatV2(string $transcription, string $sourceType, string $companyId): array
    {
        return $this->makeRequest('POST', '/api/v1/chat_processing_v2', [
            'transcription' => $transcription,
            'source_type' => $sourceType,
            'company_id' => $companyId
        ]);
    }

    public function processBackgroundAudio(string $companyId, string $agentId, array $audioFiles): array
    {
        $data = [
            'company_id' => $companyId,
            'agent_id' => $agentId
        ];

        return $this->makeRequest('POST', '/api/v1/background_processing_v2', $data, $audioFiles);
    }

    public function convertAudioToBase64(string $audioFilePath): array
    {
        return $this->makeRequest('POST', '/audio-base64', [], [
            ['file', fopen($audioFilePath, 'r'), basename($audioFilePath)]
        ]);
    }

    public function getCostAnalytics(string $companyId, string $startDate, string $endDate, ?string $provider = null, ?string $engine = null): array
    {
        $params = [
            'company_id' => $companyId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        if ($provider) $params['provider'] = $provider;
        if ($engine) $params['engine'] = $engine;

        return $this->makeRequest('GET', '/api/v1/costs/analytics', $params);
    }

    public function getCostSummary(string $companyId, int $days = 30): array
    {
        return $this->makeRequest('GET', "/api/v1/costs/summary/{$companyId}", [
            'days' => $days
        ]);
    }

    public function getMainDashboard(): array
    {
        return $this->makeRequest('GET', '/api/v1/main_dashboard');
    }

    public function addLlmKey(string $apiKey, string $llmName, ?string $companyId = null): array
    {
        $data = [
            'api_key' => $apiKey,
            'llm_name' => $llmName
        ];

        if ($companyId) {
            $data['company_id'] = $companyId;
        }

        return $this->makeRequest('POST', '/api/v1/add_llm_key', $data);
    }

    public function getLlmKeys(?string $companyId = null): array
    {
        $params = [];
        if ($companyId) {
            $params['company_id'] = $companyId;
        }

        return $this->makeRequest('GET', '/api/v1/get_llm_keys', $params);
    }

    public function getActiveLlmKey(string $llmName, ?string $companyId = null): array
    {
        $params = ['llm_name' => $llmName];
        if ($companyId) {
            $params['company_id'] = $companyId;
        }

        return $this->makeRequest('GET', '/api/v1/get_active_llm_key', $params);
    }

    public function createConfigProfile(array $profileData): array
    {
        return $this->makeRequest('POST', '/api/v1/config-profiles', $profileData);
    }

    public function getConfigProfile(string $profileId): array
    {
        return $this->makeRequest('GET', "/api/v1/config-profiles/{$profileId}");
    }

    public function updateConfigProfile(string $profileId, array $profileData): array
    {
        return $this->makeRequest('PUT', "/api/v1/config-profiles/{$profileId}", $profileData);
    }

    public function deleteConfigProfile(string $profileId): array
    {
        return $this->makeRequest('DELETE', "/api/v1/config-profiles/{$profileId}");
    }

    public function registerGroup(array $groupData): array
    {
        return $this->makeRequest('POST', '/api/v1/register_group', $groupData);
    }

    public function updateGroup(string $groupId, array $groupData): array
    {
        return $this->makeRequest(
            'PUT',
            "/api/v1/update_group?group_id={$groupId}",
            $groupData
        );
    }


    public function listGroups(int $page = 1, int $limit = 10): array
    {
        return $this->makeRequest('GET', '/api/v1/list_groups', [
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function getGroupDetails(string $groupId): array
    {
        return $this->makeRequest('GET', '/api/v1/get_group_details', [
            'group_id' => $groupId
        ]);
    }

    public function deleteGroup(string $groupId): array
    {
        return $this->makeRequest(
            'DELETE',
            "/api/v1/delete_group?group_id={$groupId}"
        );
    }

    public function getAllLlmPricing(): array
    {
        return $this->makeRequest('GET', '/api/v1/llm/pricing');
    }

    public function getAvailableEngines(): array
    {
        return $this->makeRequest('GET', '/api/v1/llm/engines');
    }

    public function updateLlmPricing(array $pricingData): array
    {
        return $this->makeRequest('POST', '/api/v1/llm/pricing', $pricingData);
    }

    public function getEnginePricing(string $engine): array
    {
        return $this->makeRequest('GET', "/api/v1/llm/pricing/{$engine}");
    }
}