<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\ExternalApiService;
class TaskController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->middleware('auth.api');
        $this->apiService = $apiService;
    }

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

   public function TaskList($companyId, Request $request)
{
    $response_audio = Http::withHeaders($this->getAuthHeaders())
        ->get("http://35.153.178.201:8080/task_list", [
            'company_id' => $companyId
        ]);

    $taskList = $response_audio->successful() ? ($response_audio->json()['tasks'] ?? []) : [];

    // Apply filters
    $filteredTasks = $this->applyFilters($taskList, $request);

    // Sort tasks by created_at in descending order (latest first)
    usort($filteredTasks, function($a, $b) {
        $dateA = strtotime($a['created_at'] ?? 0);
        $dateB = strtotime($b['created_at'] ?? 0);
        return $dateB - $dateA; // Descending order
    });

    // Check if any task has running status for auto-refresh
    $hasRunningTasks = collect($filteredTasks)->contains('status', 'running');

    $page = Paginator::resolveCurrentPage(); 
    $perPage = 100;
    $offset = ($page - 1) * $perPage;

    $pagedTasks = array_slice($filteredTasks, $offset, $perPage);

    $paginatedTasks = new LengthAwarePaginator(
        $pagedTasks,
        count($filteredTasks),
        $perPage,
        $page,
        [
            'path' => route('user.task.list', ['companyId' => $companyId]),
            'query' => $request->query()
        ]
    );
    $agentsResult = $this->apiService->getAgentsList();
    
    if (!$agentsResult['success']) {
        return back()->with('error', $agentsResult['error']);
    }
    $companyAgents = $agentsResult['agents'];
    return view('user.task.task_list', [
        'company_id' => $companyId,
        'taskList' => $paginatedTasks,
        'hasRunningTasks' => $hasRunningTasks,
        'companyAgents' => $companyAgents,
    ]);
}

private function applyFilters($tasks, $request)
{
    $status = $request->get('status', 'all');
    $time_range = $request->get('time_range', 'all');

    return collect($tasks)->filter(function($task) use ($status, $time_range) {
        // Status filter
        if ($status !== 'all' && $task['status'] !== $status) {
            return false;
        }

        // Time range filter
        if ($time_range !== 'all') {
            $taskDate = strtotime($task['created_at']);
            $now = time();
            
            switch ($time_range) {
                case 'today':
                    $startOfDay = strtotime('today');
                    if ($taskDate < $startOfDay) return false;
                    break;
                    
                case 'yesterday':
                    $startOfYesterday = strtotime('yesterday');
                    $endOfYesterday = strtotime('today') - 1;
                    if ($taskDate < $startOfYesterday || $taskDate > $endOfYesterday) return false;
                    break;
                    
                case 'last7':
                    $sevenDaysAgo = strtotime('-7 days');
                    if ($taskDate < $sevenDaysAgo) return false;
                    break;
                    
                case 'last30':
                    $thirtyDaysAgo = strtotime('-30 days');
                    if ($taskDate < $thirtyDaysAgo) return false;
                    break;
            }
        }

        return true;
    })->values()->all();
}

    public function taskDetails($workId)
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->timeout(30)
            ->retry(3, 100)
            ->get("http://35.153.178.201:8080/analysis_result/" . urlencode($workId));

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
    }

    public function deleteTask($workId)
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->timeout(30)
            ->retry(3, 100)
            ->delete("http://35.153.178.201:8080/delete_task?work_id=" . urlencode($workId));

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Task deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete task. API returned status: ' . $response->status());
    }

    public function taskStore(Request $request)
    {
        dd('Okay');
        $request->validate([
            'company_id' => 'required|string',
            'agent_id' => 'required|string',
            'agent_audio' => 'required',
            'customer_audio' => 'required',
            'combined_audio' => 'nullable',
        ]);

        $http = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getToken(),
        ])
        ->timeout(60)
        ->retry(3, 100);

        // Attach files before sending
        $http->attach(
            'agent_audio',
            fopen($request->file('agent_audio')->path(), 'r'),
            $request->file('agent_audio')->getClientOriginalName()
        );

        $http->attach(
            'customer_audio',
            fopen($request->file('customer_audio')->path(), 'r'),
            $request->file('customer_audio')->getClientOriginalName()
        );

        if ($request->hasFile('combined_audio')) {
            $http->attach(
                'combined_audio',
                fopen($request->file('combined_audio')->path(), 'r'),
                $request->file('combined_audio')->getClientOriginalName()
            );
        }

        // ✅ Now send the request only once
        $response = $http->post('http://35.153.178.201:8080/background_processing_v2', [
            'company_id' => $request->input('company_id'),
            'agent_id' => $request->input('agent_id'),
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Audio analysis started successfully!');
        }

        return back()->with('error', 'Failed to start audio analysis. API returned status: ' . $response->status());
    }


    public function reEvaluateTask(string $taskUuid)
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->timeout(30)
            ->retry(3, 100)
            ->post("http://35.153.178.201:8080/re-evaluation/{$taskUuid}");

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Task re-evaluation started successfully!');
        }

        return redirect()->back()->with('error', 'Failed to start re-evaluation. API returned status: ' . $response->status());
    }

    /**
     * Get task status for AJAX polling
     */
    public function getTaskStatus($companyId)
    {
        $response = Http::withHeaders($this->getAuthHeaders())
            ->get("http://35.153.178.201:8080/task_list", [
                'company_id' => $companyId
            ]);

        if ($response->successful()) {
            $taskList = $response->json()['tasks'] ?? [];
            
            // Check if any tasks are still running
            $hasRunningTasks = collect($taskList)->contains('status', 'running');
            
            return response()->json([
                'hasRunningTasks' => $hasRunningTasks,
                'tasks' => $taskList
            ]);
        }

        return response()->json([
            'hasRunningTasks' => false,
            'tasks' => []
        ], 500);
    }
}