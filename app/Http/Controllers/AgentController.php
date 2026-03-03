<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
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

    public function index()
    {
        $summary = $this->getAgentDashboardSummaryData();
        
        $agents = User::where('user_type', User::TYPE_AGENT)->with(['supervisor', 'roles'])->get()->map(function($user) {
            return $user->toSessionArray();
        })->toArray();

        // Limit to 8 agents for Supervisor as per previous logic
        if (auth()->check() && auth()->user()->isSupervisor()) {
            $agents = array_slice($agents, 0, 8);
        }

        $agentsWithPerformance = [];
        foreach ($agents as $agent) {
            $seed = crc32($agent['id']);
            mt_srand($seed);
            $score = mt_rand(72, 96) + (mt_rand(0, 9) / 10);
            $trend = (mt_rand(-20, 50) / 10);
            $calls = mt_rand(45, 120);
            
            $performanceData = [
                'agent_name' => $agent['name'],
                'agent_details' => [
                    'display_id' => 'AGT-' . (1000 + $seed % 9000)
                ],
                'current_scores' => [
                    'overall_score' => $score,
                    'trend' => $trend,
                    'evaluated_calls' => $calls
                ]
            ];
            mt_srand();

            $agentsWithPerformance[] = [
                'agent' => $agent,
                'performance' => $performanceData,
                'performance_error' => null
            ];
        }

        $summary['total_agents'] = count($agents);
        $summary['active_sessions'] = floor(count($agents) * 0.6);

        return view('user.agents.index', compact('summary', 'agentsWithPerformance'));
    }

    private function getAgentPerformanceHistoryData($agentId, $name = null, $company = null)
    {
        $user = User::find($agentId);
        $userName = $user ? $user->name : ($name ?? "Unknown Agent");
        
        $avgScore = rand(85, 95);

        return [
            'agent_details' => [
                'id' => $agentId,
                'display_id' => 'AGT-' . strtoupper(Str::random(5)),
                'name' => $userName,
                'position' => $user ? $user->position : 'Agent',
                'company_name' => $company ?? 'Evalia HQ'
            ],
            'tasks' => [], // Empty for now or could add mock tasks
            'current_scores' => [
                'overall_score' => $avgScore,
                'answer_accuracy' => $avgScore + rand(-2, 2),
                'response_speed' => $avgScore + rand(-3, 3),
                'customer_satisfaction' => $avgScore + rand(-1, 2),
                'professionalism' => $avgScore + rand(0, 3)
            ],
            'performance_weights' => [
                'answer_accuracy' => 0.40,
                'response_speed' => 0.30,
                'customer_satisfaction' => 0.30
            ],
            'total_tasks' => rand(10, 15),
            'avg_call_duration' => rand(180, 400),
            'history' => array_map(function($i) use ($avgScore) {
                return [
                    'date' => now()->subDays(6 - $i)->format('Y-m-d'),
                    'score' => $avgScore + rand(-3, 3)
                ];
            }, range(0, 6)),
            'summary' => [
                'total_calls' => rand(10, 15),
                'total_interaction' => rand(10, 15),
                'avg_duration' => '3:' . rand(10, 59),
                'satisfaction_rate' => $avgScore + rand(-2, 2)
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


