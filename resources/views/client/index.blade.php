@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header with Search -->
    <div class="row align-items-center m-4">
        <div class="col-md-6">
            <div>
                <h2 class="h4 mb-1 text-dark">Clients</h2>
                <p class="text-muted mb-0">Manage your client portfolio and assignments</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('client.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>New Client
                </a>
            </div>
        </div>
    </div>

    <!-- Client Cards Grid -->
    <div class="row">
        @foreach($clients as $client)
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card client-card h-100">
                <div class="card-body">
                    <!-- Client Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="client-avatar bg-primary text-white rounded mr-3 bg-dark">
                                {{ substr($client['name'], 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 text-dark">{{ $client['name'] }}</h6>
                                <small class="text-muted">Client #{{ $client['id'] }}</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('client.show', $client['id']) }}">
                                    <i class="fas fa-eye mr-2"></i>View Details
                                </a>
                                <a class="dropdown-item" href="{{ route('client.edit', $client['id']) }}">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Client Description -->
                    <p class="text-muted small mb-3">{{ $client['description'] ?? 'No description provided' }}</p>

                    <!-- Assigned Sales Person -->
                    <div class="assigned-person mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="sales-avatar bg-success text-white rounded mr-2">
                                    {{ substr($client['salesperson_name'] ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <div class="small font-weight-500 text-dark">{{ $client['salesperson_name'] ?? 'Not Assigned' }}</div>
                                    <small class="text-muted">Assigned Representative</small>
                                </div>
                            </div>
                            <span class="badge {{ $client['auto_assigned'] ? 'badge-success' : 'badge-info' }}">
                                {{ $client['auto_assigned'] ? 'Auto' : 'Manual' }}
                            </span>
                        </div>
                    </div>

                    <!-- Client Criteria -->
                    <div class="criteria-section">
                        <small class="text-muted d-block mb-2">Client Requirements</small>
                        <div class="criteria-tags">
                            @foreach(($client['criteria'] ?? []) as $criterion)
                            <span class="badge badge-light border text-dark mb-1">{{ $criterion['title'] }}</span>
                            @endforeach
                            @if(empty($client['criteria']))
                            <span class="text-muted small">No criteria set</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Card Footer -->
                <div class="card-footer bg-white border-top-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar mr-1"></i>
                            Created recently
                        </small>
                        <a href="{{ route('client.show', $client['id']) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(empty($clients) || count($clients) === 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-body text-center py-5">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-users fa-3x text-muted opacity-25"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Clients Yet</h4>
                    <p class="text-muted mb-4">Start building your client portfolio by adding your first client.</p>
                    <a href="{{ route('client.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i>Add First Client
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.client-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}
.client-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.client-avatar {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.1rem;
}
.sales-avatar {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}
.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%) !important;
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%) !important;
}
.criteria-tags .badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    margin: 0.1rem;
    border: 1px solid #e9ecef;
}
.assigned-person {
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}
.empty-state-icon {
    opacity: 0.5;
}
.font-weight-500 {
    font-weight: 500;
}
.card-footer {
    background: transparent;
}
</style>
@endpush