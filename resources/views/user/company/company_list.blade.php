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
                                <i class="fas fa-building text-primary me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="totalCompanies">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Companies</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Tasks -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tasks text-success me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="activeTasks">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Active Tasks</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Completed Tasks -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="completedTasks">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Completed</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pending Analysis -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="pendingAnalysis">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Pending</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Agents -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-secondary me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="totalAgents">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Agents</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Rate -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-star text-danger me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="averageQuality">0%</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Quality Score</p>
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
                         @if(session('user.role.name') !== 'Supervisor')
                        <a href="{{ route('user.company.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Create New</a>                  
                        @endif
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
                                    <th>Industry Sector</th>
                                    <th>Sources</th>
                                    <th>Agents</th>
                                    <th>Location</th>
                                    <th style="text-align: center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalCompanies = count($companies);
                                    
                                    function getSourceBadge($source) {
                                        $map = [
                                            'api' => ['name' => 'API', 'color' => '#0a66c2', 'icon' => 'fas fa-code'],
                                            'avaya' => ['name' => 'Avaya', 'color' => '#d32f2f', 'icon' => 'fas fa-phone'],
                                            'genesys' => ['name' => 'Genesys', 'color' => '#2e7d32', 'icon' => 'fas fa-headset'],
                                            'fb' => ['name' => 'Facebook', 'color' => '#1877f2', 'icon' => 'fab fa-facebook-f'],
                                            'linkedin' => ['name' => 'LinkedIn', 'color' => '#0077b5', 'icon' => 'fab fa-linkedin-in'],
                                            'inta' => ['name' => 'Instagram', 'color' => '#e4405f', 'icon' => 'fab fa-instagram'],
                                            'tiktok' => ['name' => 'TikTok', 'color' => '#000000', 'icon' => 'fab fa-tiktok'],
                                            'snap' => ['name' => 'Snapchat', 'color' => '#fffc00', 'text' => '#000', 'icon' => 'fab fa-snapchat-ghost'],
                                            'x' => ['name' => 'X', 'color' => '#000000', 'icon' => 'fab fa-x-twitter'],
                                            'whatsapp' => ['name' => 'WhatsApp', 'color' => '#25d366', 'icon' => 'fab fa-whatsapp'],
                                            'email' => ['name' => 'Email', 'color' => '#757575', 'icon' => 'fas fa-envelope'],
                                        ];
                                        
                                        $s = $map[strtolower($source)] ?? ['name' => $source, 'color' => '#6c757d', 'icon' => 'fas fa-link'];
                                        $textColor = $s['text'] ?? '#fff';
                                        
                                        return "<span class='badge' style='background-color: {$s['color']}; color: {$textColor} ; font-size: 10px; padding: 5px 10px; margin-right: 4px; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px;' title='{$s['name']}'><i class='{$s['icon']}'></i> {$s['name']}</span>";
                                    }
                                @endphp
                                @foreach($companies as $company)
                                    @php
                                        // Real Location Mapping
                                        $locationMap = [
                                            'ssc-jordan' => 'Amman, Jordan',
                                            'arab-bank' => 'Amman, Jordan',
                                            'orange-jo' => 'Amman, Jordan',
                                            'manaseer-group' => 'Amman, Jordan',
                                            'royal-jordanian' => 'Amman, Jordan'
                                        ];
                                        
                                        $location = $locationMap[$company['id']] ?? 'Amman, Jordan';
                                        
                                        // Stable random count of agents - Minimum 25
                                        $seed = crc32($company['id']);
                                        mt_srand($seed);
                                        $agents = mt_rand(6, 8); 
                                        
                                        // Sources: Only API and Genesys
                                        $randSources = ['api']; // All have API
                                        if (mt_rand(0, 1) > 0 || $company['id'] == 'arab-bank') {
                                            $randSources[] = 'genesys'; // Some also have Genesys
                                        }
                                        
                                        mt_srand(); // Reset
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('user.company.view', $company['id']) }}" class="fw-bold text-primary">
                                                {{ $company['name'] }}
                                            </a>
                                        </td>
                                        <td>{{ $company['group_name'] ?? 'Private Sector' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @php
                                                    $displayedSources = $company['source'] ?? $randSources;
                                                @endphp
                                                @foreach($displayedSources as $rs)
                                                    {!! getSourceBadge($rs) !!}
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $agents }}</td>
                                        <td>{{ $location }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.company.view',$company['id']) }}" class="btn btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user.task.list',$company['id']) }}" class="btn btn-icon" title="Task List">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                  @if(session('user.role.name') !== 'Supervisor')
                                                <a href="{{ route('user.company.edit',$company['id']) }}" class="btn btn-icon" title="Settings">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                                <a href="{{ route('user.company.delete',$company['id']) }}" onclick="return confirm('Are you sure to delete this?')" class="btn btn-icon btn-delete" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    {{--  <!-- Modal -->
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
                                    </div>  --}}
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
        // Use actual statistics from controller
        const stats = {
            totalCompanies: @json(count($companies)),
            activeTasks: @json($totalActiveTasks ?? 0),
            completedTasks: @json($totalCompletedTasks ?? 0),
            pendingAnalysis: @json($totalPendingAnalysis ?? 0),
            totalAgents: @json($totalAgentsCount ?? 0),
            averageQuality: @json($avgQaScore ?? 0)
        };

        // Animate counting up for each statistic
        function animateCounter(elementId, finalValue, suffix = '') {
            const element = document.getElementById(elementId);
            let current = 0;
            const increment = finalValue > 0 ? finalValue / 50 : 0;
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    current = finalValue;
                    clearInterval(timer);
                }
                
                let val = Math.floor(current);
                if (suffix === '%') {
                    element.textContent = val + suffix;
                } else if (elementId === 'activeTasks' && val === 0) {
                    element.textContent = '00';
                } else {
                    element.textContent = val.toLocaleString();
                }
            }, 30);
        }

        // Start animations
        animateCounter('totalCompanies', stats.totalCompanies);
        animateCounter('activeTasks', stats.activeTasks);
        animateCounter('completedTasks', stats.completedTasks);
        animateCounter('pendingAnalysis', stats.pendingAnalysis);
        animateCounter('totalAgents', stats.totalAgents);
        animateCounter('averageQuality', stats.averageQuality, '%');

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