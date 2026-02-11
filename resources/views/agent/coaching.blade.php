@extends('agent.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-dashboard.css') }}">
<style>
    /* Premium Dashboard Overrides */
    .dashboard-card {
        border-radius: 16px;
        border: 1px solid rgba(231, 234, 243, 0.7);
        box-shadow: 0 0.125rem 0.25rem rgba(140, 152, 164, 0.05) !important;
        transition: all 0.2s ease-in-out;
    }
    .dashboard-card:hover {
        box-shadow: 0 0.5rem 1.25rem rgba(140, 152, 164, 0.1) !important;
    }

    /* Progress Ring Specifics */
    .progress-ring {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }
    .progress-ring-circle {
        transition: stroke-dashoffset 0.8s ease-in-out;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    /* Metric Cards */
    .icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .metric-value {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #1e2022;
    }

    /* Focus Area Enhancements */
    .focus-area-card {
        border-top: 4px solid #0d6efd;
        position: relative;
    }
    .focus-area-card.system { border-top-color: #6c757d; }
    
    .task-item {
        padding: 0.85rem;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        background: #f8fafd;
    }
    .task-item:hover {
        background: #fff;
        border-color: #e7eaf3;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    
    .skill-badge {
        font-size: 10px;
        padding: 3px 10px;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .skill-communication { background-color: rgba(13, 110, 253, 0.08); color: #0d6efd; }
    .skill-process { background-color: rgba(25, 135, 84, 0.08); color: #198754; }
    .skill-knowledge { background-color: rgba(255, 193, 7, 0.08); color: #9e7a00; }

    /* Learning Resources */
    .resource-card {
        background: #fff;
        border: 1px solid #e7eaf3;
        border-radius: 12px;
        padding: 12px;
        transition: all 0.2s ease;
    }
    .resource-card:hover {
        border-color: #0d6efd;
        transform: translateY(-2px);
    }
    .video-thumbnail {
        height: 110px;
        border-radius: 8px;
        background: linear-gradient(135deg, #374bff 0%, #12c2e9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .play-icon {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.95);
        color: #0d6efd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Utilities */
    .chart-container { position: relative; height: 130px; }
    .tasks-list { max-height: 220px; overflow-y: auto; padding-right: 4px; }
    .tasks-list::-webkit-scrollbar { width: 4px; }
    .tasks-list::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    
    .history-table-container { max-height: 320px; overflow-y: auto; }
    .improvement-badge {
        background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 12px;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        color: white;
    }

    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08) !important; }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08) !important; }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08) !important; }
    
    body { overflow-x: hidden; background-color: #f9fafb; }
    .font-500 { font-weight: 500; }
    .extra-small { font-size: 10px; }
    .call-review-item {
        border-radius: 12px;
        border: 1px solid #e7eaf3;
        transition: all 0.2s ease;
        padding: 1rem;
    }
    .call-review-item:hover {
        border-color: #0d6efd;
        background-color: #f8fafd;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Coaching & Growth Plan</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Evalia</a></li>
                        <li class="breadcrumb-item active">Coaching</li>
                    </ol>
                </div>                            
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->

    <!-- Progress Overview -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card h-100 text-center">
                <div class="card-body p-4">
                    <div class="progress-ring">
                        <svg width="120" height="120">
                            <circle class="progress-ring-circle" stroke="#e9ecef" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"/>
                            <circle class="progress-ring-circle" stroke="#0d6efd" stroke-width="8" fill="transparent" r="52" cx="60" cy="60" 
                                    style="stroke-dasharray: 326.73; stroke-dashoffset: 98;"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <h3 class="fw-bold text-primary mb-0">70%</h3>
                            <small class="text-muted">Complete</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-3">Overall Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold">TASKS COMPLETED</p>
                            <h3 class="metric-value mb-1 fw-bold text-success">7/10</h3>
                            <small class="text-muted">3 remaining</small>
                        </div>
                        <div class="icon-circle bg-soft-success">
                            <i class="fas fa-check-double text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold">SCORE IMPROVEMENT</p>
                            <h3 class="metric-value mb-1 fw-bold text-primary">+15</h3>
                            <small class="text-success"><i class="fas fa-arrow-up"></i> From 72 to 87</small>
                        </div>
                        <div class="icon-circle bg-soft-primary">
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold">NEXT DUE</p>
                            <h3 class="metric-value mb-1 fw-bold text-warning">Feb 15</h3>
                            <small class="text-muted">5 days left</small>
                        </div>
                        <div class="icon-circle bg-soft-warning">
                            <i class="fas fa-calendar-alt text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Focus Areas -->
    {{-- <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-bullseye text-primary me-2"></i>Current Focus Areas</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Focus Area 1 -->
                        <div class="col-md-4">
                            <div class="focus-area-card dashboard-card h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-bold mb-0">Active Listening</h6>
                                    <span class="badge bg-soft-primary text-primary">Supervisor</span>
                                </div>
                                <p class="small text-muted mb-3">Due: <strong>Feb 15, 2026</strong></p>
                                
                                <div class="tasks-list">
                                    <div class="task-item mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked disabled id="task1">
                                            <label class="form-check-label w-100" for="task1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Use customer's name 3x per call</span>
                                                    <span class="skill-badge skill-communication">Communication</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="extra-small text-muted">Due: Feb 10</span>
                                                    <span class="badge bg-soft-success text-success extra-small">Verified</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="task-item mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="task2">
                                            <label class="form-check-label w-100" for="task2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Pause before responding</span>
                                                    <span class="skill-badge skill-communication">Communication</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="extra-small text-muted">Due: Feb 12</span>
                                                    <span class="badge bg-soft-warning text-warning extra-small">Pending</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="task-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="task3">
                                            <label class="form-check-label w-100" for="task3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Summarize customer concern</span>
                                                    <span class="skill-badge skill-communication">Communication</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="extra-small text-muted">Due: Feb 15</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Focus Area 2 -->
                        <div class="col-md-4">
                            <div class="focus-area-card dashboard-card h-100 p-3 system">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-bold mb-0">Script Compliance</h6>
                                    <span class="badge bg-soft-secondary text-secondary">System</span>
                                </div>
                                <p class="small text-muted mb-3">Overall Due: <strong>Feb 20, 2026</strong></p>
                                
                                <div class="tasks-list">
                                    <div class="task-item mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked disabled id="task4">
                                            <label class="form-check-label w-100" for="task4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Include mandatory disclaimer</span>
                                                    <span class="skill-badge skill-process">Process</span>
                                                </div>
                                                <div class="mt-1"><span class="extra-small text-muted">Due: Feb 08</span></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="task-item mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="task5">
                                            <label class="form-check-label w-100" for="task5">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Complete verification checklist</span>
                                                    <span class="skill-badge skill-process">Process</span>
                                                </div>
                                                <div class="mt-1"><span class="extra-small text-muted">Due: Feb 18</span></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="task-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked disabled id="task6">
                                            <label class="form-check-label w-100" for="task6">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Use standard closing script</span>
                                                    <span class="skill-badge skill-process">Process</span>
                                                </div>
                                                <div class="mt-1"><span class="extra-small text-muted">Due: Feb 12</span></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Focus Area 3 -->
                        <div class="col-md-4">
                            <div class="focus-area-card dashboard-card h-100 p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-bold mb-0">Product Knowledge</h6>
                                    <span class="badge bg-soft-primary text-primary">Supervisor</span>
                                </div>
                                <p class="small text-muted mb-3">Overall Due: <strong>Feb 25, 2026</strong></p>
                                
                                <div class="tasks-list">
                                    <div class="task-item mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="task7">
                                            <label class="form-check-label w-100" for="task7">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Review warranty policy updates</span>
                                                    <span class="skill-badge skill-knowledge">Knowledge</span>
                                                </div>
                                                <div class="mt-1"><span class="extra-small text-muted">Due: Feb 22</span></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="task-item mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="task8">
                                            <label class="form-check-label w-100" for="task8">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-500">Complete product quiz (80%+)</span>
                                                    <span class="skill-badge skill-knowledge">Knowledge</span>
                                                </div>
                                                <div class="mt-1"><span class="extra-small text-muted">Due: Feb 25</span></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Learning Resources & Practice -->
    <div class="row g-4 mb-4">
        <!-- Learning Resources -->
        <div class="col-md-8">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-graduation-cap text-success me-2"></i>Learning Resources</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Quick Tips -->
                    <h6 class="fw-bold small text-muted mb-3">SHORT TIPS (TEXT)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info border-0 mb-0">
                                <h6 class="fw-bold small mb-1"><i class="fas fa-lightbulb me-1"></i> Active Listening Trick</h6>
                                <p class="small mb-0">Repeat the customer's last 2-3 words to show you're engaged and encourage them to elaborate.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success border-0 mb-0">
                                <h6 class="fw-bold small mb-1"><i class="fas fa-check me-1"></i> Script Mastery</h6>
                                <p class="small mb-0">Use the "hamburger method": Script → Personal touch → Script to sound natural yet compliant.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Micro Videos -->
                    <h6 class="fw-bold small text-muted mb-3">MICRO-VIDEOS (1-3 MIN)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="resource-card">
                                <div class="video-thumbnail mb-2">
                                    <div class="play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold small mb-1">Handling Angry Customers</h6>
                                <p class="extra-small text-muted mb-0"><i class="fas fa-clock me-1"></i> 2:15</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="resource-card">
                                <div class="video-thumbnail mb-2" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <div class="play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold small mb-1">Verification Best Practices</h6>
                                <p class="extra-small text-muted mb-0"><i class="fas fa-clock me-1"></i> 1:45</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="resource-card">
                                <div class="video-thumbnail mb-2" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <div class="play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold small mb-1">Building Rapport Quickly</h6>
                                <p class="extra-small text-muted mb-0"><i class="fas fa-clock me-1"></i> 2:50</p>
                            </div>
                        </div>
                    </div>

                    <!-- Do/Don't Examples -->
                    <h6 class="fw-bold small text-muted mb-3">DO / DON'T EXAMPLES</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border border-success rounded bg-soft-success">
                                <h6 class="fw-bold small text-success mb-2"><i class="fas fa-check-circle me-1"></i> DO</h6>
                                <p class="small mb-0">"I understand how frustrating this must be for you, [Name]. Let me make sure I fix this right away."</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border border-danger rounded bg-soft-danger">
                                <h6 class="fw-bold small text-danger mb-2"><i class="fas fa-times-circle me-1"></i> DON'T</h6>
                                <p class="small mb-0">"That's not my fault. You should have read the terms and conditions before signing up."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Practice & Self-Review -->
        <div class="col-md-4">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0"><i class="fas fa-headphones text-warning me-2"></i>Practice Section</h6>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted mb-3">RECOMMENDED CALLS TO RE-LISTEN</h6>
                    
                    <div class="call-review-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small">Call #CUS-3301</span>
                            <span class="badge bg-soft-danger text-danger">Score: 72</span>
                        </div>
                        <p class="extra-small text-muted mb-2">Focus on: Empathy & Mirroring</p>
                        <button class="btn btn-sm btn-white border w-100 shadow-sm" onclick="playMockCall('CUS-3301')">
                            <i class="fas fa-play me-1 text-primary"></i> Listen & Self-Review
                        </button>
                    </div>

                    <div class="call-review-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small">Call #CUS-1120</span>
                            <span class="badge bg-soft-success text-success">Score: 85</span>
                        </div>
                        <p class="extra-small text-muted mb-2">Example of: Positive Closing</p>
                        <button class="btn btn-sm btn-white border w-100 shadow-sm" onclick="playMockCall('CUS-1120')">
                            <i class="fas fa-play me-1 text-primary"></i> Review Gold Example
                        </button>
                    </div>
                    <div class="call-review-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small">Call #CUS-1120</span>
                            <span class="badge bg-soft-success text-success">Score: 85</span>
                        </div>
                        <p class="extra-small text-muted mb-2">Example of: Positive Closing</p>
                        <button class="btn btn-sm btn-white border w-100 shadow-sm" onclick="playMockCall('CUS-1120')">
                            <i class="fas fa-play me-1 text-primary"></i> Review Gold Example
                        </button>
                    </div>
                    <div class="call-review-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small">Call #CUS-1120</span>
                            <span class="badge bg-soft-success text-success">Score: 85</span>
                        </div>
                        <p class="extra-small text-muted mb-2">Example of: Positive Closing</p>
                        <button class="btn btn-sm btn-white border w-100 shadow-sm" onclick="playMockCall('CUS-1120')">
                            <i class="fas fa-play me-1 text-primary"></i> Review Gold Example
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History: Previous Coaching Plans -->
    {{-- <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-history text-secondary me-2"></i>Coaching History & Past Plans</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive history-table-container">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Plan Period</th>
                                    <th>Focus Areas</th>
                                    <th>Status</th>
                                    <th>Final Score</th>
                                    <th>Skills Improved</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold small">Jan 1 - Jan 25, 2026</div>
                                    </td>
                                    <td class="small">Rapport, Hold Time</td>
                                    <td><span class="badge bg-soft-success text-success">Completed</span></td>
                                    <td><h6 class="mb-0 fw-bold">82/100</h6></td>
                                    <td>
                                        <span class="skill-badge skill-communication me-1">Empathy</span>
                                        <span class="skill-badge skill-process">Efficiency</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-white border px-2 shadow-xs"><i class="fas fa-eye text-muted"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold small">Dec 1 - Dec 20, 2025</div>
                                    </td>
                                    <td class="small">Technical Proficiency</td>
                                    <td><span class="badge bg-soft-success text-success">Completed</span></td>
                                    <td><h6 class="mb-0 fw-bold">78/100</h6></td>
                                    <td>
                                        <span class="skill-badge skill-knowledge">Product Knowledge</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-white border px-2 shadow-xs"><i class="fas fa-eye text-muted"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Tracking & Feedback section (already exists below in previous code, just ensuring it follows history) -->
    <div class="row g-4">
        <!-- Progress Tracking -->
        <div class="col-md-7">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-area text-info me-2"></i>Progress Tracking</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold small text-muted mb-3">SCORE TREND</h6>
                            <div class="chart-container">
                                <canvas id="scoreTrendChart"></canvas>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <div class="text-center">
                                    <h5 class="fw-bold text-danger mb-0">72</h5>
                                    <small class="text-muted">Before</small>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                                <div class="text-center">
                                    <h5 class="fw-bold text-success mb-0">87</h5>
                                    <small class="text-muted">Current</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold small text-muted mb-3">ACTION STATUS</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Completed Tasks</span>
                                    <span class="small fw-bold text-success">7 of 10</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 70%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Videos Watched</span>
                                    <span class="small fw-bold text-primary">2 of 3</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 66%"></div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <div class="improvement-badge">
                                    <i class="fas fa-trophy"></i>
                                    <span>Fast Learner!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor Feedback -->
        <div class="col-md-5">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-comments text-primary me-2"></i>Supervisor Feedback</h6>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted mb-3">LATEST FEEDBACK</h6>
                    <div class="p-3 bg-light rounded mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold small mb-0">Sarah Johnson</h6>
                                <small class="text-muted">Feb 8, 2026</small>
                            </div>
                            <span class="badge bg-success">Positive</span>
                        </div>
                        <p class="small mb-0">"Great improvement in your active listening! I noticed you're now pausing before responding and using the customer's name consistently. Keep up this momentum!"</p>
                    </div>

                    <h6 class="fw-bold small text-muted mb-3">SKILLS IMPROVED</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-soft-success text-success px-3 py-2">
                            <i class="fas fa-check me-1"></i> Empathy
                        </span>
                        <span class="badge bg-soft-success text-success px-3 py-2">
                            <i class="fas fa-check me-1"></i> Script Compliance
                        </span>
                        <span class="badge bg-soft-primary text-primary px-3 py-2">
                            <i class="fas fa-spinner fa-spin me-1"></i> Product Knowledge
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
@endsection

@push('scripts')
<script>
    // Mini Trend Chart
    const ctx = document.getElementById('scoreTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Score',
                data: [72, 76, 82, 87],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0d6efd',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 8,
                    cornerRadius: 6
                }
            },
            scales: {
                x: { 
                    grid: { display: false },
                    ticks: { font: { size: 9 } }
                },
                y: { 
                    min: 60,
                    max: 100,
                    grid: { borderDash: [3, 3], color: '#e2e8f0' },
                    ticks: { stepSize: 10, font: { size: 9 } }
                }
            }
        }
    });
</script>
@endpush
