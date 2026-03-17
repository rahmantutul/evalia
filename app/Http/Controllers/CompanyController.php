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
        // Fetch real agents from DB and group by company
        $realAgents = User::where('user_type', User::TYPE_AGENT)->get()->groupBy('company_id');
        $realSupervisors = User::where('user_type', User::TYPE_SUPERVISOR)->get()->groupBy('company_id');

        $companies    = [1, 2, 3, 4, 5];
        $customersPool = ['Kais Al-Nimri', "Dua'a Al-Saleh", 'Samer Botros'];
        $sources     = ['api', 'avaya', 'genesys', 'fb', 'linkedin'];
        $outcomes    = ['Resolved', 'Follow-up Needed'];
        $languages   = ['Arabic', 'English'];
        $sentiments  = ['Positive', 'Neutral', 'Negative'];
        $scoreBase   = [65, 72, 85, 91, 98];

        $allTasks = [];
        for ($i = 0; $i < 75; $i++) {
            $score  = $scoreBase[$i % count($scoreBase)];
            $source = $sources[$i % count($sources)];
            $cId = $companies[$i % count($companies)];

            // Try to get a real agent and supervisor for this company
            $agentName = 'Unassigned';
            if (isset($realAgents[$cId]) && $realAgents[$cId]->isNotEmpty()) {
                $agent = $realAgents[$cId][$i % $realAgents[$cId]->count()];
                $agentName = $agent->name;
            }

            $supervisorName = 'N/A';
            if (isset($realSupervisors[$cId]) && $realSupervisors[$cId]->isNotEmpty()) {
                $supervisor = $realSupervisors[$cId][$i % $realSupervisors[$cId]->count()];
                $supervisorName = $supervisor->name;
            }

            $allTasks[] = [
                'id'               => 'task-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'company_id'       => $cId,
                'score'            => $score,
                'status'           => 'completed',
                'agent_name'       => $agentName,
                'customer_name'    => $customersPool[$i % count($customersPool)],
                'supervisor_name'  => $supervisorName,
                'duration'         => '5m',
                'source'           => $source,
                'channel'          => 'Call',
                'outcome'          => $outcomes[$i % count($outcomes)],
                'coaching_required' => $score < 80 ? 'Yes' : 'No',
                'sentiment'        => $sentiments[$i % count($sentiments)],
                'call_type'        => 'Inbound',
                'lang'             => $languages[$i % count($languages)],
                'risk_flag'        => $score < 75 ? 'High' : 'No',
                'created_at'       => now()->subDays(rand(0, 5))->toDateTimeString(),
            ];
        }
        return $allTasks;
    }

    public function companyCreate()
    {
        $groups = $this->groupList();
        return view('user.company.company_create', compact('groups'));
    }

    public function companyDetails($id, Request $request)
    {
        $dbCompany = Company::findOrFail($id);

        if (Auth::check() && Auth::user()->isSupervisor()) {
            $allowedIds = [1, 2];
            if (!in_array($id, $allowedIds)) {
                return redirect()->route('user.company.list')->with('error', 'Unauthorized access to this company.');
            }
        }

        $company = [
            'id' => $dbCompany->id,
            'company_id' => $dbCompany->id,
            'name' => $dbCompany->company_name,
            'company_name' => $dbCompany->company_name,
            'group_id' => $dbCompany->group_id,
            'filler_words' => $dbCompany->filler_words ?? [],
            'main_topics' => $dbCompany->main_topics ?? [],
            'call_types' => $dbCompany->call_types ?? ['inbound', 'outbound'],
            'company_policies' => $dbCompany->company_policies ?? []
        ];
        
        $companyAgents = User::where('user_type', User::TYPE_AGENT)->where('company_id', $id)->get()->map(function($user) use ($company) {
            return [
                'id' => $user->id,
                'full_name' => $user->name,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company_name' => $company['name'],
                'position' => 'Customer Agent'
            ];
        })->toArray();

        $allRealTasks = $this->getAllTasks();
        $companyTasks = array_filter($allRealTasks, function($task) use ($id) {
            return $task['company_id'] === $id;
        });
        
        $taskList = array_values($companyTasks);
        usort($taskList, function($a, $b) {
            return strcmp($b['id'], $a['id']);
        });

        $page = Paginator::resolveCurrentPage(); 
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $pagedTasks = array_slice($taskList, $offset, $perPage);

        $paginatedTasks = new LengthAwarePaginator(
            $pagedTasks,
            count($taskList),
            $perPage,
            $page,
            [
                'path' => route('user.company.view', ['id' => $id]),
                'query' => $request->query()
            ]
        );

        $callsEvaluated = count($companyTasks);
        $totalScore = array_sum(array_column($companyTasks, 'score'));
        $avgQualityScore = $callsEvaluated > 0 ? round($totalScore / $callsEvaluated, 1) : 0;
        
        $activeAgents = $dbCompany->agents()->count();

        return view('user.company.company_details', [
            'company' => $company,
            'taskList' => $paginatedTasks,
            'company_id' => $id,
            'companyAgents' => $companyAgents,
            'callsEvaluated' => $callsEvaluated,
            'avgQualityScore' => $avgQualityScore,
            'activeAgents' => $activeAgents
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

        if (isset($data['faq']) && is_array($data['faq'])) {
            $data['faq'] = array_values($data['faq']);
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
        $company = Company::findOrFail($id);
        $groups = $this->groupList();
        
        return view('user.company.company_edit', compact('company', 'groups'));
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
        
        if (isset($data['faq']) && is_array($data['faq'])) {
            $data['faq'] = array_values($data['faq']);
        }

        $company->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Company data updated successfully',
            'data' => [],
        ]);
    }
}