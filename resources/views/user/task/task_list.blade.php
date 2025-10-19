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
                            <tbody id="taskTableBody">
                                @foreach ($taskList as $task)
                                    <tr id="task-{{ $task['id'] }}">
                                        <td class="ps-4 fw-500">#{{ $task['id'] }}</td>
                                        <td>
                                            <span class="badge bg-opacity-10 
                                                @if($task['status'] === 'completed') bg-success text-success 
                                                @elseif($task['status'] === 'processing') bg-warning text-warning 
                                                @elseif($task['status'] === 'running') bg-info text-info
                                                @else bg-danger text-danger @endif
                                                rounded-pill py-1 px-3 d-flex align-items-center" style="width: fit-content;">
                                                @if($task['status'] === 'running')
                                                    <div class="spinner-border spinner-border-sm me-1" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                @endif
                                                {{ ucfirst($task['status']) }}
                                            </span>
                                        </td>
                                        <td>@if(isset($task['duration'])) {{ $task['duration'] }} min @else -- @endif</td>
                                        <td>{{ \Carbon\Carbon::parse($task['created_at'])->format('M d, Y h:i A') }}</td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <!-- View Button - Disabled when running -->
                                                @if($task['status'] === 'running')
                                                    <button class="btn btn-sm btn-icon btn-outline-secondary" disabled
                                                            data-bs-toggle="tooltip" title="View unavailable while running">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ route('user.task.details',$task['id']) }}" 
                                                    class="btn btn-sm btn-icon btn-outline-primary" 
                                                    data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                
                                                <!-- Delete Button -->
                                                <a href="{{ route('user.task.delete',$task['id']) }}" 
                                                class="btn btn-sm btn-icon btn-outline-danger" 
                                                data-bs-toggle="tooltip" title="Delete Task"
                                                onclick="return confirm('Are you sure to delete this task?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                
                                                <!-- Re-evaluate Button -->
                                                <a href="{{ route('user.company.evaluate',$task['id']) }}" 
                                                class="btn btn-sm btn-icon btn-outline-secondary" 
                                                data-bs-toggle="tooltip" title="Re-Evaluate"
                                                onclick="return confirm('Are you sure to re-evaluate this?')">
                                                    <i class="fas fa-redo"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
                            {{ $taskList->links('pagination::bootstrap-4') }}
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