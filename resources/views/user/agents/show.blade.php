@extends('user.layouts.app')

@push('styles')
<style>
    body { background: #f5f6fa; }

    .section-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8edf2;
        padding: 24px;
    }

    .section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 16px;
    }

    /* Agent Profile Sidebar */
    .agent-avatar-lg {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        font-size: 28px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row i { color: #94a3b8; width: 16px; text-align: center; }
    .info-row-label { color: #94a3b8; font-size: 11px; font-weight: 600; }
    .info-row-value { color: #1e293b; font-weight: 700; }

    /* KPI Cards */
    .kpi-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e8edf2;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .kpi-value { font-size: 22px; font-weight: 900; color: #1e293b; line-height: 1; }
    .kpi-label { font-size: 11px; font-weight: 600; color: #94a3b8; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Metric Bars */
    .metric-row { margin-bottom: 16px; }
    .metric-row:last-child { margin-bottom: 0; }
    .metric-bar-bg {
        height: 7px;
        border-radius: 8px;
        background: #f1f5f9;
        overflow: hidden;
        margin-top: 6px;
    }
    .metric-bar-fill { height: 100%; border-radius: 8px; transition: width 1s ease; }

    /* Task Table */
    .task-table th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #94a3b8;
        background: #f8fafc;
        padding: 10px 14px;
        border-bottom: 1px solid #e8edf2;
        white-space: nowrap;
    }
    .task-table td {
        font-size: 13px;
        padding: 12px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .task-table tr:last-child td { border-bottom: none; }
    .task-table tr:hover td { background: #f8fafc; }
    .task-table-wrap { max-height: 420px; overflow-y: auto; border-radius: 10px; border: 1px solid #e8edf2; }

    .badge-status {
        font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px; text-transform: capitalize;
    }
    .badge-evaluated { background: #dcfce7; color: #15803d; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-processing { background: #dbeafe; color: #1e40af; }
    .badge-failed { background: #fee2e2; color: #991b1b; }

    .score-chip {
        font-size: 13px; font-weight: 800;
        padding: 3px 12px; border-radius: 20px;
        display: inline-block;
    }
    .risk-pill {
        font-size: 10px; font-weight: 700;
        padding: 3px 9px; border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .risk-high { background: #fee2e2; color: #b91c1c; }
    .risk-no   { background: #f1f5f9; color: #475569; }
    .risk-na   { background: #f8fafc; color: #cbd5e1; }

    .ai-tip {
        background: #f8fafc;
        border-left: 3px solid #6366f1;
        padding: 14px 16px;
        border-radius: 0 10px 10px 0;
        font-size: 13px;
        color: #475569;
        line-height: 1.65;
    }

    .topic-tag {
        display: inline-block;
        background: #eff6ff;
        color: #3b82f6;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        margin: 2px 3px 2px 0;
        border: 1px solid #bfdbfe;
    }
</style>
@endpush

@section('content')
@php
    function getSoftPctColor($val) {
        if ($val >= 90) return '#10b981';
        if ($val >= 75) return '#3b82f6';
        if ($val >= 60) return '#f59e0b';
        return '#ef4444';
    }
    $score       = $agent['current_scores']['overall_score']   ?? 0;
    $prof        = $agent['current_scores']['professionalism']  ?? 0;
    $assessment  = $agent['current_scores']['assessment']       ?? 0;
    $cooperation = $agent['current_scores']['cooperation']      ?? 0;
    $compliance  = $agent['current_scores']['compliance_rate']  ?? 100;
    $totalCalls  = $agent['summary']['total_calls']             ?? 0;
    $risks       = $agent['summary']['risks_detected']          ?? 0;
    $csat        = $agent['summary']['satisfaction_rate']       ?? 0;
@endphp

<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0" style="font-size:13px;">
            <li class="breadcrumb-item"><a href="{{ route('user.agents.index') }}" class="text-decoration-none text-muted">Agents</a></li>
            <li class="breadcrumb-item active text-dark fw-600">{{ $agent['agent_details']['name'] }}</li>
        </ol>
    </nav>

    <div class="row g-4">

        {{-- ===== SIDEBAR ===== --}}
        <div class="col-lg-3">

            {{-- Profile --}}
            <div class="section-card mb-4 text-center">
                <div class="agent-avatar-lg mx-auto mb-3">
                    {{ strtoupper(substr($agent['agent_details']['name'] ?? 'A', 0, 1)) }}
                </div>
                <h5 class="fw-800 mb-1" style="font-size:17px;">{{ $agent['agent_details']['name'] }}</h5>
                <p class="text-muted mb-2" style="font-size:12px;">{{ $agent['agent_details']['position'] ?? 'Customer Service Agent' }}</p>

                @if($agent['agent_details']['is_active'] ?? true)
                    <span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;display:inline-block;">
                        <i class="fas fa-circle" style="font-size:6px;"></i> ACTIVE
                    </span>
                @else
                    <span style="background:#f1f5f9;color:#64748b;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;display:inline-block;">
                        INACTIVE
                    </span>
                @endif

                <hr class="my-3" style="border-color:#f1f5f9;">
                <div class="text-start">
                    <div class="info-row">
                        <i class="fas fa-building"></i>
                        <div>
                            <div class="info-row-label">Company</div>
                            <div class="info-row-value">{{ $agent['agent_details']['company_name'] ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <div class="info-row-label">Email</div>
                            <div class="info-row-value text-break" style="font-size:12px;">{{ $agent['agent_details']['email'] ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-id-badge"></i>
                        <div>
                            <div class="info-row-label">Agent ID</div>
                            <div class="info-row-value">{{ $agent['agent_details']['display_id'] ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AI Suggestion --}}
            <div class="section-card">
                <div class="section-title"><i class="fas fa-robot me-1"></i> AI Insight</div>
                <div class="ai-tip">
                    @if($assessment < 75)
                        <strong>🔧 Training Needed:</strong> Knowledge base alignment is below average. Recommend targeted retraining sessions.
                    @elseif($cooperation < 75)
                        <strong>🤝 Improve Cooperation:</strong> Focus on empathetic communication and soft-skill development.
                    @elseif($score >= 90)
                        <strong>⭐ Excellent Performance:</strong> Agent is performing at a high level. Maintain consistency.
                    @else
                        <strong>📈 Keep Improving:</strong> Overall performance is good. Continue monitoring for consistency.
                    @endif
                </div>

                @if(count($agent['top_topics'] ?? []) > 0)
                <div class="mt-3">
                    <div class="section-title mb-2">Top KB Topics</div>
                    @foreach($agent['top_topics'] as $topic)
                        <span class="topic-tag">{{ $topic }}</span>
                    @endforeach
                </div>
                @endif
            </div>

        </div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="col-lg-9">

            {{-- KPI Row --}}
            <div class="row g-3 mb-4">
                @foreach([
                    ['icon'=>'fa-phone-alt', 'bg'=>'#eff6ff', 'color'=>'#3b82f6', 'val'=>$totalCalls, 'label'=>'Total Calls'],
                    ['icon'=>'fa-star',      'bg'=>'#fefce8', 'color'=>'#ca8a04', 'val'=>$score.'%', 'label'=>'Avg Score'],
                    ['icon'=>'fa-shield-alt','bg'=>'#ecfdf5', 'color'=>'#10b981', 'val'=>$compliance.'%', 'label'=>'Compliance'],
                    ['icon'=>'fa-flag',      'bg'=>'#fef2f2', 'color'=>'#ef4444', 'val'=>$risks, 'label'=>'Risk Flags'],
                ] as $kpi)
                <div class="col-6 col-xl-3">
                    <div class="kpi-card">
                        <div class="kpi-icon" style="background:{{ $kpi['bg'] }};color:{{ $kpi['color'] }};">
                            <i class="fas {{ $kpi['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="kpi-value" style="color:{{ $kpi['color'] }}">{{ $kpi['val'] }}</div>
                            <div class="kpi-label">{{ $kpi['label'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Metrics + Chart --}}
            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    <div class="section-card h-100">
                        <div class="section-title">Performance Breakdown</div>

                        @foreach([
                            ['label' => 'Professionalism', 'val' => $prof],
                            ['label' => 'Assessment',      'val' => $assessment],
                            ['label' => 'Cooperation',     'val' => $cooperation],
                        ] as $m)
                        <div class="metric-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:13px;font-weight:600;color:#475569;">{{ $m['label'] }}</span>
                                <span style="font-size:13px;font-weight:800;color:{{ getSoftPctColor($m['val']) }}">{{ $m['val'] }}%</span>
                            </div>
                            <div class="metric-bar-bg">
                                <div class="metric-bar-fill" style="width:{{ $m['val'] }}%;background:{{ getSoftPctColor($m['val']) }};"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex align-items-center gap-2 mt-4 pt-3" style="border-top:1px solid #f1f5f9;">
                            <div style="font-size:26px;font-weight:900;color:{{ getSoftPctColor($score) }}">{{ $score }}%</div>
                            <div>
                                <div style="font-size:11px;font-weight:700;color:#94a3b8;">OVERALL SCORE</div>
                                <div style="font-size:11px;color:#94a3b8;">Average of 3 categories</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="section-card h-100">
                        <div class="section-title">Performance Trend — Last 7 Days</div>
                        <div id="trendChart"></div>
                    </div>
                </div>
            </div>

            {{-- Task History Table --}}
            <div class="section-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="section-title mb-0">Call History</div>
                    <span class="text-muted" style="font-size:12px;">{{ count($agent['task_list'] ?? []) }} records</span>
                </div>

                <div class="task-table-wrap">
                    <table class="table mb-0 task-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th class="text-center">Sentiment</th>
                                <th class="text-center">Risk</th>
                                <th class="text-center">Score</th>
                                <th class="text-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agent['task_list'] as $task)
                            <tr>
                                <td>
                                    <div class="fw-600" style="font-size:13px;">{{ $task['date'] }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $task['time'] }}</div>
                                </td>
                                <td>
                                    @php
                                        $sc = ['evaluated'=>'badge-evaluated','pending'=>'badge-pending','processing'=>'badge-processing','failed'=>'badge-failed'][$task['status']] ?? 'badge-pending';
                                    @endphp
                                    <span class="badge-status {{ $sc }}">{{ ucfirst($task['status']) }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $sent = ['Positive'=>['fa-smile','#10b981'],'Negative'=>['fa-frown','#ef4444'],'Neutral'=>['fa-meh','#94a3b8']][$task['sentiment']] ?? ['fa-meh','#94a3b8'];
                                    @endphp
                                    <i class="fas {{ $sent[0] }}" style="color:{{ $sent[1] }};font-size:16px;" title="{{ $task['sentiment'] }}"></i>
                                </td>
                                <td class="text-center">
                                    @if($task['risk'] === 'High')
                                        <span class="risk-pill risk-high"><i class="fas fa-exclamation-triangle me-1" style="font-size:9px;"></i>High</span>
                                    @elseif($task['risk'] === 'No')
                                        <span class="risk-pill risk-no"><i class="fas fa-check me-1" style="font-size:9px;"></i>Clear</span>
                                    @else
                                        <span class="risk-pill risk-na">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($task['score'] > 0)
                                        <span class="score-chip" style="background:{{ getSoftPctColor($task['score']) }}18; color:{{ getSoftPctColor($task['score']) }};">
                                            {{ $task['score'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size:13px;">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.task.details', $task['id']) }}" 
                                       class="btn btn-sm btn-light border" 
                                       style="font-size:11px;font-weight:700;border-radius:8px;">
                                        View <i class="fas fa-arrow-right ms-1" style="font-size:9px;"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-phone-slash fa-2x text-muted opacity-25 mb-2 d-block"></i>
                                    <span class="text-muted small">No call records found for this agent.</span>
                                </td>
                            </tr>
                            @endforelse
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
document.addEventListener('DOMContentLoaded', function () {
    const history = @json($agent['history'] ?? []);

    const scores = history.map(d => d.score);
    const dates  = history.map(d => d.date);

    const options = {
        series: [{ name: 'Score', data: scores }],
        chart: {
            type: 'area', height: 210,
            toolbar: { show: false },
            sparkline: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2.5, colors: ['#6366f1'] },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.02, stops: [0, 95, 100] }
        },
        colors: ['#6366f1'],
        xaxis: {
            categories: dates,
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0, max: 100,
            labels: { style: { colors: '#94a3b8', fontSize: '11px' }, formatter: v => v + '%' }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: {
            theme: 'light',
            y: { formatter: v => v + '%' }
        },
        markers: { size: 4, colors: ['#6366f1'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 6 } }
    };

    new ApexCharts(document.querySelector('#trendChart'), options).render();
});
</script>
@endpush