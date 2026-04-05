<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    private function groupList()
    {
        return [
            ['id' => 'govt-sector', 'group_id' => 'govt-sector', 'name' => 'Government Sector', 'group_name' => 'Government Sector'],
            ['id' => 'private-sector', 'group_id' => 'private-sector', 'name' => 'Private Sector', 'group_name' => 'Private Sector']
        ];
    }

    public function companyList()
    {
        $companies = Company::withCount('agents')->get()->map(function($company) {
            $groups = $this->groupList();
            $groupName = 'Staff';
            foreach($groups as $g) {
                if($g['group_id'] == $company->group_id) {
                    $groupName = $g['group_name'];
                }
            }
            return [
                'id' => $company->id,
                'name' => $company->company_name,
                'group_name' => $groupName,
                'source' => $company->source,
                'agents_count' => $company->agents_count
            ];
        })->toArray();

        if (Auth::check() && Auth::user()->isSupervisor()) {
            $companies = array_slice($companies, 0, 2);
        }

        // Total Agent Count calculation
        $totalAgentsCount = array_sum(array_column($companies, 'agents_count'));

        $allTasks = $this->getAllTasks();
        
        $totalActiveTasks = 0;
        $totalCompletedTasks = 0;
        $totalPendingAnalysis = 0;
        $totalScore = 0;
        $scoreCount = 0;
        
        foreach ($allTasks as $task) {
            if ($task['status'] === 'processing') {
                $totalActiveTasks++;
            } elseif ($task['status'] === 'completed') {
                $totalCompletedTasks++;
                $totalScore += $task['score'];
                $scoreCount++;
            } elseif ($task['status'] === 'pending') {
                $totalPendingAnalysis++;
            }
        }
        
        $avgQaScore = $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : 0;
        $companyAgents = User::where('user_type', User::TYPE_AGENT)->get();

        return view('user.company.company_list', compact(
            'companies', 
            'companyAgents',
            'totalActiveTasks',
            'totalCompletedTasks',
            'totalPendingAnalysis',
            'avgQaScore',
            'totalAgentsCount'
        ));
    }

    public function getAllTasks()
    {
        // Fetch real tasks from the database
        $tasks = \App\Models\Task::with(['agent'])->get();

        return $tasks->map(function($task) {
            // Replicate the dummy structure so the rest of the controller functions correctly
            return [
                'id'                => $task->id,
                'company_id'        => $task->company_id,
                'score'             => $task->score ?? 0,
                'status'            => $task->status === 'transcribed' ? 'completed' : $task->status, // Map transcribed to completed for the demo logic if needed, or leave it as is
                'agent_name'        => $task->agent->name ?? 'Unassigned',
                'customer_name'     => 'Customer', 
                'supervisor_name'   => 'N/A',
                'duration'          => $task->duration ?? '00:00',
                'source'            => $task->source ?? 'api',
                'channel'           => $task->channel ?? 'Call',
                'outcome'           => $task->outcome ?? 'N/A',
                'coaching_required' => ($task->score > 0 && $task->score < 80) ? 'Yes' : 'No',
                'sentiment'         => $task->sentiment ?? 'Neutral',
                'call_type'         => 'Inbound',
                'lang'              => $task->lang ?? 'en',
                'risk_flag'         => $task->risk_flag ?? 'No',
                'created_at'        => $task->created_at->toDateTimeString(),
            ];
        })->toArray();
    }

    public function companyCreate()
    {
        $groups = $this->groupList();
        return view('user.company.company_create', compact('groups'));
    }

    public function companyDetails($id, Request $request)
    {
        $dbCompany = Company::findOrFail($id);
    
        // Supervisor access restriction
        if (Auth::check() && Auth::user()->isSupervisor()) {
            $allowedIds = [1, 2];
            if (!in_array($id, $allowedIds)) {
                return redirect()->route('user.company.list')->with('error', 'Unauthorized access to this company.');
            }
        }
    
        // Build company array
        $company = [
            'id'               => $dbCompany->id,
            'company_id'       => $dbCompany->id,
            'name'             => $dbCompany->company_name,
            'company_name'     => $dbCompany->company_name,
            'group_id'         => $dbCompany->group_id,
            'filler_words'     => $dbCompany->filler_words     ?? [],
            'main_topics'      => $dbCompany->main_topics      ?? [],
            'call_types'       => $dbCompany->call_types       ?? ['inbound', 'outbound'],
            'company_policies' => $dbCompany->company_policies ?? [],
            'company_risks'    => $dbCompany->company_risks    ?? [],
        ];
    
        // ── Recent 10 tasks (unfiltered) ──────────────────────────────────────
        $taskList = \App\Models\Task::where('company_id', $id)
            ->with('agent')
            ->latest()
            ->limit(10)
            ->get();
    
        // ── ALL tasks (unfiltered) – used for every KPI metric ─────────────────
        $allCompanyTasks = \App\Models\Task::where('company_id', $id)->with('agent')->get();
    
        // ── KPI metrics ────────────────────────────────────────────────────────
        $callsEvaluated  = $allCompanyTasks->count();
        $avgQualityScore = $callsEvaluated > 0 ? round($allCompanyTasks->avg('score'), 1) : 0;
        $activeAgents    = $dbCompany->agents()->where('is_active', true)->count();
    
        $callsThisWeekCount = \App\Models\Task::where('company_id', $id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();
    
        $totalRisks = $allCompanyTasks->where('risk_flag', 'Yes')->count();
    
        // ── Agent performance ──────────────────────────────────────────────────
        $companyAgents = User::where('user_type', User::TYPE_AGENT)
            ->where('company_id', $id)
            ->get()
            ->map(function ($user) use ($allCompanyTasks) {
                $agentTasks = $allCompanyTasks->where('agent_id', $user->id);
                $totalCalls = $agentTasks->count();
                $avgScore   = $totalCalls > 0 ? round($agentTasks->avg('score'), 1) : 0;
                $agentRisks = $agentTasks->where('risk_flag', 'Yes')->count();

                // Calculate agent top sentiment
                $sentCounts = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
                foreach ($agentTasks as $t) {
                    $s = ucfirst(strtolower($t->sentiment ?? 'Neutral'));
                    if (isset($sentCounts[$s])) $sentCounts[$s]++;
                }
                arsort($sentCounts);
                $agentSentiment = $totalCalls > 0 ? key($sentCounts) : 'N/A';
    
                return [
                    'id'           => $user->id,
                    'full_name'    => $user->name,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'company_name' => $user->company->company_name ?? 'N/A',
                    'score'        => $avgScore,
                    'calls'        => $totalCalls,
                    'sentiment'    => $agentSentiment,
                    'risks'        => $agentRisks,
                    'avatar'       => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff',
                ];
            })->toArray();
    
        // ── Agent Presence Ratio ───────────────────────────────────────────────
        // % of total call duration spoken by the agent
        $totalDuration = 0;
        $agentDuration = 0;
        foreach ($allCompanyTasks as $taskModel) {
            if (isset($taskModel->analysis['pause_delay_information'])) {
                $info = $taskModel->analysis['pause_delay_information'];
                $totalDuration += ($info['total_call_duration'] ?? 0);
                $agentDuration += ($info['total_agent_duration'] ?? 0);
            }
        }
        $agentPresence = $totalDuration > 0
            ? round(($agentDuration / $totalDuration) * 100, 1)
            : 0;
    
        // ── Sentiment Analysis (Dynamic from Turn-level data) ──────────────────
        $positiveTurns = 0;
        $neutralTurns  = 0;
        $negativeTurns = 0;
        $totalTurns    = 0;

        foreach ($allCompanyTasks as $taskModel) {
            $analysis = $taskModel->analysis ?? [];
            
            // Collect turns from standard keys
            $turns = $analysis['speakers_transcriptions'] 
                ?? $analysis['conversation']
                ?? $analysis['agent_speakers_transcriptions'] 
                ?? $analysis['customer_speakers_transcriptions'] 
                ?? [];

            foreach ($turns as $turn) {
                // Hamsa segments often have 'sentiment' as 'Positive', 'Neutral', or 'Negative'
                $s = ucfirst(strtolower(trim($turn['sentiment'] ?? 'Neutral')));
                $totalTurns++;
                if ($s === 'Positive') $positiveTurns++;
                elseif ($s === 'Negative') $negativeTurns++;
                else $neutralTurns++;
            }
        }

        $sentimentStats = [
            'positive' => $totalTurns > 0 ? round(($positiveTurns / $totalTurns) * 100) : 0,
            'neutral'  => $totalTurns > 0 ? round(($neutralTurns / $totalTurns) * 100) : 0,
            'negative' => $totalTurns > 0 ? round(($negativeTurns / $totalTurns) * 100) : 0,
        ];

        // Determine top sentiment for the company
        $topSentiment = 'Neutral';
        if ($totalTurns > 0) {
            $counts = ['Positive' => $positiveTurns, 'Neutral' => $neutralTurns, 'Negative' => $negativeTurns];
            arsort($counts);
            $topSentiment = key($counts);
        }

        $totalSeconds = 0;
        foreach ($allCompanyTasks as $task) {
            $duration = $task->duration ?? '00:00';
            $parts    = explode(':', $duration);
    
            if (count($parts) === 3) {
                $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
            } elseif (count($parts) === 2) {
                $totalSeconds += ($parts[0] * 60) + $parts[1];
            } else {
                $totalSeconds += (int) $duration;
            }
        }
    
        // Human time equivalent
        $humanEfficiencyMinutes = ($totalSeconds * 3) / 60;

        // 4 effective hours/day → 240 minutes
        $manualDays = $humanEfficiencyMinutes / 240;

        // Convert to hours (consistent now)
        $humanEfficiencyHours = $manualDays * 4;

        // Costs
        $manualCost = $humanEfficiencyHours * 25.00;
        $evaliaCost = $callsEvaluated * 0.50;

        $netSavings = $manualCost - $evaliaCost;

        // ROI
        $roiPercentage = $evaliaCost > 0
            ? round(($netSavings / $evaliaCost) * 100)
            : 0;
    
        $scoreImprovement = 2.7;
    
        // ── Performance Trend Data (Real) ──────────────────────────────────────
        // ── Performance Trend Data (Real with Zero-Filling) ────────────────────
        // 1. Daily Trend (Last 7 days)
        $dailyData = \App\Models\Task::where('company_id', $id)
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('score')
            ->selectRaw('DATE(created_at) as date, AVG(score) as avg_score')
            ->groupBy('date')
            ->pluck('avg_score', 'date');

        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('M d');
            $dailyTrend[] = [
                'label' => $label,
                'value' => isset($dailyData[$date]) ? round($dailyData[$date], 1) : 0
            ];
        }

        // 2. Weekly Trend (Last 12 weeks)
        $weeklyData = \App\Models\Task::where('company_id', $id)
            ->where('created_at', '>=', now()->subWeeks(12))
            ->whereNotNull('score')
            ->selectRaw('YEAR(created_at) as year, WEEK(created_at) as week, AVG(score) as avg_score')
            ->groupBy('year', 'week')
            ->get()
            ->keyBy(fn($i) => $i->year . '-' . $i->week);

        $weeklyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subWeeks($i);
            
            // Re-fetch weekly data to use ISO week for matching
            $avg = \App\Models\Task::where('company_id', $id)
                ->whereBetween('created_at', [$d->copy()->startOfWeek()->toDateTimeString(), $d->copy()->endOfWeek()->toDateTimeString()])
                ->avg('score');

            $weeklyTrend[] = [
                'label' => "Week " . $d->format('W'),
                'value' => $avg ? round($avg, 1) : 0
            ];
        }

        // 3. Monthly Trend (Last 12 months)
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $avg = \App\Models\Task::where('company_id', $id)
                ->whereYear('created_at', $d->year)
                ->whereMonth('created_at', $d->month)
                ->avg('score');

            $monthlyTrend[] = [
                'label' => $d->format('M Y'),
                'value' => $avg ? round($avg, 1) : 0
            ];
        }

        // ── Return view ────────────────────────────────────────────────────────
        return view('user.company.company_details', [
            'company'          => $company,
            'taskList'         => $taskList,
            'company_id'       => $id,
            'companyAgents'    => $companyAgents,
            'callsEvaluated'   => $callsEvaluated,
            'avgQualityScore'  => $avgQualityScore,
            'activeAgents'     => $activeAgents,
            'callsThisWeekCount' => $callsThisWeekCount,
            'totalRisks'       => $totalRisks,
            'agentPresence'    => $agentPresence,
            'topSentiment'     => $topSentiment,
            'roiStats'         => [
                'hours_saved' => $humanEfficiencyHours,
                'manual_days' => $manualDays,
                'manual_cost' => $manualCost,
                'evalia_cost' => $evaliaCost,
                'net_savings' => $manualCost - $evaliaCost,
                'roi_percent' => $roiPercentage,
            ],
            'sentimentStats' => $sentimentStats,
            'trendData' => [
                'daily'   => $dailyTrend,
                'weekly'  => $weeklyTrend,
                'monthly' => $monthlyTrend
            ],
            'scoreImprovement' => $scoreImprovement,
        ]);
    }

 

    public function companyDelete($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return redirect()->route('user.company.list')->with('success', 'Company deleted successfully.');
    }

    public function companyStore(Request $request)
    {
        $data = $request->all();

        // Convert Tagify JSON/CSV strings to arrays for JSON casting
        $tagifyFields = [
            'filler_words', 'main_topics', 'call_types', 'call_outcomes',
            'restricted_phrases', 'source', 'agent_assessments_configs',
            'agent_cooperation_configs', 'agent_performance_configs'
        ];

        foreach ($tagifyFields as $field) {
            if (isset($data[$field])) {
                $decoded = json_decode($data[$field], true);
                if (is_array($decoded)) {
                    $data[$field] = array_column($decoded, 'value');
                } elseif (is_string($data[$field])) {
                    $data[$field] = array_map('trim', explode(',', $data[$field]));
                }
            }
        }

        if (isset($data['company_policies']) && is_string($data['company_policies'])) {
            $data['company_policies'] = array_map('trim', explode("\n", $data['company_policies']));
        }
        if (isset($data['company_risks']) && is_string($data['company_risks'])) {
            $data['company_risks'] = array_map('trim', explode("\n", $data['company_risks']));
        }

        if (isset($data['faq']) && is_array($data['faq'])) {
            $data['faq'] = array_values($data['faq']);
        }

        if (isset($data['data_extraction_config']) && is_string($data['data_extraction_config'])) {
            $data['data_extraction_config'] = json_decode($data['data_extraction_config'], true);
        }

        $company = Company::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Company registered successfully',
            'data' => ['id' => $company->id],
        ]);
    }

    public function companyEdit($id)
    {
        $company = Company::with('agents')->findOrFail($id);
        $groups = $this->groupList();
        $companyAgents = $company->agents;
        
        return view('user.company.company_edit', compact('company', 'groups', 'companyAgents'));
    }

    public function companyUpdate(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $data = $request->all();

        $tagifyFields = [
            'filler_words', 'main_topics', 'call_types', 'call_outcomes',
            'restricted_phrases', 'source', 'agent_assessments_configs',
            'agent_cooperation_configs', 'agent_performance_configs'
        ];

        foreach ($tagifyFields as $field) {
            if (isset($data[$field])) {
                $decoded = json_decode($data[$field], true);
                if (is_array($decoded)) {
                    $data[$field] = array_column($decoded, 'value');
                } elseif (is_string($data[$field])) {
                    $data[$field] = array_map('trim', explode(',', $data[$field]));
                }
            }
        }

        if (isset($data['company_policies']) && is_string($data['company_policies'])) {
            $data['company_policies'] = array_map('trim', explode("\n", $data['company_policies']));
        }
        if (isset($data['company_risks']) && is_string($data['company_risks'])) {
            $data['company_risks'] = array_map('trim', explode("\n", $data['company_risks']));
        }
        
        if (isset($data['faq']) && is_array($data['faq'])) {
            $data['faq'] = array_values($data['faq']);
        }

        if (isset($data['data_extraction_config']) && is_string($data['data_extraction_config'])) {
            $data['data_extraction_config'] = json_decode($data['data_extraction_config'], true);
        }

        $company->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Company data updated successfully',
            'data' => [],
        ]);
    }
}