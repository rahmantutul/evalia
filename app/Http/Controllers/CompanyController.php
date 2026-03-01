<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use App\Services\ExternalApiService;

class CompanyController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->middleware('auth.api');
        $this->apiService = $apiService;
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
        $companies = [
            ['id' => 'ssc-jordan', 'name' => 'Social Security Jordan', 'group_name' => 'Government Sector'],
            ['id' => 'arab-bank', 'name' => 'Arab Bank', 'group_name' => 'Private Sector'],
            ['id' => 'orange-jo', 'name' => 'Orange Jordan', 'group_name' => 'Private Sector'],
            ['id' => 'manaseer-group', 'name' => 'Manaseer Group', 'group_name' => 'Private Sector'],
            ['id' => 'royal-jordanian', 'name' => 'Royal Jordanian', 'group_name' => 'Private Sector']
        ];

        // Stable Agent Calculation for Total - Aiming for 30-40 Total
        $totalAgentsCount = 0;
        foreach ($companies as $c) {
            $seed = crc32($c['id']);
            mt_srand($seed);
            $totalAgentsCount += mt_rand(6, 8); // ~7 per company * 5 = ~35 total
        }
        mt_srand(); // Reset

        if (session('user.role.name') === 'Supervisor') {
            $companies = array_slice($companies, 0, 2);
            // Re-calculate total for supervisor if needed
        }

        $agentsResult = $this->apiService->getAgentsList();
        $companyAgents = $agentsResult['success'] ? $agentsResult['agents'] : [];

        // Calculate real statistics from actual task data
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

    /**
     * Get all tasks across all companies
     */
    public function getAllTasks()
    {
        return $this->apiService->getGlobalTaskPool();
    }

    public function companyCreate()
    {
        $groups = $this->groupList();
        return view('user.company.company_create', compact('groups'));
    }

    public function companyDetails($id, Request $request)
    {
        if (session('user.role.name') === 'Supervisor') {
            $allowedIds = ['ssc-jordan', 'arab-bank'];
            if (!in_array($id, $allowedIds)) {
                return redirect()->route('user.company.list')->with('error', 'Unauthorized access to this department.');
            }
        }

        $allCompanies = [
            'ssc-jordan' => ['id' => 'ssc-jordan', 'name' => 'Social Security Jordan', 'group_id' => 'govt-sector', 'filler_words' => ['Umm', 'Well', 'Okay'], 'main_topics' => ['Subscriptions', 'Retirement', 'Installments'], 'policies' => ['Identity Verification Required', 'Response within 24h']],
            'arab-bank' => ['id' => 'arab-bank', 'name' => 'Arab Bank', 'group_id' => 'private-sector', 'filler_words' => ['Hello', 'Please'], 'main_topics' => ['Loans', 'Cards'], 'policies' => ['Banking Secrecy']],
            'orange-jo' => ['id' => 'orange-jo', 'name' => 'Orange Jordan', 'group_id' => 'private-sector', 'filler_words' => ['Hi', 'Yes'], 'main_topics' => ['Bills', 'Internet'], 'policies' => ['First Call Resolution']],
        ];

        $companyData = $allCompanies[$id] ?? [
            'id' => $id,
            'company_id' => $id,
            'name' => 'General Company',
            'company_name' => 'General Company',
            'group_id' => 'private-sector',
            'filler_words' => ['umm', 'so'],
            'main_topics' => ['support'],
            'call_types' => ['inbound'],
            'company_policies' => ['Professional conduct required']
        ];

        $company = [
            'id' => $companyData['id'],
            'company_id' => $companyData['id'],
            'name' => $companyData['name'],
            'company_name' => $companyData['name'],
            'group_id' => $companyData['group_id'],
            'filler_words' => $companyData['filler_words'] ?? [],
            'main_topics' => $companyData['main_topics'] ?? [],
            'call_types' => ['inbound', 'outbound'],
            'company_policies' => $companyData['policies'] ?? []
        ];
        
        // Task List logic (using real tasks if available)
        $agentsResult = $this->apiService->listUsers(0, 100);
        $allUsers = $agentsResult['users'] ?? [];
        $companyAgents = array_filter($allUsers, function($user) use ($company) {
            return ($user['role']['name'] ?? '') === 'Agent' && ($user['company_name'] ?? '') === $company['name'];
        });

        // Ensure we show the same mock agents if needed
        if (count($companyAgents) == 0) {
            $seed = crc32($id);
            mt_srand($seed);
            $targetCount = mt_rand(6, 8);
            
            $firstNames = ['Ahmed', 'Sara', 'Omar', 'Nour', 'Zaid', 'Layla', 'Fadi', 'Mona', 'Hassan', 'Rania', 'Yousif', 'Dana', 'Khaled', 'Maya', 'Ibrahim', 'Salma'];
            $lastNames = ['Al-Masri', 'Al-Abadi', 'Al-Khouri', 'Haddad', 'Nassar', 'Sayegh', 'Jaber', 'Zeidan', 'Salem', 'Hamdan', 'Badwan', 'Hijazi'];
            
            for ($i = 0; $i < $targetCount; $i++) {
                $fName = $firstNames[($i) % count($firstNames)];
                $lName = $lastNames[($i) % count($lastNames)];
                $fullName = $fName . ' ' . $lName;
                $companyAgents[] = [
                    'id' => 'mock-' . $id . '-' . $i,
                    'full_name' => $fullName,
                    'name' => $fullName,
                    'email' => 'agent' . ($i+1) . '@crtvai.com',
                    'phone' => '+962 7 9008 7879',
                    'company_name' => $company['name'],
                    'position' => 'Customer Agent'
                ];
            }
            mt_srand();
        }

        // Get real tasks for this company
        $allRealTasks = $this->getAllTasks();
        $companyTasks = array_filter($allRealTasks, function($task) use ($id) {
            return $task['company_id'] === $id;
        });
        
        // Sort by most recent first (assuming we have created_at or use task ID)
        $taskList = array_values($companyTasks);
        usort($taskList, function($a, $b) {
            return strcmp($b['id'], $a['id']); // Sort by ID descending
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

        // Calculate company counts
        $callsEvaluated = count($companyTasks);
        $totalScore = array_sum(array_column($companyTasks, 'score'));
        $avgQualityScore = $callsEvaluated > 0 ? round($totalScore / $callsEvaluated, 1) : 0;
        
        // Stable Agent count for this company (same logic as company list)
        $seed = crc32($id);
        mt_srand($seed);
        $activeAgents = mt_rand(6, 8);
        mt_srand(); // Reset

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
        return redirect()->back()->with('success', 'Company deleted successfully (official).');
    }

    public function companyStore(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Company registered successfully',
            'data' => ['company_id' => Str::uuid()->toString()],
        ]);
    }

    public function companyEdit($id)
    {
        $company = [
            'id' => $id,
            'company_id' => $id,
            'name' => 'Social Security Jordan',
            'company_name' => 'Social Security Jordan',
            'group_id' => 'govt-sector',
            'filler_words' => ['Umm', 'Amm'],
            'main_topics' => ['Retirement'],
            'call_types' => ['inbound'],
            'company_policies' => ['Policy 1']
        ];
        $groups = $this->groupList();
        
        return view('user.company.company_edit', compact('company', 'groups'));
    }

    public function companyUpdate(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Company data updated successfully',
            'data' => [],
        ]);
    }
}