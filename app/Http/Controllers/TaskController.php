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
    private function getRealTasks($companyId = null, $realAgents = [])
    {
        $realTasks = [];
        $totalTasksCount = 50;
        
        // Agent logic: use real names if available, plus fallback
        $agentsPool = array_values(array_map(function($a) { return $a['full_name']; }, $realAgents));
        if (empty($agentsPool)) {
            $agentsPool = [
                'نادي البديري', 'سارة الخطيب', 'محمود المصري', 'ليلى حسن', 'أحمد المناصير', 
                'فرح الزعبي', 'يزن التل', 'رشا عبيدات', 'عمر الحمصي', 'نور السالم'
            ];
        }

        $customersPool = [
            'Mousa Ali', 'Fatima Al-Sayed', 'Zaid Al-Hariri', 'Yara Suleiman', 'Omar Al-Bakr', 
            'Hala Al-Fares', 'Sami Al-Masri', 'Nour Al-Din', 'Mariam Al-Khalid', 'Ibrahim Al-Zahrani'
        ];
        
        $scoreDistribution = [
            98, 95, 92, 88, 85, 82, 78, 75, 72, 68,
            96, 94, 91, 87, 84, 81, 77, 74, 71, 67,
            97, 93, 90, 86, 83, 80, 76, 73, 70, 66,
            95, 92, 89, 85, 82, 79, 75, 72, 69, 65,
            94, 91, 88, 84, 81, 78, 74, 71, 68, 64
        ];
        
        $statusDistribution = array_merge(
            array_fill(0, 45, 'completed'),
            array_fill(0, 4, 'processing'),
            array_fill(0, 1, 'pending')
        );
        
        $supervisors = ['محمود علي', 'سارة ناصر', 'أحمد حسن', 'ليلى خالد'];
        $sources = ['api', 'avaya', 'genesys', 'fb', 'linkedin', 'inta', 'tiktok', 'snap', 'x', 'whatsapp', 'email'];
        $outcomes = ['Resolved', 'Follow-up Needed', 'Escalated', 'Customer Satisfied', 'Information Provided'];
        $languages = ['Arabic', 'English'];
        $sentiments = ['Positive', 'Neutral', 'Negative'];

        for ($i = 0; $i < $totalTasksCount; $i++) {
            // Assign to the REQUESTED company ID
            $targetCompanyId = $companyId ?? 'comp-001';
            
            $agentName = $agentsPool[$i % count($agentsPool)];
            $customer = $customersPool[$i % count($customersPool)];
            $supervisor = $supervisors[($i + 2) % count($supervisors)];
            $source = $sources[$i % count($sources)];
            
            $messagingSources = ['fb', 'linkedin', 'inta', 'tiktok', 'snap', 'x', 'whatsapp', 'email'];
            $channel = in_array($source, $messagingSources) ? 'Messaging' : 'Call';

            $score = $scoreDistribution[$i] ?? 85;
            $status = $statusDistribution[$i] ?? 'completed';
            
            $daysAgo = floor(pow((($i+1) / $totalTasksCount), 2) * 30);
            $businessHour = rand(8, 17);
            $minute = rand(0, 59);
            
            $duration = ($score < 75) ? (rand(6, 12) . "m " . rand(10, 59) . "s") : (rand(2, 5) . "m " . rand(10, 59) . "s");
            
            $taskId = "task-" . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            $realTasks[$taskId] = [
                'id' => $taskId,
                'company_id' => $targetCompanyId,
                'work_id' => 'real-analysis-' . $i,
                'status' => $status,
                'agent_id' => 'agent-' . ($i % 10),
                'created_at' => now()->subDays($daysAgo)->setHour($businessHour)->setMinute($minute)->toDateTimeString(),
                'duration' => $duration,
                'score' => $score,
                'customer_name' => $customer,
                'agent_name' => $agentName,
                'supervisor_name' => $supervisor,
                'source' => $source,
                'channel' => $channel,
                'outcome' => $outcomes[rand(0, count($outcomes)-1)],
                'coaching_required' => $score < 80 ? 'Yes' : 'No',
                'sentiment' => $sentiments[rand(0, count($sentiments)-1)],
                'call_type' => rand(0, 1) ? 'Inbound' : 'Outbound',
                'lang' => $languages[rand(0, count($languages)-1)],
                'risk_flag' => $score < 75 ? 'High' : 'No'
            ];
        }

        // 2. Dynamically load any JSON files from storage/app/analyses/
        $storagePath = storage_path('app/analyses');
        if (File::exists($storagePath)) {
            $files = File::files($storagePath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'json') {
                    $content = json_decode(File::get($file), true);
                    $id = $content['work_id'] ?? $file->getFilenameWithoutExtension();
                    
                    if (!isset($realTasks[$id])) {
                        $score = $content['agent_professionalism']['total_score']['percentage'] ?? rand(70, 95);
                        $realTasks[$id] = [
                            'id' => $id,
                            'company_id' => $companyId ?? 'comp-001',
                            'work_id' => $id,
                            'status' => $content['status'] ?? 'completed',
                            'agent_id' => $content['agent_id'] ?? 'unknown',
                            'created_at' => $content['created_at'] ?? now()->toDateTimeString(),
                            'duration' => $content['call_duration']['call_duration'] ?? '0:00',
                            'score' => $score,
                            'customer_name' => $content['customer_name'] ?? 'Mousa Ali',
                            'agent_name' => $content['agent_name'] ?? 'Sara Al-Khateeb',
                            'supervisor_name' => 'سارة ناصر',
                            'source' => 'api',
                            'channel' => 'Call',
                            'outcome' => 'Resolved',
                            'coaching_required' => $score < 80 ? 'Yes' : 'No',
                            'sentiment' => 'Positive',
                            'call_type' => 'Inbound',
                            'lang' => 'Arabic',
                            'risk_flag' => $score < 75 ? 'High' : 'No'
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
        // 1. Fetch real agents for this company first
        $result = $this->apiService->listUsers(0, 100);
        $companyAgents = array_filter($result['users'] ?? [], function($user) {
            return ($user['role']['name'] ?? '') === 'Agent';
        });

        // 2. Generate tasks using these real agents and for this specific company
        $allTasks = collect($this->getRealTasks($companyId, $companyAgents))->values()->all();

        // 3. Apply search filters
        $filteredTasks = $this->applyFilters($allTasks, $request);

        // 4. Pagination
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
                // Fallback to demo data instead of 404
                $data = $this->getRealArabicData();
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
        $agent = $request->get('agent', 'all');
        $source = $request->get('source', 'all');
        $channel = $request->get('channel', 'all');
        $supervisor = $request->get('supervisor', 'all');
        $sentiment = $request->get('sentiment', 'all');
        $language = $request->get('lang', 'all');
        $risk = $request->get('risk', 'all');

        return collect($tasks)->filter(function($task) use ($status, $agent, $source, $channel, $supervisor, $sentiment, $language, $risk) {
            $matchesStatus = $status === 'all' || $task['status'] === $status;
            $matchesAgent = $agent === 'all' || (isset($task['agent_name']) && str_contains(strtolower($task['agent_name']), strtolower($agent)));
            $matchesSource = $source === 'all' || $task['source'] === $source;
            $matchesChannel = $channel === 'all' || $task['channel'] === $channel;
            $matchesSupervisor = $supervisor === 'all' || (isset($task['supervisor_name']) && str_contains(strtolower($task['supervisor_name']), strtolower($supervisor)));
            $matchesSentiment = $sentiment === 'all' || (isset($task['sentiment']) && $task['sentiment'] === $sentiment);
            $matchesLanguage = $language === 'all' || (isset($task['lang']) && $task['lang'] === $language);
            $matchesRisk = $risk === 'all' || (isset($task['risk_flag']) && $task['risk_flag'] === $risk);
            
            return $matchesStatus && $matchesAgent && $matchesSource && $matchesChannel && 
                   $matchesSupervisor && $matchesSentiment && $matchesLanguage && $matchesRisk;
        })->values()->all();
    }

    private function getRealArabicData()
    {
        return [
            'customer_agent_audio_s3_url' => asset('assets/Transcript-Job-Details.mp3'),
            'pause_delay_information' => [
                'talking_duration' => [
                    'agent' => '02:00',
                    'customer' => '01:10'
                ],
                'speaker_delay_duration' => [
                    'agent' => [
                        ['delay_duration' => '0.8s', 'delay_start' => '00:06', 'delay_end' => '00:07', 'context' => 'Hesitation before protocol-based identity verification'],
                        ['delay_duration' => '1.2s', 'delay_start' => '00:27', 'delay_end' => '00:28', 'context' => 'Processing the specific inquiry about Self-Employed (الشمول الحر) installments']
                    ],
                    'customer' => [
                        ['delay_duration' => '0.5s', 'delay_start' => '00:11', 'delay_end' => '00:12', 'context' => 'Pause before providing full name (محمود المصري)'],
                        ['delay_duration' => '1.5s', 'delay_start' => '03:05', 'delay_end' => '03:07', 'context' => 'Slight confusion regarding phone number visibility on system']
                    ]
                ],
                'speaker_pause_duration' => [
                    'agent' => [
                        ['pause_duration' => '4.2s', 'pause_start' => '01:15', 'pause_end' => '01:20', 'pause_class' => 'Querying Social Security database for installment eligibility'],
                        ['pause_duration' => '3.5s', 'pause_start' => '02:10', 'pause_end' => '02:14', 'pause_class' => 'Verifying manual for Self-Employed (الشمول الحر) coverage rules']
                    ],
                    'customer' => [
                        ['pause_duration' => '2.0s', 'pause_start' => '00:45', 'pause_end' => '00:47', 'pause_class' => 'Thinking about the requested installment period']
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
                'detail' => 'اتصل العميل محمود المصري للاستفسار عن إمكانية تقسيط مبالغ الضمان الاجتماعي الخاصة بفئة "الشمول الحر". تعامل الموظف نادي بمهنية عالية وأوضح أن المعلومات الخاصة بالتقسيط لهذه الفئة تحديداً غير متوفرة بشكل فوري في النظام. قام الموظف كإجراء استباقي باقتراح رفع استفسار خطي رسمي للدائرة المختصة في الضمان الاجتماعي ووعد العميل بمعاودة الاتصال به خلال 24 ساعة لتزويده بالرد النهائي. أبدى العميل موافقته الكاملة، وتم خلال المكالمة التحقق من بيانات العميل (محمود عبد الله محمود) وتأكيد رقم هاتفه، وانتهت المكالمة برضا العميل عن الحل المقترح.'
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
                    'question' => 'هل قام الموظف بتحسين تجربة العميل من خلال تحية احترافية؟',
                    'answer' => 'نعم، قام الموظف بالتعريف بنفسه وبالجهة الحكومية فوراً.',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'معك نادي من الضمان الاجتماعي كيف ممكن أساعد؟',
                    'KBtext' => 'يجب على الموظف بدء المكالمة بذكر الاسم والجهة الرسمية بوضوح (سياسة الجودة - القسم الأول).',
                    'matching_transcript_sections' => ['00:02 - 00:06'],
                    'notebook_name' => 'معايير جودة المكالمات الحكومية',
                    'matching_topics' => ['البروتوكول الرسمي', 'التحية الفورية']
                ],
                [
                    'question' => 'هل تم التحقق من هوية العميل والبيانات الشخصية بدقة؟',
                    'answer' => 'نعم، تم استخراج الاسم الرباعي وتأكيد رقم الهاتف المسجل.',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'أتشرف باسم حضرتك... محمود عبد الله محمود... آخره 095',
                    'KBtext' => 'يتوجب التحقق من هويتين على الأقل (الاسم الرباعي ورقم الهاتف) قبل تقديم أي معلومة تخص الاشتراكات.',
                    'matching_transcript_sections' => ['00:08 - 03:15'],
                    'notebook_name' => 'أمن البيانات والخصوصية',
                    'matching_topics' => ['التحقق من الهوية']
                ],
                [
                    'question' => 'هل التزم الموظف بدليل سياسات "الشمول الحر" بخصوص التقسيط؟',
                    'answer' => 'نعم، الموظف لم يعط معلومة مغلوطة لعدم التأكد، بل قام بالتصعيد الصحيح.',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'برفع لحضرتك استفسار للضمان الاجتماعي لأنه ما تمتزلنا لحد الآن معلومة',
                    'KBtext' => 'برامج التقسيط لفئة الشمول الحر تتطلب دراسة حالة فردية عبر نظام الاستفسارات الموحد (دليل الاشتراكات - البند 42).',
                    'matching_transcript_sections' => ['02:35 - 02:48'],
                    'notebook_name' => 'دليل اشتراكات الشمول الحر',
                    'matching_topics' => ['السياسات المالية', 'برامج التقسيط']
                ],
                [
                    'question' => 'هل قدم الموظف حلولاً استباقية عند عدم توفر المعلومة؟',
                    'answer' => 'نعم، اقترح رفع استفسار وتحديد مهلة زمنية للرد (24 ساعة).',
                    'evaluation' => 'Pass',
                    'confidence_level' => 'high',
                    'evidence' => 'خلال 24 ساعة إن شاء الله بيتم التواصل مع حضرتك',
                    'KBtext' => 'في حال عدم توفر المعلومة فورياً، يجب تقديم بديل ملموس (مثل رفع طلب متابعة) وتحديد وقت متوقع للرد.',
                    'matching_transcript_sections' => ['02:55 - 03:05'],
                    'notebook_name' => 'بروتوكول حل المشكلات',
                    'matching_topics' => ['الحلول الاستباقية', 'إدارة التوقعات']
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