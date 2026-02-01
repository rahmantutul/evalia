<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    protected $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->middleware('auth.api');
        $this->apiService = $apiService;
    }

    /**
     * Get all "Real" tasks from the storage or hardcoded list.
     */
    private function getRealTasks($companyId = null)
    {
        $companies = ['ssc-jordan', 'arab-bank', 'orange-jo', 'manaseer-group', 'royal-jordanian'];
        $agentsPool = [
            'نادي البديري', 'سارة الخطيب', 'محمود المصري', 'ليلى حسن', 'أحمد المناصير', 
            'فرح الزعبي', 'يزن التل', 'رشا عبيدات', 'عمر الحمصي', 'نور السالم', 
            'خالد الجزار', 'منى السعيد', 'باسل الرواشدة', 'ديما النسور'
        ];
        $customersPool = [
            'قيس النمري', 'دعاء الصالح', 'سامر بطرس', 'ماهر القاسم', 'هبة الله', 
            'زيد الفايز', 'طارق الخطيب', 'لينا المصري', 'فارس الحموري', 'سلمى الأحمد', 
            'رامي الكيلاني', 'جنى الروسان', 'يوسف القضاة', 'أمل حداد'
        ];

        $allTasks = [];
        $totalTasksCount = 54;
        
        // Realistic score distribution (bell curve centered around 82-88)
        $scoreDistribution = [
            65, 68, 72, 75, 77, // 5 low performers (9%)
            79, 80, 81, 82, 83, 84, 85, 86, 87, 88, // 10 average (19%)
            82, 83, 84, 85, 86, 87, 88, 89, 90, 91, // 10 good (19%)
            85, 86, 87, 88, 89, 90, 91, 92, 93, 94, // 10 very good (19%)
            88, 89, 90, 91, 92, 93, 94, 95, // 8 excellent (15%)
            91, 92, 93, 94, 95, 96, 97, 98, 98, 97, 96 // 11 top performers (20%)
        ];
        
        // Realistic status distribution: 90% completed, 8% processing, 2% pending
        $statusDistribution = array_merge(
            array_fill(0, 49, 'completed'),
            array_fill(0, 4, 'processing'),
            array_fill(0, 1, 'pending')
        );
        
        for ($i = 1; $i <= $totalTasksCount; $i++) {
            // Distribute across 5 companies evenly
            $targetCompanyId = $companies[($i - 1) % count($companies)];
            
            $agent = $agentsPool[$i % count($agentsPool)];
            $customer = $customersPool[($i + 5) % count($customersPool)];
            
            // Realistic score from distribution
            $score = $scoreDistribution[$i - 1];
            
            // Realistic status
            $status = $statusDistribution[$i - 1];
            
            // Realistic timestamps: spread over last 30 days, weighted toward recent
            $daysAgo = floor(pow(($i / $totalTasksCount), 2) * 30);
            $businessHour = rand(8, 17); // Business hours 8 AM - 5 PM
            $minute = rand(0, 59);
            
            // Realistic duration based on complexity (longer calls = more issues = lower scores)
            if ($score < 75) {
                $duration = rand(6, 12) . "m " . rand(10, 59) . "s";
            } elseif ($score < 85) {
                $duration = rand(3, 6) . "m " . rand(10, 59) . "s";
            } else {
                $duration = rand(2, 4) . "m " . rand(10, 59) . "s";
            }
            
            $allTasks[] = [
                'id' => "task-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'company_id' => $targetCompanyId,
                'work_id' => 'real-arabic',
                'status' => $status,
                'agent_id' => 'agent-' . ($i % 14),
                'created_at' => now()->subDays($daysAgo)->setHour($businessHour)->setMinute($minute)->toDateTimeString(),
                'duration' => $duration,
                'score' => $score,
                'customer_name' => $customer,
                'agent_name' => $agent
            ];
        }

        // Filter by company if provided
        if ($companyId) {
            $realTasks = array_filter($allTasks, function($task) use ($companyId) {
                return $task['company_id'] === $companyId;
            });
        } else {
            $realTasks = $allTasks;
        }

        // Convert back to associative array for dynamic loading merging
        $indexedTasks = [];
        foreach ($realTasks as $task) {
            $indexedTasks[$task['id']] = $task;
        }
        $realTasks = $indexedTasks;

        // 2. Dynamically load any JSON files from storage/app/analyses/
        $storagePath = storage_path('app/analyses');
        if (File::exists($storagePath)) {
            $files = File::files($storagePath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'json') {
                    $content = json_decode(File::get($file), true);
                    $id = $content['work_id'] ?? $file->getFilenameWithoutExtension();
                    
                    if (!isset($realTasks[$id])) {
                        $realTasks[$id] = [
                            'id' => $id,
                            'work_id' => $id,
                            'status' => $content['status'] ?? 'completed',
                            'agent_id' => $content['agent_id'] ?? 'unknown',
                            'created_at' => $content['created_at'] ?? now()->toDateTimeString(),
                            'duration' => $content['call_duration']['call_duration'] ?? '0:00',
                            'score' => $content['agent_professionalism']['total_score']['percentage'] ?? 0,
                            'customer_name' => $content['customer_name'] ?? 'Mousa Ali',
                            'agent_name' => $content['agent_name'] ?? 'Sara Al-Khateeb'
                        ];
                    }
                }
            }
        }

        return $realTasks;
    }

    /**
     * Helper to find a specific task by ID.
     */
    private function findTaskById($taskId)
    {
        $allTasks = $this->getRealTasks();
        return $allTasks[$taskId] ?? null;
    }

    public function TaskList($companyId, Request $request)
    {
        // Get tasks for THIS specific company
        $allTasks = collect($this->getRealTasks($companyId))->values()->all();

        // Apply filters
        $filteredTasks = $this->applyFilters($allTasks, $request);

        // Pagination
        $page = Paginator::resolveCurrentPage(); 
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $pagedTasks = array_slice($filteredTasks, $offset, $perPage);

        $paginatedTasks = new LengthAwarePaginator(
            $pagedTasks,
            count($filteredTasks),
            $perPage,
            $page,
            ['path' => route('user.task.list', ['companyId' => $companyId])]
        );

        $result = $this->apiService->listUsers(0, 100);
        $companyAgents = array_filter($result['users'] ?? [], function($user) {
            return ($user['role']['name'] ?? '') === 'Agent';
        });

        return view('user.task.task_list', [
            'company_id' => $companyId,
            'taskList' => $paginatedTasks,
            'hasRunningTasks' => false,
            'companyAgents' => $companyAgents,
        ]);
    }

    public function taskDetails($workId)
    {
        // 1. Try to find the task in our lists
        $task = $this->findTaskById($workId);
        
        // 2. Determine which data to load
        $templateId = $task['work_id'] ?? $workId;

        if ($templateId === 'real-arabic') {
            $data = $this->getRealArabicData();
        } else {
            // Try to load from JSON file in storage
            $filePath = storage_path("app/analyses/{$templateId}.json");
            if (File::exists($filePath)) {
                $data = json_decode(File::get($filePath), true);
            } else {
                abort(404, "Task analysis not found for ID: {$workId} (Template: {$templateId})");
            }
        }

        return view('user.task.task_details', [
            'data' => $data,
            'workId' => $workId,
            'status' => 'completed'
        ]);
    }

    private function applyFilters($tasks, $request)
    {
        $status = $request->get('status', 'all');
        return collect($tasks)->filter(function($task) use ($status) {
            return $status === 'all' || $task['status'] === $status;
        })->values()->all();
    }

    private function getRealArabicData()
    {
        return [
            'customer_agent_audio_s3_url' => asset('assets/call center calls sample/Hamsa--Transcript-Job-Details.mp3'),
            'pause_delay_information' => [
                'talking_duration' => [
                    'agent' => '02:15',
                    'customer' => '01:30'
                ],
                'speaker_delay_duration' => [
                    'agent' => [
                        ['duration' => '0.8s', 'time' => '00:06', 'context' => 'After greeting'],
                        ['duration' => '1.2s', 'time' => '00:27', 'context' => 'Before clarifying installment']
                    ],
                    'customer' => [
                        ['duration' => '0.5s', 'time' => '00:11', 'context' => 'Before giving name'],
                        ['duration' => '1.5s', 'time' => '03:05', 'context' => 'Before asking about phone number']
                    ]
                ]
            ],
            'call_duration' => [
                'call_duration' => '03:30'
            ],
            'pace' => [
                'agent_pace' => 130,
                'customer_pace' => 110
            ],
            'speaker_loudness' => [
                'agent' => [
                    'lower_loudness_percentage' => 2.5,
                    'optimal_loudness_percentage' => 94.2,
                    'upper_loudness_percentage' => 3.3
                ],
                'customer' => [
                    'lower_loudness_percentage' => 10.5,
                    'optimal_loudness_percentage' => 85.2,
                    'upper_loudness_percentage' => 4.3
                ]
            ],
            'agent_professionalism' => [
                'total_score' => ['percentage' => 96, 'score' => 48, 'max_score' => 50],
                'speech_characteristics' => [
                    'volume' => [
                        'loudness_class' => 'optimal',
                        'optimal_loudness_percentage' => 94
                    ],
                    'speed' => 130,
                    'pauses' => 8,
                    'tone_analysis' => [
                        'friendly' => 92,
                        'confident' => 88,
                        'empathetic' => 85
                    ]
                ],
                'linguistic_analysis' => [
                    'formal_language_percentage' => 95
                ],
                'customer_satisfaction' => [
                    'score' => 10,
                    'evidence' => '"لا خلاص الله يسعدك"',
                    'reasoning' => 'The customer explicitly thanked the agent and expressed satisfaction after the inquiry protocol was explained.',
                    'determination' => 'Very High'
                ],
                'professionalism' => [
                    'score' => 10,
                    'evidence' => 'Agent used proper greeting and professional closing: "شكراً حضرتك على التواصل... في أمان الله"',
                    'reasoning' => 'Perfect adherence to government service standards.',
                    'determination' => 'Exceptional'
                ],
                'tone_consistency' => [
                    'score' => 9,
                    'evidence' => 'Maintained a calm and helpful demeanor throughout the detailed search.',
                    'reasoning' => 'Steady emotional intelligence.',
                    'determination' => 'Consistent'
                ],
                'polite_language_usage' => [
                    'score' => 10,
                    'evidence' => 'Consistent use of "سيد محمود", "سيدي الكريم", and "تفضل".',
                    'reasoning' => 'Excellent use of honorifics.',
                    'determination' => 'Polite'
                ],
                'configured_standards_compliance' => [
                    'score' => 9,
                    'evidence' => 'Followed the correct verification protocol for the phone number and full name.',
                    'reasoning' => 'Compliant with data collection rules.',
                    'determination' => 'Compliant'
                ]
            ],
            'agent_assessment' => [
                'total_score' => ['percentage' => 82],
                'communication' => [
                    'score' => 9,
                    'evidence' => '"برفع لحضرتك استفسار للضمان الاجتماعي"',
                    'reasoning' => 'Agent clearly explained why an immediate answer wasn\'t available and bridged to a solution.',
                    'determination' => 'Strong'
                ],
                'problem_solving' => [
                    'score' => 8,
                    'evidence' => 'Escalated to an inquiry system when immediate info was missing.',
                    'reasoning' => 'Effective workaround for missing data.',
                    'determination' => 'Effective'
                ],
                'technical_knowledge' => [
                    'score' => 7,
                    'evidence' => 'Needed to verify if "Self-Employed" coverage has installment programs.',
                    'reasoning' => 'Could benefit from more direct training on niche coverage types.',
                    'determination' => 'Acceptable'
                ],
                'efficiency' => [
                    'score' => 8,
                    'evidence' => 'Research time was kept under 90 seconds.',
                    'reasoning' => 'Handled the lookup within reasonable limits.',
                    'determination' => 'Efficient'
                ]
            ],
            'agent_cooperation' => [
                'total_score' => ['percentage' => 98],
                'agent_proactive_assistance' => [
                    'score' => 10,
                    'evidence' => 'Offered to submit the inquiry without customer having to visit.',
                    'reasoning' => 'Went above and beyond to provide a solution.',
                    'determination' => 'Highly Proactive'
                ],
                'agent_responsiveness' => [
                    'score' => 9,
                    'evidence' => 'Immediate response to the customer\'s greeting and name.',
                    'reasoning' => 'Active listening present.',
                    'determination' => 'Responsive'
                ],
                'agent_empathy' => [
                    'score' => 9,
                    'evidence' => '"والا يهمك سيدي الكريم"',
                    'reasoning' => 'Understood the customer\'s need for financial clarity.',
                    'determination' => 'Empathetic'
                ],
                'effectiveness' => [
                    'score' => 10,
                    'evidence' => 'Ended call with a confirmed 24-hour follow-up window.',
                    'reasoning' => 'Complete resolution of the inquiry process.',
                    'determination' => 'Effective'
                ]
            ],
            'transcription_summaries' => [
                'detail' => 'Customer Mahmoud Al-Masri called to inquire about installment plans for the Self-Employed (شمول حر) social security coverage. The agent, Nadi, was polite and professional. Since data on installments for this specific category was not immediately available, the agent proactively offered to submit a formal inquiry and promised a callback within 24 hours. The customer agreed, verified his details, and ended the call satisfied.'
            ],
            'topics' => [
                'main' => ['تقسيط الضمان الاجتماعي'],
                'other' => ['الشمول الحر', 'البحث عن معلومات', 'طلب متابعة']
            ],
            'most_common_words' => [
                'agent' => [
                    ['word' => 'الضمان', 'frequency' => 8],
                    ['word' => 'حضرتك', 'frequency' => 7],
                    ['word' => 'تقسيط', 'frequency' => 6],
                    ['word' => 'لحظات', 'frequency' => 5],
                    ['word' => 'استفسار', 'frequency' => 4],
                    ['word' => 'سيد', 'frequency' => 4],
                    ['word' => 'محمود', 'frequency' => 3],
                    ['word' => 'برنامج', 'frequency' => 3],
                ],
                'customer' => [
                    ['word' => 'تقسيط', 'frequency' => 4],
                    ['word' => 'محمود', 'frequency' => 3],
                    ['word' => 'آه', 'frequency' => 3],
                    ['word' => 'شغلة', 'frequency' => 2],
                    ['word' => 'شمول', 'frequency' => 2],
                    ['word' => 'حر', 'frequency' => 2],
                ]
            ],
            'call_outcome' => ['تم تقديم الطلب', 'وعد بالمتابعة خلال 24 ساعة', 'تم التحقق من الزبون'],
            'speakers_transcriptions' => [
                ['start_time' => '00:02', 'end_time' => '00:06', 'speaker' => 'agent', 'transcript' => 'معك نادي من الضمان الاجتماعي كيف ممكن أساعد؟', 'sentiment' => 'Positive'],
                ['start_time' => '00:07', 'end_time' => '00:07', 'speaker' => 'customer', 'transcript' => 'السلام عليكم', 'sentiment' => 'Positive'],
                ['start_time' => '00:08', 'end_time' => '00:11', 'speaker' => 'agent', 'transcript' => 'وعليكم السلام تفضل أتشرف باسم حضرتك', 'sentiment' => 'Positive'],
                ['start_time' => '00:12', 'end_time' => '00:13', 'speaker' => 'customer', 'transcript' => 'محمود المصري', 'sentiment' => 'Neutral'],
                ['start_time' => '00:14', 'end_time' => '00:15', 'speaker' => 'agent', 'transcript' => 'تفضل سيد محمود', 'sentiment' => 'Positive'],
                ['start_time' => '00:16', 'end_time' => '00:23', 'speaker' => 'customer', 'transcript' => 'الله يسلمك، بدي أسأل على شغلة هيك أنا شمول الحر هل في تقسيط إله؟', 'sentiment' => 'Neutral'],
                ['start_time' => '00:24', 'end_time' => '00:24', 'speaker' => 'agent', 'transcript' => 'تقسيط؟', 'sentiment' => 'Neutral'],
                ['start_time' => '00:25', 'end_time' => '00:27', 'speaker' => 'customer', 'transcript' => 'آه تقسيط للضمان', 'sentiment' => 'Neutral'],
                ['start_time' => '00:28', 'end_time' => '00:32', 'speaker' => 'agent', 'transcript' => 'كيف يعني تقسيط؟ الشمول الحر يعني هل مشمّر في برنامج تقسيط؟', 'sentiment' => 'Neutral'],
                ['start_time' => '00:33', 'end_time' => '00:33', 'speaker' => 'customer', 'transcript' => 'آه', 'sentiment' => 'Neutral'],
                ['start_time' => '00:34', 'end_time' => '00:36', 'speaker' => 'agent', 'transcript' => 'لحظات لو سمحت أتأكد لحضرتك؟', 'sentiment' => 'Positive'],
                ['start_time' => '00:37', 'end_time' => '00:37', 'speaker' => 'customer', 'transcript' => 'نعم', 'sentiment' => 'Neutral'],
                ['start_time' => '00:38', 'end_time' => '00:51', 'speaker' => 'agent', 'transcript' => 'هأل هو الأشخاص اللي يعني بيدخلوا في موضوع التقسيط، لحظات أتأكد لحضرتك الفئات؟', 'sentiment' => 'Neutral'],
                ['start_time' => '00:52', 'end_time' => '00:53', 'speaker' => 'agent', 'transcript' => 'امهني لحظات لو سمحت سيد محمود', 'sentiment' => 'Positive'],
                ['start_time' => '00:54', 'end_time' => '00:59', 'customer' => 'محمود المصري', 'speaker' => 'customer', 'transcript' => 'آه تمام', 'sentiment' => 'Neutral'],
                ['start_time' => '01:00', 'end_time' => '02:34', 'speaker' => 'agent', 'transcript' => 'الشمول الحر أصحاب الشمول الحر استفسار حضرتك هل بيستفيدوا من برنامج تقسيط ولا؟ لحظات', 'sentiment' => 'Neutral'],
                ['start_time' => '02:35', 'end_time' => '02:48', 'speaker' => 'agent', 'transcript' => 'سيدي الكريم ممكن أرفع لحضرتك استفسار لأنه ما تمتزلنا لحد الآن بمعلومة إنه هل بيستفيدوا من برنامج تقسيط أم لا، برفع لحضرتك استفسار للضمان الاجتماعي حابب سيد محمود؟', 'sentiment' => 'Positive'],
                ['start_time' => '02:48', 'end_time' => '02:48', 'speaker' => 'customer', 'transcript' => 'okay', 'sentiment' => 'Positive'],
                ['start_time' => '02:49', 'end_time' => '02:52', 'speaker' => 'agent', 'transcript' => 'والا يهمك زورني باسم حضرتك الرباعي لو سمحت', 'sentiment' => 'Positive'],
                ['start_time' => '02:53', 'end_time' => '02:54', 'speaker' => 'customer', 'transcript' => 'محمود عبد الله محمود', 'sentiment' => 'Neutral'],
                ['start_time' => '02:55', 'end_time' => '03:05', 'speaker' => 'agent', 'transcript' => 'والا يهمك راح أرفع لحضرتك استفسار للضمان وخلال 24 ساعة إن شاء الله بيتم التواصل مع حضرتك', 'sentiment' => 'Positive'],
                ['start_time' => '03:06', 'end_time' => '03:08', 'speaker' => 'customer', 'transcript' => 'طيب الرقم يعني مبين أنتوا ولا؟', 'sentiment' => 'Neutral'],
                ['start_time' => '03:09', 'end_time' => '03:15', 'speaker' => 'agent', 'transcript' => 'رقم آخره اللي هو 095 رقم حضرتك هو الرقم اللي...', 'sentiment' => 'Neutral'],
                ['start_time' => '03:16', 'end_time' => '03:16', 'speaker' => 'customer', 'transcript' => 'تمام', 'sentiment' => 'Positive'],
                ['start_time' => '03:16', 'end_time' => '03:19', 'speaker' => 'agent', 'transcript' => 'نعم نعم والا يهمك أي استفسار تاني حابب تستعلم عنه؟', 'sentiment' => 'Positive'],
                ['start_time' => '03:20', "end_time" => '03:21', 'speaker' => 'customer', 'transcript' => 'لا خلاص الله يسعدك', 'sentiment' => 'Positive'],
                ['start_time' => '03:22', 'end_time' => '03:30', 'speaker' => 'agent', 'transcript' => 'شكراً حضرتك على التواصل سيتم إرسال رسالة لغاية التقييم يومك سعيد في أمان الله.', 'sentiment' => 'Positive']
            ],
            'agent_speakers_transcriptions' => array_fill(0, 15, ['sentiment' => 'Positive']),
            'customer_speakers_transcriptions' => array_fill(0, 12, ['sentiment' => 'Neutral']),
            'analysis_alignment_result_notebook' => [
                [
                    'question' => 'هل قام الموظف بتحية العميل بشكل احترافي؟',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'معك نادي من الضمان الاجتماعي كيف ممكن أساعد؟'
                ],
                [
                    'question' => 'هل قام الموظف بالتحقق من هوية العميل؟',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'أتشرف باسم حضرتك... محمود عبد الله محمود'
                ],
                [
                    'question' => 'هل تم حل المشكلة أو تصعيدها بشكل صحيح؟',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'برفع لحضرتك استفسار للضمان الاجتماعي'
                ]
            ]
        ];
    }

    public function deleteTask($workId)
    {
        return redirect()->back()->with('success', 'Task removed.');
    }

    public function taskStore(Request $request)
    {
        return back()->with('success', 'Upload feature placeholder.');
    }

    public function reEvaluateTask(string $taskUuid)
    {
        return redirect()->back()->with('success', 'Re-evaluation placeholder.');
    }

    public function getTaskStatus($companyId)
    {
        return response()->json(['hasRunningTasks' => false, 'tasks' => []]);
    }
}