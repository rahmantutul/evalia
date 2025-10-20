@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">

    <!-- Dashboard Header -->
    <div class="container">
        <div class="row align-items-center bg-secondary p-3 rounded mb-3">
            <div class="col-md-8">
                <h4 class="text-dark">Company Audio list</h4>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="badge bg-light text-dark p-2 rounded-pill shadow-sm">
                    <i class="fas fa-building"></i>
                    <span class="fw-500">Company ID: <span class="text-primary">{{ $company_id }}</span></span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="card border-0 shadow-sm overflow-hidden">
               <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history text-primary me-2 fs-5"></i>
                        <h5 class="mb-0 fw-600">All Analyses</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                       
                        <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-2 px-3 py-2" 
                                data-bs-toggle="modal" data-bs-target="#audioUploadModal{{ $company_id }}">
                            <i class="fas fa-plus-circle"></i>
                            <span class="d-none d-sm-inline">New Task</span>
                        </button>
                    </div>
                </div>
                
                  <!-- Modal -->
                <div class="modal fade" id="audioUploadModal{{ $company_id }}" tabindex="-1" aria-labelledby="audioUploadModalLabel" aria-hidden="true">
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

                                <!-- Hidden company_id -->
                                <input type="hidden" name="company_id" value="{{ $company_id }}">

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
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
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
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => 'all', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="all">All Status</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item status-filter {{ request('status') === 'completed' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => 'completed', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="completed">Completed</a></li>
                                        <li><a class="dropdown-item status-filter {{ request('status') === 'processing' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => 'processing', 'time_range' => request('time_range', 'all')]) }}" 
                                            data-status="processing">Processing</a></li>
                                        <li><a class="dropdown-item status-filter {{ request('status') === 'failed' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => 'failed', 'time_range' => request('time_range', 'all')]) }}" 
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
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'all']) }}" 
                                            data-time="all">All Time</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'today' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'today']) }}" 
                                            data-time="today">Today</a></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'yesterday' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'yesterday']) }}" 
                                            data-time="yesterday">Yesterday</a></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'last7' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'last7']) }}" 
                                            data-time="last7">Last 7 Days</a></li>
                                        <li><a class="dropdown-item time-filter {{ request('time_range') === 'last30' ? 'active' : '' }}" 
                                            href="{{ route('user.task.list', ['companyId' => $company_id, 'status' => request('status', 'all'), 'time_range' => 'last30']) }}" 
                                            data-time="last30">Last 30 Days</a></li>
                                    </ul>
                                </div>
                                
                                <!-- Clear Filters -->
                                @if(request('status') || request('time_range'))
                                <a href="{{ route('user.task.list', ['companyId' => $company_id]) }}" class="btn btn-sm btn-outline-danger">
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
                                        <th>Duration</th>
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
                                                @if(isset($task['duration'])) 
                                                    <span class="d-flex align-items-center">
                                                        <i class="fas fa-clock text-muted me-1 small"></i>
                                                        {{ $task['duration'] }} min
                                                    </span>
                                                @else 
                                                    <span class="text-muted">--</span>
                                                @endif
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if there are any running tasks
        function hasRunningTasks() {
            const runningBadges = document.querySelectorAll('.badge.bg-info');
            return runningBadges.length > 0;
        }

        // Function to refresh the task list
        function refreshTaskList() {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Create a temporary DOM element to parse the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract the table body from the response
                const newTableBody = doc.getElementById('taskTableBody');
                if (newTableBody) {
                    document.getElementById('taskTableBody').innerHTML = newTableBody.innerHTML;
                    
                    // Reinitialize tooltips
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                    // Continue auto-refresh if there are still running tasks
                    if (hasRunningTasks()) {
                        setTimeout(refreshTaskList, 30000); // 30 seconds
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing task list:', error);
                // Retry after 30 seconds even if there's an error
                if (hasRunningTasks()) {
                    setTimeout(refreshTaskList, 30000);
                }
            });
        }

        // Start auto-refresh only if there are running tasks
        if (hasRunningTasks()) {
            console.log('Auto-refresh started: Running tasks detected');
            setTimeout(refreshTaskList, 30000); // First refresh after 30 seconds
        }
    });
</script>
@endpush