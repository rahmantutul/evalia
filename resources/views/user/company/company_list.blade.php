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
            display: flex;
            float: left;
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

    .company-page .card {
        border: none;
        border-radius: 12px;
    }

    .company-page .card-header {
        background: #fff;
    }

    .company-page .card-body {
        padding: 0.75rem 1rem;
    }

    @media (max-width: 767.98px) {
        .company-page {
            padding-bottom: 1rem !important;
        }

        /* Stats bar */
        .company-page .sticky-top-bar {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
            margin-bottom: 0.5rem;
        }

        .company-page .card-body.py-3 {
            padding: 0.6rem 0.75rem !important;
        }

        .company-page .row.g-3 {
            --bs-gutter-x: 0.6rem;
            --bs-gutter-y: 0.5rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .company-page .stat-item {
            border-right: none !important;
            border-bottom: 1px solid #f0f2f5 !important;
            padding: 0.4rem 0.15rem !important;
            margin-bottom: 0 !important;
        }

        .company-page .stat-item:nth-child(3) {
            border-bottom: none !important;
        }

        .company-page .stat-item:nth-child(n+4) {
            border-bottom: none !important;
        }

        .company-page .stat-item .d-flex {
            gap: 0.3rem;
            align-items: center;
            min-width: 0;
        }

        .company-page .stat-item i {
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .company-page .stat-item h6 {
            font-size: 0.82rem;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .company-page .stat-item p {
            font-size: 0.58rem !important;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Company card */
        .company-page .row.mb-4.mt-3 {
            margin-top: 0.5rem !important;
        }

        .company-page .users-card,
        .company-page .card {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            border: none;
        }

        .company-page .card-header {
            padding: 0.7rem 0.75rem !important;
            background: #fff;
            border-bottom: 1px solid #f0f2f5;
            border-radius: 12px 12px 0 0 !important;
        }

        .company-page .card-header .col {
            flex-direction: column;
            align-items: stretch !important;
            gap: 0.5rem;
        }

        .company-page .card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1a1d21;
            margin-bottom: 0 !important;
        }

        .company-page .card-header .btn {
            justify-content: center;
            min-height: 36px;
            width: 100%;
            font-size: 0.82rem;
            font-weight: 500;
            border-radius: 8px;
        }

        /* Datatable toolbar */
        .company-page .datatable-wrapper .datatable-top {
            padding: 0.5rem 0.6rem !important;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            border-bottom: 1px solid #f0f2f5;
        }

        .company-page .datatable-wrapper .datatable-top > div:first-child {
            float: none !important;
            width: 100%;
        }

        .company-page .datatable-wrapper .datatable-top > div:last-child {
            float: none !important;
            width: 100%;
        }

        .company-page .datatable-wrapper .datatable-input {
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #e2e5ea;
            border-radius: 8px;
            font-size: 0.82rem;
            background: #f8f9fb;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .company-page .datatable-wrapper .datatable-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
            background: #fff;
            outline: none;
        }

        .company-page .datatable-wrapper .datatable-info {
            font-size: 0.72rem;
            color: #6b7280;
            margin: 0 !important;
            padding: 0 0.6rem 0.4rem;
        }

        /* Table → cards */
        .company-page .table-responsive {
            overflow: visible;
        }

        .company-page table.datatable,
        .company-page table.datatable thead,
        .company-page table.datatable tbody,
        .company-page table.datatable th,
        .company-page table.datatable td,
        .company-page table.datatable tr {
            display: block;
        }

        .company-page table.datatable thead {
            display: none;
        }

        .company-page table.datatable tbody {
            background: #f4f6f9;
            padding: 0.6rem;
        }

        .company-page table.datatable tbody tr {
            background: #fff;
            border: 1px solid #e8ecf1;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            margin-bottom: 0.7rem;
            overflow: hidden;
            padding: 0 0 0.25rem 0;
        }

        .company-page table.datatable tbody tr:last-child {
            margin-bottom: 0;
        }

        .company-page table.datatable tbody td {
            border: 0 !important;
            display: grid;
            grid-template-columns: minmax(70px, 35%) minmax(0, 1fr);
            gap: 0.4rem;
            min-height: 32px;
            padding: 0.35rem 0.5rem !important;
            text-align: left !important;
            word-break: break-word;
        }

        .company-page table.datatable tbody td::before {
            color: #6b7280;
            content: attr(data-label);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        /* Company name row — full width */
        .company-page table.datatable tbody td[data-label="Company"] {
            background: linear-gradient(180deg, #fff 0%, #fbfcfe 100%);
            border-bottom: 1px solid #eef1f5 !important;
            display: flex;
            align-items: center;
            padding: 0.6rem 0.5rem !important;
        }

        .company-page table.datatable tbody td[data-label="Company"]::before {
            content: none;
        }

        .company-page table.datatable tbody td[data-label="Company"] a {
            font-size: 0.88rem;
            text-decoration: none;
        }

        .company-page table.datatable tbody td[data-label="Company"] a:hover {
            text-decoration: underline;
        }

        /* Industry & Agents rows */
        .company-page table.datatable tbody td[data-label="Industry"],
        .company-page table.datatable tbody td[data-label="Agents"] {
            padding: 0.4rem 0.5rem !important;
        }

        /* Actions row */
        .company-page table.datatable tbody td[data-label="Actions"] {
            border-top: 1px solid #eef1f5 !important;
            display: block;
            padding: 0.5rem 0.5rem 0.55rem !important;
            background: #fafbfc;
        }

        .company-page table.datatable tbody td[data-label="Actions"]::before {
            content: none;
        }

        .company-page table.datatable tbody td[data-label="Actions"] .btn-group {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.4rem;
            width: 100%;
        }

        .company-page table.datatable tbody td[data-label="Actions"] .btn-icon {
            width: 100%;
            height: 36px;
            border-radius: 8px;
            font-size: 0.72rem;
            gap: 0.3rem;
            border: 1px solid #e2e5ea;
            background: #fff;
        }

        .company-page table.datatable tbody td[data-label="Actions"] .btn-icon::after {
            display: none;
        }

        /* Pagination */
        .company-page .datatable-wrapper .datatable-bottom {
            padding: 0.5rem 0.6rem !important;
            border-top: 1px solid #f0f2f5;
        }

        .company-page .datatable-wrapper .datatable-pagination {
            text-align: center;
        }

        .company-page .datatable-wrapper .datatable-pagination ul {
            display: flex;
            justify-content: center;
            gap: 0.2rem;
            flex-wrap: wrap;
        }

        .company-page .datatable-wrapper .datatable-pagination li {
            float: none;
        }

        .company-page .datatable-wrapper .datatable-pagination a,
        .company-page .datatable-wrapper .datatable-pagination button {
            padding: 0.35rem 0.6rem !important;
            font-size: 0.75rem;
            border-radius: 6px;
            border: 1px solid #e2e5ea;
            color: #495057;
            background: #fff;
            min-width: 32px;
            text-align: center;
        }

        .company-page .datatable-wrapper .datatable-pagination .datatable-active a,
        .company-page .datatable-wrapper .datatable-pagination .datatable-active button {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
    }

    @media (max-width: 420px) {
        .company-page table.datatable tbody td {
            grid-template-columns: 1fr;
            gap: 0.2rem;
        }

        .company-page table.datatable tbody td::before {
            margin-bottom: 0.1rem;
        }

        .company-page table.datatable tbody td[data-label="Actions"] .btn-group {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    </style>
@endpush

@section('content')
<div class="container-fluid company-page">
    <!-- Sticky Statistics Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm sticky-top-bar">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <!-- Total Companies -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-primary me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="totalCompanies">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Companies</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Tasks -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tasks text-success me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="activeTasks">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Active Tasks</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Completed Tasks -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="completedTasks">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Completed</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pending Analysis -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="pendingAnalysis">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Pending</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Agents -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-secondary me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="totalAgents">0</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Agents</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Rate -->
                        <div class="col-md-2 col-sm-4 col-6 stat-item">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-star text-danger me-2"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="averageQuality">0%</h6>
                                    <p class="text-muted small mb-0" style="font-size: 10px;">Quality Score</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company List -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">                      
                        <h4 class="card-title mb-0">Company List</h4>
                         @if(session('user.role.name') !== 'Supervisor')
                        <a href="{{ route('user.company.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Create New</a>                  
                        @endif
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
                                    <th>Company Name</th>
                                    <th>Industry Sector</th>
                                    <th>Agents</th>
                                    <th style="text-align: center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalCompanies = count($companies);
                                    
                                @endphp
                                @foreach($companies as $company)
                                    @php
                                        // Use real count from controller
                                        $agentsCount = $company['agents_count'] ?? 0;
                                        
                                        // Default sources fallback if not in DB
                                        $defaultSources = ['api'];
                                        if ($company['id'] == 'arab-bank') {
                                            $defaultSources[] = 'genesys';
                                        }
                                    @endphp
                                    <tr>
                                        <td data-label="Company">
                                            <a href="{{ route('user.company.view', $company['id']) }}" class="fw-bold text-primary">
                                                {{ $company['name'] }}
                                            </a>
                                        </td>
                                        <td data-label="Industry">{{ $company['group_name'] ?? 'Private Sector' }}</td>
                                        <td data-label="Agents">{{ $agentsCount }}</td>
                                        <td data-label="Actions">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.company.view',$company['id']) }}" class="btn btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user.task.list',$company['id']) }}" class="btn btn-icon" title="Task List">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                  @if(session('user.role.name') !== 'Supervisor')
                                                <a href="{{ route('user.company.edit',$company['id']) }}" class="btn btn-icon" title="Settings">
                                                    <i class="fas fa-cogs"></i>
                                                </a>
                                                <form action="{{ route('user.company.delete',$company['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-delete" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    {{--  <!-- Modal -->
                                    <div class="modal fade" id="audioUploadModal{{ $company['id'] }}" tabindex="-1" aria-labelledby="audioUploadModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <!-- Modal Header -->
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-600 text-black" id="audioUploadModalLabel" >
                                                    <i class="fas fa-wave-square text-black me-2"></i> Audio Upload Form
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('user.task.store') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row g-4 mb-4">

                                                                <div class="col-md-4">
                                                                    <div class="card border-0 shadow-sm h-100">
                                                                        <div class="card-header bg-white border-0 py-2">
                                                                            <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                                                                                <span class="bg-dark bg-opacity-10 text-primary p-2 me-2 rounded">
                                                                                    <i class="fas fa-microphone"></i>
                                                                                </span>
                                                                                Customer Audio
                                                                            </h5>
                                                                        </div>
                                                                        <div class="card-body d-flex flex-column">
                                                                            <p class="text-muted small mb-4">Upload customer audio file in WAV or MP3 format</p>
                                                                            <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                                                                                <i class="bi bi-cloud-upload text-primary fs-1 mb-2"></i>
                                                                                <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                                                                                <span class="text-muted small mb-3">or click to browse</span>
                                                                                <span class="badge bg-light text-dark px-3 py-2">Max 50MB</span>
                                                                                <input type="file" name="customer_audio" class="d-none" accept="audio/*" required>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="card border-0 shadow-sm h-100">
                                                                        <div class="card-header bg-white border-0 py-2">
                                                                            <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                                                                                <span class="bg-danger bg-opacity-10 text-danger p-2 me-2 rounded">
                                                                                    <i class="fas fa-headset fs-5"></i>
                                                                                </span>
                                                                                Agent Audio
                                                                            </h5>
                                                                        </div>
                                                                        <div class="card-body d-flex flex-column">
                                                                            <p class="text-muted small mb-4">Upload agent audio file in WAV or MP3 format</p>
                                                                            <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                                                                                <i class="bi bi-cloud-upload text-danger fs-1 mb-2"></i>
                                                                                <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                                                                                <span class="text-muted small mb-3">or click to browse</span>
                                                                                <span class="badge bg-light text-dark px-3 py-2">Max 50MB</span>
                                                                                <input type="file" name="agent_audio" class="d-none" accept="audio/*" required>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="card border-0 shadow-sm h-100">
                                                                        <div class="card-header bg-white border-0 py-2">
                                                                            <h5 class="card-title mb-0 fw-600 d-flex align-items-center">
                                                                                <span class="bg-success bg-opacity-10 text-success p-2 me-2 rounded">
                                                                                    <i class="fas fa-microphone fs-5"></i>
                                                                                </span>
                                                                                Combined Audio
                                                                            </h5>
                                                                        </div>
                                                                        <div class="card-body d-flex flex-column">
                                                                            <p class="text-muted small mb-4">Upload pre-mixed audio file (optional)</p>
                                                                            <label class="upload-container flex-grow-1 d-flex flex-column justify-content-center align-items-center border-2 border-dashed rounded p-4 bg-light bg-opacity-25">
                                                                                <i class="bi bi-cloud-upload text-success fs-1 mb-2"></i>
                                                                                <span class="text-center mb-1 fw-500">Drag & drop files here</span>
                                                                                <span class="text-muted small mb-3">or click to browse</span>
                                                                                <span class="badge bg-light text-dark px-3 py-2">Max 100MB</span>
                                                                                <input type="file" name="combined_audio" class="d-none" accept="audio/*">
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="row mb-4">
                                                                <div class="col-12">
                                                                    <div class="card border-0 shadow-sm">
                                                                        <div class="card-header bg-white border-0 py-2">
                                                                            <h5 class="card-title mb-0 fw-500 d-flex align-items-center">
                                                                                <span class="bg-info bg-opacity-10 text-info p-2 me-2 rounded">
                                                                                    <i class="fas fa-user-tie"></i>
                                                                                </span>
                                                                                Select Agent
                                                                            </h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="form-group">
                                                                                <label for="agent_id" class="form-label fw-500 mb-2">Choose an agent for this analysis</label>
                                                                                <select name="agent_id" id="agent_id" class="form-select form-select-lg py-3 select2" required>
                                                                                    <option value="">-- Select Agent --</option>
                                                                                        @foreach($companyAgents as $agent)
                                                                                            <option value="{{ $agent['id'] }}">
                                                                                                {{ $agent['agent_id_display'] }} - {{ $agent['name'] }} 
                                                                                                @if($agent['email'])
                                                                                                    ({{ $agent['email'] }})
                                                                                                @endif
                                                                                            </option>
                                                                                        @endforeach
                                                                                </select>
                                                                                @error('agent_id')
                                                                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Hidden company_id -->
                                                            <input type="hidden" name="company_id" value="{{ $company['id'] }}">

                                                            <!-- Action Buttons -->
                                                            <div class="d-flex justify-content-center mb-2">
                                                                <button type="submit" class="btn btn-primary px-5 py-2 me-3 rounded-pill fw-600 shadow-sm">
                                                                    <i class="fas fa-chart-line me-2"></i> Analyze Audio
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>  --}}
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Use actual statistics from controller
        const stats = {
            totalCompanies: @json(count($companies)),
            activeTasks: @json($totalActiveTasks ?? 0),
            completedTasks: @json($totalCompletedTasks ?? 0),
            pendingAnalysis: @json($totalPendingAnalysis ?? 0),
            totalAgents: @json($totalAgentsCount ?? 0),
            averageQuality: @json($avgQaScore ?? 0)
        };

        // Animate counting up for each statistic
        function animateCounter(elementId, finalValue, suffix = '') {
            const element = document.getElementById(elementId);
            let current = 0;
            const increment = finalValue > 0 ? finalValue / 50 : 0;
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    current = finalValue;
                    clearInterval(timer);
                }
                
                let val = Math.floor(current);
                if (suffix === '%') {
                    element.textContent = val + suffix;
                } else if (elementId === 'activeTasks' && val === 0) {
                    element.textContent = '00';
                } else {
                    element.textContent = val.toLocaleString();
                }
            }, 30);
        }

        // Start animations
        animateCounter('totalCompanies', stats.totalCompanies);
        animateCounter('activeTasks', stats.activeTasks);
        animateCounter('completedTasks', stats.completedTasks);
        animateCounter('pendingAnalysis', stats.pendingAnalysis);
        animateCounter('totalAgents', stats.totalAgents);
        animateCounter('averageQuality', stats.averageQuality, '%');

        // Make the statistics bar sticky when scrolling
        const stickyBar = document.querySelector('.sticky-top-bar');
        const originalOffsetTop = stickyBar.offsetTop;
        
        function handleScroll() {
            if (window.pageYOffset > originalOffsetTop) {
                stickyBar.classList.add('sticky-active');
            } else {
                stickyBar.classList.remove('sticky-active');
            }
        }
        
        window.addEventListener('scroll', handleScroll);

        // Add CSS for sticky behavior
        const style = document.createElement('style');
        style.textContent = `
            .sticky-top-bar.sticky-active {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1020;
                margin: 0 15px;
                width: calc(100% - 30px);
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                animation: slideDown 0.3s ease;
            }
            
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            .stat-item {
                transition: transform 0.2s ease;
            }
            
            .stat-item:hover {
                transform: translateY(-2px);
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush
