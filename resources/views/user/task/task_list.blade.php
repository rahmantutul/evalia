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
                    <i class="bi bi-building me-2"></i>
                    <span class="fw-500">Company ID: <span class="text-primary">{{ $companyId }}</span></span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom py-2">
                    <h5 class="mb-0 fw-600 d-flex align-items-center">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        All Analyses
                    </h5>
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
                                @foreach ($paginatedTask as $task)
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
                                            <div class="d-flex justify-content-end gap-2">
                                                <!-- View Button -->
                                                <a href="{{ route('user.task.details',$task['id']) }}" 
                                                class="btn btn-sm btn-icon btn-outline-primary" 
                                                data-bs-toggle="tooltip" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Delete Button -->
                                                <a href="{{ route('user.task.delete',$task['id']) }}" 
                                                class="btn btn-sm btn-icon btn-outline-danger" 
                                                data-bs-toggle="tooltip" title="Delete Task"
                                                onclick="return confirm('Are you sure to delete this task?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                
                                                <!-- Re-run Button -->
                                                <button class="btn btn-sm btn-icon btn-outline-secondary" 
                                                        data-bs-toggle="tooltip" title="Re-run Task">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $paginatedTask->firstItem() }} to {{ $paginatedTask->lastItem() }} of {{ $paginatedTask->total() }} entries
                        </div>
                        <div>
                            {{ $paginatedTask->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>
@endsection