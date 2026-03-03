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

    /* Premium Modal Styling */
    .modal-content.premium-modal {
        border: none;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
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
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-900 text-slate-900 mb-1">Communication Intelligence</h3>
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
        <form action="{{ url()->current() }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 border-slate-200">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 filter-input" 
                           placeholder="Search records, agents, outcome..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="source" class="form-select filter-select">
                    <option value="all">Source: All</option>
                    <option value="api" {{ request('source') == 'api' ? 'selected' : '' }}>API</option>
                    <option value="avaya" {{ request('source') == 'avaya' ? 'selected' : '' }}>Avaya</option>
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
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-3">Filter</button>
                @if(request()->anyFilled(['search', 'source', 'agent', 'risk']))
                    <a href="{{ url()->current() }}" class="btn btn-light border w-50 rounded-3"><i class="bi bi-x"></i></a>
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
                                @if(($task['risk_flag'] ?? 'No') == 'High')
                                    <span class="badge-risk bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">HIGH RISK</span>
                                @else
                                    <span class="badge-risk bg-success bg-opacity-10 text-success border border-success border-opacity-10">SECURE</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('user.task.details', $task['id']) }}" class="btn-action" title="View Intelligence">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
                <form action="{{ route('user.task.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-slate-500">1. Select Audio Source</label>
                        <label class="upload-zone w-100 py-5 text-center cursor-pointer mb-0">
                            <i class="bi bi-cloud-arrow-up fs-2 text-slate-400 d-block mb-2"></i>
                            <span class="fw-bold text-slate-700">Browse Record</span>
                            <span class="text-slate-400 d-block small">MP3 / WAV (Max 100MB)</span>
                            <input type="file" name="audio_file" class="d-none" required onchange="this.previousElementSibling.previousElementSibling.textContent = this.files[0].name; this.previousElementSibling.previousElementSibling.className='fw-bold text-primary'">
                        </label>
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
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                        Initiate AI Analysis
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection