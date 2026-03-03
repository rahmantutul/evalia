@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-users mr-2"></i>
                        Clients Assigned to {{ $salesPerson['name'] }}
                    </h4>
                    <p class="text-muted mb-0">View all clients managed by this sales person</p>
                </div>
                <div>
                    <a href="{{ route('sales_person.show', $salesPerson['id']) }}" class="btn btn-light mr-2">
                        <i class="fas fa-user mr-2"></i>View Profile
                    </a>
                    <a href="{{ route('sales_person.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Team
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['total_clients'] }}</h4>
                            <small>Total Clients</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['auto_assigned'] }}</h4>
                            <small>Auto-Matched</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-robot fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['manual_assigned'] }}</h4>
                            <small>Manual Assignments</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-hand-paper fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $stats['matching_percentage'] }}%</h4>
                            <small>Avg. Match Rate</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-dark">Client Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Description</th>
                                    <th>Criteria Match</th>
                                    <th>Assignment Type</th>
                                    <th>Client Criteria</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <div class="font-weight-600 text-dark">{{ $client['name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($client['description'], 50) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $matchingCriteria = array_intersect($salesPerson['criteria_ids'], $client['criteria_ids']);
                                            $matchScore = count($matchingCriteria);
                                            $totalCriteria = count($client['criteria_ids']);
                                            $matchPercentage = $totalCriteria > 0 ? round(($matchScore / $totalCriteria) * 100) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                <div class="progress-bar {{ $matchPercentage > 70 ? 'bg-success' : ($matchPercentage > 40 ? 'bg-warning' : 'bg-danger') }}"
                                                     style="width: {{ $matchPercentage }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $matchPercentage }}%</small>
                                        </div>
                                        <small class="text-muted">{{ $matchScore }}/{{ $totalCriteria }} criteria</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $client['auto_assigned'] ? 'badge-success' : 'badge-info' }}">
                                            {{ $client['auto_assigned'] ? 'Auto-Matched' : 'Manual' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="criteria-tags">
                                            @foreach($client['criteria'] as $criteria)
                                            <span class="badge badge-light border text-dark mb-1">{{ $criteria['title'] }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('client.show', $client['id']) }}" class="btn btn-outline-primary" title="View Client">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('client.edit', $client['id']) }}" class="btn btn-outline-secondary" title="Edit Client">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(count($clients) === 0)
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Clients Assigned</h5>
                        <p class="text-muted">This sales person doesn't have any clients assigned yet.</p>
                        <a href="{{ route('client.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add New Client
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress {
    border-radius: 10px;
    background: #f0f0f0;
}
.progress-bar {
    border-radius: 10px;
}
.criteria-tags .badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    margin: 0.1rem;
    display: inline-block;
}
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
}
.card .card-body {
    padding: 1.5rem;
}
</style>
@endpush