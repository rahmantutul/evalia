@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
    <style>
        /* Tooltip styling */
        .action-btn {
            position: relative;
            margin-left: 5px;
        }

        .action-btn:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .tooltip-text {
            visibility: hidden;
            width: max-content;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 3px 6px;
            position: absolute;
            z-index: 1;
            top: -30px;
            right: 50%;
            transform: translateX(50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            white-space: nowrap;
        }

        .table td .btn-group {
            display: flex;
            float: left;
        }
        
        /* Sticky Statistics Bar Styles */
        .sticky-top-bar {
            transition: all 0.3s ease;
            top: 20px;
        }
        
        .sticky-top-bar.sticky-active {
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-item {
            border-right: 1px solid #e9ecef;
            padding-right: 15px;
        }
        
        .stat-item:last-child {
            border-right: none;
            padding-right: 0;
        }
        
        @media (max-width: 768px) {
            .stat-item {
                border-right: none;
                border-bottom: 1px solid #e9ecef;
                padding-right: 0;
                padding-bottom: 10px;
                margin-bottom: 10px;
            }
            
            .stat-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
        }
    </style>
     <style>
    .btn-group {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 6px;
        color: #6c757d;
        background: #e8eff5;
        transition: all 0.2s ease;
        text-decoration: none;
        position: relative;
    }

    .btn-icon:hover {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-1px);
    }

    .btn-icon:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 100;
    }

    .btn-delete:hover {
        background: #dc3545;
        color: white;
    }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Sticky Statistics Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm sticky-top-bar">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <!-- Total Companies -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-building text-primary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="totalCompanies">0</h5>
                                    <small class="text-muted">Total Companies</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Tasks -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-tasks text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="activeTasks">0</h5>
                                    <small class="text-muted">Active Tasks</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Completed Tasks -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-check-circle text-info fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="completedTasks">0</h5>
                                    <small class="text-muted">Completed Tasks</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pending Analysis -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-clock text-warning fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="pendingAnalysis">0</h5>
                                    <small class="text-muted">Pending Analysis</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Agents -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-secondary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-users text-secondary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="totalAgents">0</h5>
                                    <small class="text-muted">Total Agents</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Rate -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-chart-line text-danger fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="successRate">0%</h5>
                                    <small class="text-muted">Success Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company List -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">                      
                        <h4 class="card-title mb-0">Company List</h4>
                        <a href="{{ route('user.company.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Create New</a>                  
                    </div>                                 
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif 

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <table class="table datatable mb-0" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Industry</th>
                                    <th>Agents</th>
                                    <th>Location</th>
                                    <th style="text-align: center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAgents = 0;
                                    $totalActiveTasks = 0;
                                    $totalCompletedTasks = 0;
                                    $totalPendingAnalysis = 0;
                                @endphp
                                @foreach($companies as $company)
                                    @php
                                        $industries = ['Tech', 'Finance', 'Healthcare', 'Education', 'Logistics'];
                                        $locations = ['New York', 'San Francisco', 'Chicago', 'Los Angeles', 'Miami'];
                                        $agents = rand(1, 5);
                                        $activeTasks = rand(1, 8);
                                        $completedTasks = rand(5, 25);
                                        $pendingAnalysis = rand(1, 5);
                                        
                                        $industry = $industries[array_rand($industries)];
                                        $location = $locations[array_rand($locations)];
                                        
                                        $totalAgents += $agents;
                                        $totalActiveTasks += $activeTasks;
                                        $totalCompletedTasks += $completedTasks;
                                        $totalPendingAnalysis += $pendingAnalysis;
                                    @endphp
                                    <tr>
                                        <td>{{ $company['name'] }}</td>
                                        <td>{{ $industry }}</td>
                                        <td>{{ $agents }}</td>
                                        <td>{{ $location }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.company.view',$company['id']) }}" class="btn btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-icon" data-bs-toggle="modal" data-bs-target="#audioUploadModal{{ $company['id'] }}" style="height: 38px;" title="Add a new task">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <a href="{{ route('user.task.list',$company['id']) }}" class="btn btn-icon" title="Task List">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                <a href="{{ route('user.company.edit',$company['id']) }}" class="btn btn-icon" title="Settings">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                                <a href="{{ route('user.company.delete',$company['id']) }}" onclick="return confirm('Are you sure to delete this?')" class="btn btn-icon btn-delete" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="audioUploadModal{{ $company['id'] }}" tabindex="-1" aria-labelledby="audioUploadModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <!-- Modal Header -->
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-600 text-black" id="audioUploadModalLabel" >
                                                    <i class="fas fa-wave-square text-black me-2"></i> Audio Upload Form
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
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
                            <input type="file" name="customer_audio" class="d-none" accept="audio/*" required>
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
                            <input type="file" name="agent_audio" class="d-none" accept="audio/*" required>
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
                        <p class="text-muted small mb-4">Upload pre-mixed audio file (optional)</p>
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
                            <select name="agent_id" id="agent_id" class="form-select form-select-lg py-3 select2" required>
                                <option value="">-- Select Agent --</option>
                                    @foreach($companyAgents as $agent)
                                        <option value="{{ $agent['id'] }}">
                                            {{ $agent['agent_id_display'] }} - {{ $agent['name'] }} 
                                            @if($agent['email'])
                                                ({{ $agent['email'] }})
                                            @endif
                                        </option>
                                    @endforeach
                            </select>
                            @error('agent_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden company_id -->
        <input type="hidden" name="company_id" value="{{ $company['id'] }}">

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
                                @endforeach         
                            </tbody>
                        </table>                                             
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate statistics from actual data
        const stats = {
            totalCompanies: {{ count($companies) }},
            activeTasks: {{ $totalActiveTasks }},
            completedTasks: {{ $totalCompletedTasks }},
            pendingAnalysis: {{ $totalPendingAnalysis }},
            totalAgents: {{ $totalAgents }},
            successRate: {{ $totalCompletedTasks > 0 ? round(($totalCompletedTasks / ($totalCompletedTasks + $totalActiveTasks + $totalPendingAnalysis)) * 100) : 0 }}
        };

        // Animate counting up for each statistic
        function animateCounter(elementId, finalValue, suffix = '') {
            const element = document.getElementById(elementId);
            let current = 0;
            const increment = finalValue / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    current = finalValue;
                    clearInterval(timer);
                }
                if (suffix === '%') {
                    element.textContent = Math.floor(current) + suffix;
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 30);
        }

        // Start animations
        animateCounter('totalCompanies', stats.totalCompanies);
        animateCounter('activeTasks', stats.activeTasks);
        animateCounter('completedTasks', stats.completedTasks);
        animateCounter('pendingAnalysis', stats.pendingAnalysis);
        animateCounter('totalAgents', stats.totalAgents);
        animateCounter('successRate', stats.successRate, '%');

        // Make the statistics bar sticky when scrolling
        const stickyBar = document.querySelector('.sticky-top-bar');
        const originalOffsetTop = stickyBar.offsetTop;
        
        function handleScroll() {
            if (window.pageYOffset > originalOffsetTop) {
                stickyBar.classList.add('sticky-active');
            } else {
                stickyBar.classList.remove('sticky-active');
            }
        }
        
        window.addEventListener('scroll', handleScroll);

        // Add CSS for sticky behavior
        const style = document.createElement('style');
        style.textContent = `
            .sticky-top-bar.sticky-active {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1020;
                margin: 0 15px;
                width: calc(100% - 30px);
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                animation: slideDown 0.3s ease;
            }
            
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            .stat-item {
                transition: transform 0.2s ease;
            }
            
            .stat-item:hover {
                transform: translateY(-2px);
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush