@extends('user.layouts.app')

@php
    if (!function_exists('timeToSeconds')) {
        function timeToSeconds($time) {
            if (!$time) return 0;
            $parts = array_reverse(explode(':', $time));
            $seconds = 0;
            if (isset($parts[0])) $seconds += (int)$parts[0];
            if (isset($parts[1])) $seconds += (int)$parts[1] * 60;
            if (isset($parts[2])) $seconds += (int)$parts[2] * 3600;
            return $seconds;
        }
    }

    if (!function_exists('formatDuration')) {
        function formatDuration($startTime, $endTime) {
            if (!$startTime || !$endTime) {
                return '00:00';
            }
            $start = timeToSeconds($startTime);
            $end = timeToSeconds($endTime);
            $duration = max(0, $end - $start);
            return gmdate('i:s', $duration);
        }
    }
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/task_details.css') }}">
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            padding: 0.75rem 1.25rem;
        }

        .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            font-size: 12px;
            font-style: italic;
            cursor: help;
        }
    </style>

    <style>
/* Ensure accordion content stays visible */
.accordion-collapse {
    transition: height 0.35s ease !important;
}

.accordion-collapse.show {
    visibility: visible !important;
    height: auto !important;
}

/* Prevent any hiding animations */
.accordion-button:not(.collapsed)::after {
    transform: rotate(-180deg) !important;
}

/* Fix for Bootstrap 5 accordion */
.collapse:not(.show) {
    display: none;
}

.collapse.show {
    display: block;
}
</style>
@endpush

