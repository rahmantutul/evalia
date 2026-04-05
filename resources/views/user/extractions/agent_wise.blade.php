@extends('user.layouts.app')

@section('title', 'Intelligence Feed')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-layer-group text-primary me-2"></i> Intelligence Feed
            </h3>
            <p class="text-muted small mb-0">Explore core data points extracted from your analyzed calls automatically.</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end mt-3 mt-md-0">
            <div class="bg-white p-2 rounded-4 shadow-sm border d-flex align-items-center" style="min-width: 300px;">
                <i class="fas fa-filter text-muted ms-2 me-3"></i>
                <form action="{{ route('user.extractions.agent-wise') }}" method="GET" class="w-100 me-2" id="agentFilterForm">
                    <select name="agent_id" class="form-select border-0 shadow-none fw-semibold text-dark" onchange="this.form.submit()">
                        <option value="all" {{ $selectedAgentId == 'all' ? 'selected' : '' }}>Filter All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $selectedAgentId == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
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
                <h4 class="text-dark fw-bold">No intelligence data yet</h4>
                <p class="text-muted mx-auto" style="max-width: 400px;">Run AI Analysis on your calls to automatically populate this feed with custom JSON extractions.</p>
                <a href="{{ route('user.home') }}" class="btn btn-primary rounded-pill px-4 mt-2">Go to Dashboard</a>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($extractions as $extraction)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden extraction-card">
                        <div class="row g-0">
                            <!-- Left Sidebar Panel -->
                            <div class="col-lg-3 bg-light-soft border-end p-4">
                                <div class="d-flex flex-column h-100">
                                    <div class="mb-auto">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <span class="badge bg-white shadow-sm text-dark border-0 px-3 py-2 rounded-pill fw-bold" style="font-size: 11px;">
                                                #TASK-{{ $extraction['task_id'] }}
                                            </span>
                                            <div class="score-pill px-2 py-1 rounded-pill {{ $extraction['score'] >= 80 ? 'bg-success' : ($extraction['score'] >= 50 ? 'bg-warning' : 'bg-danger') }} text-white fw-bold shadow-sm" style="font-size: 10px;">
                                                {{ $extraction['score'] }}%
                                            </div>
                                        </div>

                                        <a href="{{ route('user.agents.show', $extraction['agent_id']) }}" class="agent-link d-flex align-items-center p-2 rounded-3 text-decoration-none mb-3">
                                            <div class="avatar-sm bg-primary text-black rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold; overflow: hidden;">
                                                {{ strtoupper(mb_substr($extraction['agent_name'] ?? 'A', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-dark fw-bold small" style="line-height: 1;">{{ $extraction['agent_name'] }}</div>
                                                <small class="text-muted" style="font-size: 10px;">View Agent Profile</small>
                                            </div>
                                        </a>

                                        <div class="mt-4">
                                            <div class="d-flex align-items-center text-muted mb-2 small">
                                                <i class="far fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::parse($extraction['created_at'])->format('M d, Y') }}
                                            </div>
                                            <div class="d-flex align-items-center text-muted mb-2 small">
                                                <i class="far fa-clock me-2"></i> {{ \Carbon\Carbon::parse($extraction['created_at'])->format('H:i A') }}
                                            </div>
                                            <div class="d-flex align-items-center text-muted small">
                                                <i class="fas fa-hourglass-half me-2"></i> {{ $extraction['duration'] }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('user.task.details', $extraction['task_id']) }}" class="btn btn-dark btn-sm w-100 rounded-pill shadow-sm">
                                            <i class="fas fa-play-circle me-1"></i> Review Call
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Content Panel -->
                            <div class="col-lg-9 bg-white p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <h6 class="text-uppercase fw-bold text-muted small mb-0 letter-spacing-1">
                                        <i class="fas fa-bolt text-warning me-1"></i> AI Insights
                                    </h6>
                                    <hr class="flex-grow-1 ms-3 opacity-10">
                                </div>

                                <div class="row g-3">
                                    @foreach($extraction['data'] as $item)
                                        <div class="col-md-4">
                                            <div class="insight-box p-3 rounded-4 border bg-white h-100">
                                                <div class="text-muted text-uppercase fw-bold mb-2" style="font-size: 9px; letter-spacing: 0.5px;">{{ $item['label'] }}</div>
                                                <div class="text-dark fw-bold mb-0" style="font-size: 14px;">
                                                    @if(is_bool($item['value']))
                                                        @if($item['value'])
                                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i> Yes</span>
                                                        @else
                                                            <span class="text-danger"><i class="fas fa-times-circle me-1"></i> No</span>
                                                        @endif
                                                    @else
                                                        {{ $item['value'] ?? 'N/A' }}
                                                    @endif
                                                </div>
                                                @if(!empty($item['evidence']))
                                                    <div class="mt-2 pt-2 border-top">
                                                        <div class="evidence-text text-muted fst-italic small" title="{{ $item['evidence'] }}">
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
    @endif
</div>

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
