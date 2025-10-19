












@extends('user.layouts.app')
@push('styles')
<style>

    .stats-bar {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .stat-item {
        padding: 20px;
        border-right: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .stat-item:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .stat-icon.icon-blue {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }

    .stat-icon.icon-green {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #15803d;
    }

    .stat-icon.icon-amber {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
    }

    .stat-icon.icon-cyan {
        background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
        color: #0e7490;
    }

    .stat-icon.icon-emerald {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #047857;
    }

    .stat-icon.icon-slate {
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        color: #334155;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ============================================
       CARD STYLES
       ============================================ */
    .main-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 24px;
    }

    .card-title-custom {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.3px;
        margin-bottom: 4px;
    }

    .card-subtitle {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 400;
    }

    /* ============================================
       BUTTON STYLES
       ============================================ */
    .btn-custom {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
    }

    .btn-custom:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    /* ============================================
       TABLE STYLES
       ============================================ */
    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        color: #475569;
        border: none;
        padding: 16px 20px;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-modern thead th:first-child {
        border-top-left-radius: 12px;
    }

    .table-modern thead th:last-child {
        border-top-right-radius: 12px;
    }

    .table-modern tbody td {
        padding: 18px 20px;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #475569;
        font-size: 0.9rem;
        background: #ffffff;
    }

    .table-modern tbody tr {
        transition: all 0.2s ease;
    }

    /* ============================================
       EXPANDABLE ROW STYLES
       ============================================ */
    .expandable-row {
        cursor: pointer;
    }

    .expandable-row:hover {
        background: linear-gradient(135deg, #fafbfc 0%, #f8fafc 100%) !important;
    }

    .expandable-row.expanded {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        border-left: 3px solid #3b82f6;
    }

    .expand-icon {
        transition: transform 0.3s ease;
        font-size: 12px;
        color: #94a3b8;
        display: inline-block;
    }

    .expandable-row.expanded .expand-icon {
        transform: rotate(90deg);
        color: #3b82f6;
    }

    .performance-details {
        display: none;
        background: linear-gradient(135deg, #fafbfc 0%, #f8fafc 100%);
    }

    .expandable-row.expanded + .performance-details {
        display: table-row;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ============================================
       BADGE STYLES
       ============================================ */
    .badge-modern {
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        letter-spacing: 0.3px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    /* ============================================
       ACTION BUTTON STYLES
       ============================================ */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-action:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.25);
    }

    /* ============================================
       PERFORMANCE CONTENT STYLES
       ============================================ */
    .performance-content-wrapper {
        padding: 32px;
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    }

    .performance-header {
        padding-bottom: 24px;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 24px;
    }

    .metric-card-enhanced {
        background: #ffffff;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #e2e8f0 0%, #cbd5e1 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .metric-card-enhanced:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        transform: translateY(-4px);
    }

    .metric-card-enhanced:hover::before {
        opacity: 1;
    }

    .metric-card-enhanced.card-blue:hover::before {
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    }

    .metric-card-enhanced.card-green:hover::before {
        background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
    }

    .metric-card-enhanced.card-cyan:hover::before {
        background: linear-gradient(90deg, #06b6d4 0%, #0891b2 100%);
    }

    .metric-card-enhanced.card-amber:hover::before {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }

    .metric-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    .bg-blue-light { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
    .bg-green-light { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); }
    .bg-cyan-light { background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); }
    .bg-amber-light { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }

    .text-blue-600 { color: #2563eb; }
    .text-green-600 { color: #16a34a; }
    .text-cyan-600 { color: #0891b2; }
    .text-amber-600 { color: #d97706; }

    .metric-value-enhanced {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 10px;
        display: flex;
        align-items: baseline;
        letter-spacing: -1px;
    }

    .metric-unit {
        font-size: 1.1rem;
        font-weight: 600;
        color: #94a3b8;
        margin-left: 6px;
    }

    .metric-label-enhanced {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .progress-enhanced {
        height: 10px;
        border-radius: 6px;
        background-color: #f1f5f9;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    .progress-bar-enhanced {
        height: 100%;
        border-radius: 6px;
        transition: width 0.8s ease;
        position: relative;
        overflow: hidden;
    }

    .progress-bar-enhanced::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .bg-gradient-blue {
        background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
    }

    .bg-gradient-green {
        background: linear-gradient(90deg, #4ade80 0%, #22c55e 50%, #16a34a 100%);
    }

    .bg-gradient-cyan {
        background: linear-gradient(90deg, #22d3ee 0%, #06b6d4 50%, #0891b2 100%);
    }

    .bg-gradient-amber {
        background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
    }

    .metric-trend {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 8px;
    }

    /* ============================================
       PERFORMANCE HISTORY STYLES
       ============================================ */
    .performance-history-section {
        background: #ffffff;
        border-radius: 14px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .performance-history-table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .performance-history-table thead th {
        padding: 16px 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid #e2e8f0;
    }

    .performance-history-table tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .history-row {
        transition: all 0.2s ease;
    }

    .history-row:hover {
        background: linear-gradient(135deg, #fafbfc 0%, #f8fafc 100%);
    }

    .history-row:last-child td {
        border-bottom: none;
    }

    .period-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4338ca;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .score-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .score-overall {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .mini-progress-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .score-text {
        font-size: 0.875rem;
        font-weight: 700;
        color: #334155;
    }

    .mini-progress {
        height: 6px;
        background-color: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        width: 90px;
        margin: 0 auto;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    .mini-progress-bar {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }

    .bg-green-500 { background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%); }
    .bg-cyan-500 { background: linear-gradient(90deg, #06b6d4 0%, #0891b2 100%); }
    .bg-amber-500 { background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); }

    /* ============================================
       LOADING & EMPTY STATES
       ============================================ */
    .loading-spinner {
        border: 3px solid #e2e8f0;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        animation: spin 0.8s linear infinite;
        margin: 0 auto 12px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .empty-state {
        background: linear-gradient(135deg, #fafbfc 0%, #f1f5f9 100%);
        border-radius: 14px;
        border: 2px dashed #cbd5e1;
        padding: 48px 24px;
    }

    .empty-state-icon {
        opacity: 0.4;
    }

    /* ============================================
       ALERT STYLES
       ============================================ */
    .alert-modern {
        border-radius: 12px;
        border: 1px solid;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    .alert-success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border-color: #86efac;
        color: #15803d;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-color: #fca5a5;
        color: #991b1b;
    }

    /* ============================================
       UTILITIES
       ============================================ */
    .text-slate-800 { color: #1e293b; }
    .text-slate-700 { color: #334155; }
    .text-slate-600 { color: #475569; }
    .text-slate-500 { color: #64748b; }
    .text-slate-300 { color: #cbd5e1; }

    .bg-slate-100 { background-color: #f1f5f9; }

    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }

    /* ============================================
       RESPONSIVE STYLES
       ============================================ */
    @media (max-width: 1200px) {
        .stat-value {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .stat-item {
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            font-size: 18px;
        }

        .stat-value {
            font-size: 1.4rem;
        }
        
        .performance-content-wrapper {
            padding: 20px;
        }

        .metric-card-enhanced {
            padding: 18px;
        }

        .metric-value-enhanced {
            font-size: 1.75rem;
        }

        .performance-history-section {
            padding: 18px;
        }

        .performance-history-table thead th,
        .performance-history-table tbody td {
            padding: 14px 12px;
            font-size: 0.8rem;
        }

        .mini-progress {
            width: 70px;
        }
    }

    @media (max-width: 576px) {
        .card-title-custom {
            font-size: 1.1rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .metric-value-enhanced {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Statistics Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-bar">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Total Agents -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center h-100">
                                <div class="stat-icon icon-blue me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="stat-value" id="totalAgents">{{ $summary['total_agents'] ?? 0 }}</div>
                                    <div class="stat-label">Total Agents</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Agents -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center h-100">
                                <div class="stat-icon icon-green me-3">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="stat-value" id="activeAgents">{{ $summary['active_agents'] ?? 0 }}</div>
                                    <div class="stat-label">Active</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Average Rating -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center h-100">
                                <div class="stat-icon icon-amber me-3">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="stat-value" id="averageRating">{{ number_format($summary['avg_rating'] ?? 0, 1) }}</div>
                                    <div class="stat-label">Avg Rating</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Calls -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center h-100">
                                <div class="stat-icon icon-cyan me-3">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="stat-value" id="totalCalls">{{ $summary['total_calls'] ?? 0 }}</div>
                                    <div class="stat-label">Total Calls</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success Rate -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center h-100">
                                <div class="stat-icon icon-emerald me-3">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="stat-value" id="successRate">{{ number_format($summary['success_rate'] ?? 0, 1) }}%</div>
                                    <div class="stat-label">Success Rate</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Response Time -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center h-100">
                                <div class="stat-icon icon-slate me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="stat-value" id="avgResponseTime">{{ number_format($summary['avg_response_time'] ?? 0, 1) }}s</div>
                                    <div class="stat-label">Avg Response</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent List -->
    <div class="row">
        <div class="col-12">
            <div class="main-card">
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="card-title-custom mb-1">
                                <i class="fas fa-users-cog me-2 text-blue-600"></i>
                                Agent Management
                            </h5>
                            <p class="card-subtitle mb-0">View and manage all your agents</p>
                        </div>
                        <a href="{{ route('user.agents.dashboard') }}" class="btn-custom">
                            <i class="fas fa-chart-bar"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-modern alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif 

                    @if(session('error'))
                        <div class="alert alert-danger alert-modern alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-modern mb-0" id="agentsTable">
                            <thead>
                                <tr>
                                    <th width="50"></th>
                                    <th>Agent ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Company</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agents as $agent)
                                <!-- Main Agent Row -->
                                <tr class="expandable-row" data-agent-id="{{ $agent['id'] }}">
                                    <td>
                                        <i class="fas fa-chevron-right expand-icon"></i>
                                    </td>
                                    <td>
                                        <span class="badge-modern">
                                            {{ $agent['agent_id_display'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold text-slate-800">{{ $agent['name'] }}</div>
                                        </div>
                                    </td>
                                    <td class="text-slate-600">{{ $agent['email'] }}</td>
                                    <td class="text-slate-600">
                                        {{ $agent['phone'] ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge-modern">
                                            {{ $agent['company'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('user.agents.show', $agent['id']) }}" class="btn-action" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('user.agents.performance', $agent['id']) }}" class="btn-action" title="Performance">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Performance Details Row -->
                                <tr class="performance-details">
                                    <td colspan="7" class="p-0">
                                        <div class="performance-metrics" id="performance-{{ $agent['id'] }}">
                                            <div class="text-center py-5">
                                                <div class="loading-spinner"></div>
                                                <p class="mt-3 text-slate-500 mb-0 fw-semibold">Loading performance data...</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state d-inline-block">
                                            <div class="empty-state-icon mb-3">
                                                <i class="fas fa-users fa-3x text-slate-300"></i>
                                            </div>
                                            <h6 class="text-slate-600 mb-2">No Agents Found</h6>
                                            <p class="text-slate-500 mb-0 text-sm">Get started by adding your first agent</p>
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
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================
        // COUNTER ANIMATIONS
        // ============================================
        function animateCounter(elementId, finalValue, suffix = '', isDecimal = false) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            let current = 0;
            const increment = finalValue / 40;
            const duration = 1500;
            const stepTime = duration / 40;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    current = finalValue;
                    clearInterval(timer);
                }
                
                if (isDecimal) {
                    element.textContent = current.toFixed(1) + suffix;
                } else if (suffix === '%') {
                    element.textContent = Math.round(current) + suffix;
                } else {
                    element.textContent = Math.round(current);
                }
            }, stepTime);
        }

        // Start counter animations
        const stats = {
            totalAgents: {{ $summary['total_agents'] ?? 0 }},
            activeAgents: {{ $summary['active_agents'] ?? 0 }},
            averageRating: {{ $summary['avg_rating'] ?? 0 }},
            totalCalls: {{ $summary['total_calls'] ?? 0 }},
            successRate: {{ $summary['success_rate'] ?? 0 }},
            avgResponseTime: {{ $summary['avg_response_time'] ?? 0 }}
        };

        setTimeout(() => {
            animateCounter('totalAgents', stats.totalAgents);
            animateCounter('activeAgents', stats.activeAgents);
            animateCounter('averageRating', parseFloat(stats.averageRating), '', true);
            animateCounter('totalCalls', stats.totalCalls);
            animateCounter('successRate', stats.successRate, '%');
            animateCounter('avgResponseTime', stats.avgResponseTime, 's', true);
        }, 200);

        // ============================================
        // EXPANDABLE ROW FUNCTIONALITY
        // ============================================
        const expandableRows = document.querySelectorAll('.expandable-row');
        
        expandableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't expand if clicking on action buttons
                if (e.target.closest('.btn-action')) {
                    return;
                }

                const agentId = this.getAttribute('data-agent-id');
                const isExpanded = this.classList.contains('expanded');
                
                // Close all other expanded rows
                document.querySelectorAll('.expandable-row.expanded').forEach(expandedRow => {
                    if (expandedRow !== this) {
                        expandedRow.classList.remove('expanded');
                    }
                });
                
                // Toggle current row
                this.classList.toggle('expanded');
                
                // Load performance data if expanding and not already loaded
                if (!isExpanded && !this.dataset.loaded) {
                    loadPerformanceData(agentId);
                    this.dataset.loaded = 'true';
                }
            });
        });

        // ============================================
        // LOAD PERFORMANCE DATA
        // ============================================
        function loadPerformanceData(agentId) {
            const performanceContainer = document.getElementById(`performance-${agentId}`);
            
            const url = "{{ route('agents.performance.data', ['agentId' => ':agentId']) }}".replace(':agentId', agentId);
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        performanceContainer.innerHTML = createPerformanceHTML(data.performance);
                    } else {
                        performanceContainer.innerHTML = createErrorState();
                    }
                })
                .catch(error => {
                    console.error('Error loading performance data:', error);
                    performanceContainer.innerHTML = createErrorState();
                });
        }

        // ============================================
        // CREATE PERFORMANCE HTML
        // ============================================
        function createPerformanceHTML(performance) {
            const current = performance.current_scores || {};
            const history = performance.performance_history || [];
            
            // Helper functions
            const getTrendIcon = (current, previous) => {
                if (!previous) return '';
                const diff = current - previous;
                if (diff > 0) return '<i class="fas fa-arrow-up text-success ms-2" style="font-size: 0.7rem;"></i>';
                if (diff < 0) return '<i class="fas fa-arrow-down text-danger ms-2" style="font-size: 0.7rem;"></i>';
                return '<i class="fas fa-minus text-muted ms-2" style="font-size: 0.7rem;"></i>';
            };
            
            const getTrendPercentage = (current, previous) => {
                if (!previous || previous === 0) return '';
                const diff = ((current - previous) / previous * 100).toFixed(1);
                const color = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted';
                const icon = diff > 0 ? '↑' : diff < 0 ? '↓' : '→';
                return `<span class="${color} fw-bold" style="font-size: 0.75rem;">${icon} ${Math.abs(diff)}%</span>`;
            };
            
            const previousScores = history.length > 0 ? history[0] : null;
            
            return `
                <div class="performance-content-wrapper">
                    <!-- Header Section -->
                    <div class="performance-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-2 text-slate-800 fw-bold" style="font-size: 1.1rem;">
                                    <i class="fas fa-chart-pie me-2 text-blue-600"></i>
                                    Performance Overview
                                </h6>
                                <p class="mb-0 text-slate-500 text-sm">Real-time metrics and performance indicators</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge" style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); color: #475569; padding: 10px 16px; border-radius: 10px; font-weight: 600; border: 1px solid #e2e8f0;">
                                    <i class="far fa-clock me-2"></i>
                                    ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Metrics Grid -->
                    <div class="row g-3 mb-4">
                        <!-- Overall Score -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card-enhanced card-blue">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="metric-icon-wrapper bg-blue-light">
                                        <i class="fas fa-chart-line text-blue-600"></i>
                                    </div>
                                    ${previousScores ? getTrendIcon(current.overall_score || 0, previousScores.overall_score || 0) : ''}
                                </div>
                                <div class="metric-value-enhanced text-blue-600">
                                    ${current.overall_score ? current.overall_score.toFixed(1) : '0.0'}
                                    <span class="metric-unit">/ 100</span>
                                </div>
                                <div class="metric-label-enhanced mb-2">Overall Score</div>
                                <div class="progress-enhanced">
                                    <div class="progress-bar-enhanced bg-gradient-blue" style="width: ${current.overall_score || 0}%"></div>
                                </div>
                                ${previousScores ? `<div class="metric-trend mt-3">${getTrendPercentage(current.overall_score || 0, previousScores.overall_score || 0)} vs last period</div>` : ''}
                            </div>
                        </div>

                        <!-- Answer Accuracy -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card-enhanced card-green">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="metric-icon-wrapper bg-green-light">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    ${previousScores ? getTrendIcon(current.answer_accuracy || 0, previousScores.answer_accuracy || 0) : ''}
                                </div>
                                <div class="metric-value-enhanced text-green-600">
                                    ${current.answer_accuracy || 0}
                                    <span class="metric-unit">%</span>
                                </div>
                                <div class="metric-label-enhanced mb-2">Answer Accuracy</div>
                                <div class="progress-enhanced">
                                    <div class="progress-bar-enhanced bg-gradient-green" style="width: ${current.answer_accuracy || 0}%"></div>
                                </div>
                                ${previousScores ? `<div class="metric-trend mt-3">${getTrendPercentage(current.answer_accuracy || 0, previousScores.answer_accuracy || 0)} vs last period</div>` : ''}
                            </div>
                        </div>

                        <!-- Response Speed -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card-enhanced card-cyan">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="metric-icon-wrapper bg-cyan-light">
                                        <i class="fas fa-bolt text-cyan-600"></i>
                                    </div>
                                    ${previousScores ? getTrendIcon(current.response_speed || 0, previousScores.response_speed || 0) : ''}
                                </div>
                                <div class="metric-value-enhanced text-cyan-600">
                                    ${current.response_speed || 0}
                                    <span class="metric-unit">%</span>
                                </div>
                                <div class="metric-label-enhanced mb-2">Response Speed</div>
                                <div class="progress-enhanced">
                                    <div class="progress-bar-enhanced bg-gradient-cyan" style="width: ${current.response_speed || 0}%"></div>
                                </div>
                                ${previousScores ? `<div class="metric-trend mt-3">${getTrendPercentage(current.response_speed || 0, previousScores.response_speed || 0)} vs last period</div>` : ''}
                            </div>
                        </div>

                        <!-- Customer Satisfaction -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card-enhanced card-amber">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="metric-icon-wrapper bg-amber-light">
                                        <i class="fas fa-star text-amber-600"></i>
                                    </div>
                                    ${previousScores ? getTrendIcon(current.customer_satisfaction || 0, previousScores.customer_satisfaction || 0) : ''}
                                </div>
                                <div class="metric-value-enhanced text-amber-600">
                                    ${current.customer_satisfaction || 0}
                                    <span class="metric-unit">%</span>
                                </div>
                                <div class="metric-label-enhanced mb-2">Customer Satisfaction</div>
                                <div class="progress-enhanced">
                                    <div class="progress-bar-enhanced bg-gradient-amber" style="width: ${current.customer_satisfaction || 0}%"></div>
                                </div>
                                ${previousScores ? `<div class="metric-trend mt-3">${getTrendPercentage(current.customer_satisfaction || 0, previousScores.customer_satisfaction || 0)} vs last period</div>` : ''}
                            </div>
                        </div>
                    </div>

                    ${history.length > 0 ? `
                    <!-- Performance History Section -->
                    <div class="performance-history-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0 text-slate-800 fw-bold">
                                <i class="fas fa-history me-2 text-slate-500"></i>
                                Performance History
                            </h6>
                            <span class="badge bg-slate-100 text-slate-600 fw-semibold" style="padding: 8px 14px; border-radius: 8px;">
                                Last ${Math.min(history.length, 5)} periods
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table performance-history-table mb-0">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-calendar-alt me-2 text-slate-500"></i>
                                                Period
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <i class="fas fa-chart-line me-2 text-blue-600"></i>
                                                Overall
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <i class="fas fa-check-circle me-2 text-green-600"></i>
                                                Accuracy
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <i class="fas fa-bolt me-2 text-cyan-600"></i>
                                                Speed
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <i class="fas fa-star me-2 text-amber-600"></i>
                                                Satisfaction
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${history.slice(0, 5).map((item, index) => `
                                        <tr class="history-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="period-badge me-3">${index + 1}</span>
                                                    <span class="fw-bold text-slate-700">${item.period || 'N/A'}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="score-badge score-overall">${item.overall_score ? item.overall_score.toFixed(1) : '0.0'}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="mini-progress-wrapper">
                                                    <span class="score-text">${item.answer_accuracy || 0}%</span>
                                                    <div class="mini-progress">
                                                        <div class="mini-progress-bar bg-green-500" style="width: ${item.answer_accuracy || 0}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="mini-progress-wrapper">
                                                    <span class="score-text">${item.response_speed || 0}%</span>
                                                    <div class="mini-progress">
                                                        <div class="mini-progress-bar bg-cyan-500" style="width: ${item.response_speed || 0}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="mini-progress-wrapper">
                                                    <span class="score-text">${item.customer_satisfaction || 0}%</span>
                                                    <div class="mini-progress">
                                                        <div class="mini-progress-bar bg-amber-500" style="width: ${item.customer_satisfaction || 0}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    ` : `
                    <div class="empty-state text-center">
                        <div class="empty-state-icon mb-3">
                            <i class="fas fa-chart-bar fa-4x text-slate-300"></i>
                        </div>
                        <h6 class="text-slate-600 fw-bold mb-2">No Performance History</h6>
                        <p class="text-slate-500 text-sm mb-0">Performance data will appear here once available</p>
                    </div>
                    `}
                </div>
            `;
        }

        // ============================================
        // ERROR STATE
        // ============================================
        function createErrorState() {
            return `
                <div class="performance-content-wrapper">
                    <div class="empty-state text-center">
                        <div class="empty-state-icon mb-3">
                            <i class="fas fa-exclamation-triangle fa-4x text-slate-300"></i>
                        </div>
                        <h6 class="text-slate-600 fw-bold mb-2">Failed to Load Performance Data</h6>
                        <p class="text-slate-500 text-sm mb-0">Please try again later</p>
                    </div>
                </div>
            `;
        }

        // ============================================
        // INITIALIZE DATATABLE
        // ============================================
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#agentsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[1, 'asc']],
                language: {
                    search: "",
                    searchPlaceholder: "Search agents...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ agents",
                    infoEmpty: "No agents found",
                    infoFiltered: "(filtered from _MAX_ total agents)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        }
    });
</script>
@endpush