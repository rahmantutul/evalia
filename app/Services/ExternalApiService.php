<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExternalApiService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = '';
        $this->timeout = 30;
    }

    /**
     * Get current user token from session
     */
    private function getToken()
    {
        return session('user_access_token', 'dummy-token');
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        // For static dummy site, any login succeeds
        session(['user_access_token' => 'dummy-token-' . uniqid()]);
        session(['mock_username' => $email]); // Store username for getCurrentUser mock
        
        return [
            'success' => true,
            'access_token' => 'dummy-token',
            'token_type' => 'bearer'
        ];
    }

    /**
     * Get current user profile
     */
    public function getCurrentUser()
    {
        $username = session('mock_username', 'admin');
        
        $user = [
            'id' => '1',
            'full_name' => 'أحمد حسان',
            'email' => 'ahmed.hassan@ssc.gov.jo',
            'position' => 'Senior Quality Analyst',
            'phone' => '+962 79 123 4567',
            'role' => [
                'id' => 'ecedd3ec-6b66-45e1-9c1b-6cc3ee772762',
                'name' => 'Admin'
            ],
            'company' => [
                'id' => 'ssc-jordan',
                'name' => 'الضمان الاجتماعي - الأردن'
            ]
        ];

        if ($username === 'agent') {
            $user['full_name'] = 'نادي البديري';
            $user['role'] = ['id' => 'role-3', 'name' => 'Agent'];
        } elseif ($username === 'supervisor') {
            $user['full_name'] = 'Supervisor User';
            $user['role'] = ['id' => 'role-supervisor', 'name' => 'Supervisor'];
        }

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function getRolePermissions($roleId)
    {
        return [
            'success' => true,
            'permissions' => [
                'dashboard.view',
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'companies.view',
                'companies.create',
                'tasks.view',
                'agents.view'
            ]
        ];
    }

    /**
     * Register new user
     */
    public function register($userData)
    {
        return [
            'success' => true,
            'user' => array_merge($userData, ['id' => uniqid()])
        ];
    }

    /**
     * List users
     */
    private function normalizeName($name)
    {
        return trim(preg_replace('/\s+/', ' ', (string)$name));
    }

    public function listUsers($skip = 0, $limit = 100)
    {
        $pool = $this->getAgentsPool();
        $users = [];
        foreach ($pool as $index => $name) {
            $slug = (string) Str::slug($name);
            $id = $slug ?: 'agt-' . md5($name);
            
            $users[] = [
                'id' => $id,
                'full_name' => $name,
                'username' => 'user_' . $index,
                'email' => ($slug ?: 'agent.' . $index) . '@crtvai.com',
                'position' => 'Customer Support',
                'phone' => "+962 7 9008 7879",
                'is_active' => true,
                'role' => ['name' => 'Agent'],
                'company' => ['id' => 'ssc-jordan', 'name' => 'Social Security Jordan'],
                'company_name' => 'Social Security Jordan'
            ];
        }

        return [
            'success' => true,
            'users' => $users
        ];
    }

    /**
     * Get user by ID
     */
    public function getUser($userId, $name = null)
    {
        $allUsers = $this->listUsers()['users'];
        $user = collect($allUsers)->firstWhere('id', (string) $userId);
        
        // If not found by ID (common for mock-agent-X), try finding by name
        if (!$user && $name) {
            $normalizedSearchName = $this->normalizeName($name);
            $user = collect($allUsers)->first(function($u) use ($normalizedSearchName) {
                return $this->normalizeName($u['full_name']) === $normalizedSearchName;
            });
        }

        if (!$user) {
            $user = [
                'id' => $userId,
                'full_name' => $name ?? "مستخدم افتراضي",
                'username' => "user_" . Str::random(5),
                'email' => "agent@crtvai.com",
                'position' => 'Agent',
                'phone' => '+962 7 9008 7879',
                'is_active' => true,
                'role' => ['id' => 'role-1', 'name' => 'Agent'],
                'company' => ['id' => 'ssc-jordan', 'name' => 'الضمان الاجتماعي - الأردن'],
                'company_name' => 'الضمان الاجتماعي - الأردن'
            ];
        }

        return [
            'success' => true,
            'user' => $user
        ];
    }

    /**
     * Create new user
     */
    public function createUser($userData)
    {
        return [
            'success' => true,
            'user' => array_merge($userData, ['id' => uniqid()])
        ];
    }

    /**
     * Update user
     */
    public function updateUser($userId, $userData)
    {
        return [
            'success' => true,
            'user' => array_merge($userData, ['id' => $userId])
        ];
    }

    /**
     * Deactivate user
     */
    public function deactivateUser($userId)
    {
        return ['success' => true, 'message' => 'User deactivated successfully (Mock)'];
    }

    public function changeUserPassword($userId, $newPassword)
    {
        return ['success' => true];
    }

    public function changePassword($userId, $newPassword)
    {
        return ['success' => true];
    }

    public function getRoles()
    {
        return [
            'success' => true,
            'roles' => [
                ['id' => 'ecedd3ec-6b66-45e1-9c1b-6cc3ee772762', 'name' => 'Admin'],
                ['id' => 'role-2', 'name' => 'Manager'],
                ['id' => 'role-3', 'name' => 'Agent'],
                ['id' => 'role-supervisor', 'name' => 'Supervisor']
            ]
        ];
    }

    public function getCompanies()
    {
        return [
            'success' => true,
            'companies' => [
                ['id' => 'ssc-jordan', 'name' => 'الضمان الاجتماعي - الأردن', 'industry' => 'Government'],
                ['id' => 'arab-bank', 'name' => 'البنك العربي', 'industry' => 'Finance'],
                ['id' => 'orange-jo', 'name' => 'أورنج الأردن', 'industry' => 'Telecommunications'],
                ['id' => 'manaseer-group', 'name' => 'مجموعة المناصير', 'industry' => 'Energy'],
                ['id' => 'royal-jordanian', 'name' => 'الملكية الأردنية', 'industry' => 'Aviation']
            ]
        ];
    }

    public function getAgentDashboardSummary()
    {
        return [
            'success' => true,
            'data' => [
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
            ]
        ];
    }

    public function getAgentsList()
    {
        $allAgents = $this->listUsers()['users'];
        $agents = [];
        foreach ($allAgents as $agent) {
            $agents[] = [
                'id' => $agent['id'],
                'name' => $agent['full_name'],
                'status' => rand(0, 10) > 2 ? 'online' : 'offline',
                'last_active' => now()->subMinutes(rand(1, 1440))->toIso8601String(),
                'performance_score' => rand(85, 98)
            ];
        }
        return [
            'success' => true,
            'agents' => $agents
        ];
    }

    public function getAgentPerformanceHistory($agentId, $name = null, $company = null)
    {
        $userData = $this->getUser($agentId, $name)['user'];
        $pool = $this->getGlobalTaskPool();
        
        $normalizedUserName = $this->normalizeName($userData['full_name'] ?? '');
        
        $agentTasks = array_filter($pool, function($t) use ($normalizedUserName) {
            return $this->normalizeName($t['agent_name']) === $normalizedUserName;
        });
        
        // Sort tasks by date descending to show most recent first
        usort($agentTasks, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        $totalTasks = count($agentTasks);
        $avgScore = $totalTasks > 0 ? round(array_sum(array_column($agentTasks, 'score')) / $totalTasks, 1) : rand(85, 95);

        return [
            'success' => true,
            'data' => [
                'agent_details' => [
                    'id' => $agentId,
                    'display_id' => 'AGT-' . strtoupper(Str::random(5)),
                    'name' => $userData['full_name'],
                    'position' => $userData['position'],
                    'company_name' => $company ?? 'الضمان الاجتماعي - الأردن'
                ],
                'tasks' => array_values($agentTasks),
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
                'total_tasks' => $totalTasks ?: rand(10, 15),
                'avg_call_duration' => rand(180, 400),
                'history' => array_map(function($i) use ($avgScore) {
                    return [
                        'date' => now()->subDays(6 - $i)->format('Y-m-d'),
                        'score' => $avgScore + rand(-3, 3)
                    ];
                }, range(0, 6)),
                'summary' => [
                    'total_calls' => $totalTasks ?: rand(10, 15),
                    'total_interaction' => rand(10, 15),
                    'avg_duration' => '3:' . rand(10, 59),
                    'satisfaction_rate' => $avgScore + rand(-2, 2)
                ]
            ]
        ];
    }

    public function getAgentsPool() 
    {
        return [
            'Ahmed Al-Masri', 'Sara Al-Abadi', 'Omar Al-Khouri', 'Nour Haddad', 'Zaid Nassar', 
            'Layla Sayegh', 'Fadi Jaber', 'Mona Zeidan', 'Hassan Salem', 'Rania Hamdan', 
            'Yousif Badwan', 'Dana Hijazi', 'Khaled Al-Masri', 'Maya Al-Abadi', 'Ibrahim Al-Khouri', 'Salma Haddad'
        ];
    }

    public function getGlobalTaskPool()
    {
        $companies = ['ssc-jordan', 'arab-bank', 'orange-jo', 'manaseer-group', 'royal-jordanian'];
        $agentsPool = $this->getAgentsPool();
        $customersPool = [
            'Kais Al-Nimri', 'Dua\'a Al-Saleh', 'Samer Botros', 'Maher Al-Qasim', 'Heba Allah', 
            'Zaid Al-Fayez', 'Tariq Al-Khateeb', 'Lina Al-Masri', 'Fares Al-Hamouri', 'Salma Al-Ahmad', 
            'Rami Al-Kilani', 'Jana Al-Rousan', 'Yousif Al-Qudah', 'Amal Haddad'
        ];
        
        $supervisors = ['Mahmoud Ali', 'Sara Nasser', 'Ahmed Hassan', 'Layla Khaled'];
        $sources = ['api', 'avaya', 'genesys', 'fb', 'linkedin', 'inta', 'tiktok', 'snap', 'x', 'whatsapp', 'email'];
        $outcomes = ['Resolved', 'Follow-up Needed', 'Escalated', 'Customer Satisfied', 'Information Provided', 'Inquiry Completed'];
        $languages = ['Arabic', 'English'];
        $sentiments = ['Positive', 'Neutral', 'Negative'];

        $totalTasksCount = 75;
        $scoreBase = [
            65, 68, 72, 75, 77, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88,
            82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 85, 86, 87, 88, 89,
            90, 91, 92, 93, 94, 88, 89, 90, 91, 92, 93, 94, 95, 91, 92,
            93, 94, 95, 96, 97, 98, 98, 97, 96
        ];

        $allTasks = [];
        for ($i = 0; $i < $totalTasksCount; $i++) {
            $agent = $agentsPool[$i % count($agentsPool)];
            $customer = $customersPool[($i + 5) % count($customersPool)];
            $score = $scoreBase[$i % count($scoreBase)];
            $companyId = $companies[$i % count($companies)];
            $supervisor = $supervisors[$i % count($supervisors)];
            $source = $sources[$i % count($sources)];
            
            $messagingSources = ['fb', 'linkedin', 'inta', 'tiktok', 'snap', 'x', 'whatsapp', 'email'];
            $channel = in_array($source, $messagingSources) ? 'Messaging' : 'Call';

            if ($score < 75) {
                $duration = rand(6, 12) . "m " . rand(10, 59) . "s";
            } elseif ($score < 85) {
                $duration = rand(3, 6) . "m " . rand(10, 59) . "s";
            } else {
                $duration = rand(2, 4) . "m " . rand(10, 59) . "s";
            }
            
            $allTasks[] = [
                'id' => "task-" . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'company_id' => $companyId,
                'score' => $score,
                'status' => 'completed',
                'agent_name' => $agent,
                'customer_name' => $customer,
                'supervisor_name' => $supervisor,
                'duration' => $duration,
                'source' => $source,
                'channel' => $channel,
                'outcome' => $outcomes[$i % count($outcomes)],
                'coaching_required' => $score < 80 ? 'Yes' : 'No',
                'sentiment' => $sentiments[$i % count($sentiments)],
                'call_type' => rand(0, 1) ? 'Inbound' : 'Outbound',
                'lang' => $languages[$i % count($languages)],
                'risk_flag' => $score < 75 ? 'High' : 'No',
                'created_at' => now()->subDays(rand(0, 5))->setHour(rand(8, 17))->setMinute(rand(0, 59))->toDateTimeString()
            ];
        }
        return $allTasks;
    }
}