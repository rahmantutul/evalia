<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Services\ExternalApiService;

class AgentController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->middleware('auth.api');
        $this->apiService = $apiService;
    }

    /**
     * Display agent dashboard with performance summary
     */
    public function dashboard()
    {
        $summaryResult = $this->apiService->getAgentDashboardSummary();
        
        if (!$summaryResult['success']) {
            return back()->with('error', $summaryResult['error']);
        }

        $agentsResult = $this->apiService->getAgentsList();
        if (!$agentsResult['success']) {
            return back()->with('error', $agentsResult['error']);
        }

        $summary = $summaryResult['data'];
        $agents = $agentsResult['agents'];

        return view('user.agents.dashboard', compact('summary', 'agents'));
    }

    /**
     * Display agent details
     */
    public function show($agentId)
    {
        // Get agent performance history which includes agent details
        $performanceResult = $this->apiService->getAgentPerformanceHistory($agentId);
        
        if (!$performanceResult['success']) {
            return back()->with('error', $performanceResult['error']);
        }

        $agent = $performanceResult['data'];
        return view('user.agents.show', compact('agent'));
    }

    /**
     * Display agent performance history
     */
    public function performanceHistory($agentId)
    {
        $result = $this->apiService->getAgentPerformanceHistory($agentId);
        
        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        $performance = $result['data'];
        return view('user.agents.performance_history', compact('performance'));
    }

    /**
     * Display only agent list (if needed separately)
     */
    public function index()
    {
        // Get dashboard summary for statistics
        $summaryResult = $this->apiService->getAgentDashboardSummary();
        
        if (!$summaryResult['success']) {
            return back()->with('error', $summaryResult['error']);
        }

        // Get all users and filter for agents
        $page = 1;
        $limit = 1000;
        $skip = ($page - 1) * $limit;

        $result = $this->apiService->listUsers($skip, $limit);
        
        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        $allUsers = $result['users'] ?? [];
        
        // Filter only active agents (with role-based company filter)
        $agents = array_filter($allUsers, function($user) {
            $isAgent = is_array($user) && 
                ($user['is_active'] ?? false) === true && 
                ($user['role']['name'] ?? '') === 'Agent';

            if (!$isAgent) {
                return false;
            }

            if (session('user.role.name') === 'Supervisor') {
                $allowedCompanies = ['الضمان الاجتماعي - الأردن', 'البنك العربي'];
                return in_array($user['company_name'] ?? '', $allowedCompanies);
            }

            return true;
        });

        // Reset array keys
        $agents = array_values($agents);

        // Get performance history for each agent
        $agentsWithPerformance = [];
        foreach ($agents as $agent) {
            $performanceResult = $this->apiService->getAgentPerformanceHistory($agent['id']);
            
            $agentsWithPerformance[] = [
                'agent' => $agent,
                'performance' => $performanceResult['success'] ? $performanceResult['data'] : null,
                'performance_error' => $performanceResult['success'] ? null : $performanceResult['error']
            ];
        }

        $summary = $summaryResult['data'];
        
        // Calculate dynamic stats from the current agent pool
        $needsCoaching = 0;
        $topPerformers = 0;
        foreach ($agentsWithPerformance as $item) {
            $score = $item['performance']['current_scores']['overall_score'] ?? 0;
            if ($score < 75) $needsCoaching++;
            if ($score >= 90) $topPerformers++;
        }

        if (session('user.role.name') === 'Supervisor') {
            $summary['total_agents'] = count($agents);
            $summary['active_sessions'] = floor(count($agents) * 0.6);
            $summary['total_tasks'] = floor($summary['total_tasks'] * (2/5));
            $summary['completed_tasks'] = floor($summary['completed_tasks'] * (2/5));
        }

        $summary['needs_coaching'] = $needsCoaching;
        $summary['top_performers'] = $topPerformers;

        return view('user.agents.index', compact('summary', 'agentsWithPerformance'));
    }
    public function getPerformanceData($agentId)
    {
        $result = $this->apiService->getAgentPerformanceHistory($agentId);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'performance' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }
}


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
