<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class AgentController extends Controller
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

    private function fetchCompanyData()
    {
        $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/list_of_companies');
        
        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData['data'] ?? [];
        }

        return [];
    }

    public function agentList()
    {
        $agents = $this->fetchAgentsData();
        return view('user.agent.agent_list', compact('agents'));
    }

    public function agentCreate()
    {
        $agents = $this->fetchAgentsData();
        $companies = $this->fetchCompanyData();
        return view('user.agent.agent_create', compact('agents','companies'));
    }

    public function agentEdit($agentId)
    {
        $agents = $this->fetchAgentsData();
        $companies = $this->fetchCompanyData();

        $response = $response = $this->makeAuthenticatedRequest('GET', "http://13.218.100.190:8080/api/v1/company-agents/get-agent/{$agentId}");

        $agentData = $response->json();
        $agentData = $agentData['data'];
        return view('user.agent.agent_edit', compact('agents','companies','agentData'));
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

    public function deleteAgent($agentId)
    {
        try {
            $response = $this->makeAuthenticatedRequest('DELETE', 'http://13.218.100.190:8080/api/v1/delete_agent', [
                'agent_id' => $agentId
            ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Agent deleted successfully.');
            }

            return redirect()->back()->with('error', 'Failed to delete agent: ' . $response->body());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
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

            $queryString = http_build_query($payload);

            $response = $this->makeAuthenticatedRequest(
            'POST',
            "http://13.218.100.190:8080/api/v1/company-agents/add-agent?$queryString",
            [],
            [
                'headers' => [
                    'Accept'                 => 'application/json',
                    'X-Request-Verification' => (string) Str::uuid(),
                ]
            ]
        );

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Agent created successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to create agent: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }
    public function agentUpdate(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'agent_id'     => 'required|string',
            'company_id'   => 'required|string',
            'agent_name'   => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'description'  => 'nullable|string',
            'supervisor_id'=> 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ]);

        try {
            // Get agent ID from the request
            $agentId = $request->input('agent_id');

            // Build payload from the request
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

            // Convert payload to query string
            $queryString = http_build_query($payload);

            // Make the PUT request to the API
            $response = $this->makeAuthenticatedRequest(
                'PUT',
                "http://13.218.100.190:8080/api/v1/company-agents/update-agent/{$agentId}?{$queryString}",
                [], // Empty body since data is in query string
                [
                    'headers' => [
                        'Accept'                 => 'application/json',
                        'Content-Type'           => 'application/json',
                        'X-Request-Verification' => (string) Str::uuid(),
                    ]
                ]
            );

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Agent updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to update agent: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }
    public function updateAgent(Request $request, $agentId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $response = $this->makeAuthenticatedRequest('PUT', 'http://13.218.100.190:8080/api/v1/update_agent', [
                'agent_id' => $agentId,
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Agent updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to update agent: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }
     public function agentDelete($id)
    {
        try {
            $url = "http://13.218.100.190:8080/api/v1/company-agents/delete-agent/{$id}";

            $response = $this->makeAuthenticatedRequest('DELETE', $url);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Agent deleted successfully.');
            } else {
                $errorMessage = $response->json()['detail'][0]['msg'] ?? $response->body();
                return redirect()->back()->with('error', 'Failed to delete company: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }
}
























// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class AgentController extends Controller
// {
//     public function agentStore(Request $request)
//     {
//         return response()->json(['message' => 'Agent stored successfully']);
//     }

//     public function agentDetails()
//     {
//         return view('user.agent.agent_details');
//     }

//     public function agentTask()
//     {
//         return redirect()->back()->with('success', 'Agent deleted successfully');
//     }

//     public function agentList()
//     {
//         return view('user.agent.agent_list');
//     }

//     public function agentCreate()
//     {
//         return view('user.agent.agent_create');
//     }
//     public function agentEdit()
//     {
//         return view('user.agent.agent_create');
//     }
// }