@section('content')
    <div class="container-fluid py-4 professional-theme">
        
        <!-- Header Section -->
        <div class="row mb-4 align-items-center animate-fade">
            <div class="col-md-8">
                <h2 class="mb-1 text-dark fw-bold">Task Details <small class="text-muted fs-6 ms-2">#{{ $workId }}</small></h2>
                <div class="d-flex align-items-center">
                    <span class="badge {{ $status === 'evaluated' ? 'bg-success' : ($status === 'transcribed' ? 'bg-info' : 'bg-warning') }} me-2">
                        {{ ucfirst($status) }}
                    </span>
                    @if(isset($data['created_at']))
                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ date('M d, Y H:i', strtotime($data['created_at'])) }}</small>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('user.company.evaluate', $workId) }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                    <i class="fas fa-robot me-2"></i> Run AI Analysis
                </a>
            </div>
        </div>

        <div class="row gy-4">
            @php
                // Resolve policy_compliance with fallbacks:
                // 1. gpt_evaluation.policy_compliance (standard location)
                // 2. policy_compliance at root of data (merged during evaluation)
                // 3. empty array (not yet evaluated or no policies configured)
                $policyCompliance = $data['gpt_evaluation']['policy_compliance']
                    ?? $data['policy_compliance']
                    ?? [];

                // If GPT evaluation has notebook results, use them
                if (isset($data['gpt_evaluation']['notebook_analysis'])) {
                    $data['analysis_alignment_result_notebook'] = $data['gpt_evaluation']['notebook_analysis'];
                }

                // Initialize sentiment counters
                $agentSentiments = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];
                $customerSentiments = ['Positive' => 0, 'Neutral' => 0, 'Negative' => 0];

                // Calculate agent sentiments
                if (isset($data['agent_speakers_transcriptions'])) {
                    foreach ($data['agent_speakers_transcriptions'] as $transcript) {
                        $sentiment = $transcript['sentiment'] ?? 'Neutral';
                        $agentSentiments[$sentiment]++;
                    }
                }

                // Calculate customer sentiments
                if (isset($data['customer_speakers_transcriptions'])) {
                    foreach ($data['customer_speakers_transcriptions'] as $transcript) {
                        $sentiment = $transcript['sentiment'] ?? 'Neutral';
                        $customerSentiments[$sentiment]++;
                    }
                }

                // Calculate totals
                $totalAgent = array_sum($agentSentiments);
                $totalCustomer = array_sum($customerSentiments);
                $total = $totalAgent + $totalCustomer;

                // Calculate percentages
                $positivePercent =
                    $total > 0
                        ? round((($agentSentiments['Positive'] + $customerSentiments['Positive']) / $total) * 100)
                        : 0;
                $neutralPercent =
                    $total > 0
                        ? round((($agentSentiments['Neutral'] + $customerSentiments['Neutral']) / $total) * 100)
                        : 0;
                $negativePercent =
                    $total > 0
                        ? round((($agentSentiments['Negative'] + $customerSentiments['Negative']) / $total) * 100)
                        : 0;

                // Set default values if not present in data
                $agentPace = round($data['pace']['agent_pace'] ?? 0);
                $customerPace = round($data['pace']['customer_pace'] ?? 0);
                $lowLoudness = $data['speaker_loudness']['agent']['lower_loudness_percentage'] ?? 0;
                $optimalLoudness = $data['speaker_loudness']['agent']['optimal_loudness_percentage'] ?? 0;
                $highLoudness = $data['speaker_loudness']['agent']['upper_loudness_percentage'] ?? 0;
            @endphp
            <div class="col-lg-3 animate-fade">
                <div class="card h-100 ">
                    <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <h6 class="card-title mb-0">Sentiment Analysis</h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Emotional tone measurement">i</div>
                    </div>
                    <div class="card-body">
                        <div style="height: 150px;">
                            <canvas id="sentimentChart" height="150"></canvas>
                        </div>
                        <div class="mt-1 text-center">
                            <span class="badge mx-1" style="background-color: #8FA998 !important;">{{ $positivePercent }}% Positive</span>
                            <span class="badge mx-1" style="background-color: #9A8E85 !important;">{{ $neutralPercent }}% Neutral</span>
                            <span class="badge mx-1" style="background-color: #BF8F8F !important;">{{ $negativePercent }}% Negative</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Speech Rate Chart -->
            <div class="col-lg-3 animate-fade">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <h6 class="card-title mb-0">Speech Rate <small class="text-muted">(Optimal: 100-150 wpm)</small>
                        </h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Words per minute analysis">i</div>
                    </div>
                    <div class="card-body">
                        <div style="height: 150px;">
                            <canvas id="speechRateChart" height="150"></canvas>
                        </div>
                        <div class="mt-1 text-center">
                            <span class="badge mx-1" style="background-color: #8FA3A9 !important;">Agent: {{ $agentPace }} WPM</span>
                            <span class="badge mx-1" style="background-color: #8FA998 !important;">Customer: {{ $customerPace }} WPM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loudness Chart -->
            <div class="col-lg-3 animate-fade">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <h6 class="card-title mb-0">Voice Loudness</h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Volume distribution during call">i</div>
                    </div>
                    <div class="card-body">
                        <div style="height: 150px;">
                            <canvas id="loudnessChart" height="150"></canvas>
                        </div>
                        <div class="mt-1 text-center">
                            <span class="badge mx-1" style="background-color: #C9B178 !important;">Low: {{ number_format($lowLoudness,2) }}%</span>
                            <span class="badge mx-1" style="background-color: #8FA998 !important;">Optimal: {{ number_format($optimalLoudness,2) }}%</span>
                            <span class="badge mx-1" style="background-color: #BF8F8F !important;">High: {{ number_format($highLoudness,2) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 animate-fade">
                <div class="card h-100 evaluation-card">
                    <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <h6 class="card-title mb-0">Knowledge base Analysis</h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Distribution of question evaluations">i</div>
                    </div>
                    <div class="card-body">
                        <!-- Chart container -->
                        <div style="height: 150px; position: relative;">
                            <canvas id="evaluationChart" width="400" height="100"></canvas>
                        </div>

                        <!-- Horizontal evaluation row -->
                        <small>Questions analyzed, Check the chart above for details.</small>
                    </div>
                </div>
            </div>
            
            <!-- Interaction Insights Section -->
            <div class="col-12 animate-fade">
                <div class="card shadow-sm border-0 rounded-4" style="background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);">
                    <div class="card-body py-4">
                        <div class="row text-center g-4">
                            <div class="col-md-2 col-6 border-end">
                                <i class="bi bi-clock text-primary fs-4 mb-2 d-block"></i>
                                <h4 class="fw-bold mb-1 text-dark">{{ $data['call_duration']['call_duration'] ?? '00:00' }}</h4>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Duration</div>
                            </div>
                            <div class="col-md-2 col-6 border-end">
                                <i class="bi bi-mic text-success fs-4 mb-2 d-block"></i>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $data['pause_delay_information']['talking_duration']['agent'] ?? '00:00' }} / {{ $data['pause_delay_information']['talking_duration']['customer'] ?? '00:00' }}</h4>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Talking (A/C)</div>
                            </div>
                            <div class="col-md-2 col-6 border-end">
                                <i class="bi bi-chat-dots text-secondary fs-4 mb-2 d-block"></i>
                                <h4 class="fw-bold mb-1 text-dark">{{ $data['advanced_metrics']['dialogue_turns'] ?? 0 }}</h4>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Dialogue Turns</div>
                            </div>
                            <div class="col-md-2 col-6 border-end">
                                <i class="bi bi-lightning-charge text-warning fs-4 mb-2 d-block"></i>
                                <h4 class="fw-bold mb-1 text-dark">{{ $data['pause_delay_information']['average_latency'] ?? '0s' }}</h4>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Avg Latency</div>
                            </div>
                            <div class="col-md-2 col-6 border-end">
                                <i class="bi bi-hourglass-split text-info fs-4 mb-2 d-block"></i>
                                <h4 class="fw-bold mb-1 text-dark">{{ $data['pause_delay_information']['silence_duration'] ?? '00:00' }}</h4>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Silence Time</div>
                            </div>
                            <div class="col-md-2 col-6">
                                <i class="bi bi-speedometer2 text-success fs-4 mb-2 d-block"></i>
                                <h4 class="fw-bold mb-1 text-dark">{{ isset($data['advanced_metrics']['total_call_duration']) && $data['advanced_metrics']['dialogue_turns'] > 0 ? number_format($data['advanced_metrics']['total_call_duration'] / $data['advanced_metrics']['dialogue_turns'], 1) : 0 }}s</h4>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Avg Turn duration</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="agent-performance-dashboard">
                    <!-- Main Header with Overall Scores -->
                    <div class="dashboard-header">
                        <div class="agent-profile">
                            <div class="avatar">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="agent-info">
                                <h3>Agent Performance</h3>
                                <div class="overall-score">
                                    <div class="score-circle"
                                        style="--percentage: {{ $data['agent_professionalism']['total_score']['percentage'] ?? 0 }}%">
                                        <span>{{ $data['agent_professionalism']['total_score']['percentage'] ?? 0 }}%</span>
                                    </div>
                                    <div class="score-details">
                                        <div class="score-item">
                                            <span class="label">Professionalism</span>
                                            <span
                                                class="value">{{ $data['agent_professionalism']['total_score']['percentage'] ?? 0 }}%</span>
                                        </div>
                                        <div class="score-item">
                                            <span class="label">Assessment</span>
                                            <span
                                                class="value">{{ $data['agent_assessment']['total_score']['percentage'] ?? 0 }}%</span>
                                        </div>
                                        <div class="score-item">
                                            <span class="label">Cooperation</span>
                                            <span
                                                class="value">{{ $data['agent_cooperation']['total_score']['percentage'] ?? 0 }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Speech Characteristics -->
                        <div class="speech-analysis">
                            <h4>Speech Analysis</h4>
                            <div class="speech-metrics">
                                <div class="metric">
                                    <i class="fas fa-volume-up"></i>
                                    <span>{{ ucfirst($data['agent_professionalism']['speech_characteristics']['volume']['loudness_class'] ?? '') }}
                                        Volume</span>
                                    <div class="progress-bar">
                                        @php
                                            $volPercent = $data['agent_professionalism']['speech_characteristics']['volume']['optimal_loudness_percentage'] ?? 0;
                                        @endphp
                                        <div class="progress"
                                            style="width: {{ $volPercent }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="metric">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>{{ round($data['agent_professionalism']['speech_characteristics']['speed'] ?? 0) }}
                                        WPM</span>
                                    <div class="progress-bar">
                                        <div class="progress" style="width: 80%"></div>
                                        <!-- Adjust based on your ideal speed range -->
                                    </div>
                                </div>
                                <div class="metric">
                                    <i class="fas fa-pause"></i>
                                    <span>{{ $data['agent_professionalism']['speech_characteristics']['pauses'] ?? 0 }}
                                        Pauses</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="tabs">
                        <button class="tab-btn active" data-tab="professionalism">
                            <i class="fas fa-award"></i> Professionalism
                        </button>
                        <button class="tab-btn" data-tab="assessment">
                            <i class="fas fa-clipboard-check"></i> Skills Assessment
                        </button>
                        <button class="tab-btn" data-tab="cooperation">
                            <i class="fas fa-handshake"></i> Cooperation
                        </button>
                        <button class="tab-btn" data-tab="linguistic">
                            <i class="fas fa-language"></i> Linguistic Analysis
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="tab-content">
                        <!-- Professionalism Tab -->
                        <div class="tab-pane active" id="professionalism">
                            <div class="metrics-grid">
                                @foreach (['customer_satisfaction', 'professionalism', 'tone_consistency', 'polite_language_usage', 'configured_standards_compliance'] as $metric)
                                    <div class="metric-card">
                                        <div class="metric-score">
                                            {{ $data['agent_professionalism'][$metric]['score'] ?? 0 }}/10</div>
                                        <h4>{{ ucfirst(str_replace('_', ' ', $metric)) }}</h4>
                                        <div class="metric-details">
                                            <p><strong>Evidence:</strong>
                                                "{{ $data['agent_professionalism'][$metric]['evidence'] ?? '' }}"</p>
                                            <p><strong>Reasoning:</strong>
                                                {{ $data['agent_professionalism'][$metric]['reasoning'] ?? '' }}</p>
                                            <p><strong>Determination:</strong>
                                                {{ $data['agent_professionalism'][$metric]['determination'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="metric-card">
                                    <div class="performance-summary">
                                        <div class="summary-header">
                                            <i
                                                class="fas fa-{{ ($data['agent_professionalism']['total_score']['percentage'] ?? 0) > 80 ? 'trophy' : 'check-circle' }}"></i>
                                            <h3>{{ ($data['agent_professionalism']['total_score']['percentage'] ?? 0) > 80 ? 'Excellent Performance' : 'Good Performance' }}
                                            </h3>
                                        </div>
                                        <div class="summary-content">
                                            <p>{{ $data['agent_professionalism']['customer_satisfaction']['reasoning'] ?? '' }}
                                            </p>
                                            <div class="summary-stats">
                                                <div class="stat">
                                                    <span>Total Score:</span>
                                                    <span>{{ $data['agent_professionalism']['total_score']['score'] ?? 0 }}/{{ $data['agent_professionalism']['total_score']['max_score'] ?? 0 }}</span>
                                                </div>
                                                <div class="stat">
                                                    <span>Customer Satisfaction:</span>
                                                    <span>{{ $data['agent_professionalism']['customer_satisfaction']['score'] ?? 0 }}/10</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Assessment Tab -->
                        <div class="tab-pane" id="assessment">
                            <div class="metrics-grid">
                                @foreach (['communication', 'problem_solving', 'technical_knowledge', 'efficiency'] as $metric)
                                    <div class="metric-card">
                                        <div class="metric-score">{{ $data['agent_assessment'][$metric]['score'] ?? 0 }}/10
                                        </div>
                                        <h4>{{ ucfirst(str_replace('_', ' ', $metric)) }}</h4>
                                        <div class="metric-details">
                                            <p><strong>Evidence:</strong>
                                                "{{ $data['agent_assessment'][$metric]['evidence'] ?? '' }}"</p>
                                            <p><strong>Reasoning:</strong>
                                                {{ $data['agent_assessment'][$metric]['reasoning'] ?? '' }}</p>
                                            <p><strong>Determination:</strong>
                                                {{ $data['agent_assessment'][$metric]['determination'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Cooperation Tab -->
                        <div class="tab-pane" id="cooperation">
                            <div class="metrics-grid">
                                @foreach (['agent_proactive_assistance', 'agent_responsiveness', 'agent_empathy', 'effectiveness'] as $metric)
                                    <div class="metric-card">
                                        <div class="metric-score">{{ $data['agent_cooperation'][$metric]['score'] ?? 0 }}/10
                                        </div>
                                        <h4>{{ ucfirst(str_replace('agent_', '', str_replace('_', ' ', $metric))) }}</h4>
                                        <div class="metric-details">
                                            <p><strong>Evidence:</strong>
                                                "{{ $data['agent_cooperation'][$metric]['evidence'] ?? 'N/A' }}"</p>
                                            <p><strong>Reasoning:</strong>
                                                {{ $data['agent_cooperation'][$metric]['reasoning'] ?? '' }}</p>
                                            <p><strong>Determination:</strong>
                                                {{ $data['agent_cooperation'][$metric]['determination'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Linguistic Analysis Tab -->
                        <div class="tab-pane" id="linguistic">
                            <div class="container-fluid py-3">

                                <!-- Tone Analysis -->
                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div
                                        class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            🎭 Tone Analysis
                                        </h6>
                                        <span class="text-muted small">Speech Characteristics</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Friendly -->
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold">Friendly</span>
                                                    <span class="text-muted small">
                                                        {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['friendly'] ?? 0 }}%
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success rounded-pill" role="progressbar"
                                                        style="width: {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['friendly'] ?? 0 }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Confident -->
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold">Confident</span>
                                                    <span class="text-muted small">
                                                        {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['confident'] ?? 0 }}%
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-primary rounded-pill" role="progressbar"
                                                        style="width: {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['confident'] ?? 0 }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Empathetic -->
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold">Empathetic</span>
                                                    <span class="text-muted small">
                                                        {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['empathetic'] ?? 0 }}%
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-warning rounded-pill" role="progressbar"
                                                        style="width: {{ $data['agent_professionalism']['speech_characteristics']['tone_analysis']['empathetic'] ?? 0 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Language Usage -->
                                <div class="card shadow-sm border-0 rounded-3">
                                    <div
                                        class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            📝 Language Usage
                                        </h6>
                                        <span class="text-muted small">Linguistic Analysis</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center g-3">
                                            <!-- Formal Language -->
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded-3 shadow-sm h-100">
                                                    <div class="fw-semibold text-dark">Formal Language</div>
                                                    <div class="fs-5 fw-bold text-primary">
                                                        {{ $data['agent_professionalism']['linguistic_analysis']['formal_language_percentage'] ?? 0 }}%
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Polite Phrases -->
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded-3 shadow-sm h-100">
                                                    <div class="fw-semibold text-dark">Polite Phrases</div>
                                                    <div class="fs-5 fw-bold text-success">
                                                        {{ $data['agent_professionalism']['polite_language_usage']['score'] ?? 0 }}/10
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <style>
                            #linguistic .progress-bar {
                                transition: width 0.6s ease;
                            }
                        </style>

                    </div>
                </div>
                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            🔠 Most Frequent Words
                        </h6>
                        <div class="info-icon text-secondary" data-bs-toggle="tooltip"
                            title="Top words used during conversation">i</div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Agent Words -->
                            <div class="col-6">
                                <div class="speaker-card p-3 rounded-3 bg-light h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-headset text-primary me-2 fs-5"></i>
                                        <span class="fw-semibold text-dark">Agent</span>
                                    </div>
                                    <div class="word-cloud">
                                        @php $agentWords = array_slice($data['most_common_words']['agent'] ?? [], 0, 15); @endphp
                                        @forelse ($agentWords as $word)
                                            <span class="word-tag">
                                                {{ $word['word'] }}
                                                <span class="frequency-badge">{{ $word['frequency'] }}</span>
                                            </span>
                                        @empty
                                            <div class="text-center py-3 opacity-50">
                                                <small class="text-muted italic">No words detected</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Words -->
                            <div class="col-6">
                                <div class="speaker-card p-3 rounded-3 bg-light h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person text-success me-2 fs-5"></i>
                                        <span class="fw-semibold text-dark">Customer</span>
                                    </div>
                                    <div class="word-cloud">
                                        @php $customerWords = array_slice($data['most_common_words']['customer'] ?? [], 0, 15); @endphp
                                        @forelse ($customerWords as $word)
                                            <span class="word-tag">
                                                {{ $word['word'] }}
                                                <span class="frequency-badge">{{ $word['frequency'] }}</span>
                                            </span>
                                        @empty
                                            <div class="text-center py-3 opacity-50">
                                                <small class="text-muted italic">No words detected</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Right Column - Call Details -->
            <div class="col-lg-5">
                <div class="d-flex flex-column h-100">
                    <div class="card shadow-sm border-0 rounded-4 animate-fade delay-2">

                        <div
                            class="card-header d-flex justify-content-between align-items-center p-2 bg-white border-bottom">
                            <h6 class="card-title mb-0 fw-bold text-dark">📊 Call Summary</h6>
                            <div class="info-icon" data-bs-toggle="tooltip" title="Automatically generated call summary">
                                i</div>
                        </div>

                        <div class="card-body">

                            <!-- Audio Player -->
                            <div class="p-3 mb-3 rounded-3 bg-light">
                                <!-- Hidden audio element -->
                                <audio id="audioPlayer"
                                    @if(!empty($data['customer_agent_audio_s3_url'])) src="{{ $data['customer_agent_audio_s3_url'] }}" @endif
                                    preload="metadata"
                                    onerror="console.error('Audio failed to load:', this.error, this.src)">
                                </audio>

                                <div class="d-flex justify-content-center gap-3 mb-2">
                                    <button class="btn btn-outline-secondary btn-sm rounded-circle" title="Skip Backward"
                                        onclick="skip(-10)">
                                        <i class="fas fa-step-backward"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm rounded-circle" id="playButton" title="Play"
                                        onclick="togglePlay()">
                                        <i class="fas fa-play" id="playIcon"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm rounded-circle" title="Skip Forward"
                                        onclick="skip(10)">
                                        <i class="fas fa-step-forward"></i>
                                    </button>
                                    @if (isset($data['customer_agent_audio_s3_url']))
                                        <a href="{{ $data['customer_agent_audio_s3_url'] }}"
                                            class="btn btn-outline-success btn-sm rounded-circle" title="Download"
                                            download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>

                                <!-- Progress Bar (click to seek) -->
                                <div class="progress mb-2" id="progressBarContainer" style="height: 6px; border-radius: 10px; cursor: pointer;">
                                    <div class="progress-bar bg-primary" id="progressBar"></div>
                                </div>

                                <!-- Time Display -->
                                <div class="text-center small text-muted">
                                    <span id="currentTime">0:00</span> /
                                    <span id="duration">{{ $data['call_duration']['call_duration'] ?? '0:00' }}</span>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-2">Detailed Summary</h6>
                                <div class="p-3 bg-light rounded-3 small text-black">
                                    {{ $data['transcription_summaries']['detail'] ?? 'No detailed summary available' }}
                                </div>
                            </div>

                            <!-- Key Points -->
                            <!-- Call Outcome only -->
                            @if(isset($data['call_outcome']))
                                <div class="mt-2">
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="fas fa-ticket-alt text-primary me-2"></i> Call Outcome
                                    </h6>
                                    <div class="ps-2">
                                        <span class="text-secondary ms-1">
                                            {{ is_array($data['call_outcome']) ? implode('، ', $data['call_outcome']) : $data['call_outcome'] }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4 animate-fade delay-3" style="overflow: hidden;">
                        <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-800 text-dark" style="font-size: 0.9rem;">
                                    <i class="bi bi-chat-left-dots text-primary me-2"></i>Full Conversation
                                </h6>
                            </div>
                        </div>

                        {{-- Search bar --}}
                        <div class="px-3 pb-1">
                            <div class="input-group input-group-sm bg-light rounded-pill px-2 border-0" style="border: 1px solid #f1f5f9 !important;">
                                <span class="input-group-text bg-transparent border-0 text-muted" style="padding: 0 5px;"><i class="bi bi-search" style="font-size: 11px;"></i></span>
                                <input type="text" id="transSearch" class="form-control bg-transparent border-0 py-1" placeholder="Search..." style="font-size: 12px; box-shadow: none; height: 28px;">
                            </div>
                        </div>

                        <div class="card-body p-0">
                            {{-- Transcription Body --}}
                            <div class="transcription-wrap px-3 py-1" id="transcription-body" style="max-height: 500px; overflow-y: auto;">
                                @php
                                    $allTurns = [];
                                    // Try different possible keys for conversation data
                                    if (isset($data['speakers_transcriptions']) && !empty($data['speakers_transcriptions'])) {
                                        $allTurns = $data['speakers_transcriptions'];
                                    } elseif (isset($data['conversation']) && !empty($data['conversation'])) {
                                        $allTurns = array_map(function($t) {
                                            return [
                                                'speaker' => $t['speaker'] ?? 'Unknown',
                                                'transcript' => $t['text'] ?? ($t['transcript'] ?? ''),
                                                'start_time' => isset($t['start_time']) ? (is_numeric($t['start_time']) ? gmdate("i:s", (int)$t['start_time']) : $t['start_time']) : '00:00',
                                                'end_time' => isset($t['end_time']) ? (is_numeric($t['end_time']) ? gmdate("i:s", (int)$t['end_time']) : $t['end_time']) : '00:00',
                                                'sentiment' => $t['sentiment'] ?? 'Neutral'
                                            ];
                                        }, $data['conversation']);
                                    }
                                @endphp

                                @if(!empty($allTurns))
                                    @foreach($allTurns as $turn)
                                        <div class="transcript-turn mb-2" data-text="{{ strtolower($turn['transcript'] ?? '') }}">
                                            <div class="d-flex justify-content-between align-items-center mb-0">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold speaker-name" style="font-size: 0.85rem; color: #1e293b;">{{ $turn['speaker'] }}</span>
                                                    <span class="text-muted" style="font-size: 10px;">• {{ $turn['speaker'] }}</span>
                                                </div>
                                                <span class="time-range px-1 rounded text-primary fw-bold" style="font-size: 0.7rem; background: #eef2ff;">
                                                    {{ $turn['start_time'] }} - {{ $turn['end_time'] }}
                                                </span>
                                            </div>
                                            <div class="arabic-text mt-1" style="direction: rtl; text-align: right; font-family: 'Cairo', 'Tahoma', sans-serif; font-size: 0.95rem; line-height: 1.5; color: #334155;">
                                                {{ $turn['transcript'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <i class="bi bi-chat-slash fs-1 text-muted opacity-25"></i>
                                        <p class="text-muted mt-2">No transcription data available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-6 animate-fade">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center p-2 bg-white border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">📊 Sentiment Timeline</h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Detailed timeline of sentiment changes">i
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Add Visualization for Timeline -->
                        <div class="mb-4 p-3 bg-light rounded-3" style="height: 300px;">
                            <canvas id="sentimentTimelineChart"></canvas>
                        </div>

                        <div class="professional-card">

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-pills nav-fill mb-3 sentiment-tabs" id="sentimentTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab"
                                        data-bs-target="#all" type="button" role="tab">All</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agent"
                                        type="button" role="tab">Agent</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="customer-tab" data-bs-toggle="tab"
                                        data-bs-target="#customer" type="button" role="tab">Customer</button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content p-0">

                                <!-- All Tab -->
                                <div class="tab-pane fade show active" id="all" role="tabpanel"
                                    aria-labelledby="all-tab">
                                    <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0 sentiment-table">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>Speaker</th>
                                                    <th>Type</th>
                                                    <th>Duration</th>
                                                    <th>Intensity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data['speakers_transcriptions']))
                                                    @foreach ($data['speakers_transcriptions'] as $transcript)
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge rounded-pill 
                                                            @if (($transcript['speaker'] ?? '') == 'agent') bg-warning-subtle text-warning
                                                            @elseif(($transcript['speaker'] ?? '') == 'customer') bg-info-subtle text-info
                                                            @else bg-secondary-subtle text-secondary @endif px-3 py-1">
                                                                    {{ ucfirst($transcript['speaker'] ?? 'Unknown') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge rounded-pill 
                                                            @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success-subtle text-success
                                                            @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger-subtle text-danger
                                                            @else bg-warning-subtle text-warning @endif px-3 py-1">
                                                                    <i
                                                                        class="bi 
                                                                @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bi-emoji-smile
                                                                @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bi-emoji-frown
                                                                @else bi-dash-circle @endif me-1"></i>
                                                                    {{ $transcript['sentiment'] ?? 'Neutral' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{ formatDuration($transcript['start_time'] ?? null, $transcript['end_time'] ?? null) }}
                                                            </td>
                                                            <td>
                                                                <div class="progress sentiment-progress"
                                                                    style="height: 5px; border-radius: 6px;">
                                                                    @php
                                                                        $sentiment = $transcript['sentiment'] ?? 'Neutral';
                                                                        $intensity = ($sentiment == 'Positive' || $sentiment == 'Negative') ? 85 : 50;
                                                                    @endphp
                                                                    <div class="progress-bar 
                                                                @if ($sentiment == 'Positive') bg-success
                                                                @elseif($sentiment == 'Negative') bg-danger
                                                                @else bg-warning @endif"
                                                                        role="progressbar"
                                                                        style="width: {{ $intensity }}%; transition: width 0.6s ease;">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-4">
                                                            <i class="bi bi-info-circle me-2"></i>No sentiment data
                                                            available
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Agent Tab -->
                                <div class="tab-pane fade" id="agent" role="tabpanel" aria-labelledby="agent-tab">
                                    <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0 sentiment-table">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Duration</th>
                                                    <th>Intensity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data['speakers_transcriptions']))
                                                    @php $hasAgentData = false; @endphp
                                                    @foreach ($data['speakers_transcriptions'] as $transcript)
                                                        @if (($transcript['speaker'] ?? '') == 'agent')
                                                            @php $hasAgentData = true; @endphp
                                                            <tr>
                                                                <td>
                                                                    <span
                                                                        class="badge rounded-pill 
                                                                @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success-subtle text-success
                                                                @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger-subtle text-danger
                                                                @else bg-warning-subtle text-warning @endif px-3 py-1">
                                                                        <i
                                                                            class="bi 
                                                                    @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bi-emoji-smile
                                                                    @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bi-emoji-frown
                                                                    @else bi-dash-circle @endif me-1"></i>
                                                                        {{ $transcript['sentiment'] ?? 'Neutral' }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ formatDuration($transcript['start_time'] ?? null, $transcript['end_time'] ?? null) }}
                                                                </td>
                                                                <td>
                                                                    <div class="progress sentiment-progress"
                                                                        style="height: 5px; border-radius: 6px;">
                                                                        <div class="progress-bar 
                                                                    @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success
                                                                    @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger
                                                                    @else bg-warning @endif"
                                                                            role="progressbar"
                                                                                style="width: {{ ($transcript['sentiment'] ?? '') === 'Neutral' ? 50 : 85 }}%; transition: width 0.6s ease;">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    @if (!$hasAgentData)
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-4">
                                                                <i class="bi bi-info-circle me-2"></i>No agent data
                                                                available
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">
                                                            <i class="bi bi-info-circle me-2"></i>No sentiment data
                                                            available
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Customer Tab -->
                                <div class="tab-pane fade" id="customer" role="tabpanel"
                                    aria-labelledby="customer-tab">
                                    <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0 sentiment-table">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Duration</th>
                                                    <th>Intensity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data['speakers_transcriptions']))
                                                    @php $hasCustomerData = false; @endphp
                                                    @foreach ($data['speakers_transcriptions'] as $transcript)
                                                        @if (($transcript['speaker'] ?? '') == 'customer')
                                                            @php $hasCustomerData = true; @endphp
                                                            <tr>
                                                                <td>
                                                                    <span
                                                                        class="badge rounded-pill 
                                                                @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success-subtle text-success
                                                                @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger-subtle text-danger
                                                                @else bg-warning-subtle text-warning @endif px-3 py-1">
                                                                        <i
                                                                            class="bi 
                                                                    @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bi-emoji-smile
                                                                    @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bi-emoji-frown
                                                                    @else bi-dash-circle @endif me-1"></i>
                                                                        {{ $transcript['sentiment'] ?? 'Neutral' }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ formatDuration($transcript['start_time'] ?? null, $transcript['end_time'] ?? null) }}
                                                                </td>
                                                                <td>
                                                                    <div class="progress sentiment-progress"
                                                                        style="height: 5px; border-radius: 6px;">
                                                                        <div class="progress-bar 
                                                                    @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success
                                                                    @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger
                                                                    @else bg-warning @endif"
                                                                            role="progressbar"
                                                                                style="width: {{ ($transcript['sentiment'] ?? '') === 'Neutral' ? 50 : 85 }}%; transition: width 0.6s ease;">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    @if (!$hasCustomerData)
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-4">
                                                                <i class="bi bi-info-circle me-2"></i>No customer data
                                                                available
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">
                                                            <i class="bi bi-info-circle me-2"></i>No sentiment data
                                                            available
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div><!-- tab-content -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 animate-fade">

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center p-2 bg-white border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">📋 Policy Compliance Check</h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Analysis of agent performance against organization policies">i</div>
                    </div>

                    @php
                        $totalPolicies   = count($policyCompliance);
                        $metPolicies     = collect($policyCompliance)->filter(fn($i) => !str_contains(strtolower($i['evaluation'] ?? ''), 'not') || str_contains(strtolower($i['evaluation'] ?? ''), 'applicable'))->count();
                        $failedPolicies  = collect($policyCompliance)->filter(fn($i) => str_contains(strtolower($i['evaluation'] ?? ''), 'does not meet'))->count();
                        $naPolicies      = collect($policyCompliance)->filter(fn($i) => str_contains(strtolower($i['evaluation'] ?? ''), 'not applicable'))->count();
                        $metPolicies     = $totalPolicies - $failedPolicies - $naPolicies;
                        $complianceRate  = $totalPolicies > 0 ? round(($metPolicies / $totalPolicies) * 100) : 0;
                    @endphp

                    @if($totalPolicies > 0)
                        {{-- Summary Bar --}}
                        <div class="px-3 py-2 border-bottom bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted fw-semibold">Overall Compliance</small>
                                <span class="fw-bold {{ $complianceRate >= 80 ? 'text-success' : ($complianceRate >= 50 ? 'text-warning' : 'text-danger') }}">
                                    {{ $complianceRate }}%
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 6px; border-radius: 4px;">
                                <div class="progress-bar {{ $complianceRate >= 80 ? 'bg-success' : ($complianceRate >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                    style="width: {{ $complianceRate }}%; transition: width 0.8s ease;"></div>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="small"><i class="fas fa-check-circle text-success me-1"></i>{{ $metPolicies }} Met</span>
                                <span class="small"><i class="fas fa-times-circle text-danger me-1"></i>{{ $failedPolicies }} Failed</span>
                                @if($naPolicies > 0)
                                    <span class="small"><i class="fas fa-minus-circle text-secondary me-1"></i>{{ $naPolicies }} N/A</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="card-body p-0">
                        @if(empty($policyCompliance))
                            <div class="text-center py-5 px-3">
                                @if(isset($data['needs_evaluation']) && $data['needs_evaluation'])
                                    <i class="fas fa-robot fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                    <p class="text-muted mb-1 fw-semibold">No Evaluation Yet</p>
                                    <small class="text-muted">Run AI Analysis to generate policy compliance results.</small>
                                @elseif($status === 'evaluated')
                                    <i class="fas fa-clipboard-list fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                    <p class="text-muted mb-1 fw-semibold">No Policies Configured</p>
                                    <small class="text-muted">Add company policies in the Company Settings to enable compliance checking.</small>
                                @else
                                    <i class="fas fa-hourglass-half fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                    <p class="text-muted mb-1">Awaiting evaluation</p>
                                @endif
                            </div>
                        @else
                            <div class="accordion accordion-flush" id="policyAccordion">
                                @foreach ($policyCompliance as $index => $item)
                                    @php
                                        $evalLower  = strtolower($item['evaluation'] ?? '');
                                        $isFailed   = str_contains($evalLower, 'does not meet');
                                        $isNA       = str_contains($evalLower, 'not applicable');
                                        $badgeClass = $isFailed ? 'bg-danger-subtle text-danger border-danger'
                                                    : ($isNA    ? 'bg-secondary-subtle text-secondary border-secondary'
                                                                : 'bg-success-subtle text-success border-success');
                                        $iconClass  = $isFailed ? 'fa-times-circle' : ($isNA ? 'fa-minus-circle' : 'fa-check-circle');
                                        $confidencePct = is_numeric($item['confidence'] ?? '') ? (float)$item['confidence'] * 100 : 0;
                                        $confColor  = $confidencePct >= 80 ? 'bg-success' : ($confidencePct >= 50 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="policyHeading{{ $index }}">
                                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} d-flex justify-content-between align-items-center py-3"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#policyCollapse{{ $index }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="policyCollapse{{ $index }}">
                                                <span class="fw-semibold text-dark" style="flex: 1;">
                                                    <i class="fas fa-shield-alt me-2 text-primary"></i>
                                                    {{ $item['title'] ?? 'Policy ' . ($index + 1) }}
                                                </span>
                                                <span class="badge {{ $badgeClass }} border ms-2 rounded-pill px-3 py-1" style="white-space: nowrap;">
                                                    <i class="fas {{ $iconClass }} me-1"></i>
                                                    {{ $item['evaluation'] ?? '' }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="policyCollapse{{ $index }}"
                                            class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                            aria-labelledby="policyHeading{{ $index }}"
                                            data-bs-parent="#policyAccordion">
                                            <div class="accordion-body bg-light-subtle">
                                                <div class="row g-3">

                                                    <div class="col-12">
                                                        <div class="small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                                            <i class="fas fa-file-contract text-primary me-1"></i> Policy Requirement
                                                        </div>
                                                        <div class="p-2 border rounded bg-white shadow-sm" style="border-left: 3px solid #0d6efd !important;">
                                                            {{ $item['requirement'] ?? '—' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                                            <i class="fas fa-user-tie text-secondary me-1"></i> Agent Action
                                                        </div>
                                                        <div class="p-2 border rounded bg-white shadow-sm" style="border-left: 3px solid {{ $isFailed ? '#dc3545' : '#198754' }} !important;">
                                                            {{ $item['action'] ?? '—' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                                            <i class="fas fa-bookmark text-info me-1"></i> Reference
                                                        </div>
                                                        <span class="badge bg-white text-primary border px-2 py-1 shadow-sm">
                                                            {{ $item['reference'] ?? 'N/A' }}
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                                            <i class="fas fa-sitemap text-muted me-1"></i> Section
                                                        </div>
                                                        <span class="badge bg-light text-dark border shadow-sm px-2 py-1">
                                                            {{ $item['section'] ?? 'General' }}
                                                        </span>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                                            <i class="fas fa-chart-line text-success me-1"></i> Confidence
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="progress flex-grow-1" style="height: 7px; border-radius: 4px;">
                                                                <div class="progress-bar {{ $confColor }}" role="progressbar"
                                                                    style="width: {{ $confidencePct }}%; transition: width 0.8s ease;"></div>
                                                            </div>
                                                            <span class="small fw-bold {{ $confidencePct >= 80 ? 'text-success' : ($confidencePct >= 50 ? 'text-warning' : 'text-danger') }}">
                                                                {{ round($confidencePct) }}%
                                                            </span>
                                                        </div>
                                                    </div>

                                                    @if(!empty($item['topics']))
                                                        <div class="col-12">
                                                            <div class="small text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                                                <i class="fas fa-tags text-warning me-1"></i> Related Topics
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($item['topics'] as $topic)
                                                                    <span class="badge bg-white text-dark border shadow-sm">{{ $topic }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            📚 Knowledge Base Reference
                        </h6>
                        <div class="info-icon text-secondary" data-bs-toggle="tooltip"
                            title="Customer response validation">i</div>
                    </div>

                    <div class="card-body p-0">
                        <div class="accordion accordion-flush" id="analysisAccordion">
                            @if(!empty($data['analysis_alignment_result_notebook']))
                                @foreach ($data['analysis_alignment_result_notebook'] as $index => $result)
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button
                                                class="accordion-button collapsed d-flex justify-content-between align-items-center"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $index }}" aria-expanded="false"
                                                aria-controls="collapse{{ $index }}">
                                                <span class="fw-semibold text-dark">
                                                    <i class="fas fa-question-circle me-2 text-primary"></i>
                                                    {{ Str::limit($result['question'] ?? '', 80) }}
                                                </span>
                                                <span class="badge bg-light text-dark border ms-2">
                                                    {{ is_numeric($result['confidence_level'] ?? '') ? $result['confidence_level'] : ucfirst($result['confidence_level'] ?? '') }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $index }}" data-bs-parent="#analysisAccordion">
                                            <div class="accordion-body">
                                                <div class="row g-3">

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-lightbulb text-warning me-1"></i> Answer
                                                        </div>
                                                        <div class="fw-medium">{{ $result['answer'] ?? '' }}</div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-check-circle text-success me-1"></i> Evaluation
                                                        </div>
                                                        <span class="badge bg-{{ str_contains($result['evaluation'] ?? '', '❌') ? 'danger' : (str_contains($result['evaluation'] ?? '', '⚠️') ? 'warning' : 'success') }} rounded-pill">
                                                            {{ $result['evaluation'] ?? '' }}
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-book text-info me-1"></i> Knowledge Base
                                                        </div>
                                                        <div class="fw-light">
                                                            {{ $result['KBtext'] ?? 'No relevant text in knowledge base' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-project-diagram text-secondary me-1"></i> Matching
                                                            Sections
                                                        </div>
                                                        <div style="max-width: 300px;">
                                                            @if(!empty($result['matching_transcript_sections']))
                                                                @foreach ($result['matching_transcript_sections'] as $section)
                                                                    <span
                                                                        class="badge bg-light text-dark border">{{ $section }}</span>
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-notebook text-primary me-1"></i> Notebook
                                                        </div>
                                                        <span class="fw-medium">{{ $result['notebook_name']?? '' }}</span>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-tags text-info me-1"></i> Topics
                                                        </div>
                                                        @if(!empty($result['matching_topics']))
                                                            @foreach ($result['matching_topics'] as $topic)
                                                                <span
                                                                    class="badge bg-light text-dark border">{{ $topic }}</span>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="small text-muted mb-1">
                                                            <i class="fas fa-chart-line text-success me-1"></i> Confidence
                                                        </div>
                                                        <span
                                                            class="badge bg-{{ ($result['confidence_level'] ?? '') == 'high' || (is_numeric($result['confidence_level'] ?? '') && $result['confidence_level'] >= 0.8) ? 'success' : (($result['confidence_level'] ?? '') == 'medium' || (is_numeric($result['confidence_level'] ?? '') && $result['confidence_level'] >= 0.5) ? 'warning' : 'danger') }}">
                                                            {{ is_numeric($result['confidence_level'] ?? '') ? $result['confidence_level'] : ucfirst($result['confidence_level'] ?? '') }}
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5 px-3">
                                    @if(isset($data['needs_evaluation']) && $data['needs_evaluation'])
                                        <i class="fas fa-brain fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                        <p class="text-muted mb-1 fw-semibold">No KB Analysis Yet</p>
                                        <small class="text-muted">Run AI Analysis to check the conversation against your Knowledge Base.</small>
                                    @elseif($status === 'evaluated')
                                        <i class="fas fa-book-open fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                        <p class="text-muted mb-1 fw-semibold">No KB Matches Found</p>
                                        <small class="text-muted">The AI did not find any factual claims in this call that required Knowledge Base verification.</small>
                                    @else
                                        <i class="fas fa-hourglass-half fs-2 text-muted opacity-50 mb-3 d-block"></i>
                                        <p class="text-muted mb-1">Awaiting KB verification</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
   <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audioPlayer = document.getElementById('audioPlayer');
            const playButton = document.getElementById('playButton');
            const playIcon = document.getElementById('playIcon');
            const progressBar = document.getElementById('progressBar');
            const currentTimeEl = document.getElementById('currentTime');
            const durationEl = document.getElementById('duration');

            // Update time display format
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
            }

            // Update progress bar
            function updateProgress() {
                const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                progressBar.style.width = `${percent}%`;
                currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
            }

            // Set audio duration when metadata is loaded
            audioPlayer.addEventListener('loadedmetadata', function() {
                durationEl.textContent = formatTime(audioPlayer.duration);
            });

            // Update progress while playing
            audioPlayer.addEventListener('timeupdate', updateProgress);

            // Toggle play/pause
            window.togglePlay = function() {
                if (audioPlayer.paused) {
                    audioPlayer.play();
                    playIcon.classList.remove('fa-play');
                    playIcon.classList.add('fa-pause');
                    playButton.title = 'Pause';
                } else {
                    audioPlayer.pause();
                    playIcon.classList.remove('fa-pause');
                    playIcon.classList.add('fa-play');
                    playButton.title = 'Play';
                }
            };

            // Skip forward/backward
            window.skip = function(seconds) {
                audioPlayer.currentTime += seconds;
                updateProgress();
            };

            // Click on progress bar container to seek
            const progressBarContainer = document.getElementById('progressBarContainer');
            if (progressBarContainer) {
                progressBarContainer.addEventListener('click', function(e) {
                    if (audioPlayer.duration) {
                        const rect = this.getBoundingClientRect();
                        const percent = (e.clientX - rect.left) / rect.width;
                        audioPlayer.currentTime = percent * audioPlayer.duration;
                        updateProgress();
                    }
                });
            }

            // Log if audio has no source
            if (!audioPlayer.src || audioPlayer.src === window.location.href) {
                console.warn('Audio player: no source URL set. The task may not have an uploaded audio file.');
                playButton.disabled = true;
                playButton.title = 'No audio file available';
            }
        });
    </script>

    <script>
        // Toggle timestamps visibility
        document.getElementById('showTimestamps').addEventListener('change', function() {
            const timestamps = document.querySelectorAll('.timestamp');
            timestamps.forEach(ts => {
                ts.style.display = this.checked ? 'inline' : 'none';
            });
        });
    </script>
    <script>
        document.querySelectorAll('.tabs').forEach(tabContainer => {
            const buttons = tabContainer.querySelectorAll('.tab-btn');
            const panes = tabContainer.nextElementSibling.querySelectorAll('.tab-pane');

            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    // reset only inside this tab group
                    buttons.forEach(b => b.classList.remove('active'));
                    panes.forEach(p => p.classList.remove('active'));

                    // activate selected
                    btn.classList.add('active');
                    tabContainer.nextElementSibling
                        .querySelector('#' + btn.dataset.tab)
                        .classList.add('active');
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define new color palette
            const chartColors = {
                sentiment: ['#8FA998', '#9A8E85', '#BF8F8F'], // Success (Greenish), Neutral (Grayish), Danger (Reddish)
                speechRate: ['#8FA3A9', '#8FA998'], // Muted slate blue, muted sage green
                loudness: ['#C9B178', '#8FA998', '#BF8F8F'], // Soft gold, muted sage, dusty rose
            };

            // Sentiment Chart
            new Chart(document.getElementById('sentimentChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Positive', 'Neutral', 'Negative'],
                    datasets: [{
                        data: [{{ $positivePercent }}, {{ $neutralPercent }}, {{ $negativePercent }}],
                        backgroundColor: chartColors.sentiment,
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 13 }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Speech Rate Chart
            new Chart(document.getElementById('speechRateChart'), {
                type: 'bar',
                data: {
                    labels: ['Agent', 'Customer'],
                    datasets: [{
                        label: 'Words per Minute',
                        data: [{{ $agentPace }}, {{ $customerPace }}],
                        backgroundColor: chartColors.speechRate,
                        borderWidth: 0,
                        borderRadius: 6,
                        hoverBackgroundColor: ['#7A9199', '#7F9889'] // Darker versions
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'WPM',
                                font: { size: 13, weight: 'bold' }
                            },
                            grid: { drawBorder: false }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Loudness Chart
            new Chart(document.getElementById('loudnessChart'), {
                type: 'pie',
                data: {
                    labels: ['Low', 'Optimal', 'High'],
                    datasets: [{
                        data: [{{ $lowLoudness }}, {{ $optimalLoudness }}, {{ $highLoudness }}],
                        backgroundColor: chartColors.loudness,
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 13 }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @php
        $items = $data['analysis_alignment_result_notebook'] ?? [];
        // Map long evaluation strings to simple categories for the chart
        $evaluationCounts = collect($items)->map(function($item) {
            $eval = $item['evaluation'] ?? '';
            if (str_contains($eval, '✅')) return 'Correct';
            if (str_contains($eval, '⚠️')) return 'Partial';
            if (str_contains($eval, '❌')) return 'Incorrect';
            return 'Other';
        })->groupBy(fn($i) => $i)->map->count()->toArray();
        
        // Ensure consistent order: Correct, Partial, Incorrect, Other
        $orderedCounts = [];
        foreach(['Correct', 'Partial', 'Incorrect', 'Other'] as $key) {
            if (isset($evaluationCounts[$key])) {
                $orderedCounts[$key] = $evaluationCounts[$key];
            }
        }
        $evaluationCounts = $orderedCounts;
        $evaluationTypes = array_keys($evaluationCounts);
        $total = count($items);

        // Map categories to specific premium colors
        $categoryGradients = [
            'Correct' => ['#8FA998', '#B5C7CC'],   // Sage Green
            'Partial' => ['#C9B178', '#E8E5E0'],   // Soft Gold
            'Incorrect' => ['#BF8F8F', '#D1CDC7'], // Dusty Rose
            'Other' => ['#A89F96', '#C5BDB5']      // Taupe
        ];
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('evaluationChart').getContext('2d');
            const total = @json($total);
            const labels = @json($evaluationTypes);
            const dataValues = @json(array_values($evaluationCounts));

            // Create gradient colors based on categories
            const gradientColors = labels.map((label) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                const colors = @json($categoryGradients)[label] || ['#A89F96', '#C5BDB5'];
                gradient.addColorStop(0, colors[0]);
                gradient.addColorStop(1, colors[1]);
                return gradient;
            });

            // Center text plugin
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw(chart) {
                    const { ctx, width, height } = chart;
                    ctx.restore();
                    const fontSize = Math.round(height / 6);
                    ctx.font = `bold ${fontSize}px sans-serif`;
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#4A453E'; // Using our text-primary color
                    ctx.fillText(total, width / 2, height / 2);
                    ctx.save();
                }
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: gradientColors,
                        borderColor: '#fff',
                        borderWidth: 2,
                        hoverOffset: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(74, 69, 62, 0.9)', // Dark version of text-primary
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            bodyFont: {
                                weight: 'bold'
                            },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                },
                plugins: [centerTextPlugin]
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced Sentiment Timeline Chart
            const timelineCtx = document.getElementById('sentimentTimelineChart').getContext('2d');
            @php
                $timelinePoints = [];
                if (isset($data['speakers_transcriptions'])) {
                    foreach (array_slice($data['speakers_transcriptions'], 0, 100) as $t) {
                        $s = $t['sentiment'] ?? 'Neutral';
                        $val = 0;
                        if ($s == 'Positive') $val = 1;
                        if ($s == 'Negative') $val = -1;
                        $timelinePoints[] = [
                            't' => $t['start_time'] ?? '00:00',
                            'v' => $val,
                            'speaker' => ucfirst($t['speaker'] ?? 'Unknown')
                        ];
                    }
                }
            @endphp
            
            const rawTimelineData = @json($timelinePoints);
            
            if (rawTimelineData && rawTimelineData.length > 0) {
                new Chart(timelineCtx, {
                    type: 'line',
                    data: {
                        labels: rawTimelineData.map(d => d.t),
                        datasets: [{
                            label: 'Sentiment Flow',
                            data: rawTimelineData.map(d => d.v),
                            borderColor: '#8FA998',
                            backgroundColor: 'rgba(143, 169, 152, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: rawTimelineData.map(d => {
                                if (d.v > 0) return '#198754';
                                if (d.v < 0) return '#dc3545';
                                return '#ffc107';
                            }),
                            segment: {
                                borderColor: ctx => {
                                    if (!ctx.p1) return '#ffc107';
                                    const val = ctx.p1.parsed.y;
                                    if (val > 0) return '#198754';
                                    if (val < 0) return '#dc3545';
                                    return '#ffc107';
                                }
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const d = rawTimelineData[context.dataIndex];
                                        const sentiment = d.v > 0 ? 'Positive' : (d.v < 0 ? 'Negative' : 'Neutral');
                                        return `${d.speaker || 'Unknown'}: ${sentiment}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                min: -1.2,
                                max: 1.2,
                                ticks: {
                                    callback: function(value) {
                                        if (value === 1) return 'Positive';
                                        if (value === 0) return 'Neutral';
                                        if (value === -1) return 'Negative';
                                        return '';
                                    },
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    maxTicksLimit: 12
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Transcription Search ──────────────────────────────────
            const transSearch = document.getElementById('transSearch');
            const transBody   = document.getElementById('transcription-body');

            if (transSearch && transBody) {
                transSearch.addEventListener('input', function() {
                    const term = this.value.toLowerCase().trim();
                    const turns = transBody.querySelectorAll('.transcript-turn');
                    
                    turns.forEach(turn => {
                        const text = turn.dataset.text || '';
                        if (text.includes(term)) {
                            turn.style.display = '';
                        } else {
                            turn.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
@endpush

