@extends('user.layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-dashboard.css') }}">
<style>
    .btn-period.active {
        background-color: #0d6efd;
        color: white;
    }
    .trend-up    { color: #198754; }
    .trend-down  { color: #dc3545; }
    .trend-neutral { color: #6c757d; }

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
        margin-bottom: 20px;
    }
    .dashboard-card {
        border-radius: 12px;
        border: none;
        background-color: #fff;
    }
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chart-container {
        position: relative;
        height: 300px;
        padding: 1.25rem;
    }
    .sentiment-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 50rem;
        font-size: 0.75rem;
    }
    .agent-card {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        border: 1px solid #f1f1f1;
    }
    .agent-card.top-agent {
        background-color: rgba(25, 135, 84, 0.05);
        border-left: 4px solid #198754;
    }
    .agent-card.low-agent {
        background-color: rgba(220, 53, 69, 0.05);
        border-left: 4px solid #dc3545;
    }
    .top-performer-badge, .high-achiever-badge, .consistent-badge, .needs-improvement-badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.6rem;
        border-radius: 50rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .top-performer-badge {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754 !important;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }
    .high-achiever-badge {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd !important;
        border: 1px solid rgba(13, 110, 253, 0.2);
    }
    .consistent-badge {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0 !important;
        border: 1px solid rgba(13, 202, 240, 0.2);
    }
    .needs-improvement-badge {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545 !important;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }
    .top-performer-badge:hover, .high-achiever-badge:hover,
    .consistent-badge:hover,  .needs-improvement-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        color: inherit !important;
    }
    .progress-thin { height: 8px; }
    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    /* Intelligence Engine Modal & Overlay */
    .premium-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }
    .upload-zone {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .upload-zone:hover {
        border-color: #4361ee;
        background: #f8f9ff;
    }
    #ai-processing-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 14, 33, 0.95);
        backdrop-filter: blur(10px);
        z-index: 9999;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    #ai-processing-overlay.active { display: flex; }
    .ai-orb {
        width: 100px; height: 100px; border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #6366f1, #3046bc);
        animation: orbPulse 2s infinite;
        display: flex; align-items: center; justify-content: center; margin-bottom: 25px;
    }
    .ai-orb i { font-size: 38px; color: #fff; }
    @keyframes orbPulse {
        0%   { box-shadow: 0 0 0 0 rgba(99,102,241,0.5); }
        70%  { box-shadow: 0 0 0 30px rgba(99,102,241,0); }
        100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
    }
    .waveform { display: flex; align-items: center; gap: 5px; margin-bottom: 30px; }
    .waveform .bar { width: 4px; border-radius: 4px; background: #6366f1; animation: wave 1.2s infinite; }
    @keyframes wave { 0%, 100% { transform: scaleY(0.5); } 50% { transform: scaleY(1); } }
    .ai-step { display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 10px; }
    .ai-step.active { color: #fff; }
    .ai-step.done   { color: #22c55e; }

    /* Searchable Agent Selector */
    .agent-selector-container {
        position: relative;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .agent-selector-container:focus-within {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
    .agent-selector-display {
        padding: 0.75rem 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 15px;
        min-height: 52px;
    }
    .agent-selector-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-top: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        z-index: 1060;
        display: none;
        overflow: hidden;
    }
    .agent-selector-dropdown.show { display: block; }
    .agent-search-wrapper {
        padding: 12px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .agent-options-list {
        max-height: 250px;
        overflow-y: auto;
    }
    .agent-option {
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.1s;
    }
    .agent-option:hover { background: #f1f5f9; }
    .agent-option.selected { background: #eff6ff; }
    .agent-option.d-none { display: none !important; }
    .agent-opt-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 12px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    @cannot('dashboard.view')
        <div class="row min-vh-75 d-flex align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card border-0 shadow-sm p-5">
                    <div class="icon-circle bg-soft-danger mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-lock text-danger fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">Restricted Access</h3>
                    <p class="text-muted mb-4">You do not have the necessary permissions to view the Global Analysis Dashboard. Please contact your system administrator if you believe this is an error.</p>
                    <a href="{{ url()->previous() }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i> Return Back
                    </a>
                </div>
            </div>
        </div>
    @else

    {{-- ── Page Header ────────────────────────────────────────────────────── --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Global Analysis Dashboard</h4>
                <p class="text-muted mb-0">Combined overview across all {{ $totalCompanies }} companies</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('user.company.list') }}"
                   class="btn btn-outline-secondary fw-600 shadow-sm d-flex align-items-center justify-content-center"
                   style="height:38px;">
                    <i class="fas fa-building me-2"></i> Companies
                </a>
                <button type="button"
                        class="btn btn-outline-secondary fw-600 shadow-sm d-flex align-items-center justify-content-center"
                        data-bs-toggle="modal" data-bs-target="#audioUploadModal"
                        style="height:38px;">
                    <i class="fas fa-plus me-2"></i> Upload &amp; Analyze Audio
                </button>
            </div>
        </div>
    </div>

    {{-- ── KPI Cards ───────────────────────────────────────────────────────── --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-primary border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Active Agents</p>
                            <h3 class="metric-value mb-1 fw-bold text-primary">{{ $activeAgents }}</h3>
                            <small class="text-muted">Across {{ $totalCompanies }} companies</small>
                        </div>
                        <div class="icon-circle bg-soft-primary">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-success border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Quality Score</p>
                            <h3 class="metric-value mb-1 fw-bold text-success">{{ number_format($avgQualityScore, 1) }}%</h3>
                            <small class="trend-up"><i class="fas fa-arrow-up me-1"></i> {{ number_format($scoreImprovement, 1) }}% improvement</small>
                        </div>
                        <div class="icon-circle bg-soft-success">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-info border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Evaluated Calls</p>
                            <h3 class="metric-value mb-1 fw-bold text-info">{{ $callsEvaluated }}</h3>
                            <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> {{ $callsThisWeekCount }} this week</small>
                        </div>
                        <div class="icon-circle bg-soft-info">
                            <i class="fas fa-phone-alt text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-danger border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Risk Detected</p>
                            <h3 class="metric-value mb-1 fw-bold text-danger">{{ $totalRisks }}</h3>
                            <small class="text-muted">Total flagged cases</small>
                        </div>
                        <div class="icon-circle bg-soft-danger">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROI / Financial Impact ──────────────────────────────────────────── --}}
    <div class="row g-4 mb-4 mt-1">

        {{-- Human Time Saved --}}
        <div class="col-md-4">
            <div class="dashboard-card h-100 shadow-soft border-start border-primary border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-circle bg-soft-primary me-3">
                            <i class="fas fa-clock text-primary"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">Human Time Saved</h6>
                    </div>
                    <div class="mt-2">
                        <h3 class="metric-value mb-0 fw-bold text-primary">
                            {{ number_format($roiStats['hours_saved'], 1) }} Hours
                        </h3>
                        <p class="text-muted small mb-0">
                            Based on <strong>85 min/day</strong> capacity.
                            Equivalent to
                            <strong>{{ number_format($roiStats['manual_days'], 1) }}</strong>
                            manual working days.
                        </p>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Formula: (total call seconds × 3) ÷ 60 ÷ 85 min/day × 8 hr/day
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Savings --}}
        <div class="col-md-4">
            <div class="dashboard-card h-100 shadow-soft border-start border-success border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-circle bg-soft-success me-3">
                            <i class="fas fa-hand-holding-usd text-success"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">Financial Savings</h6>
                    </div>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">
                                Estimated Manual
                                <i class="fas fa-info-circle text-muted ms-1"
                                   data-bs-toggle="tooltip"
                                   title="Human hours × $25/hr rate"></i>
                            </span>
                            <span class="fw-bold text-dark">${{ number_format($roiStats['manual_cost'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">
                                Evalia AI Cost
                                <i class="fas fa-info-circle text-muted ms-1"
                                   data-bs-toggle="tooltip"
                                   title="{{ $callsEvaluated }} calls × $0.50/call"></i>
                            </span>
                            <span class="fw-bold text-success">${{ number_format($roiStats['evalia_cost'], 2) }}</span>
                        </div>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Net Savings:</span>
                            <h4 class="text-success fw-bold mb-0">+${{ number_format($roiStats['net_savings'], 2) }}</h4>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Formula: Manual Cost − Evalia Cost
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROI Indicator --}}
        <div class="col-md-4">
            <div class="dashboard-card h-100 shadow-soft border-start border-info border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="icon-circle bg-soft-info me-3">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">ROI Indicator</h6>
                    </div>
                    <div class="mt-2">
                        <h3 class="metric-value mb-1 fw-bold text-info">
                            {{ number_format($roiStats['roi_percent'], 0) }}%
                        </h3>
                        <div class="progress progress-thin mb-2">
                            <div class="progress-bar bg-info"
                                 role="progressbar"
                                 style="width: {{ min($roiStats['roi_percent'], 100) }}%"
                                 aria-valuenow="{{ $roiStats['roi_percent'] }}"
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <p class="text-muted small mb-0">
                            Efficiency gain vs human capital.
                            Volume: <strong>{{ $callsEvaluated }} calls</strong>.
                        </p>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Formula: (Net Savings ÷ Evalia Cost) × 100
                        </small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Performance Trend + Sentiment ─────────────────────────────────── --}}
    <div class="row g-4 mb-4">

        <div class="col-md-8">
            <div class="section-card h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title trend-title mb-0">Performance Trend — Last 7 Days</div>
                    <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                        <button class="btn btn-period active border-0 px-3" data-granularity="daily"   style="font-size:11px;">Daily</button>
                        <button class="btn btn-period border-0 px-3"        data-granularity="weekly"  style="font-size:11px;">Weekly</button>
                        <button class="btn btn-period border-0 px-3"        data-granularity="monthly" style="font-size:11px;">Monthly</button>
                    </div>
                </div>
                <div id="trendChart" style="height:230px; width:100%;"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">Customer Sentiment Analysis</h6>
                </div>
                <div class="chart-container">
                    <canvas id="sentimentChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3 pb-3" id="sentimentBadges">
                    {{-- populated by JS --}}
                </div>
            </div>
        </div>

    </div>

    {{-- ── Recent Analyses + Agent Performance ────────────────────────────── --}}
    <div class="row g-4">

        {{-- Recent Analyses Table --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-600 d-flex align-items-center">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Recent Analyses
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Task ID</th>
                                    <th>Agent</th>
                                    <th>Score</th>
                                    <th>Sentiment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($taskList as $task)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">#{{ $task->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar-title rounded-circle bg-primary-soft text-primary fw-bold me-2"
                                                      style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;background:#eef2ff;">
                                                    {{ strtoupper(substr($task->agent->name ?? 'U', 0, 1)) }}
                                                </span>
                                                @if($task->agent)
                                                    <a href="{{ route('user.agents.show', ['agentId' => $task->agent_id]) }}" class="small fw-medium text-dark text-decoration-none hover-primary">
                                                        {{ $task->agent->name }}
                                                    </a>
                                                @else
                                                    <span class="small fw-medium">{{ $task->agent->name ?? 'Unassigned' }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($task->score > 0)
                                                <span class="fw-bold {{ $task->score >= 80 ? 'text-success' : ($task->score >= 60 ? 'text-warning' : 'text-danger') }}">
                                                    {{ $task->score }}%
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $sent = $task->sentiment ?? 'Neutral'; @endphp
                                            <span class="badge rounded-pill {{ $sent === 'Positive' ? 'bg-success-subtle text-success' : ($sent === 'Negative' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary') }}">
                                                {{ $sent }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-opacity-10
                                                @if(in_array($task->status, ['completed','evaluated'])) bg-success text-success
                                                @elseif($task->status === 'processing') bg-warning text-warning
                                                @else bg-secondary text-secondary @endif
                                                rounded-pill py-1 px-3">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="small fw-medium">{{ $task->created_at->format('M j, Y') }}</span>
                                                <span class="text-muted small">{{ $task->created_at->format('g:i A') }}</span>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('user.task.details', $task->id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user.task.delete', $task->id) }}"
                                                   class="btn btn-sm btn-outline-danger" title="Delete Task"
                                                   onclick="return confirm('Delete this task?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                            No tasks found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($taskList->count() >= 10)
                <div class="card-footer bg-white border-top py-3 text-center">
                    <a href="{{ route('user.task.list') }}" class="btn btn-sm btn-light fw-600">
                        View All Analyses <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Agent Performance --}}
        <div class="col-md-6">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">
                            <a href="{{ route('user.agents.index') }}" class="text-dark text-decoration-none hover-primary">
                                Agent Performance <i class="fas fa-external-link-alt ms-1 small opacity-50"></i>
                            </a>
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    type="button" id="agentSortDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                Top Performers
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="agentSortDropdown">
                                <li><a class="dropdown-item agent-sort active" href="#" data-sort="top">Top Performers (>80%)</a></li>
                                <li><a class="dropdown-item agent-sort" href="#" data-sort="needs-improvement">Needs Improvement (<80%)</a></li>
                                <li><a class="dropdown-item agent-sort" href="#" data-sort="all">All Agents</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3" id="agentPerformance" style="height: 480px; overflow-y: auto;">
                    {{-- populated by JS --}}
                </div>
                <div class="card-footer bg-white border-top py-2 text-center">
                    <a href="{{ route('user.agents.index') }}" class="btn btn-link btn-sm text-primary fw-bold text-decoration-none">
                        View All Agents <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Intelligence Engine Modal ──────────────────────────────────────────── --}}
<div class="modal fade" id="audioUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-slate-900">Intelligence Engine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="transcription-form" action="{{ route('user.task.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-2">1. Audio Source</label>
                        <label class="upload-zone w-100 py-5 text-center mb-0">
                            <i class="fas fa-cloud-upload-alt fs-2 text-muted d-block mb-2"></i>
                            <span class="fw-bold text-dark" id="upload-filename">Browse Record</span>
                            <span class="text-muted d-block small">MP3, WAV, M4A (Max 100MB)</span>
                            <input type="file" name="audio_file" class="d-none" accept="audio/*" required
                                   onchange="document.getElementById('upload-filename').textContent = this.files[0].name;
                                             document.getElementById('upload-filename').className='fw-bold text-primary';">
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-2">2. Select Company</label>
                        <select name="company_id" id="modal-company-select"
                                class="form-select form-select-lg py-3 border-2"
                                style="border-radius:12px; font-size:15px;" required>
                            <option value="">Choose Company...</option>
                            @foreach(\App\Models\Company::orderBy('company_name')->get() as $co)
                                <option value="{{ $co->id }}">{{ $co->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-2">3. Workforce Assignment</label>
                        <div class="agent-selector-container" id="agent-selector-container">
                            <div class="agent-selector-display" id="agent-selector-display">
                                <span class="text-muted" id="selected-agent-name">Choose Agent...</span>
                                <i class="fas fa-chevron-down small opacity-50"></i>
                            </div>
                            <div class="agent-selector-dropdown" id="agent-selector-dropdown">
                                <div class="agent-search-wrapper">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted small"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="agent-selector-search" placeholder="Search agent name...">
                                    </div>
                                </div>
                                <div class="agent-options-list" id="agent-options-list">
                                    @foreach($companyAgents as $agent)
                                        <div class="agent-option" 
                                             data-id="{{ $agent['id'] }}" 
                                             data-company-id="{{ $agent['company_id'] }}"
                                             data-name="{{ strtolower($agent['full_name']) }}">
                                            <div class="agent-opt-avatar bg-primary-soft text-primary" style="background:#eef2ff;">
                                                {{ strtoupper(substr($agent['full_name'] ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-medium small text-dark">{{ $agent['full_name'] }}</div>
                                                <div class="text-muted" style="font-size: 10px;">{{ $agent['company_name'] }}</div>
                                            </div>
                                            <i class="fas fa-check text-primary small d-none check-icon"></i>
                                        </div>
                                    @endforeach
                                    <div id="no-agents-found" class="p-3 text-center text-muted small d-none">No agents found</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="agent_id" id="modal-agent-id-hidden" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm rounded-3"
                            style="background: linear-gradient(135deg, #4361ee 0%, #3046bc 100%); border:none;">
                        <i class="fas fa-microchip me-2"></i>Initiate AI Analysis
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- AI Processing Overlay --}}
<div id="ai-processing-overlay">
    <div class="ai-orb"><i class="fas fa-wave-square"></i></div>
    <div class="ai-title text-white">AI Engine Processing</div>
    <p class="text-white-50 mb-4 small">Please keep this window open. Analyzing patterns...</p>
    <div class="waveform">
        <div class="bar" style="height:20px; animation-delay:0s;"></div>
        <div class="bar" style="height:40px; animation-delay:0.1s;"></div>
        <div class="bar" style="height:30px; animation-delay:0.2s;"></div>
        <div class="bar" style="height:50px; animation-delay:0.3s;"></div>
        <div class="bar" style="height:35px; animation-delay:0.4s;"></div>
    </div>
    <div class="ai-steps">
        <div class="ai-step done"><i class="fas fa-check-circle"></i><span>Audio file uploaded</span></div>
        <div class="ai-step active" id="step-transcribe"><i class="fas fa-dot-circle"></i><span>Transcribing speech...</span></div>
        <div class="ai-step"        id="step-analyze">  <i class="fas fa-dot-circle"></i><span>Deep Analysis &amp; NLP...</span></div>
        <div class="ai-step"        id="step-save">     <i class="fas fa-dot-circle"></i><span>Saving Intelligence...</span></div>
    </div>
    @endcannot
</div>
@endsection

@push('scripts')
@can('dashboard.view')
<script>
    // ── Server data passed from controller ────────────────────────────────────
    const serverTrendData    = @json($trendData);
    const serverSentimentData = @json($sentimentStats);
    const phpAgents          = @json($companyAgents);

    // ── State ─────────────────────────────────────────────────────────────────
    let trendChart       = null;
    let sentimentChart   = null;
    let currentGranularity = 'daily';
    let currentAgentSort   = 'top';

    // ── Boot ──────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        updateTrendChart();
        updateSentimentChart();
        updateAgentPerformance();
        setupEventListeners();
        initTooltips();

        // AI overlay on form submit
        const form    = document.getElementById('transcription-form');
        const overlay = document.getElementById('ai-processing-overlay');
        if (form) {
            form.addEventListener('submit', function () {
                overlay.classList.add('active');
                setTimeout(() => {
                    const s1 = document.getElementById('step-transcribe');
                    if (s1) { s1.classList.replace('active','done'); s1.querySelector('i').className = 'fas fa-check-circle'; }
                    const s2 = document.getElementById('step-analyze');
                    if (s2) s2.classList.add('active');
                }, 4000);
                setTimeout(() => {
                    const s2 = document.getElementById('step-analyze');
                    if (s2) { s2.classList.replace('active','done'); s2.querySelector('i').className = 'fas fa-check-circle'; }
                    const s3 = document.getElementById('step-save');
                    if (s3) s3.classList.add('active');
                }, 8000);
            });
        }
    });

    // ── Event listeners ───────────────────────────────────────────────────────
    function setupEventListeners() {
        // Trend period buttons
        document.querySelectorAll('.btn-period').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.btn-period').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentGranularity = this.getAttribute('data-granularity');
                updateTrendChart();
            });
        });

        // Agent sort dropdown
        document.querySelectorAll('.agent-sort').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelectorAll('.agent-sort').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentAgentSort = this.getAttribute('data-sort');
                document.getElementById('agentSortDropdown').textContent = this.textContent;
                updateAgentPerformance();
            });
        });

        // Searchable Agent Selector Logic
        const container = document.getElementById('agent-selector-container');
        const display = document.getElementById('agent-selector-display');
        const dropdown = document.getElementById('agent-selector-dropdown');
        const search = document.getElementById('agent-selector-search');
        const optionsList = document.getElementById('agent-options-list');
        const hiddenInput = document.getElementById('modal-agent-id-hidden');
        const selectedName = document.getElementById('selected-agent-name');
        const companySelect = document.getElementById('modal-company-select');

        if (display) {
            display.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
                if (dropdown.classList.contains('show')) search.focus();
            });

            dropdown.addEventListener('click', (e) => e.stopPropagation());

            document.addEventListener('click', (e) => {
                dropdown.classList.remove('show');
            });

            search.addEventListener('input', () => {
                const query = search.value.toLowerCase().trim();
                const currentCoId = companySelect.value;
                let foundAny = false;

                container.querySelectorAll('.agent-option').forEach(opt => {
                    const name = opt.getAttribute('data-name');
                    const coId = opt.getAttribute('data-company-id');
                    const matchesSearch = name.includes(query);
                    const matchesCompany = !currentCoId || coId == currentCoId;

                    if (matchesSearch && matchesCompany) {
                        opt.classList.remove('d-none');
                        foundAny = true;
                    } else {
                        opt.classList.add('d-none');
                    }
                });

                const noFoundDiv = document.getElementById('no-agents-found');
                if (noFoundDiv) {
                    noFoundDiv.classList.toggle('d-none', foundAny);
                }
            });

            container.querySelectorAll('.agent-option').forEach(opt => {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.getAttribute('data-id');
                    const name = this.querySelector('.fw-medium').textContent;

                    hiddenInput.value = id;
                    selectedName.textContent = name;
                    selectedName.classList.remove('text-muted');
                    selectedName.classList.add('text-dark');
                    
                    container.querySelectorAll('.agent-option').forEach(o => {
                        o.classList.remove('selected');
                        const check = o.querySelector('.check-icon');
                        if (check) check.classList.add('d-none');
                    });
                    
                    this.classList.add('selected');
                    const check = this.querySelector('.check-icon');
                    if (check) check.classList.remove('d-none');
                    dropdown.classList.remove('show');
                });
            });

            if (companySelect) {
                companySelect.addEventListener('change', function() {
                    // Reset selection if current agent is from another company
                    const currentId = hiddenInput.value;
                    if (currentId) {
                        const currentOpt = container.querySelector(`.agent-option[data-id="${currentId}"]`);
                        if (currentOpt && this.value && currentOpt.getAttribute('data-company-id') != this.value) {
                            hiddenInput.value = '';
                            selectedName.textContent = 'Choose Agent...';
                            selectedName.classList.add('text-muted');
                            selectedName.classList.remove('text-dark');
                            currentOpt.classList.remove('selected');
                            const check = currentOpt.querySelector('.check-icon');
                            if (check) check.classList.add('d-none');
                        }
                    }
                    // Refresh search view for company filter
                    search.dispatchEvent(new Event('input'));
                });
            }
        }
    }

    function initTooltips() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    // ── Performance Trend Chart ───────────────────────────────────────────────
    function updateTrendChart() {
        const titleMap = {
            daily:   'Performance Trend — Last 7 Days',
            weekly:  'Performance Trend — Last 12 Weeks',
            monthly: 'Performance Trend — Last 12 Months',
        };

        const titleEl = document.querySelector('.section-title.trend-title');
        if (titleEl) titleEl.textContent = titleMap[currentGranularity];

        const dataSet = serverTrendData[currentGranularity] || [];
        const labels  = dataSet.map(d => d.label);
        const values  = dataSet.map(d => d.value);

        const options = {
            series: [{ name: 'Score', data: values }],
            chart: {
                type: 'area',
                height: 230,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5, colors: ['#6366f1'] },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.02, stops: [0, 95, 100] },
            },
            colors: ['#6366f1'],
            xaxis: {
                categories: labels,
                labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks:  { show: false },
            },
            yaxis: {
                min: 0, max: 100,
                labels: {
                    style: { colors: '#94a3b8', fontSize: '11px' },
                    formatter: v => Math.round(v) + '%',
                },
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { theme: 'light', y: { formatter: v => v + '%' } },
            markers: { size: 4, colors: ['#6366f1'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 6 } },
        };

        const container = document.querySelector('#trendChart');
        if (container) {
            container.innerHTML = '';
            new ApexCharts(container, options).render();
        }
    }

    // ── Sentiment Doughnut ────────────────────────────────────────────────────
    function updateSentimentChart() {
        const ctx  = document.getElementById('sentimentChart').getContext('2d');
        const data = serverSentimentData;

        if (sentimentChart) sentimentChart.destroy();

        sentimentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [data.positive, data.neutral, data.negative],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                    ],
                    borderColor: ['rgb(25,135,84)', 'rgb(255,193,7)', 'rgb(220,53,69)'],
                    borderWidth: 1,
                    hoverOffset: 10,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Inter' } } },
                },
                cutout: '70%',
            },
        });

        document.getElementById('sentimentBadges').innerHTML = `
            <span class="sentiment-badge bg-success bg-opacity-10 text-success">${data.positive}% Positive</span>
            <span class="sentiment-badge bg-warning bg-opacity-10 text-warning">${data.neutral}% Neutral</span>
            <span class="sentiment-badge bg-danger  bg-opacity-10 text-danger">${data.negative}% Negative</span>
        `;
    }

    // ── Agent Performance Cards ───────────────────────────────────────────────
    function updateAgentPerformance() {
        let allAgents = phpAgents.map(a => ({
            id:        a.id,
            name:      a.full_name || a.name,
            company:   a.company_name,
            score:     a.score,
            sentiment: a.sentiment,
            calls:     a.calls,
            risks:     a.risks,
            avatar:    a.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(a.full_name || a.name)}&background=random&color=fff`,
        }));

        let filtered = [];
        if (currentAgentSort === 'top') {
            filtered = allAgents.filter(a => a.calls > 0 && a.score >= 80);
            filtered.sort((a, b) => b.score - a.score);
        } else if (currentAgentSort === 'needs-improvement') {
            filtered = allAgents.filter(a => a.calls > 0 && a.score < 80);
            filtered.sort((a, b) => a.score - b.score);
        } else if (currentAgentSort === 'all') {
            filtered = allAgents;
            filtered.sort((a, b) => b.score - a.score);
        } else {
            filtered = allAgents.filter(a => a.calls > 0);
            filtered.sort((a, b) => b.score - a.score);
        }

        const agents = filtered; // Show all filtered agents since we have scrolling now


        const container = document.getElementById('agentPerformance');
        if (!agents.length) {
            container.innerHTML = `<div class="text-center text-muted py-4"><i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>No agents found</div>`;
            return;
        }

        let html = '';
        agents.forEach(agent => {
            const sentClass = agent.sentiment === 'Positive' ? 'text-success'
                            : agent.sentiment === 'Negative' ? 'text-danger'
                            : 'text-info';

            const badge = agent.risks > 0
                ? `<span class="needs-improvement-badge ms-2"><i class="fas fa-exclamation-triangle me-1"></i>${agent.risks} Risks</span>`
                : `<span class="top-performer-badge ms-2"><i class="fas fa-check-circle me-1"></i>0 Risks</span>`;

            const agentUrl = '{{ route('user.agents.show', ['agentId' => ':id']) }}'.replace(':id', agent.id);

            html += `
                <div class="agent-card ${agent.score >= 90 ? 'top-agent' : ''} ${agent.score < 75 ? 'low-agent' : ''} shadow-sm border-0 mb-3">
                    <div class="d-flex align-items-center">
                        <img src="${agent.avatar}" class="avatar-sm me-3 border shadow-sm" alt="${agent.name}">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold d-flex align-items-center flex-wrap">
                                <a style="color:#000;" href="${agentUrl}">${agent.name}</a>
                                ${badge}
                            </h6>
                            <small class="text-muted">${agent.company} &bull; ${agent.calls} calls</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 ${agent.score >= 90 ? 'text-success' : (agent.score < 75 ? 'text-danger' : '')} fw-bold">
                                ${agent.score}%
                            </h5>
                            <small class="${sentClass} fw-medium">${agent.sentiment} Sentiment</small>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }
</script>
@endcan
@endpush