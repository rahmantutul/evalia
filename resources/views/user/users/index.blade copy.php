@extends('user.layouts.app')
@push('styles')
<style>

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 6px;
    color: #6c757d;
    background: #e8eff5;
    transition: all 0.2s ease;
    text-decoration: none;
    position: relative;
}

.btn-icon:hover {
    background: #e9ecef;
    color: #495057;
    transform: translateY(-1px);
}

.btn-icon:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 100;
}

.btn-delete:hover {
    background: #dc3545;
    color: white;
}

/* Role-specific badge colors */
.badge-role-superadmin { background-color: #6f42c1; color: white; }
.badge-role-admin { background-color: #dc3545; color: white; }
.badge-role-manager { background-color: #0dcaf0; color: white; }
.badge-role-supervisor { background-color: #fd7e14; color: white; }
.badge-role-agent { background-color: #20c997; color: white; }
.badge-role-user { background-color: #6c757d; color: white; }
.badge-role-analyst { background-color: #ffc107; color: #000; }

/* Dynamic role colors - will be applied via inline styles */
.stat-role-item {
    transition: transform 0.2s ease;
}

.stat-role-item:hover {
    transform: translateY(-2px);
}

/* Default color mapping for common roles */
.role-color-superadmin { background: linear-gradient(135deg, #6f42c1, #8c68d1); color: white; }
.role-color-admin { background: linear-gradient(135deg, #dc3545, #e35d6a); color: white; }
.role-color-manager { background: linear-gradient(135deg, #0dcaf0, #47d5f0); color: white; }
.role-color-supervisor { background: linear-gradient(135deg, #fd7e14, #fd9843); color: white; }
.role-color-agent { background: linear-gradient(135deg, #20c997, #3dd5a8); color: white; }
.role-color-user { background: linear-gradient(135deg, #6c757d, #8b96a0); color: white; }
</style>
<style>
    /* Tooltip styling */
    .action-btn {
        position: relative;
        margin-left: 5px;
    }

    .action-btn:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .tooltip-text {
        visibility: hidden;
        width: max-content;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 3px 6px;
        position: absolute;
        z-index: 1;
        top: -30px;
        right: 50%;
        transform: translateX(50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        white-space: nowrap;
    }
    
    /* Sticky Statistics Bar Styles */
    .sticky-top-bar {
        transition: all 0.3s ease;
        top: 20px;
    }
    
    .sticky-top-bar.sticky-active {
        position: sticky;
        top: 0;
        z-index: 1020;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .stat-item {
        border-right: 1px solid #e9ecef;
        padding-right: 15px;
    }
    
    .stat-item:last-child {
        border-right: none;
        padding-right: 0;
    }
    
    @media (max-width: 768px) {
        .stat-item {
            border-right: none;
            border-bottom: 1px solid #e9ecef;
            padding-right: 0;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .stat-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
    }
</style>
<style>

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 6px;
    color: #6c757d;
    background: #e8eff5;
    transition: all 0.2s ease;
    text-decoration: none;
    position: relative;
}

.btn-icon:hover {
    background: #e9ecef;
    color: #495057;
    transform: translateY(-1px);
}

.btn-icon:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 100;
}

.btn-delete:hover {
    background: #dc3545;
    color: white;
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
                        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Add User</a>                  
                    </div>                                 
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif 

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <table class="table datatable" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usersWithCompanies as $user)
                                @php
                                    $roleName = $user['role']['name'] ?? 'user';
                                    $roleKey = strtolower($roleName);
                                    $badgeClass = 'badge-role-' . $roleKey;
                                @endphp
                                <tr>
                                    <td>{{ $user['full_name'] ?? 'N/A' }}</td>
                                    <td>{{ $user['email'] }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $roleName }}
                                        </span>
                                    </td>
                                    <td>{{ $user['company_name'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $user['is_active'] ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.show', $user['id']) }}" class="btn btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-icon" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user['is_active'])
                                                    <form action="{{ route('users.destroy', $user['id']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-icon btn-delete" title="Deactivate" onclick="return confirm('Are you sure to deactivate this user?')">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-icon btn-danger opacity-50" disabled title="User Deactivated">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
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
    // Auto-update statistics with animation
    function updateStatistics() {
        const stats = [
            { id: 'totalUsers', value: {{ count($usersWithCompanies) }} },
            { id: 'activeUsers', value: {{ collect($usersWithCompanies)->where('is_active', true)->count() }} },
            { id: 'inactiveUsers', value: {{ collect($usersWithCompanies)->where('is_active', false)->count() }} },
        ];

        stats.forEach(stat => {
            const element = document.getElementById(stat.id);
            if (element) {
                element.textContent = stat.value;
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateStatistics();
    });
</script>
@endpush