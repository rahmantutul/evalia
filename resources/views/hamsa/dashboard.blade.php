@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xxl-10 col-xl-11 col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="h3 mb-2 text-gray-800 fw-bold">
                        <i class="fas fa-home text-primary me-2"></i>Hamsa AI Dashboard
                    </h1>
                    <p class="text-muted">Welcome to your AI workspace. Monitor your usage and access tools quickly.</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-3">
                        <i class="fas fa-rocket text-info me-1"></i>AI Powered
                    </span>
                    <small class="text-muted">Last updated: {{ now()->format('M j, Y g:i A') }}</small>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted text-uppercase fw-semibold mb-2">
                                        <i class="fas fa-microphone me-2 text-primary"></i>Transcriptions
                                    </h6>
                                    <h2 class="fw-bold text-dark mb-0">{{ $usage['transcriptions'] ?? 0 }}</h2>
                                    <small class="text-muted">Total processed</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-microphone fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: {{ min(($usage['transcriptions'] ?? 0) * 10, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted text-uppercase fw-semibold mb-2">
                                        <i class="fas fa-volume-up me-2 text-success"></i>TTS Jobs
                                    </h6>
                                    <h2 class="fw-bold text-dark mb-0">{{ $usage['tts_jobs'] ?? 0 }}</h2>
                                    <small class="text-muted">Audio generated</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-volume-up fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(($usage['tts_jobs'] ?? 0) * 10, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted text-uppercase fw-semibold mb-2">
                                        <i class="fas fa-robot me-2 text-info"></i>AI Generations
                                    </h6>
                                    <h2 class="fw-bold text-dark mb-0">{{ $usage['ai_generations'] ?? 0 }}</h2>
                                    <small class="text-muted">Content created</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-robot fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: {{ min(($usage['ai_generations'] ?? 0) * 10, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted text-uppercase fw-semibold mb-2">
                                        <i class="fas fa-headset me-2 text-warning"></i>Voice Agents
                                    </h6>
                                    <h2 class="fw-bold text-dark mb-0">{{ $usage['voice_agents'] ?? 0 }}</h2>
                                    <small class="text-muted">Active agents</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-headset fa-2x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min(($usage['voice_agents'] ?? 0) * 10, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Recent Jobs -->
            <div class="row g-4">
                <!-- Quick Actions -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ url('/hamsa/transcribe') }}" class="card action-card border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="action-icon bg-primary bg-opacity-10 rounded-3 p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                                <i class="fas fa-microphone fa-2x text-primary"></i>
                                            </div>
                                            <h6 class="fw-semibold text-dark mb-2">Transcribe Audio</h6>
                                            <p class="text-muted small mb-0">Convert speech to text with AI</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ url('/hamsa/tts') }}" class="card action-card border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="action-icon bg-success bg-opacity-10 rounded-3 p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                                <i class="fas fa-volume-up fa-2x text-success"></i>
                                            </div>
                                            <h6 class="fw-semibold text-dark mb-2">Text to Speech</h6>
                                            <p class="text-muted small mb-0">Generate natural audio from text</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ url('/hamsa/translate') }}" class="card action-card border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="action-icon bg-info bg-opacity-10 rounded-3 p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                                <i class="fas fa-language fa-2x text-info"></i>
                                            </div>
                                            <h6 class="fw-semibold text-dark mb-2">Translate Text</h6>
                                            <p class="text-muted small mb-0">Translate between 100+ languages</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ url('/hamsa/ai/generate') }}" class="card action-card border-0 text-decoration-none h-100">
                                        <div class="card-body text-center p-4">
                                            <div class="action-icon bg-warning bg-opacity-10 rounded-3 p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                                <i class="fas fa-robot fa-2x text-warning"></i>
                                            </div>
                                            <h6 class="fw-semibold text-dark mb-2">AI Content</h6>
                                            <p class="text-muted small mb-0">Generate content with AI assistance</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Jobs -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-semibold text-dark">
                                    <i class="fas fa-history text-primary me-2"></i>Recent Jobs
                                </h5>
                                <a href="{{ url('/hamsa/jobs') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if(!empty($recentJobs))
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-muted fw-semibold small text-uppercase">Job ID</th>
                                                <th class="text-muted fw-semibold small text-uppercase">Type</th>
                                                <th class="text-muted fw-semibold small text-uppercase">Status</th>
                                                <th class="text-muted fw-semibold small text-uppercase">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentJobs as $job)
                                            <tr class="job-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="job-icon bg-light rounded-2 p-2 me-3">
                                                            @if($job['type'] == 'transcription')
                                                                <i class="fas fa-microphone text-primary"></i>
                                                            @elseif($job['type'] == 'tts')
                                                                <i class="fas fa-volume-up text-success"></i>
                                                            @elseif($job['type'] == 'translation')
                                                                <i class="fas fa-language text-info"></i>
                                                            @else
                                                                <i class="fas fa-robot text-warning"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-dark">{{ substr($job['id'], 0, 8) }}...</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-capitalize text-dark">{{ $job['type'] }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusConfig = [
                                                            'completed' => ['class' => 'bg-success', 'icon' => 'check'],
                                                            'processing' => ['class' => 'bg-warning', 'icon' => 'sync'],
                                                            'failed' => ['class' => 'bg-danger', 'icon' => 'exclamation'],
                                                            'pending' => ['class' => 'bg-secondary', 'icon' => 'clock']
                                                        ];
                                                        $config = $statusConfig[$job['status']] ?? $statusConfig['pending'];
                                                    @endphp
                                                    <span class="badge {{ $config['class'] }} bg-opacity-10 text-dark border-0">
                                                        <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                                        {{ ucfirst($job['status']) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($job['created_at'])->format('M j') }}
                                                    </small>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="fas fa-tasks fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="text-muted mb-2">No recent jobs</h6>
                                    <p class="text-muted small">Your completed jobs will appear here</p>
                                    <a href="{{ url('/hamsa/transcribe') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus me-1"></i>Start Your First Job
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Features Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold text-dark">
                                <i class="fas fa-star text-warning me-2"></i>Getting Started
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                                <i class="fas fa-file-audio text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Upload Audio</h6>
                                            <p class="text-muted small mb-0">Start by uploading an audio file for transcription</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success bg-opacity-10 rounded-2 p-2 me-3">
                                                <i class="fas fa-text-height text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Generate Content</h6>
                                            <p class="text-muted small mb-0">Use AI to create written content from prompts</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="bg-info bg-opacity-10 rounded-2 p-2 me-3">
                                                <i class="fas fa-globe text-info"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Translate Text</h6>
                                            <p class="text-muted small mb-0">Convert text between different languages</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add hover effects to job rows
    const jobRows = document.querySelectorAll('.job-row');
    jobRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>
@endpush

<style>
.card {
    border-radius: 12px;
}

.action-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.action-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.job-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.job-row {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.table-borderless td {
    padding: 12px 8px;
    vertical-align: middle;
}

.progress {
    border-radius: 2px;
}

.badge {
    border-radius: 6px;
    font-weight: 500;
}

.empty-state-icon {
    opacity: 0.5;
}

.action-icon {
    transition: all 0.3s ease;
}

.action-card:hover .action-icon {
    transform: scale(1.1);
}

.text-uppercase {
    letter-spacing: 0.5px;
}
</style>
@endsection