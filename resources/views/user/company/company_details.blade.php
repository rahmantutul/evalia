@extends('user.layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa; min-height: 100vh;">

    <!-- Dashboard Header -->
    <div class="container">
        <div class="row align-items-center bg-secondary p-3 rounded mb-3">
            <div class="col-md-8">
                <h3 class="fw-600 text-dark mb-2">Company Audio Analysis Dashboard</h3>
                <p class="text-primary mb-0">Analyze and evaluate customer service call</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="badge bg-light text-dark p-3 rounded-pill shadow-sm">
                    <i class="bi bi-building me-2"></i>
                    <span class="fw-500">Company ID: <span class="text-primary">20250704062737</span></span>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-600 d-flex align-items-center">
                            <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-2">
                                <i class="bi bi-person-voice fs-5"></i>
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
                            <input type="file" class="d-none" accept="audio/*">
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-600 d-flex align-items-center">
                            <span class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle me-2">
                                <i class="bi bi-headset fs-5"></i>
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
                            <input type="file" class="d-none" accept="audio/*">
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-600 d-flex align-items-center">
                            <span class="bg-success bg-opacity-10 text-success p-2 rounded-circle me-2">
                                <i class="bi bi-mic fs-5"></i>
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
                            <input type="file" class="d-none" accept="audio/*">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center mb-5">
            <button class="btn btn-primary px-5 py-2 me-3 rounded-pill fw-500 shadow-sm">
                <i class="bi bi-graph-up me-2"></i> Analyze Audio
            </button>
            <button class="btn btn-secondary px-5 py-2 rounded-pill fw-500">
                <i class="bi bi-gear me-2"></i> Settings
            </button>
        </div>

        <!-- Recent Analyses Section -->
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom py-3">
                <h5 class="mb-0 fw-600 d-flex align-items-center">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    Recent Analyses
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4">Analysis ID</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Duration</th>
                                <th class="py-3">Created</th>
                                <th class="py-3 pe-4 text-end">Actions</th>
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
                                    <td>@if(isset($task['duration'])) {{ $task['duration'] }} min @else -- @endif</td>
                                    <td>{{ \Carbon\Carbon::parse($task['created_at'])->format('M d, Y h:i A') }}</td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('user.task.details',$task['id']) }}" class="btn btn-sm btn-primary rounded-start-pill">
                                                <i class="bi bi-eye me-1"></i> View
                                            </a>
                                            <a href="{{ route('user.task.delete',$task['id']) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this?')">
                                                <i class="bi bi-eye"></i> Delete
                                            </a>
                                            <button class="btn btn-sm btn-secondary rounded-end-pill">
                                                <i class="bi bi-arrow-repeat"></i>Re-run
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top py-3">
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

<style>
    .upload-container {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .upload-container:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
        border-color: #0d6efd !important;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.05) !important;
    }
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endsection