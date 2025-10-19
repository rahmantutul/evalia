@extends('user.layouts.app')

@php
    function formatDuration($startTime, $endTime) {
        if (!$startTime || !$endTime) {
            return '00:00';
        }
        $start = strtotime('1970-01-01 ' . substr($startTime, 0, 5) . ' UTC');
        $end = strtotime('1970-01-01 ' . substr($endTime, 0, 5) . ' UTC');
        if ($start === false || $end === false) {
            return '00:00';
        }
        $duration = $end - $start;
        return gmdate('i:s', $duration);
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
@endpush

@section('content')
    <div class="container-fluid py-4 professional-theme">
        <!-- Top Call Duration Bar -->
        <div class="top-bar animate-fade">
            {{--  <div class="duration">
      <i class="bi bi-clock me-2"></i>Call Duration: {{ $data['call_duration']['call_duration'] ?? 'N/A' }}
    </div>  --}}
            <div class="duration">
                <i class="bi bi-mic me-2"></i>Talking Duration:
                {{ $data['pause_delay_information']['talking_duration']['agent'] ?? 'N/A' }} (Agent) /
                {{ $data['pause_delay_information']['talking_duration']['customer'] ?? 'N/A' }} (Customer)
            </div>
        </div>

        <div class="row gy-4">
            @php
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
                                        <div class="progress"
                                            style="width: {{ $data['agent_professionalism']['speech_characteristics']['volume']['optimal_loudness_percentage'] ?? 0 }}%">
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
                                <audio id="audioPlayer" src="{{ $data['customer_agent_audio_s3_url'] ?? '' }}"></audio>

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

                                <!-- Progress Bar -->
                                <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
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
                            @if (isset($data['topics']['other']) || isset($data['call_outcome']))
                                <div>
                                    <h6 class="fw-bold text-dark mb-2">
                                        <i class="bi bi-key-fill text-primary me-2"></i> Key Discussion Points
                                    </h6>
                                    <div class="ps-2">

                                        @if (isset($data['topics']['main']))
                                            <div class="mb-2">
                                                <span
                                                    class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                                                    <i class="bi bi-bookmark-fill me-1"></i>
                                                    Main Topic: {{ $data['topics']['main'][0] ?? 'Main Topic' }}
                                                </span>
                                            </div>
                                        @endif

                                        <div class="mb-2">
                                            <strong class="text-dark">Other Topics:</strong>
                                            @foreach ($data['topics']['other'] ?? [] as $topic)
                                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1 ms-1">
                                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                    {{ $topic }}
                                                </span>
                                            @endforeach
                                        </div>

                                        @if (isset($data['call_outcome']))
                                            <div class="mt-2">
                                                <strong class="text-dark">Call Outcome:</strong>
                                                <span class="text-secondary ms-1">
                                                    <i class="fas fa-ticket-alt me-1"></i>
                                                    {{ implode('، ', $data['call_outcome']) }}
                                                </span>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="card shadow-sm border-0 rounded-3 animate-fade delay-3">
                      <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 border-bottom px-3 py-2">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                          📝 Full Call Transcription
                        </h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Automatically generated call summary">i</div>
                        
                      </div>

                      <!-- Switch -->
                      <div class="px-3 pt-2 pb-1">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="showTimestamps" checked>
                          <label class="form-check-label small text-muted" for="showTimestamps">Show timestamps</label>
                        </div>
                      </div>

                      <!-- Transcription -->
                      <div class="transcription-container px-3 pb-3" style="max-height: 390px; overflow-y: auto;">
                        @if(isset($data['speakers_transcriptions']))
                          @foreach($data['speakers_transcriptions'] as $transcript)
                            <div class="d-flex align-items-start gap-2 py-2 border-bottom">
                              
                              <!-- Timestamp -->
                              <span class="text-muted small fw-semibold timestamp" style="min-width: 55px;">
                                [{{ $transcript['start_time'] ?? '00:00' }}]
                              </span>

                              <!-- Content -->
                              <div class="flex-grow-1">
                                <span class="fw-bold text-primary me-1 text-capitalize">
                                  {{ $transcript['speaker'] ?? 'Agent' }}:
                                </span>
                                <span class="text-dark">
                                  {{ $transcript['transcript'] ?? 'No transcript available' }}
                                </span>
                              </div>

                              <!-- Sentiment -->
                              <span class="badge rounded-pill 
                                @if(($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success
                                @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger
                                @else bg-warning text-dark @endif">
                                {{ $transcript['sentiment'] ?? 'Neutral' }}
                              </span>
                            </div>
                          @endforeach
                        @else
                          <div class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-2"></i>No transcription data available
                          </div>
                        @endif
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
                                                                @php
                                                                    $start = isset($transcript['start_time'])
                                                                        ? strtotime(
                                                                            '1970-01-01 ' .
                                                                                substr(
                                                                                    $transcript['start_time'],
                                                                                    0,
                                                                                    5,
                                                                                ) .
                                                                                ' UTC',
                                                                        )
                                                                        : 0;
                                                                    $end = isset($transcript['end_time'])
                                                                        ? strtotime(
                                                                            '1970-01-01 ' .
                                                                                substr($transcript['end_time'], 0, 5) .
                                                                                ' UTC',
                                                                        )
                                                                        : 0;
                                                                    $duration = $end - $start;
                                                                    echo gmdate('i:s', $duration);
                                                                @endphp
                                                            </td>
                                                            <td>
                                                                <div class="progress sentiment-progress"
                                                                    style="height: 5px; border-radius: 6px;">
                                                                    <div class="progress-bar 
                                                                @if (($transcript['sentiment'] ?? 'Neutral') == 'Positive') bg-success
                                                                @elseif(($transcript['sentiment'] ?? 'Neutral') == 'Negative') bg-danger
                                                                @else bg-warning @endif"
                                                                        role="progressbar"
                                                                        style="width: {{ rand(50, 90) }}%; transition: width 0.6s ease;">
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
                                                                            style="width: {{ rand(50, 90) }}%; transition: width 0.6s ease;">
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
                                                                            style="width: {{ rand(50, 90) }}%; transition: width 0.6s ease;">
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
                        <h6 class="card-title mb-0 fw-bold text-dark">⏱️ Timeline Analysis</h6>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Analysis states">i</div>
                    </div>

                    <div class="card-body">
                        <div class="professional-card">

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-pills nav-fill mb-3 sentiment-tabs" id="timeAnalysisTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="Delays-tab" data-bs-toggle="tab"
                                        data-bs-target="#Delays" type="button" role="tab">
                                        Delays
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="Pauses-tab" data-bs-toggle="tab"
                                        data-bs-target="#Pauses" type="button" role="tab">
                                        Pauses
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content p-0">

                                <!-- Delays Tab -->
                                <div class="tab-pane fade show active" id="Delays" role="tabpanel"
                                    aria-labelledby="Delays-tab">
                                    <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0 sentiment-table">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="20%">Speaker</th>
                                                    <th width="15%">Duration</th>
                                                    <th width="25%">Time</th>
                                                    <th width="40%">Context</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data['pause_delay_information']['speaker_delay_duration']))
                                                    @php
                                                        $agentDelays =
                                                            $data['pause_delay_information']['speaker_delay_duration'][
                                                                'agent'
                                                            ] ?? [];
                                                        $customerDelays =
                                                            $data['pause_delay_information']['speaker_delay_duration'][
                                                                'customer'
                                                            ] ?? [];
                                                        $allDelays = array_merge(
                                                            array_map(
                                                                fn($item) => $item + ['speaker' => 'Agent'],
                                                                $agentDelays,
                                                            ),
                                                            array_map(
                                                                fn($item) => $item + ['speaker' => 'Customer'],
                                                                $customerDelays,
                                                            ),
                                                        );
                                                    @endphp

                                                    @forelse($allDelays as $delay)
                                                        @php
                                                            $context = 'No context available';
                                                            if (isset($data['speakers_transcriptions'])) {
                                                                foreach (
                                                                    $data['speakers_transcriptions']
                                                                    as $transcript
                                                                ) {
                                                                    if (
                                                                        isset(
                                                                            $transcript['start_time'],
                                                                            $transcript['end_time'],
                                                                            $delay['delay_start'],
                                                                            $delay['delay_end'],
                                                                        )
                                                                    ) {
                                                                        $start = strtotime(
                                                                            '1970-01-01 ' .
                                                                                substr(
                                                                                    $transcript['start_time'],
                                                                                    0,
                                                                                    5,
                                                                                ) .
                                                                                ' UTC',
                                                                        );
                                                                        $end = strtotime(
                                                                            '1970-01-01 ' .
                                                                                substr($transcript['end_time'], 0, 5) .
                                                                                ' UTC',
                                                                        );
                                                                        $delayStart = strtotime(
                                                                            '1970-01-01 ' .
                                                                                substr($delay['delay_start'], 0, 5) .
                                                                                ' UTC',
                                                                        );
                                                                        $delayEnd = strtotime(
                                                                            '1970-01-01 ' .
                                                                                substr($delay['delay_end'], 0, 5) .
                                                                                ' UTC',
                                                                        );

                                                                        if (
                                                                            isset($transcript['start_time'], $transcript['end_time'], $delay['delay_start'], $delay['delay_end']) &&
                                                                            strtotime('1970-01-01 ' . substr($transcript['start_time'], 0, 5) . ' UTC') >= strtotime('1970-01-01 ' . substr($delay['delay_start'], 0, 5) . ' UTC') - 5 &&
                                                                            strtotime('1970-01-01 ' . substr($transcript['end_time'], 0, 5) . ' UTC') <= strtotime('1970-01-01 ' . substr($delay['delay_end'], 0, 5) . ' UTC') + 5
                                                                        ) {
                                                                            $context = $transcript['transcript'] ?? 'No context available';
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $delay['speaker'] == 'Agent' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} px-2 py-1">
                                                                    <i
                                                                        class="bi {{ $delay['speaker'] == 'Agent' ? 'bi-headset' : 'bi-person' }} me-1"></i>
                                                                    {{ $delay['speaker'] }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $delay['delay_duration'] ?? 'N/A' }}</td>
                                                            <td>{{ $delay['delay_start'] ?? 'N/A' }} -
                                                                {{ $delay['delay_end'] ?? 'N/A' }}</td>
                                                            <td class="text-truncate" title="{{ $context }}">
                                                                {{ $context }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-4 text-muted">
                                                                <i class="bi bi-check-circle me-2"></i> No significant
                                                                delays detected
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">
                                                            <i class="bi bi-exclamation-triangle me-2"></i> Delay data not
                                                            available
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pauses Tab -->
                                <div class="tab-pane fade" id="Pauses" role="tabpanel" aria-labelledby="Pauses-tab">
                                    <div class="sentiment-table-container" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0 sentiment-table">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="20%">Speaker</th>
                                                    <th width="15%">Duration</th>
                                                    <th width="25%">Time</th>
                                                    <th width="40%">Context</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data['pause_delay_information']['speaker_pause_duration']))
                                                    @php
                                                        $agentPauses =
                                                            $data['pause_delay_information']['speaker_pause_duration'][
                                                                'agent'
                                                            ] ?? [];
                                                        $customerPauses =
                                                            $data['pause_delay_information']['speaker_pause_duration'][
                                                                'customer'
                                                            ] ?? [];
                                                        $allPauses = array_merge(
                                                            array_map(
                                                                fn($item) => $item + ['speaker' => 'Agent'],
                                                                $agentPauses,
                                                            ),
                                                            array_map(
                                                                fn($item) => $item + ['speaker' => 'Customer'],
                                                                $customerPauses,
                                                            ),
                                                        );
                                                    @endphp

                                                    @forelse($allPauses as $pause)
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $pause['speaker'] == 'Agent' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} px-2 py-1">
                                                                    <i
                                                                        class="bi {{ $pause['speaker'] == 'Agent' ? 'bi-headset' : 'bi-person' }} me-1"></i>
                                                                    {{ $pause['speaker'] }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $pause['pause_duration'] ?? 'N/A' }}</td>
                                                            <td>{{ $pause['pause_start'] ?? 'N/A' }} -
                                                                {{ $pause['pause_end'] ?? 'N/A' }}</td>
                                                            <td class="text-truncate"
                                                                title="{{ $pause['pause_class'] ?? 'No context' }}">
                                                                {{ $pause['pause_class'] ?? 'No context' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-4 text-muted">
                                                                <i class="bi bi-check-circle me-2"></i> No significant
                                                                pauses detected
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">
                                                            <i class="bi bi-exclamation-triangle me-2"></i> Pause data not
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

            <div class="col-lg-6">
                <!-- Word Frequency -->
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
                                        @foreach (array_slice($data['most_common_words']['agent'] ?? [], 0, 15) as $word)
                                            <span class="word-tag">
                                                {{ $word['word'] }}
                                                <span class="frequency-badge">{{ $word['frequency'] }}</span>
                                            </span>
                                        @endforeach
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
                                        @foreach (array_slice($data['most_common_words']['customer'] ?? [], 0, 15) as $word)
                                            <span class="word-tag">
                                                {{ $word['word'] }}
                                                <span class="frequency-badge">{{ $word['frequency'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                                    {{ ucfirst($result['confidence_level'] ?? '') }}
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
                                                        <span class="badge bg-success rounded-pill">
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
                                                            class="badge bg-{{ ($result['confidence_level'] ?? '') == 'high' ? 'success' : (($result['confidence_level'] ?? '') == 'medium' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($result['confidence_level'] ?? '') }}
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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

            // Click on progress bar to seek
            document.querySelector('.audio-progress').addEventListener('click', function(e) {
                const percent = e.offsetX / this.offsetWidth;
                audioPlayer.currentTime = percent * audioPlayer.duration;
                updateProgress();
            });
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
                sentiment: ['#A89F96', '#9A8E85', '#C7BCB1'], // Soft taupe, muted brown-gray, warm gray
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
        $evaluationCounts = collect($items)->groupBy('evaluation')->map->count()->toArray();
        $evaluationTypes = array_keys($evaluationCounts);
        $total = count($items);

        // Define a gradient palette for premium look
        $gradients = [
            ['#D1CDC7', '#E8E5E0'], // Light grays
            ['#C7BCB1', '#D8D2C9'], // Warm grays
            ['#A89F96', '#C5BDB5'], // Taupe tones
            ['#9A8E85', '#B2A89F'], // Muted brown-grays
            ['#8FA3A9', '#B5C7CC']  // Muted slate blues
        ];
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('evaluationChart').getContext('2d');
            const total = {{ $total }};
            const labels = {!! json_encode($evaluationTypes) !!};
            const dataValues = {!! json_encode(array_values($evaluationCounts)) !!};

            // Create gradient colors dynamically
            const gradientColors = labels.map((_, index) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                const colors = {!! json_encode($gradients) !!};
                gradient.addColorStop(0, colors[index % colors.length][0]);
                gradient.addColorStop(1, colors[index % colors.length][1]);
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
@endpush

