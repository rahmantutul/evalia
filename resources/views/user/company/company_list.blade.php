@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
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
    <div class="row m-4">
        <!-- Total Agents Card -->
        <div class="col-md-3">
            <div class="card stat-card" style="border: 2px solid #0d6efd; padding: 20px;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0 text-dark">Total Agents</h5>
                            <h2 class="fw-bold mt-2 mb-0 text-primary">{{ $totalAgents ?? 42 }}</h2>
                            <p class="card-text small mb-0 text-muted"><i class="bi bi-arrow-up-short text-success"></i> +5 from last week</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-people-fill display-6 text-primary opacity-75"></i>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Total Calls Card -->
        <div class="col-md-3">
            <div class="card stat-card" style="border: 2px solid #198754;  padding: 20px;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Total Calls</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-success">{{ $totalCalls ?? 1248 }}</h2>
                        <p class="card-text small mb-0 text-muted"><i class="bi bi-arrow-up-short text-success"></i> +128 today</p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-telephone-fill display-6 text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Calls Card -->
        <div class="col-md-3">
            <div class="card stat-card" style="border: 2px solid #0dcaf0;  padding: 20px;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Active Calls</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-info">{{ $activeCalls ?? 18 }}</h2>
                        <p class="card-text small mb-0 text-muted">Avg. duration: 4.2 min</p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-telephone-outbound-fill display-6 text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Rate Card -->
        <div class="col-md-3">
            <div class="card stat-card" style="border: 2px solid #ffc107;  padding: 20px;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Resolution Rate</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-warning">{{ $resolutionRate ?? 87 }}%</h2>
                        <p class="card-text small mb-0 text-muted"><i class="bi bi-arrow-up-short text-success"></i> +3% from last month</p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-graph-up display-6 text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">                      
                        <h4 class="card-title mb-0">Company List</h4>
                        <a href="{{ route('user.company.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Create New</a>                  
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

                        <table class="table datatable mb-0" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>Industry</th>
                                    <th>Agents</th>
                                    <th>Location</th>
                                    <th style="text-align: center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paginatedCompanies  as $company)
                                    @php
                                        $industries = ['Tech', 'Finance', 'Healthcare', 'Education', 'Logistics','Tech', 'Finance', 'Healthcare', 'Education', 'Logistics'];
                                        $locations = ['New York', 'San Francisco', 'Chicago', 'Los Angeles', 'Miami'];
                                        $agents = rand(5, 50);
                                        $industry = $industries[array_rand($industries)];
                                        $location = $locations[array_rand($locations)];
                                    @endphp
                                    <tr>
                                        <td>{{ $company['id'] }}</td>
                                        <td>{{ $company['name'] }}</td>
                                        <td>{{ $industry }}</td>
                                        <td>{{ $agents }}</td>
                                        <td>{{ $location }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.company.view',$company['id']) }}" class="btn btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user.task.list',$company['id']) }}" class="btn btn-icon" title="Task List">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                <a href="{{ route('user.company.copy',$company['id']) }}" class="btn btn-icon" title="Copy" onclick="copyCompany({{ $company['id'] }}); return false;">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                                <a href="{{ route('user.company.edit',$company['id']) }}" class="btn btn-icon" title="Settings">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                                <a href="{{ route('user.company.delete',$company['id']) }}" onclick="return confirm('Are you sure to delete this?')" class="btn btn-icon btn-delete" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach         
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
