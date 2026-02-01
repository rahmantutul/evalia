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
        return [
            'success' => true,
            'user' => [
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
            ]
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
    public function listUsers($skip = 0, $limit = 100)
    {
        $agents = [
            ['id' => 'agent-nadi', 'full_name' => 'نادي البديري', 'email' => 'nadi@ssc.gov.jo', 'position' => 'Customer Support'],
            ['id' => 'agent-sara', 'full_name' => 'سارة الخطيب', 'email' => 'sara@ssc.gov.jo', 'position' => 'Account Manager'],
            ['id' => 'agent-omar', 'full_name' => 'عمر المصري', 'email' => 'omar@ssc.gov.jo', 'position' => 'Technical Support'],
            ['id' => 'agent-layla', 'full_name' => 'ليلى عودة', 'email' => 'layla@ssc.gov.jo', 'position' => 'Sales Representative'],
            ['id' => 'agent-khaled', 'full_name' => 'خالد منصور', 'email' => 'khaled@ssc.gov.jo', 'position' => 'Legal Advisor'],
            ['id' => 'agent-fatima', 'full_name' => 'فاطمة الزهراء', 'email' => 'fatima@ssc.gov.jo', 'position' => 'Public Relations'],
            ['id' => 'agent-yousef', 'full_name' => 'يوسف إبراهيم', 'email' => 'yousef@ssc.gov.jo', 'position' => 'Support Supervisor'],
            ['id' => 'agent-nour', 'full_name' => 'نور الهدى', 'email' => 'nour@ssc.gov.jo', 'position' => 'Help Desk'],
        ];

        $users = [];
        foreach ($agents as $agent) {
            $users[] = [
                'id' => $agent['id'],
                'full_name' => $agent['full_name'],
                'username' => str_replace(' ', '_', strtolower($agent['id'])),
                'email' => $agent['email'],
                'position' => $agent['position'],
                'phone' => "+962 7" . rand(10000000, 99999999),
                'is_active' => true,
                'role' => ['name' => 'Agent'],
                'company' => ['id' => 'ssc-jordan', 'name' => 'الضمان الاجتماعي - الأردن'],
                'company_name' => 'الضمان الاجتماعي - الأردن'
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
    public function getUser($userId)
    {
        $allUsers = $this->listUsers()['users'];
        $user = collect($allUsers)->firstWhere('id', $userId);

        if (!$user) {
            $user = [
                'id' => $userId,
                'full_name' => "مستخدم افتراضي",
                'username' => "user_$userId",
                'email' => "user$userId@ssc.gov.jo",
                'position' => 'Agent',
                'phone' => '+962 79 123 4567',
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
                ['id' => 'role-3', 'name' => 'Agent']
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

    public function getAgentPerformanceHistory($agentId)
    {
        $userData = $this->getUser($agentId)['user'];
        
        return [
            'success' => true,
            'data' => [
                'agent_details' => [
                    'id' => $agentId,
                    'display_id' => 'AGT-' . strtoupper(Str::random(5)),
                    'name' => $userData['full_name'],
                    'position' => $userData['position'],
                    'company_name' => 'الضمان الاجتماعي - الأردن'
                ],
                'current_scores' => [
                    'overall_score' => rand(88, 96),
                    'answer_accuracy' => rand(90, 98),
                    'response_speed' => rand(85, 95),
                    'customer_satisfaction' => rand(90, 100),
                    'professionalism' => rand(92, 100)
                ],
                'performance_weights' => [
                    'answer_accuracy' => 0.40,
                    'response_speed' => 0.30,
                    'customer_satisfaction' => 0.30
                ],
                'total_tasks' => rand(100, 500),
                'avg_call_duration' => rand(180, 400),
                'history' => [
                    ['date' => '2024-01-25', 'score' => 88],
                    ['date' => '2024-01-26', 'score' => 89],
                    ['date' => '2024-01-27', 'score' => 90],
                    ['date' => '2024-01-28', 'score' => 92],
                    ['date' => '2024-01-29', 'score' => 91],
                    ['date' => '2024-01-30', 'score' => 94],
                    ['date' => '2024-01-31', 'score' => 93]
                ],
                'summary' => [
                    'total_calls' => rand(500, 2000),
                    'avg_duration' => '3:' . rand(10, 59),
                    'satisfaction_rate' => rand(90, 98)
                ]
            ]
        ];
    }
}