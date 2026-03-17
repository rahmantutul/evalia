@extends('user.layouts.app')
@section('title', 'Agent List')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h4 class="page-title mb-0">Agents</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-2 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user.home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Agent List</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                @can('users.create')
                <a href="{{ route('users.create', ['type' => 'agent']) }}" class="btn btn-primary rounded-pill">
                    <i class="fas fa-plus me-1"></i> Add New Agent
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Agent List Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="card-title fw-bold mb-0">Agent Directory</h6>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                        <input type="text" id="agentSearch" class="form-control bg-light border-0" placeholder="Search agents...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="agentsTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Agent Name</th>
                            <th>Username / Email</th>
                            <th>Role / Position</th>
                            <th>Supervisor</th>
                            <th>Company</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agentsWithPerformance as $item)
                            @php 
                                $agent = $item['agent']; 
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <span class="avatar-title rounded-circle bg-primary-soft text-primary fw-bold" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #eef2ff;">
                                                {{ strtoupper(substr($agent['name'], 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-bold">{{ $agent['name'] }}</h6>
                                            <span class="text-muted small">ID: AGT-{{ 1000 + $agent['id'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium">{{ $agent['username'] }}</div>
                                    <div class="text-muted small">{{ $agent['email'] }}</div>
                                </td>
                                <td>
                                    <div class="text-dark">{{ $agent['role']['name'] }}</div>
                                    <div class="text-muted small">{{ $agent['position'] ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium">{{ $agent['supervisor_name'] }}</div>
                                </td>
                                <td>
                                    <div class="text-dark">{{ $agent['company']['name'] }}</div>
                                </td>
                                <td class="text-center">
                                    @if($agent['is_active'])
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                            <i class="fas fa-check-circle me-1 small"></i> Active
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                            <i class="fas fa-times-circle me-1 small"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user.agents.show', $agent['id']) }}">
                                                    <i class="fas fa-eye me-2 text-primary"></i> View Performance
                                                </a>
                                            </li>
                                            @can('users.edit')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('users.edit', $agent['id']) }}">
                                                    <i class="fas fa-edit me-2 text-info"></i> Edit Agent
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users-slash fa-3x text-muted opacity-25 mb-3"></i>
                                    <h6 class="text-muted">No agents found</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('agentSearch').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        const rows = document.querySelectorAll('#agentsTable tbody tr');
        
        rows.forEach(row => {
            if (row.textContent.toLowerCase().includes(val)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .avatar-sm { width: 40px; height: 40px; }
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-subtle { background-color: #dcfce7 !important; }
    .bg-danger-subtle  { background-color: #fee2e2 !important; }
    .text-success { color: #15803d !important; }
    .text-danger  { color: #b91c1c !important; }
    .fw-900 { font-weight: 900; }
    .rounded-4 { border-radius: 1rem !important; }
    .table-hover tbody tr:hover { background-color: #f8faff; }
</style>
@endpush