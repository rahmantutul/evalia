<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }


    public function index()
    {
        $user = auth()->user();

        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }

        if ($user->isSupervisor()) {
            return redirect()->route('supervisor.dashboard');
        }

        // ── 1. All completed/evaluated tasks (global, all companies) ──────────────
        $allTasks = \App\Models\Task::whereIn('status', ['completed', 'evaluated'])
            ->with('agent')
            ->get();

        // ── 2. Core KPIs ──────────────────────────────────────────────────────────
        $callsEvaluated      = $allTasks->count();
        $avgQualityScore     = $callsEvaluated > 0 ? round($allTasks->avg('score'), 1) : 0;
        $totalRisks          = $allTasks->where('risk_flag', 'Yes')->count();
        $totalCompanies      = \App\Models\Company::count();
        $activeAgents        = \App\Models\User::where('user_type', \App\Models\User::TYPE_AGENT)
                                    ->where('is_active', true)->count();

        $callsThisWeekCount  = \App\Models\Task::whereIn('status', ['completed', 'evaluated'])
                                    ->where('created_at', '>=', now()->startOfWeek())
                                    ->count();

        $scoreImprovement = 2.7; // kept static – replace with real delta if available

        // ── 3. Agent-presence ratio (same formula as CompanyController) ───────────
        $totalDurationSec = 0;
        $agentDurationSec = 0;
        foreach ($allTasks as $task) {
            if (isset($task->analysis['pause_delay_information'])) {
                $info = $task->analysis['pause_delay_information'];
                $totalDurationSec += ($info['total_call_duration'] ?? 0);
                $agentDurationSec += ($info['total_agent_duration'] ?? 0);
            }
        }
        $agentPresence = $totalDurationSec > 0
            ? round(($agentDurationSec / $totalDurationSec) * 100, 1)
            : 0;

        // ── 4. ROI / Financial metrics (identical formula to CompanyController) ───
        $totalSeconds = 0;
        foreach ($allTasks as $task) {
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

        $humanEfficiencyMinutes = ($totalSeconds * 3) / 60;   // 3× review factor
        $manualDays             = $humanEfficiencyMinutes / 240; // 4 effective hrs/day = 240 min
        $humanEfficiencyHours   = $manualDays * 4;
        $manualCost             = $humanEfficiencyHours * 25.00; // $25/hr QC rate
        $evaliaCost             = $callsEvaluated * 0.50;        // $0.50/call
        $netSavings             = $manualCost - $evaliaCost;
        $roiPercentage          = $evaliaCost > 0
            ? round(($netSavings / $evaliaCost) * 100)
            : 0;

        $roiStats = [
            'hours_saved' => round($humanEfficiencyHours, 1),
            'manual_days' => round($manualDays, 1),
            'manual_cost' => $manualCost,
            'evalia_cost' => $evaliaCost,
            'net_savings' => $netSavings,
            'roi_percent' => $roiPercentage,
        ];

        // ── 5. Sentiment (turn-level, identical to CompanyController) ─────────────
        $positiveTurns = 0;
        $neutralTurns  = 0;
        $negativeTurns = 0;
        $totalTurns    = 0;

        foreach ($allTasks as $taskModel) {
            $analysis = $taskModel->analysis ?? [];
            $turns = $analysis['speakers_transcriptions']
                ?? $analysis['conversation']
                ?? $analysis['agent_speakers_transcriptions']
                ?? $analysis['customer_speakers_transcriptions']
                ?? [];

            foreach ($turns as $turn) {
                $s = ucfirst(strtolower(trim($turn['sentiment'] ?? 'Neutral')));
                $totalTurns++;
                if ($s === 'Positive')       $positiveTurns++;
                elseif ($s === 'Negative')   $negativeTurns++;
                else                         $neutralTurns++;
            }
        }

        $sentimentStats = [
            'positive' => $totalTurns > 0 ? round(($positiveTurns / $totalTurns) * 100) : 0,
            'neutral'  => $totalTurns > 0 ? round(($neutralTurns  / $totalTurns) * 100) : 0,
            'negative' => $totalTurns > 0 ? round(($negativeTurns / $totalTurns) * 100) : 0,
        ];

        $topSentiment = 'Neutral';
        if ($totalTurns > 0) {
            $counts = ['Positive' => $positiveTurns, 'Neutral' => $neutralTurns, 'Negative' => $negativeTurns];
            arsort($counts);
            $topSentiment = key($counts);
        }

        // ── 6. Performance Trend (identical logic to CompanyController) ───────────
        // Daily – last 7 days
        $dailyData = \App\Models\Task::whereIn('status', ['completed', 'evaluated'])
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('score')
            ->selectRaw('DATE(created_at) as date, AVG(score) as avg_score')
            ->groupBy('date')
            ->pluck('avg_score', 'date');

        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('M d');
            $dailyTrend[] = [
                'label' => $label,
                'value' => isset($dailyData[$date]) ? round($dailyData[$date], 1) : 0,
            ];
        }

        // Weekly – last 12 weeks
        $weeklyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $d   = now()->subWeeks($i);
            $avg = \App\Models\Task::whereIn('status', ['completed', 'evaluated'])
                ->whereBetween('created_at', [
                    $d->copy()->startOfWeek()->toDateTimeString(),
                    $d->copy()->endOfWeek()->toDateTimeString(),
                ])
                ->avg('score');

            $weeklyTrend[] = [
                'label' => 'Week ' . $d->format('W'),
                'value' => $avg ? round($avg, 1) : 0,
            ];
        }

        // Monthly – last 12 months
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $d   = now()->subMonths($i);
            $avg = \App\Models\Task::whereIn('status', ['completed', 'evaluated'])
                ->whereYear('created_at', $d->year)
                ->whereMonth('created_at', $d->month)
                ->avg('score');

            $monthlyTrend[] = [
                'label' => $d->format('M Y'),
                'value' => $avg ? round($avg, 1) : 0,
            ];
        }

        $trendData = [
            'daily'   => $dailyTrend,
            'weekly'  => $weeklyTrend,
            'monthly' => $monthlyTrend,
        ];

        // ── 7. Global agent performance (same shape as CompanyController) ─────────
        $companyAgents = \App\Models\User::where('user_type', \App\Models\User::TYPE_AGENT)
            ->get()
            ->map(function ($user) use ($allTasks) {
                $agentTasks = $allTasks->where('agent_id', $user->id);
                $totalCalls = $agentTasks->count();
                $avgScore   = $totalCalls > 0 ? round($agentTasks->avg('score'), 1) : 0;
                $agentRisks = $agentTasks->where('risk_flag', 'Yes')->count();

                $sentCounts = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
                foreach ($agentTasks as $t) {
                    $s = ucfirst(strtolower($t->sentiment ?? 'Neutral'));
                    if (isset($sentCounts[$s])) $sentCounts[$s]++;
                }
                arsort($sentCounts);
                $agentSentiment = $totalCalls > 0 ? key($sentCounts) : 'N/A';

                return [
                    'id'           => $user->id,
                    'company_id'   => $user->company_id,
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

        // ── 8. Recent 10 tasks (global) ───────────────────────────────────────────
        $taskList = \App\Models\Task::with(['agent', 'company'])
            ->latest()
            ->limit(10)
            ->get();

        // ── 9. Pass everything to the view ────────────────────────────────────────
        return view('user.dashboard', [
            // KPI
            'callsEvaluated'     => $callsEvaluated,
            'avgQualityScore'    => $avgQualityScore,
            'activeAgents'       => $activeAgents,
            'totalRisks'         => $totalRisks,
            'totalCompanies'     => $totalCompanies,
            'callsThisWeekCount' => $callsThisWeekCount,
            'scoreImprovement'   => $scoreImprovement,
            // Charts / analysis
            'agentPresence'      => $agentPresence,
            'topSentiment'       => $topSentiment,
            'sentimentStats'     => $sentimentStats,
            'trendData'          => $trendData,
            // ROI
            'roiStats'           => $roiStats,
            // Tables
            'companyAgents'      => $companyAgents,
            'taskList'           => $taskList,
        ]);
    }

    public function setActiveProduct(Request $request)
    {
        $productId = $request->input('product_id');
        
        // 🔹 Clear previous product-specific session data
        session()->forget([
            'product_1_data',
            'product_2_data',
            'product_3_data',
        ]);
        
        // 🔹 Set new active product
        session(['active_product' => $productId]);
        
        // 🔹 Return success with redirect URL if needed
        return response()->json([
            'success' => true,
            'redirect_url' => route('user.home')
        ]);
    }

    public function profile()
    {
        $user = session('user');
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = session('user');
        
        $userData = [
            'id' => $user['id'] ?? '1',
            'full_name' => $request->input('name', $user['full_name'] ?? 'أحمد حسان'),
            'email' => $request->input('email', $user['email'] ?? 'ahmed.hassan@ssc.gov.jo'),
            'phone' => $request->input('phone', $user['phone'] ?? '+962 79 123 4567'),
            'company_name' => $request->input('company', $user['company_name'] ?? 'الضمان الاجتماعي - الأردن'),
            'role' => $user['role'] ?? ['name' => 'Admin'],
        ];

        session(['user' => $userData]);

        return back()->with('success', 'Profile updated successfully (Mock)!');
    }

    public function support()
    {
        return view('user.support');
    }

    public function subscription()
    {
        return view('user.subscription');
    }

    public function bots()
    {
        return view('user.bots');
    }

    public function bot_create()
    {
        return view('user.bot_create');
    }
    
    public function bot_store()
    {
        return view('user.maintenance');
    }

    public function overview()
    {
        return view('user.overview');
    }

    public function inbox()
    {
        return view('user.inbox');
    }

    public function performanceBadges()
    {
        return view('user.performance_badges');
    }
}
