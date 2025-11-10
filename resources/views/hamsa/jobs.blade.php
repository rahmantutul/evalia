@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i>Jobs
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Simple Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ url('/hamsa/jobs') }}" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search jobs..." value="{{ request('search') }}">
                                </div>
                                
                                <div class="col-md-3">
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        @foreach($availableTypes as $type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach($availableStatuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Jobs List -->
                    @if(!empty($jobs))
                        <div class="row">
                            @foreach($jobs as $job)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title text-truncate" title="{{ $job['title'] ?? 'No title' }}">
                                                {{ $job['title'] ?? 'No title' }}
                                            </h6>
                                            <span class="badge 
                                                @if($job['status'] == 'COMPLETED') bg-success
                                                @elseif($job['status'] == 'FAILED') bg-danger
                                                @elseif($job['status'] == 'PROCESSING') bg-info
                                                @else bg-warning @endif">
                                                {{ $job['status'] }}
                                            </span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark">{{ $job['type'] }}</span>
                                        </div>
                                        
                                        <div class="small text-muted mb-3">
                                            <div>Created: {{ \Carbon\Carbon::parse($job['createdAt'])->format('M j, Y H:i') }}</div>
                                            @if(isset($job['cost']))
                                            <div>Cost: {{ $job['cost'] }} credits</div>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted font-monospace">
                                                {{ substr($job['id'], 0, 8) }}...
                                            </small>
                                            <div class="btn-group">
                                                <a href="{{ url('/hamsa/jobs/' . $job['id']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($job['status'] === 'COMPLETED' && isset($job['url']))
                                                    <a href="{{ $job['url'] }}" 
                                                       class="btn btn-sm btn-outline-success"
                                                       target="_blank">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                            @if(isset($pagination['totalPages']) && $pagination['totalPages'] > 1)
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted small">
                                        Showing {{ $pagination['skip'] + 1 }} to 
                                        {{ min($pagination['skip'] + $pagination['filtered'], $pagination['total']) }} 
                                        of {{ $pagination['total'] }} results
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($pagination['page'] > 1)
                                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['page'] - 1]) }}" 
                                            class="btn btn-sm btn-outline-primary">Previous</a>
                                        @endif
                                        @if($pagination['page'] < $pagination['totalPages'])
                                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['page'] + 1]) }}" 
                                            class="btn btn-sm btn-outline-primary">Next</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-3">
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            @php
                                                $currentPage = $pagination['page'];
                                                $totalPages = $pagination['totalPages'];
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($totalPages, $currentPage + 2);
                                            @endphp

                                            @if($startPage > 1)
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a>
                                                </li>
                                                @if($startPage > 2)
                                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                                @endif
                                            @endif

                                            @for($i = $startPage; $i <= $endPage; $i++)
                                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            @if($endPage < $totalPages)
                                                @if($endPage < $totalPages - 1)
                                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                                @endif
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}">{{ $totalPages }}</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            @endif


                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5>No jobs found</h5>
                            <p class="text-muted mb-4">
                                @if(request()->anyFilled(['search', 'type', 'status']))
                                    No jobs match your current filters.
                                @else
                                    No jobs have been created yet.
                                @endif
                            </p>
                            <a href="{{ url('/hamsa/transcribe') }}" class="btn btn-primary">
                                Create First Job
                            </a>
                        </div>
                    @endif

                    @if($error)
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $error }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection