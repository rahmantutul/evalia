@extends('user.layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-dashboard.css') }}">

    <style>
        .btn-period.active {
            background-color: #0d6efd;
            color: white;
        }
        .trend-up {
            color: #198754;
        }
        .trend-down {
            color: #dc3545;
        }
        .trend-neutral {
            color: #6c757d;
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
    .top-performer-badge:hover, .high-achiever-badge:hover, .consistent-badge:hover, .needs-improvement-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        color: inherit !important;
    }
        .progress-thin {
            height: 8px;
        }
        .avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
    </style>
@endpush
@section('content')

    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Company Analysis Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive overview of call quality metrics and agent performance</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a class="btn btn-outline-secondary fw-600 shadow-sm text-back d-flex align-items-center justify-content-center" 
                    href="{{ route('user.company.edit',$company['company_id']) }}" style="height: 38px;">
                        Settings
                    </a>
                    <button type="button" class="btn btn-outline-secondary fw-600 shadow-sm text-back d-flex align-items-center justify-content-center" 
                            data-bs-toggle="modal" data-bs-target="#audioUploadModal" style="height: 38px;">
                        <i class="fas fa-plus me-2"></i> Upload & Analyze Audio
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary fw-600 shadow-sm text-back dropdown-toggle d-flex align-items-center justify-content-center" 
                                type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                            Last 30 Days
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="periodDropdown">
                            <li><a class="dropdown-item period-filter active" href="#" data-period="30">Last 30 Days</a></li>
                            <li><a class="dropdown-item period-filter" href="#" data-period="7">Last 7 Days</a></li>
                            <li><a class="dropdown-item period-filter" href="#" data-period="90">Last Quarter</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-secondary fw-600 shadow-sm text-back d-flex align-items-center justify-content-center" 
                            id="exportBtn" style="height: 38px;">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <a class="btn btn-outline-secondary fw-600 shadow-sm text-back d-flex align-items-center justify-content-center" 
                    href="{{ route('user.company.list') }}" style="height: 38px;">
                        Back to Companies
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row g-4 mb-4" id="kpiCards">
            <!-- Cards will be updated dynamically -->
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Quality Score Trend</h6>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-period active" data-granularity="daily">Daily</button>
                                <button class="btn btn-period" data-granularity="weekly">Weekly</button>
                                <button class="btn btn-period" data-granularity="monthly">Monthly</button>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
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
                        <!-- Will be updated dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Tables -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="mb-0 fw-600 d-flex align-items-center">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Recent Analyses
                            </h5>
                            
                            <!-- Filter Controls -->
                            <div class="d-flex flex-wrap gap-2">
                                <!-- Status Filter -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="statusFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter me-1"></i>
                                        Status: {{ request('status', 'all') === 'all' ? 'All' : ucfirst(request('status')) }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                                        <li><a class="dropdown-item status-filter {{ request('status', 'all') === 'all' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => 'all', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="all">All Status</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item status-filter {{ request('status') === 'completed' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => 'completed', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="completed">Completed</a></li>
                                        <li><a class="dropdown-item status-filter {{ request('status') === 'processing' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => 'processing', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="processing">Processing</a></li>
                                        <li><a class="dropdown-item status-filter {{ request('status') === 'failed' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => 'failed', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="failed">Failed</a></li>
                                    </ul>
                                </div>
                                
                                <!-- Time Filter -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="timeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-calendar me-1"></i>
                                        Time: 
                                        @switch(request('time_range', 'all'))
                                            @case('today') Today @break
                                            @case('yesterday') Yesterday @break
                                            @case('last7') Last 7 Days @break
                                            @case('last30') Last 30 Days @break
                                            @default All Time
                                        @endswitch
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="timeFilterDropdown">
                                        <li><a class="dropdown-item time-filter {{ request('time_range', 'all') === 'all' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'all']) }}" 
                                            data-time="all">All Time</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'today' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'today']) }}" 
                                            data-time="today">Today</a></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'yesterday' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'yesterday']) }}" 
                                            data-time="yesterday">Yesterday</a></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'last7' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'last7']) }}" 
                                            data-time="last7">Last 7 Days</a></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'last30' ? 'active' : '' }}" 
                                            href="{{ route('user.company.view', ['id' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'last30']) }}" 
                                            data-time="last30">Last 30 Days</a></li>
                                    </ul>
                                </div>
                                
                                <!-- Clear Filters -->
                                @if(request('status') || request('time_range'))
                                <a href="{{ route('user.company.view', ['id' => $company_id]) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>
                                    Clear Filters
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($taskList as $task)
                                        <tr>
                                            <td class="ps-4 fw-500">#{{ $task['id'] }}</td>
                                            <td>
                                                <span class="badge bg-opacity-10 
                                                    @if($task['status'] === 'completed') bg-success text-success 
                                                    @elseif($task['status'] === 'processing') bg-warning text-warning 
                                                    @else bg-danger text-danger @endif
                                                    rounded-pill py-1 px-3">
                                                    {{ ucfirst($task['status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-500 small">
                                                        {{ \Carbon\Carbon::parse($task['created_at'])->format('M j, Y') }}
                                                    </span>
                                                    <span class="text-muted small">
                                                        {{ \Carbon\Carbon::parse($task['created_at'])->format('g:i A') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <!-- View Button -->
                                                    <a href="{{ route('user.task.details',$task['id']) }}" 
                                                    class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Delete Button -->
                                                    <a href="{{ route('user.task.delete',$task['id']) }}" 
                                                    class="btn btn-sm  btn-outline-danger" title="Delete Task"
                                                    onclick="return confirm('Are you sure to delete this task?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                    
                                                    <!-- Re-run Button -->
                                                    <button class="btn btn-sm btn-outline-secondary"  title="Re-run Task">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($taskList->count() == 0)
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">No tasks found matching your filters</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-top py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $taskList->firstItem() }} to {{ $taskList->lastItem() }} of {{ $taskList->total() }} entries
                            </div>
                            <div>
                                {{ $taskList->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Agent Performance</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="agentSortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Top Performers
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="agentSortDropdown">
                                    <li><a class="dropdown-item agent-sort active" href="#" data-sort="top">Top Performers</a></li>
                                    <li><a class="dropdown-item agent-sort" href="#" data-sort="needs-improvement">Needs Improvement</a></li>
                                    <li><a class="dropdown-item agent-sort" href="#" data-sort="all">All Agents</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3" id="agentPerformance">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="audioUploadModal" tabindex="-1" aria-labelledby="audioUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
            
            <!-- Modal Header -->
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-600 text-black" id="audioUploadModalLabel" >
                <i class="fas fa-wave-square text-black me-2"></i> Audio Upload Form
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('user.task.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4 mb-4">

                    {{-- Customer Audio --}}
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-2">
                                <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                                    <span class="bg-dark bg-opacity-10 text-primary p-2 me-2 rounded">
                                        <i class="fas fa-microphone"></i>
                                    </span>
                                    Customer Audio
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="text-muted small mb-4">Upload customer audio file in WAV or MP3 format</p>
                                <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                                    <i class="bi bi-cloud-upload text-primary fs-1 mb-2"></i>
                                    <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                                    <span class="text-muted small mb-3">or click to browse</span>
                                    <span class="badge bg-light text-dark px-3 py-2">Max 50MB</span>
                                    <input type="file" name="customer_audio" class="d-none" accept="audio/*">
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Agent Audio --}}
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-2">
                                <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                                    <span class="bg-danger bg-opacity-10 text-danger p-2 me-2 rounded">
                                        <i class="fas fa-headset fs-5"></i>
                                    </span>
                                    Agent Audio
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="text-muted small mb-4">Upload agent audio file in WAV or MP3 format</p>
                                <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                                    <i class="bi bi-cloud-upload text-danger fs-1 mb-2"></i>
                                    <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                                    <span class="text-muted small mb-3">or click to browse</span>
                                    <span class="badge bg-light text-dark px-3 py-2">Max 50MB</span>
                                    <input type="file" name="agent_audio" class="d-none" accept="audio/*">
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Combined Audio --}}
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-2">
                                <h5 class="card-title mb-0 fw-600 d-flex align-items-center">
                                    <span class="bg-success bg-opacity-10 text-success p-2 me-2 rounded">
                                        <i class="fas fa-microphone fs-5"></i>
                                    </span>
                                    Combined Audio
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="text-muted small mb-4">Upload pre-mixed audio file</p>
                                <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                                    <i class="bi bi-cloud-upload text-success fs-1 mb-2"></i>
                                    <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                                    <span class="text-muted small mb-3">or click to browse</span>
                                    <span class="badge bg-light text-dark px-3 py-2">Max 100MB</span>
                                    <input type="file" name="combined_audio" class="d-none" accept="audio/*">
                                </label>
                            </div>
                        </div>
                    </div>
                    {{-- Agent Selection Dropdown --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 py-2">
                                    <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                                        <span class="bg-info bg-opacity-10 text-info p-2 me-2 rounded">
                                            <i class="fas fa-user-tie"></i>
                                        </span>
                                        Select Agent
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="agent_id" class="form-label fw-500 mb-2">Choose an agent for this analysis</label>
                                        @php
                                            // Ensure $companyAgents is always an array
                                            $agents = is_array($companyAgents) ? $companyAgents : [];
                                        @endphp

                                        <select name="agent_id" id="agent_id" class="form-select form-select-lg py-3 select2" required>
                                            <option value="">-- Select Agent --</option>
                                            @if(count($agents) > 0)
                                                @foreach($agents as $agent)
                                                    <option value="{{ $agent['id'] }}">
                                                        {{ $agent['full_name'] }} 
                                                        @if(!empty($agent['email']))
                                                            ({{ $agent['email'] }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">No agent found for this company</option>
                                            @endif
                                        </select>
                                        @error('agent_id')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden company_id -->
                <input type="hidden" name="company_id" value="{{ $company['company_id'] }}">

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center mb-2">
                    <button type="submit" class="btn btn-primary px-5 py-2 me-3 rounded-pill fw-600 shadow-sm">
                        <i class="fas fa-chart-line me-2"></i> Analyze Audio
                    </button>
                </div>
                </form>
            </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // Dummy data generators
    const generateDummyData = {
        kpiData: function(period) {
            let callsEvaluated;
            if (period === 7) callsEvaluated = 124;
            else if (period === 30) callsEvaluated = 485;
            else callsEvaluated = 1240;
            
            return {
                totalGroups: 5,
                avgQualityScore: 92.4,
                callsEvaluated: callsEvaluated,
                avgResponseTime: 8.5
            };
        },
        
        trendData: function(granularity, period) {
            let labels = [];
            let data = [];
            const now = new Date();
            
            if (granularity === 'daily') {
                const days = period === 7 ? 7 : 30;
                for (let i = days; i >= 1; i--) {
                    const date = new Date();
                    date.setDate(now.getDate() - i);
                    labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                    data.push(85 + Math.random() * 10);
                }
            } else if (granularity === 'weekly') {
                const weeks = period === 90 ? 12 : 4;
                for (let i = weeks; i >= 1; i--) {
                    labels.push(`Week ${weeks - i + 1}`);
                    data.push(88 + Math.random() * 8);
                }
            } else {
                for (let i = 5; i >= 0; i--) {
                    const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
                    labels.push(date.toLocaleDateString('en-US', { month: 'long' }));
                    data.push(90 + Math.random() * 5);
                }
            }
            return { labels, data };
        },
        
        sentimentData: function() {
            return { positive: 78, neutral: 15, negative: 7 };
        },
        
        departmentPerformance: function(sortBy) {
            const depts = [
                { name: "Amman Call Center", score: 94, trend: 2.1, calls: 150 },
                { name: "Technical Support", score: 89, trend: 1.5, calls: 95 },
                { name: "Tele-Sales", score: 87, trend: -0.5, calls: 120 },
                { name: "Complaints & Feedback", score: 91, trend: 0.8, calls: 65 },
                { name: "Customer Relations", score: 93, trend: 1.2, calls: 55 }
            ];
            return depts.sort((a, b) => b.score - a.score);
        },
        
        agentPerformance: function(sortBy) {
            const agents = [
                { name: "نادي البديري", company: "مركز الاتصال", score: 92.3, trend: 1.5, calls: 5, avatar: "https://ui-avatars.com/api/?name=Nadi+Albidiri&background=0d6efd&color=fff" },
                { name: "سارة الخطيب", company: "الدعم الفني", score: 89.1, trend: 2.0, calls: 4, avatar: "https://ui-avatars.com/api/?name=Sara+Khatib&background=198754&color=fff" },
                { name: "محمود المصري", company: "المبيعات", score: 87.5, trend: -1.2, calls: 4, avatar: "https://ui-avatars.com/api/?name=Mahmoud+Masri&background=ffc107&color=fff" },
                { name: "ليلى حسن", company: "علاقات العملاء", score: 84.2, trend: 0.5, calls: 3, avatar: "https://ui-avatars.com/api/?name=Layla+Hassan&background=0dcaf0&color=fff" },
                { name: "أحمد منصور", company: "الشكاوى", score: 78.4, trend: -3.5, calls: 3, avatar: "https://ui-avatars.com/api/?name=Ahmed+Mansour&background=dc3545&color=fff" }
            ];
            if (sortBy === 'top') return agents.sort((a, b) => b.score - a.score).slice(0, 3);
            if (sortBy === 'needs-improvement') return agents.sort((a, b) => a.score - b.score).slice(0, 3);
            return agents.sort((a, b) => b.score - a.score);
        }
    };

    // Chart instances
    let trendChart = null;
    let sentimentChart = null;
    
    // Current filters
    let currentPeriod = 30;
    let currentGranularity = 'daily';
    let currentCompanySort = 'score';
    let currentAgentSort = 'top';

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        updateDashboard();
        setupEventListeners();
    });

    // Set up event listeners
    function setupEventListeners() {
        document.querySelectorAll('.period-filter').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.period-filter').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentPeriod = parseInt(this.getAttribute('data-period'));
                document.getElementById('periodDropdown').textContent = this.textContent;
                updateDashboard();
            });
        });

        document.querySelectorAll('.btn-period').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-period').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentGranularity = this.getAttribute('data-granularity');
                updateTrendChart();
            });
        });

        document.querySelectorAll('.agent-sort').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.agent-sort').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentAgentSort = this.getAttribute('data-sort');
                updateAgentPerformance();
            });
        });

        document.getElementById('exportBtn').addEventListener('click', exportDashboardData);
    }

    // Update entire dashboard
    function updateDashboard() {
        updateKpiCards();
        updateTrendChart();
        updateSentimentChart();
        updateAgentPerformance();
    }

    // Update KPI cards
    function updateKpiCards() {
        const kpiData = generateDummyData.kpiData(currentPeriod);
        const kpiCards = document.getElementById('kpiCards');
        
        kpiCards.innerHTML = `
            <div class="col-md-3">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Total Departments</p>
                                <h3 class="metric-value mb-1">${kpiData.totalGroups}</h3>
                                <small class="text-muted">Active units</small>
                            </div>
                            <div class="icon-circle bg-soft-primary">
                                <i class="fas fa-th-large text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Quality Score</p>
                                <h3 class="metric-value mb-1">${kpiData.avgQualityScore.toFixed(1)}%</h3>
                                <small class="trend-up"><i class="fas fa-arrow-up me-1"></i> ${(Math.random() * 3).toFixed(1)}% improvement</small>
                            </div>
                            <div class="icon-circle bg-soft-success">
                                <i class="fas fa-chart-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Evaluated Calls</p>
                                <h3 class="metric-value mb-1">${kpiData.callsEvaluated}</h3>
                                <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> ${Math.floor(Math.random() * 100)} this week</small>
                            </div>
                            <div class="icon-circle bg-soft-info">
                                <i class="fas fa-phone-alt text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Avg. Response Time</p>
                                <h3 class="metric-value mb-1">${kpiData.avgResponseTime.toFixed(1)}s</h3>
                                <small class="trend-down"><i class="fas fa-arrow-down me-1"></i> ${(Math.random() * 1).toFixed(1)}s faster</small>
                            </div>
                            <div class="icon-circle bg-soft-warning">
                                <i class="fas fa-stopwatch text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Update trend chart
    function updateTrendChart() {
        const ctx = document.getElementById('trendChart').getContext('2d');
        const trendData = generateDummyData.trendData(currentGranularity, currentPeriod);
        
        if (trendChart) {
            trendChart.destroy();
        }
        
        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Quality Score',
                    data: trendData.data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100,
                        grid: { drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter' } }
                    }
                }
            }
        });
    }

    // Update sentiment chart
    function updateSentimentChart() {
        const ctx = document.getElementById('sentimentChart').getContext('2d');
        const sentimentData = generateDummyData.sentimentData();
        
        if (sentimentChart) {
            sentimentChart.destroy();
        }
        
        sentimentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [sentimentData.positive, sentimentData.neutral, sentimentData.negative],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Inter' } }
                    }
                },
                cutout: '70%'
            }
        });
        
        document.getElementById('sentimentBadges').innerHTML = `
            <span class="sentiment-badge bg-success bg-opacity-10 text-success">${sentimentData.positive}% Positive</span>
            <span class="sentiment-badge bg-warning bg-opacity-10 text-warning">${sentimentData.neutral}% Neutral</span>
            <span class="sentiment-badge bg-danger bg-opacity-10 text-danger">${sentimentData.negative}% Negative</span>
        `;
    }

    // Update agent performance
    function updateAgentPerformance() {
        const agents = generateDummyData.agentPerformance(currentAgentSort);
        const agentContainer = document.getElementById('agentPerformance');
        
        let html = '';
        agents.forEach(agent => {
            const trendClass = agent.trend > 0 ? 'trend-up' : (agent.trend < 0 ? 'trend-down' : 'trend-neutral');
            
            let badgeHtml = '';
            if (agent.score >= 90) {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="top-performer-badge ms-2"><i class="fas fa-trophy me-1"></i> Top Performer</a>`;
            } else if (agent.score >= 80) {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="high-achiever-badge ms-2"><i class="fas fa-medal me-1"></i> High Achiever</a>`;
            } else if (agent.score >= 70) {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="consistent-badge ms-2"><i class="fas fa-star me-1"></i> Consistent</a>`;
            } else {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="needs-improvement-badge ms-2"><i class="fas fa-graduation-cap me-1"></i> Needs Coaching</a>`;
            }

            html += `
                <div class="agent-card ${agent.score >= 90 ? 'top-agent' : ''} ${agent.score < 75 ? 'low-agent' : ''} shadow-sm border-0 mb-3">
                    <div class="d-flex align-items-center">
                        <img src="${agent.avatar}" class="avatar-sm me-3 border shadow-sm">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold d-flex align-items-center">
                                <a style="color: #000;" href="{{ route('user.agents.index') }}"> ${agent.name}</a> 
                                ${badgeHtml}
                            </h6>
                            <small class="text-muted">${agent.company} • ${agent.calls} calls</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 ${agent.score >= 90 ? 'text-success' : (agent.score < 75 ? 'text-danger' : '')} fw-bold">${agent.score}%</h5>
                            <small class="${trendClass}">${agent.trend > 0 ? '+' : ''}${agent.trend}%</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
            <div class="mt-4 p-3 bg-light rounded-4">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted fw-bold">Performance Distribution</small>
                    <small class="text-muted">${agents.length} agents</small>
                </div>
                <div class="progress progress-thin" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: 25%"></div>
                    <div class="progress-bar bg-primary" style="width: 55%"></div>
                    <div class="progress-bar bg-warning" style="width: 15%"></div>
                    <div class="progress-bar bg-danger" style="width: 5%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2 flex-wrap text-muted small">
                    <span><i class="fas fa-circle text-success me-1 small"></i> Exceptional (25%)</span>
                    <span><i class="fas fa-circle text-primary me-1 small"></i> High (55%)</span>
                    <span><i class="fas fa-circle text-warning me-1 small"></i> Average (15%)</span>
                    <span><i class="fas fa-circle text-danger me-1 small"></i> Poor (5%)</span>
                </div>
            </div>
        `;
        agentContainer.innerHTML = html;
    }

    // Export dashboard data to Excel
    function exportDashboardData() {
        const kpiData = generateDummyData.kpiData(currentPeriod);
        const trendData = generateDummyData.trendData(currentGranularity, currentPeriod);
        const agents = generateDummyData.agentPerformance(currentAgentSort);
        
        const wb = XLSX.utils.book_new();
        const kpiSheetData = [
            ['Metric', 'Value', 'Context'],
            ['Total Departments', kpiData.totalGroups, 'Internal Units'],
            ['Average Quality Score', `${kpiData.avgQualityScore}%`, 'Improvement shown'],
            ['Calls Evaluated', kpiData.callsEvaluated, 'Processed audio'],
            ['Average Response Time', `${kpiData.avgResponseTime}s`, 'System average']
        ];
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(kpiSheetData), 'KPIs');
        
        const agentSheetData = [['Agent', 'Category', 'Score', 'Trend', 'Calls']];
        agents.forEach(agent => agentSheetData.push([agent.name, agent.company, `${agent.score}%`, `${agent.trend}%`, agent.calls]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(agentSheetData), 'Agent Performance');
        
        const fileName = `${document.querySelector('h4').textContent.trim()}_Export_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, fileName);
    }
</script>
@endpush