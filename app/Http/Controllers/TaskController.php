<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaskController extends Controller
{
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

    public function TaskList(Request $request, $companyId)
    {
        try {
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 100);
            
            $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/task_list', [
                'company_id' => $companyId,
                'page' => $page,
                'limit' => $limit
            ]);
            
            if ($response->successful()) {
                $responseData = $response->json();
                $tasks = $responseData['data'] ?? [];
                $total = $responseData['total'] ?? count($tasks);
                $perPage = $responseData['per_page'] ?? $limit;
                $currentPage = $responseData['current_page'] ?? $page;
                
                $paginatedTask = new LengthAwarePaginator(
                    $tasks,
                    $total,
                    $perPage,
                    $currentPage,
                    [
                        'path' => Paginator::resolveCurrentPath(),
                        'query' => $request->query()
                    ]
                );
                return view('user.task.task_list', compact('paginatedTask', 'companyId'));
            } else {
                return back()->with('error', 'Failed to fetch task list: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function taskDetails($workId)
    {
        try {
            $response = $this->makeAuthenticatedRequest('GET', 'http://13.218.100.190:8080/api/v1/analysis_result', [
                'work_id' => $workId
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (!isset($result['data'])) {
                    return redirect()->back()->with('error', 'Invalid response structure from API.');
                }

                $data = $result['data'];
                return view('user.task.task_details', [
                    'data' => $data,
                    'workId' => $workId,
                    'status' => $result['status'] ?? 'completed'
                ]);
            } else {
                return redirect()->back()->with('error', 'Failed to fetch analysis result. API returned status: ' . $response->status());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }

    public function deleteTask($workId)
    {
        try {
            $response = $this->makeAuthenticatedRequest('DELETE', 'http://13.218.100.190:8080/api/v1/delete_task', [
                'work_id' => $workId
            ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Task deleted successfully.');
            }

            return redirect()->back()->with('error', 'Failed to delete task: ' . $response->body());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }

   public function taskStore(Request $request)
    {
        $request->validate([
            'company_id' => 'required|string',
            'agent_id' => 'required|string',
            'group_id' => 'required|string',
            'agent_audio' => 'required|file|mimes:mp3,wav|max:51200',
            'customer_audio' => 'required|file|mimes:mp3,wav|max:51200',
            'combined_audio' => 'nullable|file|mimes:mp3,wav|max:102400',
        ]);

        $token = $this->getToken();

        if (!$token) {
            throw new \Exception('Could not authenticate with external API: No token available');
        }

        $client = new \GuzzleHttp\Client();

        $multipart = [
            [
                'name' => 'company_id',
                'contents' => $request->input('company_id')
            ],
            [
                'name' => 'agent_id',
                'contents' => $request->input('agent_id')
            ],
            [
                'name' => 'group_id',
                'contents' => $request->input('group_id')
            ],
            [
                'name' => 'agent_audio',
                'contents' => fopen($request->file('agent_audio')->getRealPath(), 'r'),
                'filename' => $request->file('agent_audio')->getClientOriginalName(),
                'headers' => [
                    'Content-Type' => $request->file('agent_audio')->getMimeType()
                ]
            ],
            [
                'name' => 'customer_audio',
                'contents' => fopen($request->file('customer_audio')->getRealPath(), 'r'),
                'filename' => $request->file('customer_audio')->getClientOriginalName(),
                'headers' => [
                    'Content-Type' => $request->file('customer_audio')->getMimeType()
                ]
            ],
        ];

        if ($request->hasFile('combined_audio')) {
            $multipart[] = [
                'name' => 'combined_audio',
                'contents' => fopen($request->file('combined_audio')->getRealPath(), 'r'),
                'filename' => $request->file('combined_audio')->getClientOriginalName(),
                'headers' => [
                    'Content-Type' => $request->file('combined_audio')->getMimeType()
                ]
            ];
        } else {
            // send empty field just like curl example
            $multipart[] = [
                'name' => 'combined_audio',
                'contents' => ''
            ];
        }

        try {
            $response = $client->post('http://13.218.100.190:8080/api/v1/background_processing_v2', [
                'multipart' => $multipart,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'timeout' => 60, // increase in case large files
            ]);

            $responseBody = json_decode($response->getBody(), true);

            return redirect()->back()->with('success', 'Audio analysis completed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process audio: ' . $e->getMessage());
        }
    }

}