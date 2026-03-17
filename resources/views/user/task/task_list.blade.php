@extends('user.layouts.app')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4361ee 0%, #3046bc 100%);
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
    }

    /* Summary Strip */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .icon-blue { background: #eff6ff; color: #1d4ed8; }
    .icon-green { background: #f0fdf4; color: #15803d; }
    .icon-amber { background: #fffbeb; color: #b45309; }
    .icon-red { background: #fef2f2; color: #b91c1c; }

    .summary-value { font-size: 20px; font-weight: 800; color: var(--slate-900); }
    .summary-label { font-size: 11px; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px; }

    /* Modern Filter Bar */
    .modern-filter-bar {
        background: #ffffff;
        border-radius: 12px;
        padding: 15px;
        border: 1px solid var(--slate-200);
        margin-bottom: 25px;
    }

    .filter-input, .filter-select {
        height: 42px;
        border-radius: 8px;
        border: 1px solid var(--slate-200);
        padding: 0 12px;
        font-size: 13px;
        font-weight: 500;
        background-color: var(--slate-50);
        transition: all 0.2s;
    }

    .filter-input:focus, .filter-select:focus {
        border-color: #4361ee;
        background-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    /* Table Improvements */
    .records-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--slate-200);
        overflow: hidden;
    }

    .table-modern thead th {
        background: var(--slate-50);
        padding: 15px 20px;
        font-size: 11px;
        font-weight: 800;
        color: var(--slate-500);
        text-transform: uppercase;
        border-bottom: 1px solid var(--slate-200);
    }

    .table-modern tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        font-size: 13px;
    }

    .agent-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .avatar-sm {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--slate-100);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 10px;
        color: var(--slate-600);
    }

    .badge-risk {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 800;
    }

    .btn-action {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--slate-200);
        background: #ffffff;
        color: var(--slate-600);
        transition: all 0.2s;
    }

    .btn-action:hover {
        border-color: #4361ee;
        color: #4361ee;
        background: #f8f9ff;
    }

    /* ── Conversation Modal ─────────────────────────── */
    .conv-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .conv-header {
        background: linear-gradient(135deg, #4361ee 0%, #3046bc 100%);
        padding: 20px 24px;
        color: #fff;
    }
    .conv-header .badge-live {
        background: rgba(255,255,255,0.2);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }
    .conv-body {
        background: #f8fafc;
        padding: 16px 20px;
        height: 480px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0;
    }
    .conv-body::-webkit-scrollbar { width: 5px; }
    .conv-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* Turn item */
    .trans-turn {
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .trans-turn:last-child { border-bottom: none; }
    .trans-spk-name  { font-size: 0.9rem; font-weight: 700; color: #1e293b; }
    .trans-spk-label { font-size: 0.73rem; color: #94a3b8; margin-top: 1px; }
    .trans-ts        { font-size: 0.78rem; color: #4361ee; font-weight: 600; margin-top: 3px; }
    .trans-text  {
        font-family: 'Cairo', 'Tahoma', sans-serif;
        direction: rtl; text-align: right;
        font-size: 1rem; line-height: 1.7;
        color: #1e293b; margin-top: 8px;
    }
    .trans-turn[style*='none'] { display: none !important; }

    /* search inside modal */
    .conv-search-wrap {
        padding: 12px 20px 4px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }
    .conv-search-wrap .input-group {
        border-radius: 40px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .conv-search-wrap input { background: transparent; border: 0; font-size: 13px; }
    .conv-search-wrap .input-group-text { background: transparent; border: 0; color: #94a3b8; }

    /* speakers summary */
    .conv-speakers { padding: 12px 20px; background: #fff; border-top: 1px solid #f1f5f9; }
    .spk-card {
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 10px; padding: 10px 14px;
    }
    .spk-card .count { font-size: 1.4rem; font-weight: 800; color: #4361ee; }
    .spk-card .label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; }

    .conv-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        gap: 8px;
    }
    .conv-footer {
        padding: 12px 20px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 12px;
        color: #64748b;
    }

    .premium-header {
        background: var(--slate-50);
        border-bottom: 1px solid var(--slate-200);
        padding: 1.25rem 1.5rem;
    }
    .upload-zone {
        border: 2px dashed var(--slate-200);
        background: var(--slate-50);
        border-radius: 10px;
        transition: all 0.2s;
    }
    .upload-zone:hover {
        border-color: #4361ee;
        background: #f8f9ff;
    }

    /* ── AI Processing Overlay ────────────────────────── */
    #ai-processing-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 14, 33, 0.92);
        backdrop-filter: blur(8px);
        z-index: 9999;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
    }
    #ai-processing-overlay.active { display: flex; }

    .ai-orb {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%, #6366f1, #3046bc);
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.5);
        animation: orbPulse 2s infinite;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
    }
    .ai-orb i { font-size: 38px; color: #fff; }

    @keyframes orbPulse {
        0%   { box-shadow: 0 0 0 0 rgba(99,102,241,0.5); }
        70%  { box-shadow: 0 0 0 30px rgba(99,102,241,0); }
        100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
    }

    .ai-title {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }
    .ai-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.5);
        margin-bottom: 40px;
    }

    /* Waveform bars */
    .waveform {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 40px;
    }
    .waveform .bar {
        width: 4px;
        border-radius: 4px;
        background: #6366f1;
        animation: wave 1.2s ease-in-out infinite;
    }
    .waveform .bar:nth-child(1)  { height: 16px; animation-delay: 0s; }
    .waveform .bar:nth-child(2)  { height: 32px; animation-delay: 0.1s; background: #818cf8; }
    .waveform .bar:nth-child(3)  { height: 24px; animation-delay: 0.2s; }
    .waveform .bar:nth-child(4)  { height: 40px; animation-delay: 0.3s; background: #818cf8; }
    .waveform .bar:nth-child(5)  { height: 30px; animation-delay: 0.4s; }
    .waveform .bar:nth-child(6)  { height: 48px; animation-delay: 0.5s; background: #a5b4fc; }
    .waveform .bar:nth-child(7)  { height: 36px; animation-delay: 0.6s; }
    .waveform .bar:nth-child(8)  { height: 24px; animation-delay: 0.7s; background: #818cf8; }
    .waveform .bar:nth-child(9)  { height: 40px; animation-delay: 0.8s; }
    .waveform .bar:nth-child(10) { height: 20px; animation-delay: 0.9s; }
    .waveform .bar:nth-child(11) { height: 32px; animation-delay: 1.0s; background: #818cf8; }
    .waveform .bar:nth-child(12) { height: 16px; animation-delay: 1.1s; }
    @keyframes wave {
        0%, 100% { transform: scaleY(0.5); opacity: 0.6; }
        50%        { transform: scaleY(1);   opacity: 1; }
    }

    /* Step list */
    .ai-steps { display: flex; flex-direction: column; gap: 12px; }
    .ai-step {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255,255,255,0.35);
        font-size: 13px;
        font-weight: 600;
        transition: color 0.4s;
    }
    .ai-step.done  { color: rgba(255,255,255,0.9); }
    .ai-step.active { color: #a5b4fc; }
    .ai-step .step-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
        transition: all 0.4s;
    }
    .ai-step.done  .step-icon { background: #22c55e; border-color: #22c55e; color: #fff; }
    .ai-step.active .step-icon { background: #4361ee; border-color: #4361ee; color: #fff; animation: iconPulse 1s infinite; }
    @keyframes iconPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(67,97,238,0.6); }
        50% { box-shadow: 0 0 0 8px rgba(67,97,238,0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-900 text-slate-900 mb-1">
                @if(isset($company))
                    {{ $company->company_name }} records
                @else
                    Communication Intelligence
                @endif
            </h3>
            <p class="text-slate-500 mb-0">Unified platform for communication records and AI analysis.</p>
        </div>
        <button type="button" class="btn btn-primary px-4 fw-bold rounded-3 shadow-sm" style="background: var(--primary-gradient); border: none;"
                data-bs-toggle="modal" data-bs-target="#audioUploadModal{{ $company_id }}">
            <i class="bi bi-plus-circle me-2"></i>New Analysis
        </button>
    </div>

    <!-- Summary Strip -->
    <div class="summary-strip">
        <div class="summary-card">
            <div class="summary-icon icon-blue">
                <i class="bi bi-database"></i>
            </div>
            <div>
                <div class="summary-value">{{ $summary['total'] }}</div>
                <div class="summary-label">Total Records</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon icon-green">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="summary-value">{{ $summary['good_score'] }}</div>
                <div class="summary-label">High Performance</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon icon-amber">
                <i class="bi bi-patch-question"></i>
            </div>
            <div>
                <div class="summary-value">{{ $summary['needs_coaching'] }}</div>
                <div class="summary-label">Coaching Alerts</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon icon-red">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div>
                <div class="summary-value">{{ $summary['high_risk'] }}</div>
                <div class="summary-label">Critical Risks</div>
            </div>
        </div>
    </div>

    <!-- Modern Filter Bar -->
    <div class="modern-filter-bar">
        <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 border-slate-200">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 filter-input" 
                           placeholder="Search records..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select id="company-switcher" class="form-select filter-select">
                    @foreach($allCompanies as $c)
                        <option value="{{ $c->id }}" {{ $c->id == $company_id ? 'selected' : '' }}>
                            {{ $c->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="agent" class="form-select filter-select">
                    <option value="all">Agent: All</option>
                    @foreach($companyAgents as $ag)
                        <option value="{{ $ag['full_name'] }}" {{ request('agent') == $ag['full_name'] ? 'selected' : '' }}>
                            {{ $ag['full_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="risk" class="form-select filter-select">
                    <option value="all">Risk: Any</option>
                    <option value="High" {{ request('risk') == 'High' ? 'selected' : '' }}>High Risk</option>
                    <option value="No" {{ request('risk') == 'No' ? 'selected' : '' }}>No Risk</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark flex-grow-1 fw-bold rounded-3">Apply Filters</button>
                @if(request()->anyFilled(['search', 'source', 'agent', 'risk']))
                    <a href="{{ route('user.task.list', $company_id) }}" class="btn btn-light border rounded-3 px-3"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Records Table -->
    <div class="records-table-card">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>Agent Intelligence</th>
                        <th>Outcome / Channel</th>
                        <th class="text-center">AI Score</th>
                        <th>Sentiment</th>
                        <th>Risk Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($taskList as $task)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-slate-900">{{ \Carbon\Carbon::parse($task['created_at'])->format('M d, Y') }}</div>
                                <div class="text-slate-400" style="font-size: 11px;">{{ \Carbon\Carbon::parse($task['created_at'])->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="agent-pill">
                                    <div class="avatar-sm">
                                        {{ strtoupper(substr($task['agent_name'] ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-900 d-block">{{ $task['agent_name'] ?? 'Unassigned' }}</div>
                                        <div class="text-slate-400" style="font-size: 11px;">Supervisor: {{ $task['supervisor_name'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-slate-700 fw-500">{{ $task['outcome'] ?? 'Connected' }}</div>
                                <div class="text-slate-400 small">
                                    <i class="bi bi-{{ ($task['channel'] ?? 'Call') == 'Call' ? 'telephone' : 'chat-dots' }} me-1"></i>
                                    {{ strtoupper($task['source'] ?? 'API') }}
                                </div>
                            </td>
                            <td class="text-center">

                            <div class="d-inline-block text-center">
                                <div class="fw-800 text-{{ ($task['score'] ?? 0) >= 90 ? 'success' : (($task['score'] ?? 0) >= 75 ? 'primary' : 'danger') }}" style="font-size: 14px;">
                                    {{ $task['score'] ?? 0 }}%
                                </div>
                                <div class="progress mt-1" style="height: 3px; width: 60px; margin: 0 auto;">
                                    <div class="progress-bar bg-{{ ($task['score'] ?? 0) >= 90 ? 'success' : (($task['score'] ?? 0) >= 75 ? 'primary' : 'danger') }}" 
                                            style="width: {{ $task['score'] ?? 0 }}%"></div>
                                </div>
                            </div>
                            </td>
                            <td>
                                @php $sent = $task['sentiment'] ?? 'Neutral'; @endphp
                                <span class="text-{{ $sent == 'Positive' ? 'success' : ($sent == 'Negative' ? 'danger' : 'muted') }} fw-bold">
                                    {{ $sent }}
                                </span>
                            </td>
                            <td>
                                @if(($task['status'] ?? '') == 'processing')
                                    <span class="badge-risk bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10">
                                        <span class="spinner-grow spinner-grow-sm me-1" role="status"></span>
                                        PROCESSING
                                    </span>
                                @elseif(($task['risk_flag'] ?? 'No') == 'High')
                                    <span class="badge-risk bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">HIGH RISK</span>
                                @else
                                    <span class="badge-risk bg-success bg-opacity-10 text-success border border-success border-opacity-10">SECURE</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if(($task['status'] ?? '') != 'processing')
                                        {{-- Chat / Conversation button --}}
                                        @php
                                            $currentConv = $task['analysis']['conversation'] ?? [];
                                        @endphp
                                        @if(!empty($currentConv))
                                            <button type="button"
                                                class="btn-action btn-view-conv"
                                                title="View Conversation"
                                                data-agent="{{ $task['agent_name'] ?? 'Agent' }}"
                                                data-date="{{ \Carbon\Carbon::parse($task['created_at'])->format('M d, Y · h:i A') }}"
                                                data-conv="{{ json_encode($currentConv) }}">
                                                <i class="bi bi-chat-square-text"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('user.task.details', $task['id']) }}" class="btn-action" title="View Intelligence">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('user.task.delete', $task['id']) }}" class="btn-action text-danger" 
                                       onclick="return confirm('Archive this analysis record?')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-slate-400">No communication intelligence records available for the current filter.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($taskList->hasPages())
        <div class="p-3 border-top bg-slate-50">
            {{ $taskList->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="audioUploadModal{{ $company_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-modal">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-slate-900">Intelligence Engine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="transcription-form" action="{{ route('user.task.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-uppercase text-slate-500 mb-0">1. Audio Source</label>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btn-upload-mode">Upload</button>
                                <button type="button" class="btn btn-outline-primary" id="id-mode-btn">Job ID</button>
                            </div>
                        </div>

                        {{-- Upload Zone --}}
                        <div id="upload-zone-wrapper">
                            <label class="upload-zone w-100 py-5 text-center cursor-pointer mb-0">
                                <i class="bi bi-cloud-arrow-up fs-2 text-slate-400 d-block mb-2"></i>
                                <span class="fw-bold text-slate-700">Browse Record</span>
                                <span class="text-slate-400 d-block small">MP3, WAV, M4A, OGG (Max 100MB)</span>
                                <input type="file" name="audio_file" id="audio_file_input" class="d-none" accept=".wav,.mp3,.m4a,.ogg,.webm" onchange="this.previousElementSibling.previousElementSibling.textContent = this.files[0].name; this.previousElementSibling.previousElementSibling.className='fw-bold text-primary'">
                            </label>
                        </div>

                        {{-- Job ID Input --}}
                        <div id="job-id-wrapper" class="d-none">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-slate-400"><i class="bi bi-hash"></i></span>
                                <input type="text" name="hamsa_job_id" class="form-control border-start-0 filter-input" placeholder="Paste Hamsa Job ID here...">
                            </div>
                            <small class="text-muted mt-2 d-block px-1">Use an existing Hamsa Job ID to avoid re-uploading charges.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-slate-500">2. Workforce Assignment</label>
                        <select name="agent_id" class="form-select filter-select" required>
                            <option value="">Choose Agent...</option>
                            @foreach($companyAgents as $agent)
                                <option value="{{ $agent['id'] }}">{{ $agent['full_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="company_id" value="{{ $company_id }}">
                    
                    <button type="submit" id="submit-analysis-btn" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        <i class="bi bi-cpu me-2"></i>Initiate AI Analysis
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- AI Processing Overlay --}}
<div id="ai-processing-overlay">
    <div class="ai-orb">
        <i class="bi bi-waveform"></i>
    </div>
    <div class="ai-title">AI Engine Processing</div>
    <div class="ai-subtitle">Please keep this window open. This usually takes 30–60 seconds.</div>

    <div class="waveform mb-4">
        <div class="bar"></div><div class="bar"></div><div class="bar"></div>
        <div class="bar"></div><div class="bar"></div><div class="bar"></div>
        <div class="bar"></div><div class="bar"></div><div class="bar"></div>
        <div class="bar"></div><div class="bar"></div><div class="bar"></div>
    </div>

    <div class="ai-steps">
        <div class="ai-step done" id="step-upload">
            <div class="step-icon"><i class="bi bi-check"></i></div>
            <span>Audio file uploaded</span>
        </div>
        <div class="ai-step active" id="step-transcribe">
            <div class="step-icon"><i class="bi bi-soundwave"></i></div>
            <span>Transcribing speech to text...</span>
        </div>
        <div class="ai-step" id="step-analyze">
            <div class="step-icon"><i class="bi bi-cpu"></i></div>
            <span>Extracting conversation structure</span>
        </div>
        <div class="ai-step" id="step-save">
            <div class="step-icon"><i class="bi bi-database"></i></div>
            <span>Saving to intelligence database</span>
        </div>
    </div>
</div>

{{-- Conversation Popup Modal --}}
<div class="modal fade conv-modal" id="conversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <div class="conv-header d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-waveform fs-5"></i>
                        <span class="fw-800 fs-6" id="conv-modal-title">Conversation</span>
                        <span class="badge-live">TRANSCRIBED</span>
                    </div>
                    <div style="font-size: 11px; opacity: 0.7;" id="conv-modal-meta"></div>
                </div>
                <button type="button" class="btn-close btn-close-white mt-1" data-bs-dismiss="modal"></button>
            </div>


            <div class="conv-body" id="conv-body"></div>

            {{-- Speaker summary --}}
            <div class="conv-speakers d-none" id="conv-speakers"></div>

            <div class="conv-footer">
                <i class="bi bi-cpu"></i>
                <span>AI-transcribed via Hamsa Speech Engine &mdash; Arabic NLP</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── AI Processing Overlay ─────────────────────────────────
    const form    = document.getElementById('transcription-form');
    const overlay = document.getElementById('ai-processing-overlay');

    if (form) {
        form.addEventListener('submit', function () {
            overlay.classList.add('active');
            setTimeout(() => {
                document.getElementById('step-transcribe').classList.remove('active');
                document.getElementById('step-transcribe').classList.add('done');
                document.getElementById('step-transcribe').querySelector('.step-icon').innerHTML = '<i class="bi bi-check"></i>';
                document.getElementById('step-analyze').classList.add('active');
            }, 12000);
            setTimeout(() => {
                document.getElementById('step-analyze').classList.remove('active');
                document.getElementById('step-analyze').classList.add('done');
                document.getElementById('step-analyze').querySelector('.step-icon').innerHTML = '<i class="bi bi-check"></i>';
                document.getElementById('step-save').classList.add('active');
            }, 30000);
        });
    }

    // Modal Mode Toggle
    const btnUpload = document.getElementById('btn-upload-mode');
    const btnId     = document.getElementById('id-mode-btn');
    const uploadWrap = document.getElementById('upload-zone-wrapper');
    const idWrap     = document.getElementById('job-id-wrapper');
    const fileInput  = document.getElementById('audio_file_input');
    const idInput    = document.querySelector('input[name="hamsa_job_id"]');

    if (btnUpload && btnId) {
        btnUpload.addEventListener('click', () => {
            btnUpload.classList.add('active');
            btnId.classList.remove('active');
            uploadWrap.classList.remove('d-none');
            idWrap.classList.add('d-none');
            fileInput.required = true;
            idInput.required = false;
        });

        btnId.addEventListener('click', () => {
            btnId.classList.add('active');
            btnUpload.classList.remove('active');
            uploadWrap.classList.add('d-none');
            idWrap.classList.remove('d-none');
            fileInput.required = false;
            idInput.required = true;
        });
    }

    // ── Company Switcher ──────────────────────────────────────
    const companySwitcher = document.getElementById('company-switcher');
    if (companySwitcher) {
        companySwitcher.addEventListener('change', function() {
            const companyId = this.value;
            // Redirect to the task list for the selected company
            window.location.href = "{{ route('user.task.list', ':id') }}".replace(':id', companyId);
        });
    }

    // ── Conversation Modal ────────────────────────────────────
    const convModal     = new bootstrap.Modal(document.getElementById('conversationModal'));
    const convBody      = document.getElementById('conv-body');
    const convTitle     = document.getElementById('conv-modal-title');
    const convMeta      = document.getElementById('conv-modal-meta');
    const convSpeakers  = document.getElementById('conv-speakers');

    // Format seconds → mm:ss
    function fmtTime(val) {
        if (val === null || val === undefined || val === '') return '??:??';
        const n = parseFloat(val);
        if (isNaN(n)) return val; // already a string like "00:12"
        const m = Math.floor(n / 60);
        const s = Math.floor(n % 60);
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    document.querySelectorAll('.btn-view-conv').forEach(btn => {
        btn.addEventListener('click', function () {
            const agent = this.getAttribute('data-agent') || 'Agent';
            const date  = this.getAttribute('data-date')  || '';
            let   conv  = [];

            try { 
                const raw = this.getAttribute('data-conv');
                conv = JSON.parse(raw); 
            } catch(e) {
                console.error("JSON Parse Error:", e);
            }

            convTitle.textContent = agent + ' — Recording';
            convMeta.textContent  = date;

            if (!conv || !conv.length) {
                convBody.innerHTML = '<div class="conv-empty text-center py-5"><i class="bi bi-chat-slash fs-2 opacity-25"></i><p class="mt-2">No conversation data available.</p></div>';
                convSpeakers.classList.add('d-none');
            } else {
                const html = conv.map(turn => {
                    const spk  = (turn.speaker || turn.speakerId || 'Unknown').toString().trim();
                    const text = (turn.text || turn.transcript || turn.translation || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                    const startTS = fmtTime(turn.start_time || turn.start);
                    const endTS   = fmtTime(turn.end_time || turn.end);
                    const ts      = startTS + ' - ' + endTS;
                    const txtLow  = text.toLowerCase();

                    return `<div class="trans-turn px-3" data-text="${txtLow}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="trans-spk-name">${spk}</div>
                                <div class="trans-spk-label">${spk}</div>
                            </div>
                            <div class="trans-ts">${ts}</div>
                        </div>
                        <div class="trans-text mt-2">${text}</div>
                    </div>`;
                }).join('');

                convBody.innerHTML = html;

                // Build speakers summary
                const counts = {};
                conv.forEach(t => {
                    const s = (t.speaker || t.speakerId || 'Unknown').toString().trim();
                    counts[s] = (counts[s] || 0) + 1;
                });

                const spkHtml = '<h6 class="fw-bold text-dark mb-3 px-1" style="font-size:0.8rem;">Participants</h6>'
                    + '<div class="row g-2">'
                    + Object.entries(counts).map(([s, c]) =>
                        `<div class="col-6"><div class="spk-card">
                            <div class="fw-bold text-truncate" style="font-size:0.8rem;">${s}</div>
                            <div class="count" style="font-size:1.1rem;">${c}</div>
                            <div class="label" style="font-size:8px;">Segments</div>
                        </div></div>`
                    ).join('')
                    + '</div>';

                convSpeakers.innerHTML = spkHtml;
                convSpeakers.classList.remove('d-none');
            }

            convModal.show();
            setTimeout(() => { convBody.scrollTop = 0; }, 200);
        });
    });
});
</script>
@endpush
@endsection