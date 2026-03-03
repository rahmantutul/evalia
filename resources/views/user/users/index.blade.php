@extends('user.layouts.app')
@push('styles')
<style>
    .action-buttons {
        min-width: 120px;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.3s ease;
        margin: 0 2px;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .btn-view {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        border: none;
    }
    .btn-view:hover {
        background: linear-gradient(135deg, #138496, #117a8b);
        color: white;
    }
    .btn-edit {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
    }
    .btn-edit:hover {
        background: linear-gradient(135deg, #218838, #1e7e34);
        color: white;
    }
    .btn-deactivate {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border: none;
    }
    .btn-deactivate:hover {
        background: linear-gradient(135deg, #c82333, #bd2130);
        color: white;
    }
    .btn-action i {
        font-size: 12px;
    }
    .tooltip-inner {
        border-radius: 4px;
        font-size: 12px;
    }
    .bg-success-soft {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .rounded-pill {
        border-radius: 50rem !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- User List -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">                      
                        <h4 class="card-title mb-0">User Management</h4>
                        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary d-block float-end">
                            <i class="fas fa-plus me-1"></i>Add User
                        </a>                  
                    </div>                                 
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif 

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <table class="table datatable" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th width="150" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                @php
                                    $roleName = $user['role']['name'] ?? 'user';
                                    $isActive = $user['is_active'] ?? false;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded me-2">
                                                <div class="avatar-title bg-primary text-black rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                    {{ substr($user['full_name'] ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark">{{ $user['full_name'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user['email'] }}</td>
                                    <td><span class="badge bg-secondary">{{ $user['username'] }}</span></td>
                                    <td>
                                        <span class="badge bg-primary text-black px-2 py-1">
                                            <i class="fas fa-user-shield me-1"></i>{{ $roleName }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="fas fa-building me-1"></i>{{ $user['company']['name'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($isActive)
                                            <span class="badge bg-success-soft text-success px-2 py-1 border border-success-subtle rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger-soft text-danger px-2 py-1 border border-danger-subtle rounded-pill">
                                                <i class="fas fa-times-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('users.edit', $user['id']) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip" 
                                               title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($isActive)
                                                <form action="{{ route('users.destroy', $user['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="tooltip" 
                                                            title="Deactivate User"
                                                            onclick="return confirm('Deactivate {{ $user['full_name'] }}?')">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('users.activate', $user['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-success"
                                                            data-bs-toggle="tooltip" 
                                                            title="Activate User">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-2x mb-3"></i>
                                            <p class="mb-0">No active users found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
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
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Custom confirmation for deactivation
function confirmDeactivation(userName) {
    return Swal.fire({
        title: 'Deactivate User?',
        html: `Are you sure you want to deactivate <strong>${userName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Deactivate!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        return result.isConfirmed;
    });
}

// If you don't have SweetAlert, use this simpler version:
function confirmDeactivation(userName) {
    return confirm(`Are you sure you want to deactivate ${userName}?`);
}
</script>
@endpush