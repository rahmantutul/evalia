@extends('user.layouts.app')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0a66c2;
            --secondary-color: #004182;
            --accent-color: #4895ef;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            border-left: 4px solid var(--accent-color);
        }
        
        .section-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .icon-title {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        textarea.form-control {
            min-height: 120px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Tagify customization */
        .tagify {
            --tag-bg: var(--primary-color);
            --tag-hover: var(--secondary-color);
            --tag-text-color: white;
            --tags-border-color: #e0e0e0;
            --tag-remove-btn-color: white;
            padding: 0.5rem;
            border-radius: 8px;
        }
        
        .tagify__input {
            padding: 0.25rem 0.5rem !important;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-section {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .form-section:nth-child(1) { animation-delay: 0.1s; }
        .form-section:nth-child(2) { animation-delay: 0.2s; }
        .form-section:nth-child(3) { animation-delay: 0.3s; }
        .form-section:nth-child(4) { animation-delay: 0.4s; }
        .form-section:nth-child(5) { animation-delay: 0.5s; }
        .form-section:nth-child(6) { animation-delay: 0.6s; }
    </style>
    <style>
    .icon-wrapper {
        transition: all 0.3s ease;
        border: 1px solid rgba(13, 110, 253, 0.15);
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
</style>
@endpush
@section('content')
<div class="min-v d-flex align-items-center">
    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header border-0" style="background: linear-gradient(135deg, #efefef 0%, #eef2f6 100%)">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-white shadow-sm rounded-3 p-3 me-3">
                                <i class="fas fa-building-circle-arrow-right text-primary fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-dark fw-semibold">Company Settings</h3>
                                <p class="text-muted mb-0 fs-7">Update your company settings with our premium service</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form id="companyRegistrationForm" method="PUT" action="{{ route('user.company.update', $company['id'])}}" class="needs-validation" novalidate>
                            @csrf
                            @if(isset($company))
                                @method('PUT')
                            @endif
                            <div class="form-section">
                                <h4 class="section-title"><i class="bi bi-gear icon-title"></i>Content Configuration</h4>
                                <div class="row g-3">
                                    <input type="hidden" class="form-control" id="id" name="id" value="{{ $company['id'] }}" required>
                                     <!-- <div class="col-md-4">
                                        <label for="group_id" class="form-label">Select Group:</label>
                                        <select name="group_id" id="" class="form-control" required>
                                            <option value="">--Select a group--</option>
                                            @foreach ($groups as $item)
                                                <option {{ ($item['group_id'] == $company['group_id'] ) ? 'selected' : '' }} value="{{ $item['group_id'] }}">{{ $item['group_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div> -->
                                    <div class="col-md-4">
                                        <label for="company_name" class="form-label">Company Name:</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" 
                                            value="{{ $company['company_name'] }}" required>
                                        <div class="invalid-feedback">Please provide a company name.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="filler_words" class="form-label">Filler Words:</label>
                                        <input type="text" class="form-control" id="filler_words" name="filler_words" 
                                            value="{{ isset($company) && !empty($company['filler_words']) ? implode(',', $company['filler_words']) : '' }}" 
                                            placeholder="Type and press enter to add">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="main_topics" class="form-label">Main Topics:</label>
                                        <input type="text" class="form-control" id="main_topics" name="main_topics" 
                                           value="{{ isset($company) && !empty($company['main_topics']) ? (is_array($company['main_topics']) ? implode(',', $company['main_topics']) : $company['main_topics']) : '' }}"
                                            placeholder="Type and press enter to add">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="restricted_phrases" class="form-label">Restricted Phrases:</label>
                                        <input type="text" class="form-control" id="restricted_phrases" name="restricted_phrases" 
                                            value="{{ isset($company) && !empty($company['restricted_phrases']) ? implode(',', $company['restricted_phrases']) : '' }}" 
                                            placeholder="Phrases agents should avoid...">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="call_types" class="form-label">Call Types:</label>
                                        <input type="text" class="form-control" id="call_types" name="call_types" 
                                            value="{{ isset($company) && !empty($company['call_types']) ? (is_array($company['call_types']) ? implode(',', $company['call_types']) : $company['call_types']) : '' }}" placeholder="Type and press enter to add">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="source" class="form-label">Integration Sources:</label>
                                        @php
                                            $currentSources = is_array($company['source'] ?? []) ? ($company['source'] ?? []) : (isset($company['source']) ? [$company['source']] : []);
                                        @endphp
                                        <input type="text" class="form-control" id="source" name="source" 
                                            value="{{ implode(',', $currentSources) }}" 
                                            placeholder="Select integration sources">
                                        <small class="text-muted">Choose from available integration sources.</small>
                                    </div>
                                    <div class="col-12">
                                        <label for="company_overview" class="form-label">Company Overview:</label>
                                        <textarea class="form-control prompt-field" id="company_overview" name="company_overview" placeholder="Provide a brief overview of the company for AI context..." rows="3" readonly>{{ !empty($company['company_overview']) ? $company['company_overview'] : 'A leading provider of cloud-based enterprise solutions, specializing in AI-driven customer support and technical infrastructure management worldwide.' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Prompt Configurations Section -->
                            <div class="form-section position-relative" id="prompt-section">
                                <h4 class="section-title"><i class="bi bi-chat-square-text icon-title"></i>Prompt Configurations <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">CRITICAL</span></h4>
                                
                                <div class="position-relative">
                                    <div id="prompt-lock-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-75 rounded" style="z-index: 10; backdrop-filter: blur(2px);">
                                        <i class="bi bi-lock-fill text-danger fs-1 mb-2"></i>
                                        <p class="fw-bold text-dark">Modification Restricted</p>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="unlockPrompts()">Unlock to Modify</button>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="company_policies" class="form-label">Company Policies :</label>
                                            <textarea class="form-control prompt-field" id="company_policies" name="company_policies" placeholder="Enter policies, one per line..." rows="6" readonly>{{ isset($company) ? implode("\n", $company['company_policies'] ?? [
"1. الالتزام بآداب الحديث واللباقة مع العملاء في جميع الأوقات.",
"2. يمنع منعا باتا طلب أي معلومات سرية أو كلمات مرور من العميل.",
"3. يجب التحقق من هوية المتصل قبل تقديم أي معلومات حساسة.",
"4. الالتزام بوقت الاستجابة المحدد (أقل من 30 ثانية لكل رد).",
"5. في حال عدم معرفة الإجابة، يتم تصعيد التذكرة للقسم المختص بدلاً من تقديم معلومات مغلوطة.",
"6. إنهاء المكالمة بجملة ترحيبية مهذبة والتأكد من رضا العميل."
                                            ]) : '' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label d-flex justify-content-between align-items-center">
                                                Frequently Asked Questions (FAQ)
                                                <button type="button" class="btn btn-sm btn-outline-primary prompt-field" id="add-faq-btn" disabled>
                                                    <i class="bi bi-plus-lg me-1"></i>Add Pair
                                                </button>
                                            </label>
                                            <div id="faq-container">
                                                <!-- FAQ pairs added here -->
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <label for="qna_pair_prompt" class="form-label">QnA Pair Prompt:</label>
                                            <textarea class="form-control prompt-field" id="qna_pair_prompt" name="qna_pair_prompt" rows="4" readonly>{{ $company['qna_pair_prompt'] ?? 'find the answers for the provided customer questions in the following call transcription
TASK RULES:
- Always give response in Arabic Language 
- if you did not find answers reply with no-data-found
- Correct any words that contain linguistic errors in the provided text. The text is generated by AWS Transcribe and contains many errors. Replace words that do not align with the general topic of the call.' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label for="gem_qna_pair_eval" class="form-label">GEM QnA Pair Evaluation:</label>
                                            <textarea class="form-control prompt-field" id="gem_qna_pair_eval" name="gem_qna_pair_eval" rows="6" readonly>{{ $company['gem_qna_pair_eval'] ?? 'أنت مُقيّم ذكاء اصطناعي. قم بتحليل أزواج الأسئلة والأجوبة باستخدام المعلومات من القاعدة المعرفية وفقًا للآتي:
positive: الإجابة تتطابق مع المعلومات في القاعدة.
يجب تضمين النص الداعم من القاعدة.
negative: السؤال له إجابة في القاعدة لكن الإجابة المقدمة خاطئة أو تختلف عنها.
يجب تضمين النص الداعم من القاعدة.
notAvailable: السؤال لا يستند إلى معلومات في القاعدة.
استخدم النص: "لا يوجد نص ذو صلة في القاعدة المعرفية".' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label for="gpt_qna_pair_eval" class="form-label">GPT QnA Pair Evaluation:</label>
                                            <textarea class="form-control prompt-field" id="gpt_qna_pair_eval" name="gpt_qna_pair_eval" rows="6" readonly>{{ $company['gpt_qna_pair_eval'] ?? 'زودني بجميع النصوص المتعلقة بالسؤال التالي أو إجابته من قاعدة المعرفة كما هي دون أي تعديل، مع تضمين جميع النصوص ذات الصلة فقط دون إضافة أي محتوى غير مرتبط.
لا تقم بتزويدي بأية روابط فقط النصوص ذات الصله كي أقوم بتقييم السؤال والاجابه بناء عليها.
لا تقم بأضافة أي شيء للنصوص ذات الصلة فقط زودني بها كما هي 
لا تقم بذكر فيما اذا كانت الخدمه متوفرة ام لا , ولا تقم بذكر رأيك في السؤال او الجواب , فقط زودني بالنصوص المرتبطه بالسؤال و الجواب ادناه' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <!-- Operating Hours & Holidays Section -->
                            <div class="form-section">
                                <h4 class="section-title"><i class="bi bi-clock icon-title"></i>Operating Hours & Holidays</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="operating_hours" class="form-label">Operating Hours:</label>
                                        <textarea class="form-control" id="operating_hours" name="operating_hours" placeholder="e.g. Mon-Fri: 9AM-6PM" rows="2">{{ !empty($company['operating_hours']) ? $company['operating_hours'] : 'Monday - Friday: 09:00 AM - 06:00 PM EST' }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="holidays" class="form-label">Holidays:</label>
                                        <textarea class="form-control" id="holidays" name="holidays" placeholder="e.g. New Year: Jan 1st" rows="2">{{ !empty($company['holidays']) ? $company['holidays'] : 'New Year: Jan 1st, Independence Day: July 4th, Christmas: Dec 25th' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            
                            <!-- Thresholds & Limits Section -->
                            <div class="form-section">
                                <h4 class="section-title"><i class="bi bi-speedometer2 icon-title"></i>Thresholds & Limits</h4>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="delay_accept_limit" class="form-label">Delay Accept Limit:</label>
                                        <input type="number" step="0.1" class="form-control" id="delay_accept_limit" name="delay_accept_limit" 
                                            value="{{ $company['delay_accept_limit'] ?? 0 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="pause_accept_limit" class="form-label">Pause Accept Limit:</label>
                                        <input type="number" step="0.1" class="form-control" id="pause_accept_limit" name="pause_accept_limit" 
                                            value="{{ $company['pause_accept_limit'] ?? 0 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="loudness_threshold" class="form-label">Loudness Threshold:</label>
                                        <input type="number" step="0.1" class="form-control" id="loudness_threshold" name="loudness_threshold" 
                                            value="{{ $company['loudness_threshold'] ?? 0 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="interactive_threshold" class="form-label">Interactive Threshold:</label>
                                        <input type="number" step="0.1" class="form-control" id="interactive_threshold" name="interactive_threshold" 
                                            value="{{ $company['interactive_threshold'] ?? 0 }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Agent Assessment Configuration Section -->
                            <div class="form-section">
                                <h4 class="section-title"><i class="bi bi-person-badge icon-title"></i>Agent Assessment Configuration</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="call_outcomes" class="form-label">Call Outcomes:</label>
                                        <input type="text" class="form-control" id="call_outcomes" name="call_outcomes" 
                                            value="{{ isset($company) && !empty($company['call_outcomes']) ? implode(',', $company['call_outcomes']) : 'resolved,unresolved,follow_up,escalated' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="agent_assessments_configs" class="form-label">Agent Assessments:</label>
                                        <input type="text" class="form-control" id="agent_assessments_configs" name="agent_assessments_configs" 
                                            value="{{ isset($company) ? implode(',', $company['agent_assessments_configs'] ?? []) : 'cooperation,communication,problem_solving,technical_knowledge,efficiency' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="agent_cooperation_configs" class="form-label">Agent Cooperation:</label>
                                        <input type="text" class="form-control" id="agent_cooperation_configs" name="agent_cooperation_configs" 
                                            value="{{ isset($company) ? implode(',', $company['agent_cooperation_configs'] ?? []) : 'agent_proactive_assistance,agent_responsiveness,agent_emphasis,effectiveness' }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="agent_performance_configs" class="form-label">Agent Performance:</label>
                                        <input type="text" class="form-control" id="agent_performance_configs" name="agent_performance_configs" 
                                            value="{{ isset($company) ? implode(',', $company['agent_performance_configs'] ?? []) : 'customer_satisfaction,professionalism,tone_consistency,polite_language_usage,configured_standards_compliance' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Classification Settings Section -->
                            <div class="form-section">
                                <h4 class="section-title"><i class="bi bi-sliders icon-title"></i>Classification Settings</h4>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="delay_classes_medium" class="form-label">Delay Class - Medium (seconds):</label>
                                        <input type="number" step="0.1" class="form-control" id="delay_classes_medium" name="delay_classes_medium" 
                                            value="{{ $company->delay_classes_medium ?? 2.4 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="delay_classes_short" class="form-label">Delay Class - Short (seconds):</label>
                                        <input type="number" step="0.1" class="form-control" id="delay_classes_short" name="delay_classes_short" 
                                            value="{{ $company->delay_classes_short ?? 1.2 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="pause_classes_medium" class="form-label">Pause Class - Medium (seconds):</label>
                                        <input type="number" step="0.1" class="form-control" id="pause_classes_medium" name="pause_classes_medium" 
                                            value="{{ $company->pause_classes_medium ?? 2.4 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="pause_classes_short" class="form-label">Pause Class - Short (seconds):</label>
                                        <input type="number" step="0.1" class="form-control" id="pause_classes_short" name="pause_classes_short" 
                                            value="{{ $company->pause_classes_short ?? 1.2 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="common_words_threshold" class="form-label">Common Words Threshold:</label>
                                        <input type="number" step="0.1" class="form-control" id="common_words_threshold" name="common_words_threshold" 
                                            value="{{ $company['common_words_threshold'] ?? 0 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="llm_api_limit" class="form-label">LLM API Limit:</label>
                                        <input type="number" class="form-control" id="llm_api_limit" name="llm_api_limit" 
                                            value="{{ $company['llm_api_limit'] ?? 100 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="transcription_api_limit" class="form-label">Transcription API Limit:</label>
                                        <input type="number" class="form-control" id="transcription_api_limit" name="transcription_api_limit" 
                                            value="{{ $company['transcription_api_limit'] ?? 100 }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="transcription_api_rate" class="form-label">Transcription API Rate:</label>
                                        <input type="number" step="0.001" class="form-control" id="transcription_api_rate" name="transcription_api_rate" 
                                            value="{{ $company['transcription_api_rate'] ?? 0.025 }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-between mt-4">
                                <a class="btn btn-primary" href="{{ route('user.support') }}"> Need any help?</a>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-save me-2"></i>Update Company
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.tagifyInstances = {};
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Tagify for tag inputs
        const tagInputs = [
            'filler_words', 'main_topics', 'call_types',
            'call_outcomes', 'agent_assessments_configs', 
            'agent_cooperation_configs', 'agent_performance_configs',
            'restricted_phrases', 'source'
        ];
        
        tagInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                const config = {
                    duplicates: false,
                    dropdown: { 
                        enabled: 0,
                        closeOnSelect: true
                    }
                };

                // Add whitelist for source field
                if (id === 'source') {
                    config.whitelist = [
                        'API', 'Avaya', 'Genesys', 'FB', 'LinkedIn', 
                        'Instagram', 'TikTok', 'Snapchat', 
                        'X (Twitter)', 'WhatsApp', 'Email'
                    ];
                    config.enforceWhitelist = false; // Allow custom inputs
                }

                window.tagifyInstances[id] = new Tagify(input, config);
            }
        });

        // FAQ Management
        const faqContainer = document.getElementById('faq-container');
        const addFaqBtn = document.getElementById('add-faq-btn');
        let faqCount = 0;

        function createFaqPair(question = '', answer = '') {
            const div = document.createElement('div');
            div.className = 'faq-pair p-3 border rounded mb-2 bg-light position-relative';
            div.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-faq-btn prompt-field" disabled>
                    <i class="bi bi-trash"></i>
                </button>
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label fs-7 mb-1">Question</label>
                        <input type="text" name="faq[${faqCount}][question]" value="${question}" class="form-control form-control-sm mb-2 prompt-field" placeholder="Question" required readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label fs-7 mb-1">Answer</label>
                        <textarea name="faq[${faqCount}][answer]" class="form-control form-control-sm prompt-field" placeholder="Answer" rows="2" required readonly>${answer}</textarea>
                    </div>
                </div>
            `;
            
            div.querySelector('.remove-faq-btn').addEventListener('click', () => div.remove());
            faqContainer.appendChild(div);
            faqCount++;
        }

        // Add initial FAQs if they exist
        @if(isset($company['faq']) && is_array($company['faq']) && count($company['faq']) > 0)
            @foreach($company['faq'] as $faq)
                createFaqPair("{{ $faq['question'] ?? '' }}", "{{ $faq['answer'] ?? '' }}");
            @endforeach
        @else
            // Add fake data if no FAQs
            createFaqPair("How do I reset my password?", "You can reset your password by clicking on the 'Forgot Password' link on the login page and following the instructions sent to your email.");
            createFaqPair("What are your support hours?", "Our support team is available Monday through Friday, from 9 AM to 6 PM EST.");
            createFaqPair("Do you offer international shipping?", "Yes, we ship to over 50 countries worldwide. Shipping rates and times vary by location.");
        @endif

        if (addFaqBtn) addFaqBtn.addEventListener('click', () => createFaqPair());

        // Form submission handler
        const form = document.getElementById('companyRegistrationForm');
        if (!form) {
            console.error('Form not found');
            return;
        }

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            event.stopPropagation();
            

            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) {
                console.error('Submit button not found');
                return;
            }

            try {
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

                // Prepare FormData
                const formData = new FormData(form);
                
                // Submit to Laravel endpoint
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to submit form');
                }
                setTimeout(() => {
                    window.location.href = "{{ route('user.company.list') }}";
                }, 1000);
                showAlert('Company Updated successfully!', 'success');
                
            } catch (error) {
                console.error('Submission error:', error);
                showAlert(`Error: ${error.message}`, 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save me-2"></i>Update Company';
            }
        });


        // Show Bootstrap alert
        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.alert');
            if (existingAlert) existingAlert.remove();

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            form.parentNode.insertBefore(alertDiv, form);
            setTimeout(() => alertDiv.remove(), 50000);
        }
    });

    async function unlockPrompts() {
        console.log('Unlock requested');
        const { value: phrase } = await Swal.fire({
            title: 'Security Verification',
            text: 'It is highly dangerous to modify prompt configurations. Please type "I KNOW ITS CRITICAL" to proceed.',
            input: 'text',
            inputPlaceholder: 'Type here...',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Unlock',
            inputValidator: (value) => {
                if (value.trim().toUpperCase() !== 'I KNOW ITS CRITICAL') {
                    return 'Phrase does not match! Please type exactly: I KNOW ITS CRITICAL';
                }
            }
        });

        if (phrase && phrase.trim().toUpperCase() === 'I KNOW ITS CRITICAL') {
            console.log('Unlock successful');
            const overlay = document.getElementById('prompt-lock-overlay');
            if (overlay) {
                overlay.remove(); // Completely remove the overlay
            }
            
            document.querySelectorAll('.prompt-field').forEach(field => {
                field.removeAttribute('readonly');
                field.removeAttribute('disabled');
                field.style.backgroundColor = '#fff'; // Ensure it looks editable
                
                // Unlock Tagify if this field has an instance
                if (window.tagifyInstances && window.tagifyInstances[field.id]) {
                    window.tagifyInstances[field.id].setReadOnly(false);
                }
            });

            Swal.fire({
                icon: 'success',
                title: 'Unlocked',
                text: 'You can now modify the prompt configurations.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }
</script>
@endpush