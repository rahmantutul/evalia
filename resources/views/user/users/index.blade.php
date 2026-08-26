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

    .btn-label {
        display: none;
    }

    @media (max-width: 767.98px) {
        .btn-label {
            display: inline;
        }
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

    .users-page .users-card {
        border: 1px solid #e8edf3;
        border-radius: 12px;
        overflow: hidden;
    }

    .users-page .user-avatar {
        align-items: center;
        display: inline-flex;
        flex: 0 0 36px;
        height: 36px;
        justify-content: center;
        width: 36px;
    }

    @media (max-width: 767.98px) {
        .users-page {
            padding-top: 0.25rem !important;
            padding-bottom: 1rem !important;
        }

        .users-page .row.mb-4.mt-3 {
            margin-top: 0.5rem !important;
        }

        .users-page .users-card {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            border: none;
        }

        .users-page .card-header {
            padding: 0.7rem 0.75rem !important;
            background: #fff;
            border-bottom: 1px solid #f0f2f5;
            border-radius: 12px 12px 0 0 !important;
        }

        .users-page .card-header .col {
            flex-direction: column;
            align-items: stretch !important;
            gap: 0.5rem;
        }

        .users-page .card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1a1d21;
            margin-bottom: 0 !important;
        }

        .users-page .card-header .btn {
            justify-content: center;
            min-height: 36px;
            width: 100%;
            font-size: 0.82rem;
            font-weight: 500;
            border-radius: 8px;
        }

        .users-page .card-body {
            padding: 0 !important;
        }

        /* Datatable toolbar / search bar */
        .users-page .datatable-wrapper .datatable-top {
            padding: 0.5rem 0.6rem !important;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            border-bottom: 1px solid #f0f2f5;
        }

        .users-page .datatable-wrapper .datatable-top > div:first-child {
            float: none !important;
            width: 100%;
        }

        .users-page .datatable-wrapper .datatable-top > div:last-child {
            float: none !important;
            width: 100%;
        }

        .users-page .datatable-wrapper .datatable-input {
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #e2e5ea;
            border-radius: 8px;
            font-size: 0.82rem;
            background: #f8f9fb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .users-page .datatable-wrapper .datatable-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
            background: #fff;
            outline: none;
        }

        .users-page .datatable-wrapper .datatable-info {
            font-size: 0.72rem;
            color: #6b7280;
            margin: 0 !important;
            padding: 0 0.6rem 0.4rem;
        }

        /* Pagination */
        .users-page .datatable-wrapper .datatable-bottom {
            padding: 0.5rem 0.6rem !important;
            border-top: 1px solid #f0f2f5;
        }

        .users-page .datatable-wrapper .datatable-pagination {
            text-align: center;
        }

        .users-page .datatable-wrapper .datatable-pagination ul {
            display: flex;
            justify-content: center;
            gap: 0.2rem;
            flex-wrap: wrap;
        }

        .users-page .datatable-wrapper .datatable-pagination li {
            float: none;
        }

        .users-page .datatable-wrapper .datatable-pagination a,
        .users-page .datatable-wrapper .datatable-pagination button {
            padding: 0.35rem 0.6rem !important;
            font-size: 0.75rem;
            border-radius: 6px;
            border: 1px solid #e2e5ea;
            color: #495057;
            background: #fff;
            min-width: 32px;
            text-align: center;
        }

        .users-page .datatable-wrapper .datatable-pagination .datatable-active a,
        .users-page .datatable-wrapper .datatable-pagination .datatable-active button {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .users-page .table-responsive {
            overflow: visible;
        }

        .users-page table.datatable,
        .users-page table.datatable thead,
        .users-page table.datatable tbody,
        .users-page table.datatable th,
        .users-page table.datatable td,
        .users-page table.datatable tr {
            display: block;
        }

        .users-page table.datatable thead {
            display: none;
        }

        .users-page table.datatable tbody {
            background: #f8fafc;
            padding: 0.4rem 0.3rem;
        }

        .users-page table.datatable tbody tr {
            background: #fff;
            border: 1px solid #e5eaf1;
            border-radius: 14px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
            margin-bottom: 1rem;
            overflow: hidden;
            padding: 0;
        }

        .users-page table.datatable tbody tr:last-child {
            margin-bottom: 0;
        }

        .users-page table.datatable tbody td {
            border: 0 !important;
            display: grid;
            grid-template-columns: minmax(76px, 30%) minmax(0, 1fr);
            gap: 0.65rem;
            min-height: 34px;
            padding: 0.42rem 0.5rem !important;
            text-align: left !important;
            word-break: break-word;
        }

        .users-page table.datatable tbody td::before {
            color: #64748b;
            content: attr(data-label);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            line-height: 1.35;
            text-transform: uppercase;
        }

        .users-page table.datatable tbody td:first-child {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border-bottom: 1px solid #eef2f7 !important;
            display: block;
            padding: 0.6rem 0.5rem !important;
        }

        .users-page table.datatable tbody td:first-child::before,
        .users-page table.datatable tbody td:last-child::before {
            content: none;
        }

        .users-page table.datatable tbody td:first-child .d-flex {
            align-items: center !important;
            gap: 0.75rem;
        }

        .users-page table.datatable tbody td:first-child .fw-semibold {
            display: block;
            font-size: 0.98rem;
            line-height: 1.25;
        }

        .users-page .user-avatar,
        .users-page .avatar-title {
            height: 40px !important;
            width: 40px !important;
        }

        .users-page table.datatable tbody td:last-child {
            border-top: 1px solid #edf2f7 !important;
            display: block;
            margin-top: 0.45rem;
            padding: 0.5rem 0.5rem 0.6rem !important;
        }

        .users-page table.datatable tbody td:last-child .d-flex {
            display: grid !important;
            gap: 0.55rem !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            justify-content: stretch !important;
            width: 100%;
        }

        .users-page table.datatable tbody td:last-child .btn,
        .users-page table.datatable tbody td:last-child form,
        .users-page table.datatable tbody td:last-child form .btn {
            min-height: 40px;
            width: 100%;
        }

        .users-page table.datatable tbody td:last-child .btn {
            align-items: center;
            display: inline-flex;
            justify-content: center;
        }

        .users-page table.datatable tbody td[data-label="Status"] {
            position: absolute;
            right: 1rem;
            top: 1rem;
            display: block;
            min-height: 0;
            padding: 0 !important;
        }

        .users-page table.datatable tbody td[data-label="Status"]::before {
            content: none;
        }

        .users-page table.datatable tbody td[data-label="Status"] .badge {
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
            font-size: 0.68rem;
            padding: 0.32rem 0.55rem !important;
        }

        .users-page table.datatable tbody tr {
            position: relative;
        }

        .users-page table.datatable tbody td[data-label="Email"] {
            padding-top: 0.85rem !important;
        }

        .users-page table.datatable tbody td[data-label="Username"] .badge,
        .users-page table.datatable tbody td[data-label="Role"] .badge {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    @media (max-width: 420px) {
        .users-page table.datatable tbody td {
            grid-template-columns: minmax(72px, 34%) minmax(0, 1fr);
        }

        .users-page table.datatable tbody td[data-label="Status"] {
            position: static;
            padding: 0.55rem 1rem 0.2rem !important;
        }

        .users-page table.datatable tbody td[data-label="Status"] .badge {
            width: fit-content;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid users-page">
    <!-- User List -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card users-card">
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
                                    <td data-label="Name">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar avatar-sm bg-light rounded me-2">
                                                <div class="avatar-title bg-primary text-black rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                                    {{ substr($user['full_name'] ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark">{{ $user['full_name'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Email">{{ $user['email'] }}</td>
                                    <td data-label="Username"><span class="badge bg-secondary">{{ $user['username'] }}</span></td>
                                    <td data-label="Role">
                                        <span class="badge bg-primary text-black px-2 py-1">
                                            <i class="fas fa-user-shield me-1"></i>{{ $roleName }}
                                        </span>
                                    </td>
                                    <td data-label="Company">
                                        <span class="text-muted">
                                            <i class="fas fa-building me-1"></i>{{ $user['company']['name'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td data-label="Status">
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
                                    <td data-label="Actions">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('users.edit', $user['id']) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip" 
                                               title="Edit User">
                                                <i class="fas fa-edit me-1 d-none d-sm-inline"></i><span class="btn-label">Edit</span>
                                            </a>

                                            @if($isActive)
                                                <form action="{{ route('users.destroy', $user['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="tooltip" 
                                                            title="Deactivate User"
                                                            onclick="return confirmDeactivation(this.closest('form'), '{{ addslashes($user['full_name'] ?? $user['username']) }}')">
                                                        <i class="fas fa-user-slash me-1 d-none d-sm-inline"></i><span class="btn-label">Deactivate</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('users.activate', $user['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success"
                                                            data-bs-toggle="tooltip" 
                                                            title="Activate User"
                                                            onclick="return confirmActivation(this.closest('form'), '{{ addslashes($user['full_name'] ?? $user['username']) }}')">
                                                        <i class="fas fa-user-check me-1 d-none d-sm-inline"></i><span class="btn-label">Activate</span>
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

function confirmDeactivation(form, userName) {
    Swal.fire({
        title: 'Deactivate User?',
        html: 'Are you sure you want to deactivate <strong>' + userName + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Deactivate!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}

function confirmActivation(form, userName) {
    Swal.fire({
        title: 'Activate User?',
        html: 'Are you sure you want to activate <strong>' + userName + '</strong>?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Activate!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
