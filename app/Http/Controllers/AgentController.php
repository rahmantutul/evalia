<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    private function getAgentDashboardSummaryData()
    {
        return [
            'total_agents' => 48,
            'active_sessions' => 32,
            'total_tasks' => 1245,
            'completed_tasks' => 1180,
            'avg_performance' => 92.5,
            'performance_trend' => [
                ['date' => '2024-01-25', 'value' => 88],
                ['date' => '2024-01-26', 'value' => 89],
                ['date' => '2024-01-27', 'value' => 91],
                ['date' => '2024-01-28', 'value' => 90],
                ['date' => '2024-01-29', 'value' => 92],
                ['date' => '2024-01-30', 'value' => 93],
                ['date' => '2024-01-31', 'value' => 92.5]
            ]
        ];
    }

    public function dashboard()
    {
        $summary = $this->getAgentDashboardSummaryData();
        
        $allAgents = User::where('user_type', User::TYPE_AGENT)->get();
        $agents = [];
        foreach ($allAgents as $agent) {
            $agents[] = [
                'id' => $agent->id,
                'name' => $agent->name,
                'status' => rand(0, 10) > 2 ? 'online' : 'offline',
                'last_active' => now()->subMinutes(rand(1, 1440))->toIso8601String(),
                'performance_score' => rand(85, 98)
            ];
        }

        return view('user.agents.dashboard', compact('summary', 'agents'));
    }

    public function show($agentId, Request $request)
    {
        $performance = $this->getAgentPerformanceHistoryData($agentId, $request->query('name'), $request->query('company'));
        $agent = $performance;
        return view('user.agents.show', compact('agent'));
    }

    public function performanceHistory($agentId)
    {
        $performance = $this->getAgentPerformanceHistoryData($agentId);
        return view('user.agents.performance_history', compact('performance'));
    }

    public function index(Request $request)
    {
        $summary = $this->getAgentDashboardSummaryData();
        $companies = \App\Models\Company::orderBy('company_name')->get();
        
        $query = User::where('user_type', User::TYPE_AGENT)
            ->with(['supervisor', 'roles', 'company']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('company_id') && !empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        $agents = $query->get()->map(function($user) {
            return $user->toSessionArray();
        })->toArray();

        // Limit to 8 agents for Supervisor as per previous logic
        if (auth()->check() && auth()->user()->isSupervisor()) {
            $agents = array_slice($agents, 0, 8);
        }

        $agentsWithPerformance = [];
        foreach ($agents as $agent) {
            $agentId = $agent['id'];
            
            // Fetch real task metrics
            $agentTasks = Task::where('agent_id', $agentId)->get();
            $totalCalls = $agentTasks->count();
            $avgScore = $totalCalls > 0 ? $agentTasks->avg('score') : 0;
            $riskCount = $agentTasks->where('risk_flag', 'Yes')->count();
            
            // Extract audio analysis from the latest evaluated task
            $latestEvaluated = $agentTasks->where('status', 'evaluated')->sortByDesc('created_at')->first();
            $audioMetrics = [
                'speed' => 0,
                'volume' => 'N/A',
                'sentiment' => 'Neutral'
            ];
            
            if ($latestEvaluated && isset($latestEvaluated->analysis['gpt_evaluation'])) {
                $eval = $latestEvaluated->analysis['gpt_evaluation'];
                $audioMetrics['speed'] = $eval['agent_professionalism']['speech_characteristics']['speed'] ?? 0;
                $audioMetrics['volume'] = $eval['agent_professionalism']['speech_characteristics']['volume']['loudness_class'] ?? 'N/A';
                
                // Average sentiment could be complex, let's just take the latest task's overall sentiment
                $audioMetrics['sentiment'] = $latestEvaluated->sentiment ?? 'Neutral';
            }

            $performanceData = [
                'agent_name' => $agent['name'],
                'agent_details' => [
                    'display_id' => 'AGT-' . (1000 + $agentId)
                ],
                'current_scores' => [
                    'overall_score' => round($avgScore, 1),
                    'evaluated_calls' => $totalCalls,
                    'risk_count' => $riskCount,
                    'audio' => $audioMetrics
                ]
            ];

            $agentsWithPerformance[] = [
                'agent' => $agent,
                'performance' => $performanceData,
                'performance_error' => null
            ];
        }

        $summary['total_agents'] = count($agents);
        $summary['active_sessions'] = floor(count($agents) * 0.6);

        return view('user.agents.index', compact('summary', 'agentsWithPerformance', 'companies'));
    }

    private function getAgentPerformanceHistoryData($agentId, $name = null, $company = null)
    {
        $user = User::with('company')->find($agentId);
        $userName = $user ? $user->name : ($name ?? "Unknown Agent");
        
        $tasks = Task::where('agent_id', $agentId)->orderByDesc('created_at')->limit(50)->get();
        $evaluatedTasks = $tasks->where('status', 'evaluated');
        
        $totalCalls = $tasks->count();
        $avgScore = $evaluatedTasks->count() > 0 ? $evaluatedTasks->avg('score') : 0;
        
        // Detailed metrics breakdown
        $metrics = [
            'professionalism' => 0,
            'assessment'      => 0,
            'cooperation'     => 0,
        ];

        if ($evaluatedTasks->count() > 0) {
            $totalProf = 0; $totalAss = 0; $totalCoop = 0;
            foreach ($evaluatedTasks as $t) {
                $eval = $t->analysis['gpt_evaluation'] ?? [];
                $totalProf += $eval['agent_professionalism']['total_score']['percentage'] ?? 0;
                $totalAss  += $eval['agent_assessment']['total_score']['percentage'] ?? 0;
                $totalCoop += $eval['agent_cooperation']['total_score']['percentage'] ?? 0;
            }
            $metrics['professionalism'] = round($totalProf / $evaluatedTasks->count(), 1);
            $metrics['assessment']      = round($totalAss / $evaluatedTasks->count(), 1);
            $metrics['cooperation']     = round($totalCoop / $evaluatedTasks->count(), 1);
        }

        // 7-day history for chart
        $history = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $dayTasks = $evaluatedTasks->filter(fn($t) => date('Y-m-d', strtotime($t->created_at)) === $dateStr);
            $history[] = [
                'date' => $dateObj->format('M d'), // e.g. "Mar 25"
                'score' => $dayTasks->count() > 0 ? round($dayTasks->avg('score'), 1) : 0
            ];
        }

        // Extract top topics
        $topics = [];
        foreach ($evaluatedTasks as $t) {
            $eval = $t->analysis['gpt_evaluation'] ?? [];
            if (isset($eval['notebook_analysis'])) {
                foreach ($eval['notebook_analysis'] as $na) {
                    if (isset($na['matching_topics'])) {
                        foreach ($na['matching_topics'] as $topic) {
                            $topics[$topic] = ($topics[$topic] ?? 0) + 1;
                        }
                    }
                }
            }
        }
        arsort($topics);
        $topTopics = array_slice(array_keys($topics), 0, 5);

        // Compliance rate: % of tasks without risk
        $complianceRate = $totalCalls > 0 ? (($totalCalls - $tasks->where('risk_flag', 'Yes')->count()) / $totalCalls) * 100 : 100;

        return [
            'agent_details' => [
                'id' => $agentId,
                'display_id' => 'AGT-' . (1000 + $agentId),
                'name' => $userName,
                'position' => $user ? $user->position : 'Agent',
                'company_name' => $user->company->name ?? ($company ?? 'Evalia HQ'),
                'is_active' => $user->is_active ?? true,
                'email' => $user->email ?? (strtolower(Str::slug($userName)) . '@evalia.ai')
            ],
            'task_list' => $tasks->take(50)->map(fn($t) => [
                'id' => $t->id,
                'score' => $t->score ?? 0,
                'sentiment' => $t->sentiment ?? 'Neutral',
                'risk' => $t->risk_flag === 'Yes' ? 'High' : ($t->status === 'evaluated' ? 'No' : 'N/A'),
                'date' => $t->created_at->format('M d, Y'),
                'time' => $t->created_at->format('H:i A'),
                'status' => $t->status,
                'category' => 'Call Analysis',
                'duration' => $t->duration ?? '0:00'
            ])->values(),
            'current_scores' => [
                'overall_score' => round($avgScore, 1),
                'professionalism' => $metrics['professionalism'],
                'assessment'      => $metrics['assessment'],
                'cooperation'     => $metrics['cooperation'],
                'compliance_rate' => round($complianceRate, 1)
            ],
            'top_topics' => $topTopics ?: ['General Support', 'Verification', 'Account help'],
            'total_tasks' => $totalCalls,
            'avg_call_duration' => $evaluatedTasks->count() > 0 ? 320 : 0, 
            'history' => $history,
            'summary' => [
                'total_calls' => $totalCalls,
                'total_interaction' => $totalCalls, 
                'risks_detected' => $tasks->where('risk_flag', 'Yes')->count(),
                'avg_duration' => '3:20', 
                'satisfaction_rate' => $metrics['cooperation']
            ]
        ];
    }

    public function getPerformanceData($agentId)
    {
        $data = $this->getAgentPerformanceHistoryData($agentId);
        return response()->json([
            'success' => true,
            'performance' => $data
        ]);
    }
}


