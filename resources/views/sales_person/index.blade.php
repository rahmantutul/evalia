@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row m-4 ">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Sales Team</h4>
                    <p class="text-muted mb-0">Manage your sales representatives and their assignments</p>
                </div>
                <a href="{{ route('sales_person.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Add Sales Person
                </a>
            </div>
        </div>
    </div>

    @php
        $totalClients = 0;
        if(isset($salesPeople)) {
            foreach($salesPeople as $person) {
                $totalClients += $person['clients_count'] ?? 0;
            }
        }
    @endphp

    <!-- Sales People Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Team Members</h6>
                </div>
                <div class="card-body">
                    @if(isset($salesPeople) && count($salesPeople) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="salesPersonTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Sales Person</th>
                                    <th>Contact Information</th>
                                    <th>Expertise Criteria</th>
                                    <th>Client Assignment</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPeople as $index => $person)
                                <tr>
                                    <td class="text-center">
                                        <div class="avatar-sm bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark mb-1">{{ $person['name'] ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $person['description'] ?? 'No description' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="text-sm text-dark mb-1">
                                                <i class="fas fa-envelope text-primary mr-2"></i>
                                                {{ $person['email'] ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-dark">
                                                <i class="fas fa-phone text-success mr-2"></i>
                                                {{ $person['phone'] ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="criteria-container">
                                            @php
                                                $personCriteria = $person['criteria'] ?? [];
                                                $displayCriteria = array_slice($personCriteria, 0, 3);
                                            @endphp
                                            
                                            @foreach($displayCriteria as $criterion)
                                            <span class="badge badge-light border text-dark mb-1" 
                                                  data-toggle="tooltip" 
                                                  title="{{ $criterion['description'] ?? '' }}">
                                                {{ $criterion['title'] ?? 'N/A' }}
                                            </span>
                                            @endforeach
                                            
                                            @if(count($personCriteria) > 3)
                                            <span class="badge bg-dark" 
                                                  data-toggle="tooltip" 
                                                  title="Additional criteria">
                                                +{{ count($personCriteria) - 3 }} more
                                            </span>
                                            @endif
                                            
                                            @if(count($personCriteria) === 0)
                                            <span class="badge badge-light text-muted">No criteria</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 mr-3">
                                                <div class="text-sm font-weight-bold text-dark mb-1">
                                                    {{ count($person['clients'] ?? []) }} Active Clients
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    @php
                                                        $personClientCount = $person['clients_count'] ?? 0;
                                                        $percentage = $totalClients > 0 ? ($personClientCount / $totalClients) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ $percentage }}%"
                                                         data-toggle="tooltip" 
                                                         title="{{ $personClientCount }} total clients">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $personClientCount }} total assigned</small>
                                            </div>
                                            @if(count($person['clients'] ?? []) > 0)
                                            <a href="{{ route('sales_person.show', $person['id']) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-toggle="tooltip"
                                               title="View Clients">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('sales_person.show', $person['id']) }}" 
                                               class="btn btn-primary"
                                               data-toggle="tooltip"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales_person.edit', $person['id']) }}" 
                                               class="btn btn-secondary"
                                               data-toggle="tooltip"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger"
                                                    data-toggle="tooltip"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Sales People Found</h5>
                        <p class="text-muted">Get started by adding your first sales person to the team.</p>
                        <a href="{{ route('sales_person.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add First Sales Person
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
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 0.9rem;
    font-weight: 600;
}
.card {
    border: none;
    border-radius: 0.5rem;
}
.card-header {
    border-bottom: 1px solid #e3e6f0;
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.criteria-container .badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    margin: 0.15rem;
    border: 1px solid #e3e6f0;
}
.progress {
    border-radius: 10px;
    background-color: #f8f9fc;
}
.progress-bar {
    border-radius: 10px;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.btn-group .btn {
    border-radius: 0.35rem;
    margin: 0 1px;
}
#salesPersonTable tbody tr {
    transition: all 0.2s ease;
}
#salesPersonTable tbody tr:hover {
    background-color: #f8f9fc;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush