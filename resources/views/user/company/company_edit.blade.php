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
        
        .animate-fade {
            animation: fadeIn 0.3s ease-out forwards;
        }
        
        .animate-highlight {
            animation: highlightPulse 1.5s ease-out;
        }
        
        @keyframes highlightPulse {
            0% { background-color: rgba(10, 102, 194, 0.05); }
            100% { background-color: transparent; }
        }
        
        .fs-7 { font-size: 0.875rem; }
        .fs-8 { font-size: 0.75rem; }
        .italic { font-style: italic; }
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
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-white shadow-sm rounded-3 p-3 me-3">
                                    <i class="fas fa-building-circle-arrow-right text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0 text-dark fw-semibold">Company Settings</h3>
                                    <p class="text-muted mb-0 fs-7">Update your company settings with our premium service</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#dataExtractionModal">
                                <i class="bi bi-cpu me-2"></i>AI Extraction Intelligence 
                                @php $totalRules = 0; if(!empty($company->data_extraction_config)) { foreach($company->data_extraction_config as $g) { $totalRules += count($g['extractions'] ?? []); } } @endphp
                                @if($totalRules > 0)
                                    <span class="badge bg-white text-dark ms-1" style="font-size: 0.65rem;">{{ $totalRules }} Rules</span>
                                @endif
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form id="companyRegistrationForm" method="POST" action="{{ route('user.company.update', $company['id'])}}" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="data_extraction_config" id="data_extraction_config_input" value="{{ json_encode($company->data_extraction_config ?? []) }}">
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
                                    <div class="col-md-6">
                                        <label for="restricted_phrases" class="form-label">Restricted Phrases:</label>
                                        <input type="text" class="form-control" id="restricted_phrases" name="restricted_phrases" 
                                            value="{{ isset($company) && !empty($company['restricted_phrases']) ? implode(',', $company['restricted_phrases']) : '' }}" 
                                            placeholder="Phrases agents should avoid...">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="call_types" class="form-label">Call Types:</label>
                                        <input type="text" class="form-control" id="call_types" name="call_types" 
                                            value="{{ isset($company) && !empty($company['call_types']) ? (is_array($company['call_types']) ? implode(',', $company['call_types']) : $company['call_types']) : '' }}" placeholder="Type and press enter to add">
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <label for="source" class="form-label">Integration Sources:</label>
                                        @php
                                            $currentSources = is_array($company['source'] ?? []) ? ($company['source'] ?? []) : (isset($company['source']) ? [$company['source']] : []);
                                        @endphp
                                        <input type="text" class="form-control" id="source" name="source" 
                                            value="{{ implode(',', $currentSources) }}" 
                                            placeholder="Select integration sources">
                                        <small class="text-muted">Choose from available integration sources.</small>
                                    </div> --}}
                                    <div class="col-12">
                                        <label for="company_overview" class="form-label">Company Overview:</label>
                                        <textarea class="form-control prompt-field" id="company_overview" name="company_overview" placeholder="Provide a brief overview of the company" rows="3" >{{ !empty($company['company_overview']) ? $company['company_overview'] : 'A leading provider of cloud-based enterprise solutions, specializing in AI-driven customer support and technical infrastructure management worldwide.' }}</textarea>
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
                                            <label for="company_risks" class="form-label">Company Risks :</label>
                                            <textarea class="form-control prompt-field" id="company_risks" name="company_risks" placeholder="Enter risks to flag, one per line..." rows="6" readonly>{{ isset($company) ? implode("\n", $company['company_risks'] ?? [
"1. العميل غاضب جداً ويهدد بتقديم شكوى رسمية.",
"2. العميل يطلب استرداد مبلغ مالي غير مستحق.",
"3. العميل يحاول الحصول على معلومات تخص عميل آخر.",
"4. العميل يتحدث بلغة غير لائقة أو مسيئة."
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
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                                        <i class="bi bi-save me-2"></i>Update Company
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Minimalist Data Extraction Modal -->
<div class="modal fade" id="dataExtractionModal" tabindex="-1" aria-labelledby="dataExtractionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center text-white">
                    <i class="bi bi-cpu-fill me-2 fs-5"></i>
                    <h6 class="modal-title mb-0 fw-bold text-black">AI Extraction Intelligence</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0 bg-light" style="max-height: 70vh; overflow-y: auto;">
                <div class="p-4">
                    <div id="extraction-configs-container" class="d-flex flex-column gap-4">
                        <!-- Rule sets will appear as premium cards here -->
                    </div>
                </div>
            </div>
            
            <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted italic">Changes are saved immediately to the database.</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary px-4 fw-bold rounded-pill shadow-sm" id="save-extraction-configs">
                        Save Config
                    </button>
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
        // Pass company agents to JS
        const companyAgents = @json($companyAgents->map(fn($a) => ['id' => $a->id, 'value' => $a->name, 'email' => $a->email]));
        let extractionConfigs = @json($company->data_extraction_config ?? []);

        // Initial hidden input for the config
        const configInput = document.getElementById('data_extraction_config_input');

        const extractionContainer = document.getElementById('extraction-configs-container');
        let groupCounter = 0;

        function createExtractionRule(groupId, rule = {type: 'string', description: ''}) {
            const ruleId = Math.random().toString(36).substr(2, 9);
            const div = document.createElement('div');
            div.className = 'extraction-rule d-flex gap-2 mb-2 animate-fade';
            
            div.innerHTML = `
                <div class="bg-light rounded-3 p-2 flex-grow-1 border">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm border-0 bg-transparent text-primary fw-bold rule-type">
                                <option value="string" ${rule.type === 'string' ? 'selected' : ''}>Text</option>
                                <option value="integer" ${rule.type === 'integer' ? 'selected' : ''}>Number</option>
                                <option value="boolean" ${rule.type === 'boolean' ? 'selected' : ''}>Yes/No</option>
                                <option value="date" ${rule.type === 'date' ? 'selected' : ''}>Date</option>
                                <option value="json" ${rule.type === 'json' ? 'selected' : ''}>JSON</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control form-control-sm border-0 bg-transparent rule-description" placeholder="Description..." value="${rule.description}">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm text-danger remove-rule-btn px-2">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            `;

            div.querySelector('.remove-rule-btn').addEventListener('click', () => {
                div.style.opacity = '0';
                setTimeout(() => div.remove(), 200);
            });
            
            return div;
        }

        function createExtractionGroup(groupData = {agent_ids: [], extractions: []}) {
            const groupIdx = ++groupCounter;
            const groupId = `group_${groupIdx}`;
            const div = document.createElement('div');
            div.className = 'extraction-group bg-white border-0 rounded-4 shadow-sm overflow-hidden animate-fade';
            
            div.innerHTML = `
                <div class="p-3 border-bottom bg-white">
                    <div class="w-100">
                        <label class="form-label fs-8 text-uppercase fw-bold text-muted mb-1">Assign to Agents</label>
                        <div class="w-100"><input class="agent-selector-input"></div>
                    </div>
                </div>
                <div class="p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold text-dark mb-0">Extraction Requirements</label>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold add-rule-btn">
                            <i class="bi bi-plus-lg me-1"></i>Add Rule
                        </button>
                    </div>
                    <div class="rules-container d-flex flex-column gap-2"></div>
                </div>
            `;

            const rulesContainer = div.querySelector('.rules-container');
            const addRuleBtn = div.querySelector('.add-rule-btn');
            const agentInput = div.querySelector('.agent-selector-input');

            const tagify = new Tagify(agentInput, {
                whitelist: companyAgents,
                enforceWhitelist: true,
                dropdown: { enabled: 0, maxItems: 5, mapValueTo: 'value', searchKeys: ['value'] }
            });

            if (groupData.agent_ids && groupData.agent_ids.length > 0) {
                const selectedAgents = companyAgents.filter(a => groupData.agent_ids.includes(a.id));
                tagify.addTags(selectedAgents);
            }

            div.tagify = tagify;

            addRuleBtn.addEventListener('click', () => rulesContainer.appendChild(createExtractionRule(groupId)));

            if (groupData.extractions && groupData.extractions.length > 0) {
                groupData.extractions.forEach(rule => rulesContainer.appendChild(createExtractionRule(groupId, rule)));
            } else {
                rulesContainer.appendChild(createExtractionRule(groupId));
            }

            extractionContainer.appendChild(div);
        }

        // Initialize from existing or create fresh
        if (extractionConfigs && extractionConfigs.length > 0) {
            extractionConfigs.forEach(group => createExtractionGroup(group));
        } else {
            createExtractionGroup();
        }

        document.getElementById('save-extraction-configs').addEventListener('click', async () => {
            const groups = [];
            document.querySelectorAll('.extraction-group').forEach(groupDiv => {
                const agentIds = groupDiv.tagify.value.map(tag => tag.id);
                const extractions = [];
                groupDiv.querySelectorAll('.extraction-rule').forEach(ruleDiv => {
                    const type = ruleDiv.querySelector('.rule-type').value;
                    const description = ruleDiv.querySelector('.rule-description').value.trim();
                    if (description) {
                        extractions.push({ type, description });
                    }
                });

                if (agentIds.length > 0 && extractions.length > 0) {
                    groups.push({ agent_ids: agentIds, extractions });
                }
            });

            // Update local hidden input for form persistence
            configInput.value = JSON.stringify(groups);
            
            const saveBtn = document.getElementById('save-extraction-configs');
            const originalHtml = saveBtn.innerHTML;
            
            try {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                // Direct AJAX save to the database
                const response = await fetch("{{ route('user.company.update', $company['id']) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        data_extraction_config: JSON.stringify(groups)
                    })
                });

                const result = await response.json();

                if (!response.ok) throw new Error(result.message || 'Failed to save configuration');

                Swal.fire({
                    icon: 'success',
                    title: 'Configuration Saved!',
                    text: 'AI Extraction rules have been updated successfully in the database.',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Update the badge on the main page
                const badge = document.querySelector('.btn-primary .badge');
                let totalRules = 0;
                groups.forEach(g => totalRules += g.extractions.length);

                if (totalRules > 0) {
                    if (badge) {
                        badge.textContent = `${totalRules} Rules`;
                    } else {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'badge bg-white text-primary ms-1';
                        newBadge.style.fontSize = '0.65rem';
                        newBadge.textContent = `${totalRules} Rules`;
                        document.querySelector('[data-bs-target="#dataExtractionModal"]').appendChild(newBadge);
                    }
                } else if (badge) {
                    badge.remove();
                }

                bootstrap.Modal.getInstance(document.getElementById('dataExtractionModal')).hide();

            } catch (error) {
                console.error('Save error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: error.message
                });
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalHtml;
            }
        });

        // Initialize Tagify for tag inputs
        const tagInputs = [
            'filler_words', 'main_topics', 'call_types',
            'call_outcomes', 'agent_assessments_configs', 
            'agent_cooperation_configs', 'agent_performance_configs',
            'restricted_phrases'/*, 'source'*/
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