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

        $agentsResult = $this->apiService->getAgentsList();
        
        if (!$agentsResult['success']) {
            return back()->with('error', $agentsResult['error']);
        }

        $summary = $summaryResult['data'];
        $agents = $agentsResult['agents'];
        return view('user.agents.index', compact('summary', 'agents'));
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
