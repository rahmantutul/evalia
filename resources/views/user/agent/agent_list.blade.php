@extends('user.layouts.app')
@push('styles')
<style>
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


    <style>

        .stat-card {
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 23px;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .display-6 {
            font-size: 2rem;
        }

        .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fw-bold {
            font-size: 1.8rem;
        }

        .card-text {
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    @php
        $totalAgents = 2;
        $activeAgents = 2;
        $companiesCount = 2;
        $agentsPerCompany = 1;
        $inactiveAgents = 0;
    @endphp
    <div class="row m-4">
        <!-- Total Agents Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #0d6efd;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Total Agents</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-primary">{{ $totalAgents ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            <i class="fas fa-arrow-up text-success"></i> 
                            {{ $activeAgents ?? 0 }} active
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-tie display-6 text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Agents Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #198754;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Active Agents</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-success">{{ $activeAgents ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            <i class="fas fa-chart-pie text-success"></i> 
                            {{ $totalAgents ? round(($activeAgents / $totalAgents) * 100, 1) : 0 }}% of total
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-check display-6 text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Companies Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #0dcaf0;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Companies</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-info">{{ $companiesCount ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            Avg: {{ $agentsPerCompany ?? 0 }} agents/company
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-building display-6 text-info opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Agents Card -->
        <div class="col-md-3 mb-3">
            <div class="card stat-card" style="border: 2px solid #ffc107;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 text-dark">Inactive Agents</h5>
                        <h2 class="fw-bold mt-2 mb-0 text-warning">{{ $inactiveAgents ?? 0 }}</h2>
                        <p class="card-text small mb-0 text-muted">
                            <i class="fas fa-exclamation-triangle text-warning"></i> 
                            {{ $totalAgents ? round(($inactiveAgents / $totalAgents) * 100, 1) : 0 }}% of total
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-alt-slash display-6 text-warning opacity-75"></i>
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
                        <h4 class="card-title mb-0">Agent List</h4>
                        <a href="{{ route('user.agent.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Add Agent</a>                  
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
                                <thead class="table-dark">
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Agent Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agents as $parent)
                                        @foreach($parent['agents'] as $agent)
                                        <tr>
                                            <td>{{ $parent['company_name'] }}</td>
                                            <td>{{ $agent['agent_name'] }}</td>
                                            <td>{{ $agent['email'] }}</td>
                                            <td>{{ $agent['phone_number'] }}</td>
                                            <td>{{ $agent['description'] }}</td>
                                            <td>
                                                <span class="badge {{ $agent['is_active'] ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $agent['is_active'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('user.agent.details',$agent['agent_id']) }}" class="btn btn-icon" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user.agent.edit',$agent['agent_id']) }}" class="btn btn-icon" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('user.agent.delete',$agent['agent_id']) }}" onclick="return confirm('Are you sure to delete this?')" class="btn btn-icon btn-delete" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
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
