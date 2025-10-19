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

    .table td .btn-group {
        float: right;
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
.btn-group {
    display: flex;
    gap: 8px;
}

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
    <!-- Sticky Statistics Bar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm sticky-top-bar">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <!-- Total Users -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-users text-primary fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="totalUsers">{{ count($users) }}</h5>
                                    <small class="text-muted">Total Users</small>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Role Statistics -->
                        @php
                            // Get all unique roles from users
                            $roles = collect($users)
                                ->pluck('role.name')
                                ->filter()
                                ->unique()
                                ->values();
                            
                            // Define role colors and icons
                            $roleConfig = [
                                'superadmin' => ['color' => 'role-color-superadmin', 'icon' => 'crown'],
                                'admin' => ['color' => 'role-color-admin', 'icon' => 'user-shield'],
                                'manager' => ['color' => 'role-color-manager', 'icon' => 'user-tie'],
                                'supervisor' => ['color' => 'role-color-supervisor', 'icon' => 'user-check'],
                                'agent' => ['color' => 'role-color-agent', 'icon' => 'headset'],
                                'user' => ['color' => 'role-color-user', 'icon' => 'user'],
                            ];
                            
                            // Generate colors for unknown roles
                            $colorPalette = [
                                'linear-gradient(135deg, #6610f2, #8b43f5)',
                                'linear-gradient(135deg, #6f42c1, #9d70e0)',
                                'linear-gradient(135deg, #e83e8c, #f16ba3)',
                                'linear-gradient(135deg, #fd7e14, #ff9d4d)',
                                'linear-gradient(135deg, #20c997, #3dd5a8)',
                                'linear-gradient(135deg, #0dcaf0, #47d5f0)',
                                'linear-gradient(135deg, #ffc107, #ffd54f)',
                                'linear-gradient(135deg, #6c757d, #8b96a0)',
                            ];
                        @endphp

                        @foreach($roles as $index => $roleName)
                            @php
                                $roleKey = strtolower($roleName);
                                $roleCount = collect($users)->filter(function($user) use ($roleName) {
                                    return ($user['role']['name'] ?? '') === $roleName;
                                })->count();
                                
                                // Get role configuration or use defaults
                                $config = $roleConfig[$roleKey] ?? null;
                                $bgClass = $config['color'] ?? '';
                                $icon = $config['icon'] ?? 'user';
                                $colorIndex = $index % count($colorPalette);
                                $dynamicColor = $colorPalette[$colorIndex];
                                
                                // Determine text color based on background brightness
                                $isLight = in_array($colorIndex, [4, 6]); // indices for light backgrounds
                                $textColor = $isLight ? 'text-dark' : 'text-white';
                            @endphp

                            <div class="col-md-2 col-sm-4 col-6 stat-role-item">
                                <div class="d-flex align-items-center p-2 rounded {{ $bgClass }} {{ $textColor }}" 
                                     @if(!$bgClass) style="background: {{ $dynamicColor }};" @endif>
                                    <div class="flex-shrink-0 p-2 rounded me-3 bg-white bg-opacity-20" style="color: #000">
                                        <i class="fas fa-{{$icon}} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-0 fw-bold" id="roleCount-{{ $roleKey }}">
                                            {{ $roleCount }}
                                        </h5>
                                        <small>{{ $roleName }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!-- Active Users -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-user-check text-success fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="activeUsers">
                                        {{ collect($users)->where('is_active', true)->count() }}
                                    </h5>
                                    <small class="text-muted">Active Users</small>
                                </div>
                            </div>
                        </div>
                        <!-- Inactive Users -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-user-slash text-danger fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold" id="inactiveUsers">
                                        {{ collect($users)->where('is_active', false)->count() }}
                                    </h5>
                                    <small class="text-muted">Inactive Users</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                @forelse($users as $user)
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
                                    <td>{{ $user['company_id'] ?? 'N/A' }}</td>
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
                                                    <form action="{{ route('users.activate', $user['id']) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icon" title="Activate">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    </form>
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
        { id: 'totalUsers', value: {{ count($users) }} },
        { id: 'activeUsers', value: {{ collect($users)->where('is_active', true)->count() }} },
        { id: 'inactiveUsers', value: {{ collect($users)->where('is_active', false)->count() }} },
    ];

    stats.forEach(stat => {
        const element = document.getElementById(stat.id);
        if (element) {
            element.textContent = stat.value;
        }
    });
}

// Initialize statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
});
</script>
@endpush