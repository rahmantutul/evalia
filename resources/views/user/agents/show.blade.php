@extends('user.layouts.app')

@push('styles')
<style>
    /* Premium Profile Aesthetics */
    .profile-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 100%;
    }

    .profile-cover {
        height: 120px;
        background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
        position: relative;
    }

    .profile-avatar-wrapper {
        position: absolute;
        bottom: -40px;
        left: 25px;
        padding: 4px;
        background: #ffffff;
        border-radius: 18px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 16px;
        object-fit: cover;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .profile-content {
        padding: 55px 25px 30px;
    }

    .agent-name-lg {
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .display-id-badge {
        font-size: 11px;
        font-weight: 800;
        color: #0a66c2;
        background: #eff6ff;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
        margin-bottom: 12px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .info-item:last-child { border-bottom: none; }

    .info-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f8fafc;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .info-label { font-size: 12px; color: #64748b; margin-bottom: 1px; }
    .info-value { font-size: 14px; font-weight: 700; color: #1e293b; }

    /* Metric Cards */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .metric-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s;
    }
    .metric-card:hover { transform: translateY(-3px); }

    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .metric-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .metric-value { font-size: 26px; font-weight: 900; color: #0f172a; margin-bottom: 2px; }
    .metric-label { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Detail Cards */
    .detail-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 25px;
        display: flex;
        flex-direction: column;
    }

    .card-title-premium {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
    }

    /* Soft & Percent Wise Progress Bars */
    .progress-classic {
        height: 8px !important;
        border-radius: 10px;
        background-color: #f1f5f9 !important;
        overflow: hidden;
        margin-top: 5px;
    }

    .progress-bar-soft {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    /* Table Adjustment */
    .evaluations-container {
        max-height: 380px !important;
        overflow-y: auto;
        margin-top: 10px;
        scrollbar-width: thin;
    }
    
    .table-premium thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 2;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 15px;
    }

    .status-pill {
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .topic-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 6px;
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
        border: 1px solid #e2e8f0;
    }

    .ai-commentary {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-left: 4px solid #0a66c2;
        padding: 15px;
        border-radius: 0 12px 12px 0;
        margin-top: 15px;
    }

    .intelligence-tag {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        margin-left: 8px;
    }

</style>
@endpush

@section('content')
@php
    function getSoftPctColor($val) {
        if ($val >= 90) return '#10b981'; // Emerald
        if ($val >= 75) return '#3b82f6'; // Blue
        if ($val >= 60) return '#f59e0b'; // Amber
        return '#ef4444'; // Red
    }
@endphp

<div class="container-fluid p-4">
    <!-- Breadcrumb -->
    <div class="row align-items-center mb-4 text-white">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('user.agents.index') }}" class="text-dark fw-600">Workforce</a></li>
                    <li class="breadcrumb-item active fw-600">Agent Intelligence Profile</li>
                </ol>
            </nav>
            <h2 class="fw-900 text-dark mb-0">Agent Performance Dashboard</h2>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-xl-3">
            <div class="profile-card">
                <div class="profile-cover">
                    <div class="profile-avatar-wrapper">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($agent['agent_details']['name'] ?? 'Agent') }}&background=random&color=fff&bold=true&size=128" class="profile-avatar" alt="Avatar">
                    </div>
                </div>
                <div class="profile-content">
                    <div class="display-id-badge">{{ $agent['agent_details']['display_id'] ?? 'AGT-77421' }}</div>
                    <h3 class="agent-name-lg">{{ $agent['agent_details']['name'] ?? 'Sara Al-Khateeb' }}</h3>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="badge rounded-pill" style="background: #dcfce7; color: #166534; font-weight: 800; font-size: 10px;">
                           <i class="fas fa-circle me-1" style="font-size: 6px;"></i> ACTIVE
                        </span>
                        <span class="text-secondary small fw-bold">|</span>
                        <span class="text-secondary small fw-bold">{{ $agent['agent_details']['position'] ?? 'Customer Service' }}</span>
                    </div>

                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-user-tie"></i></div>
                            <div>
                                <div class="info-label">Supervisor</div>
                                <div class="info-value">Mahmoud Ali</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <div class="info-label">Organization Email</div>
                                <div class="info-value">{{ strtolower(Str::slug($agent['agent_details']['name'] ?? 'agent')) }}@crtvai.com</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div>
                                <div class="info-label">Direct Extension</div>
                                <div class="info-value">+962 7 9008 7879</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main -->
        <div class="col-xl-9">
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon-box" style="background: #eff6ff; color: #1d4ed8;"><i class="fas fa-wave-square"></i></div>
                        <span class="badge bg-soft-success text-success fw-900">+4.2%</span>
                    </div>
                    <div class="metric-value">{{ number_format($agent['summary']['total_interaction'] ?? 1420) }}</div>
                    <div class="metric-label">Interactions</div>
                </div>
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon-box" style="background: #f0fdf4; color: #15803d;"><i class="fas fa-brain"></i></div>
                        <span class="badge bg-soft-primary text-primary fw-900">Score</span>
                    </div>
                    @php $score = $agent['current_scores']['overall_score'] ?? 92.5; @endphp
                    <div class="metric-value" style="color: {{ getSoftPctColor($score) }}">{{ number_format($score, 1) }}%</div>
                    <div class="metric-label">Intelligence Score</div>
                </div>
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon-box" style="background: #fef3c7; color: #b45309;"><i class="fas fa-heart"></i></div>
                        <span class="badge bg-soft-warning text-warning fw-900">CSAT</span>
                    </div>
                    @php $csat = $agent['summary']['satisfaction_rate'] ?? 96; @endphp
                    <div class="metric-value" style="color: {{ getSoftPctColor($csat) }}">{{ $csat }}%</div>
                    <div class="metric-label">Satisfaction Rate</div>
                </div>
                <div class="metric-card">
                    <div class="metric-header">
                        <div class="metric-icon-box" style="background: #ecfeff; color: #0e7490;"><i class="fas fa-shield-alt"></i></div>
                        <span class="badge bg-soft-info text-info fw-900">Low Risk</span>
                    </div>
                    <div class="metric-value">98.2%</div>
                    <div class="metric-label">Compliance</div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="detail-card">
                        <h4 class="card-title-premium">
                            <div class="icon-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            Intelligence Metrics Breakdown
                        </h4>
                        
                        @foreach([
                            ['name' => 'Answer Accuracy', 'val' => $agent['current_scores']['answer_accuracy'] ?? 94],
                            ['name' => 'Response Speed', 'val' => $agent['current_scores']['response_speed'] ?? 88],
                            ['name' => 'Professionalism', 'val' => $agent['current_scores']['professionalism'] ?? 96],
                            ['name' => 'Problem Solving', 'val' => 90],
                            ['name' => 'Tone & Empathy', 'val' => $agent['current_scores']['customer_satisfaction'] ?? 92]
                        ] as $metric)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-secondary" style="font-size: 13px;">{{ $metric['name'] }}</span>
                                <span class="fw-800" style="font-size: 13px; color: {{ getSoftPctColor($metric['val']) }}">{{ $metric['val'] }}%</span>
                            </div>
                            <div class="progress-classic">
                                <div class="progress-bar-soft" 
                                     style="width: {{ $metric['val'] }}%; background-color: {{ getSoftPctColor($metric['val']) }} !important;">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="detail-card">
                        <h4 class="card-title-premium">
                            <div class="icon-sm bg-success rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            Performance Trend
                        </h4>
                        <div id="performanceTrendChart" style="height: 250px; width: 100%;"></div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-1">
                <div class="col-lg-12">
                    <div class="detail-card">
                        <div class="row">
                            <div class="col-md-7">
                                <h4 class="card-title-premium">
                                    <div class="icon-sm bg-info rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                    Knowledge Proficiency & Cognitive Insights
                                </h4>
                                
                                <div class="mt-4">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="p-3 border rounded-3 bg-light">
                                                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Policy Compliance</div>
                                                <div class="h5 fw-900 text-dark mb-0">98.4% <span class="text-success" style="font-size: 11px;"><i class="fas fa-caret-up"></i> 1.2%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 border rounded-3 bg-light">
                                                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Sentiment Accuracy</div>
                                                <div class="h5 fw-900 text-dark mb-0">92.1% <span class="text-danger" style="font-size: 11px;"><i class="fas fa-caret-down"></i> 0.5%</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="fw-bold text-secondary small text-uppercase mb-2" style="font-size: 10px; letter-spacing: 1px;">Top Proficiency Topics</div>
                                    <div>
                                        <span class="topic-badge">Pension Inquiries</span>
                                        <span class="topic-badge">Maternity Support</span>
                                        <span class="topic-badge">Early Retirement</span>
                                        <span class="topic-badge">Digital Registration</span>
                                        <span class="topic-badge">Social Security Law</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="ai-commentary h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="fw-bold text-primary small text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-robot me-1"></i> Intelligence Summary</div>
                                        <span class="intelligence-tag bg-soft-success text-success">Balanced</span>
                                    </div>
                                    <p class="text-slate-600 small mb-0" style="line-height: 1.6;">
                                        Agent demonstrates exceptional <strong>"Answer Accuracy"</strong> especially in <em>Pension and Law</em> categories. 
                                        Cognitive analysis suggests high emotional intelligence during escalation handle. 
                                        <br><br>
                                        <i class="fas fa-lightbulb text-warning me-1"></i> <strong>Coaching Tip:</strong> Focus on reducing "Silence Gap" during technical law verification steps.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evaluations Table -->
            <div class="detail-card mt-4">
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <h4 class="card-title-premium mb-0">
                        <div class="icon-sm bg-dark rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                            <i class="fas fa-history"></i>
                        </div>
                        Recent Intelligence Evaluations
                    </h4>
                </div>

                <div class="evaluations-container">
                    <table class="table table-hover align-middle table-premium">
                        <thead>
                            <tr class="text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px;">
                                <th>Session Date</th>
                                <th>Category</th>
                                <th>Outcome</th>
                                <th class="text-center">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $evalLogs = [
                                    ['date' => 'Today', 'time'=>'10:24 AM', 'cat' => 'Complaints', 'status' => 'Resolved', 'score' => 96],
                                    ['date' => 'Yesterday', 'time'=>'04:12 PM', 'cat' => 'Inquiry', 'status' => 'Escalated', 'score' => 82],
                                    ['date' => 'Feb 10', 'time'=>'09:45 AM', 'cat' => 'General', 'status' => 'Resolved', 'score' => 94],
                                    ['date' => 'Feb 09', 'time'=>'11:00 AM', 'cat' => 'Technical', 'status' => 'Pending', 'score' => 74],
                                    ['date' => 'Feb 08', 'time'=>'02:30 PM', 'cat' => 'Billing', 'status' => 'Resolved', 'score' => 88],
                                ];
                            @endphp
                            @foreach($evalLogs as $log)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ $log['date'] }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $log['time'] }}</div>
                                </td>
                                <td><span class="badge bg-light text-secondary border fw-bold" style="font-size: 10px;">{{ $log['cat'] }}</span></td>
                                <td>
                                    @php 
                                        $sc = $log['status'] == 'Resolved' ? 'success' : ($log['status'] == 'Escalated' ? 'danger' : 'warning');
                                    @endphp
                                    <span class="status-pill" style="background: var(--bs-soft-{{ $sc }}); color: var(--bs-{{ $sc }});">
                                        {{ $log['status'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-900" style="color: {{ getSoftPctColor($log['score']) }}">{{ $log['score'] }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const historyData = @json($agent['history'] ?? []);
        
        const options = {
            series: [{
                name: 'Performance',
                data: historyData.map(item => item.score || item.value)
            }],
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, colors: ['#0a66c2'] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: historyData.map(item => item.date),
                labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
            },
            grid: { borderColor: '#f1f5f9' },
            tooltip: { theme: 'dark' }
        };

        const chart = new ApexCharts(document.querySelector("#performanceTrendChart"), options);
        chart.render();
    });
</script>
@endpush