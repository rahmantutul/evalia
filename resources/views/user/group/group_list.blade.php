@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
    <style>
        /* Statistics cards styling */
        .stat-card {
            border-radius: 12px;
            padding: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.07) !important;
        }
        
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
    @php
        $totalGroups = 8;
        $activeGroups = 8;
        $recentGroups = 8;
        $inactiveGroups = 0;
        $totalMembers = 22;
    @endphp
    <!-- Statistics Section -->
    <div class="row m-4">
        <!-- Total Groups Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #0d6efd;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Total Groups</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-primary">{{ $totalGroups ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            <i class="fas fa-layer-group text-primary"></i> 
                            All groups in system
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-layer-group display-6 text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Groups Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #198754;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Active Groups</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-success">{{ $activeGroups ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            <i class="fas fa-chart-pie text-success"></i> 
                            {{ $totalGroups ? round(($activeGroups / $totalGroups) * 100, 1) : 0 }}% of total
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle display-6 text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #0dcaf0;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Total Members</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-info">{{ $totalMembers ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            Avg: {{ $totalGroups ? round($totalMembers / $totalGroups, 1) : 0 }} members/group
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-users display-6 text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Groups Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #ffc107;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Inactive Groups</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-warning">{{ $inactiveGroups ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            <i class="fas fa-exclamation-triangle text-warning"></i> 
                            {{ $totalGroups ? round(($inactiveGroups / $totalGroups) * 100, 1) : 0 }}% of total
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle display-6 text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Group List Section -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col d-flex justify-content-between align-items-center">                      
                            <h4 class="card-title mb-0">Group List</h4>
                        <a href="{{ route('user.group_data.create') }}" type="button" class="btn btn-sm btn-primary">
                                Create New 
                            </a>                   
                        </div>

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
                       <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Group ID</th>
                                    <th>Group Name</th>
                                    <th>Group description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($paginatedGroups) > 0)
                                    @foreach($paginatedGroups as $group)
                                        <tr>
                                            <td>{{ $group['group_id'] }}</td>
                                            <td>{{ $group['group_name'] }}</td> 
                                            <td>{{ $group['description'] }}</td> 
                                            <td>
                                                    <span class="badge bg-success">Active</span>
                                            </td>
                                            <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.group_data.edit',$group['group_id']) }}" class="btn btn-icon" title="Settings">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                                <a href="{{ route('user.group_data.delete', $group['group_id']) }}" onclick="return confirm('Are you sure to delete this?')" class="btn btn-icon btn-delete" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No groups found.</td>
                                    </tr>
                                @endif         
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
@endpush