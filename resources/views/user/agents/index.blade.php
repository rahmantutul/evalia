@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Agent Performance</h4>
                    <p class="text-muted mb-0">Monitor agent performance and metrics</p>
                </div>
                <div class="text-end">
                    <div class="text-muted small">Last Updated</div>
                    <div class="fw-semibold">{{ now()->format('M j, Y g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent Cards -->
    <div class="row">
        @foreach($agentsWithPerformance as $agentData)
        @php
            $agent = $agentData['agent'];
            $performance = $agentData['performance'];
            $hasPerformance = !empty($performance);
            $scores = $hasPerformance ? $performance['current_scores'] : [];
            $agentDetails = $hasPerformance ? $performance['agent_details'] : [];
        @endphp
        
        <div class="col-xl-6 col-xxl-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <!-- Agent Header -->
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <span class="text-primary fw-bold fs-4">{{ substr($agent['full_name'] ?? 'A', 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fw-bold mb-1">{{ $agent['full_name'] ?? 'N/A' }}</h5>
                            <p class="text-muted mb-1 small">{{ $agentDetails['position'] ?? $agent['position'] ?? 'Agent' }}</p>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success rounded-pill small">
                                    <i class="fas fa-circle me-1 small"></i>Active
                                </span>
                                <span class="text-muted small ms-2">{{ $agentDetails['company_name'] ?? $agent['company_name'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Performance Score -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <div class="circular-progress" 
                                 data-score="{{ $scores['overall_score'] ?? 0 }}">
                                <span class="score-text fw-bold">{{ $scores['overall_score'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Overall Score</small>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div class="row g-2 mb-3">
                        <div class="col-4 text-center">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-primary">{{ $performance['total_tasks'] ?? 0 }}</div>
                                <small class="text-muted">Tasks</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">{{ $scores['answer_accuracy'] ?? 0 }}</div>
                                <small class="text-muted">Accuracy</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-info">{{ $scores['response_speed'] ?? 0 }}</div>
                                <small class="text-muted">Speed</small>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Scores -->
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Customer Satisfaction</span>
                            <span class="fw-semibold">{{ $scores['customer_satisfaction'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Professionalism</span>
                            <span class="fw-semibold">{{ $scores['professionalism'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Avg Call Duration</span>
                            <span class="fw-semibold">{{ $performance['avg_call_duration'] ?? 0 }}s</span>
                        </div>
                    </div>

                    {{--  <!-- Action Buttons -->
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewAgentDetails('{{ $agent['id'] }}')">
                            <i class="fas fa-chart-bar me-1"></i>View Details
                        </button>
                    </div>  --}}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(count($agentsWithPerformance) === 0)
    <div class="text-center py-5">
        <div class="text-muted mb-3">
            <i class="fas fa-users fa-3x"></i>
        </div>
        <h5 class="text-muted">No agents found</h5>
        <p class="text-muted">There are currently no active agents in the system.</p>
    </div>
    @endif
</div>

<style>
.avatar-lg {
    width: 60px;
    height: 60px;
}

.circular-progress {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#007bff 0%, #e9ecef 0%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.circular-progress::before {
    content: '';
    position: absolute;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: white;
}

.score-text {
    position: relative;
    z-index: 1;
    font-size: 1.25rem;
}

.card {
    border-radius: 12px;
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<script>
function viewAgentDetails(agentId) {
    window.location.href = `/agents/${agentId}/performance`;
}

// Update circular progress bars
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.circular-progress');
    progressBars.forEach(bar => {
        const score = parseFloat(bar.getAttribute('data-score'));
        const percentage = Math.min(score, 100);
        bar.style.background = `conic-gradient(#007bff ${percentage}%, #e9ecef ${percentage}%)`;
    });
});
</script>
@endsection