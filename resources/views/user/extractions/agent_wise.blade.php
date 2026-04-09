@extends('user.layouts.app')

@section('title', 'Intelligence Feed')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-4">
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-layer-group text-primary me-2"></i> Intelligence Feed
            </h3>
            <p class="text-muted small mb-0">Filtered view of core data points extracted from analyzed calls.</p>
        </div>
        <div class="col-md-8 d-flex justify-content-md-end mt-3 mt-md-0">
            <div class="bg-white p-3 rounded-4 shadow-sm border d-flex flex-wrap align-items-center gap-3">
                <form action="{{ route('user.extractions.agent-wise') }}" method="GET" class="d-flex flex-wrap align-items-center gap-3 w-100" id="filterForm">
                    <!-- Group Filter -->
                    <div style="min-width: 150px;">
                        <label class="small text-muted fw-bold mb-1 d-block text-uppercase" style="font-size: 10px;">Filter Group</label>
                        <select name="group_name" class="form-select form-select-sm border border-light shadow-none fw-semibold text-dark rounded-3" onchange="this.form.submit()">
                            <option value="all" {{ $selectedGroupName == 'all' ? 'selected' : '' }}>All Groups</option>
                            @foreach($groupNames as $name)
                                <option value="{{ $name }}" {{ $selectedGroupName == $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Agent Filter -->
                    <div style="min-width: 150px;">
                        <label class="small text-muted fw-bold mb-1 d-block text-uppercase" style="font-size: 10px;">Filter Agent</label>
                        <select name="agent_id" class="form-select form-select-sm border border-light shadow-none fw-semibold text-dark rounded-3" onchange="this.form.submit()">
                            <option value="all" {{ $selectedAgentId == 'all' ? 'selected' : '' }}>All Agents</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ $selectedAgentId == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Filters -->
                    <div style="min-width: 130px;">
                        <label class="small text-muted fw-bold mb-1 d-block text-uppercase" style="font-size: 10px;">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm border border-light shadow-none rounded-3" value="{{ $startDate }}" onchange="this.form.submit()">
                    </div>

                    <div style="min-width: 130px;">
                        <label class="small text-muted fw-bold mb-1 d-block text-uppercase" style="font-size: 10px;">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm border border-light shadow-none rounded-3" value="{{ $endDate }}" onchange="this.form.submit()">
                    </div>

                    <div class="pt-3">
                        <a href="{{ route('user.extractions.agent-wise') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold" title="Reset Filters">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(empty($extractions))
        <div class="card border-0 shadow-sm rounded-4 py-5">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <div class="avatar-lg bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-microchip fs-1 text-muted opacity-50"></i>
                    </div>
                </div>
                <h4 class="text-dark fw-bold">No results matching your filters</h4>
                <p class="text-muted mx-auto" style="max-width: 400px;">Try adjusting your agent, group, or date filters to find matching intelligence data.</p>
                <a href="{{ route('user.extractions.agent-wise') }}" class="btn btn-outline-primary rounded-pill px-4 mt-2">Clear Filters</a>
            </div>
        </div>
    @else
        @php
            /* Group extractions by agent */
            $grouped = [];
            foreach ($extractions as $ex) {
                $key = $ex['agent_id'] . '||' . ($ex['agent_name'] ?? 'Unknown');
                $grouped[$key][] = $ex;
            }
        @endphp

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="fas fa-layer-group text-primary me-2"></i>Extraction Results
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1" style="font-size:.7rem;">
                    <i class="fas fa-user-tie me-1"></i>{{ count($grouped) }} {{ Str::plural('Agent', count($grouped)) }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:40px;"></th>
                            <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Agent</th>
                            <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Calls</th>
                            <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Avg Score</th>
                            <th style="font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;">Latest</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grouped as $agentKey => $agentExtractions)
                            @php
                                [$agentId, $agentName] = explode('||', $agentKey, 2);
                                $rowId   = 'agent-row-' . $agentId;
                                $count   = count($agentExtractions);
                                $avgScore = round(collect($agentExtractions)->avg('score'));
                                $latest  = collect($agentExtractions)->sortByDesc('created_at')->first();
                            @endphp

                            <!-- Agent Header Row (clickable) -->
                            <tr class="agent-summary-row" onclick="toggleExRow('{{ $rowId }}')" style="cursor:pointer;">
                                <td class="ps-4">
                                    <i class="fas fa-chevron-right text-muted ex-row-icon" id="icon-{{ $rowId }}" style="font-size:.7rem; transition:transform .2s;"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width:32px;height:32px;font-size:.8rem;background:linear-gradient(135deg,#6366f1,#4f46e5);">
                                            {{ strtoupper(mb_substr($agentName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-dark" style="font-size:.875rem;">{{ $agentName }}</span>
                                            <small class="text-muted d-block" style="font-size:.7rem;">#{{ $agentId }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $count }} {{ Str::plural('call', $count) }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $avgScore >= 80 ? 'bg-success' : ($avgScore >= 50 ? 'bg-warning' : 'bg-danger') }} bg-opacity-10
                                                       {{ $avgScore >= 80 ? 'text-success' : ($avgScore >= 50 ? 'text-warning' : 'text-danger') }}
                                                       border {{ $avgScore >= 80 ? 'border-success' : ($avgScore >= 50 ? 'border-warning' : 'border-danger') }} border-opacity-25"
                                          style="font-size:.7rem;">
                                        {{ $avgScore }}%
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($latest['created_at'])->format('M d, Y H:i') }}</small>
                                </td>
                            </tr>

                            <!-- Expanded Extraction Cards Row (hidden by default) -->
                            <tr id="{{ $rowId }}" class="d-none">
                                <td colspan="5" class="p-0">
                                    <div class="px-4 py-3" style="background:#f8faff; border-top:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0;">
                                        <div class="row g-3">
                                            @foreach($agentExtractions as $extraction)
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden extraction-card">
                                                        <div class="row g-0">
                                                            <!-- Left Sidebar Panel -->
                                                            <div class="col-lg-2 bg-light-soft border-end p-3">
                                                                <div class="d-flex flex-column h-100">
                                                                    <div class="mb-auto">
                                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                                            <span class="badge bg-white shadow-sm text-dark border-0 px-2 py-1 rounded-pill fw-bold" style="font-size: 10px;">
                                                                                #TASK-{{ $extraction['task_id'] }}
                                                                            </span>
                                                                            <div class="score-pill px-2 py-1 rounded-pill {{ $extraction['score'] >= 80 ? 'bg-success' : ($extraction['score'] >= 50 ? 'bg-warning' : 'bg-danger') }} text-white fw-bold shadow-sm" style="font-size: 10px;">
                                                                                {{ $extraction['score'] }}%
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            <div class="d-flex align-items-center text-muted mb-1 small">
                                                                                <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($extraction['created_at'])->format('M d, Y') }}
                                                                            </div>
                                                                            <div class="d-flex align-items-center text-muted mb-1 small">
                                                                                <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($extraction['created_at'])->format('H:i A') }}
                                                                            </div>
                                                                            <div class="d-flex align-items-center text-muted small">
                                                                                <i class="fas fa-hourglass-half me-1"></i> {{ $extraction['duration'] }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3">
                                                                        <a href="{{ route('user.task.details', $extraction['task_id']) }}" class="btn btn-dark btn-sm w-100 rounded-pill shadow-sm" onclick="event.stopPropagation()">
                                                                            <i class="fas fa-play-circle me-1"></i> Review Call
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Right Content Panel -->
                                                            <div class="col-lg-10 bg-white p-3">
                                                                <div class="d-flex align-items-center mb-3">
                                                                    <h6 class="text-uppercase fw-bold text-muted small mb-0 letter-spacing-1">
                                                                        <i class="fas fa-bolt text-warning me-1"></i> AI Insights
                                                                    </h6>
                                                                    <hr class="flex-grow-1 ms-3 opacity-10">
                                                                </div>
                                                                <div class="row g-2">
                                                                    @foreach($extraction['data'] as $item)
                                                                        <div class="col-md-3 col-sm-6">
                                                                            <div class="insight-box p-2 rounded-4 border bg-white h-100">
                                                                                <div class="text-muted text-uppercase fw-bold mb-1" style="font-size: 9px; letter-spacing: 0.5px;">{{ $item['label'] }}</div>
                                                                                <div class="text-dark fw-bold mb-0" style="font-size: 13px;">
                                                                                    @if(is_bool($item['value']))
                                                                                        @if($item['value'])
                                                                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Yes</span>
                                                                                        @else
                                                                                            <span class="text-danger"><i class="fas fa-times-circle me-1"></i>No</span>
                                                                                        @endif
                                                                                    @else
                                                                                        {{ $item['value'] ?? 'N/A' }}
                                                                                    @endif
                                                                                </div>
                                                                                @if(!empty($item['evidence']))
                                                                                    <div class="mt-1 pt-1 border-top">
                                                                                        <div class="evidence-text text-muted fst-italic" title="{{ $item['evidence'] }}" style="font-size:9px;">
                                                                                            "{{ Str::limit($item['evidence'], 60) }}"
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleExRow(rowId) {
    const row  = document.getElementById(rowId);
    const icon = document.getElementById('icon-' + rowId);
    if (!row) return;
    const hidden = row.classList.contains('d-none');
    row.classList.toggle('d-none', !hidden);
    icon.style.transform = hidden ? 'rotate(90deg)' : 'rotate(0deg)';
}
</script>
@endpush

<style>
.bg-light-soft {
    background-color: #f8f9fc;
}
.letter-spacing-1 {
    letter-spacing: 1px;
}
.extraction-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(0,0,0,.05) !important;
}
.extraction-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,.08) !important;
}
.agent-link {
    background-color: white;
    border: 1px solid #edf2f7;
    transition: background-color 0.2s;
}
.agent-link:hover {
    background-color: #f1f5f9;
}
.insight-box {
    transition: all 0.2s;
    border-color: #f1f5f9 !important;
}
.insight-box:hover {
    border-color: #6366f1 !important;
    background-color: #fcfcff;
}
.evidence-text {
    font-size: 10px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.score-pill {
    min-width: 45px;
    text-align: center;
}
.avatar-sm {
    flex-shrink: 0;
    font-size: 14px;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
}
.avatar-lg {
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>
@endsection
