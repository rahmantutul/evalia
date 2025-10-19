@extends('user.layouts.app')
@push('styles')
<style>
    /* Expandable row styles */
    .expandable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .expandable-row:hover {
        background-color: #fafbfc !important;
    }

    .expandable-row.expanded {
        background-color: #f8fafc !important;
    }

    .expand-icon {
        transition: transform 0.2s ease;
        font-size: 11px;
        color: #6b7280;
    }

    .expandable-row.expanded .expand-icon {
        transform: rotate(90deg);
        color: #3b82f6;
    }

    .performance-details {
        display: none;
        background: #fafbfc;
    }

    .expandable-row.expanded + .performance-details {
        display: table-row;
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .performance-metrics {
        padding: 20px;
    }

    .metric-card {
        background: white;
        border-radius: 8px;
        padding: 16px;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: #1f2937;
    }

    .metric-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
    }

    .progress {
        height: 6px;
        border-radius: 3px;
        background-color: #f3f4f6;
        margin-top: 8px;
    }

    .progress-bar {
        border-radius: 3px;
    }

    /* Compact Statistics */
    .stat-item {
        padding: 12px 0;
        border-right: 1px solid #f1f5f9;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        background: #f8fafc;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* Professional table styling */
    .table-professional {
        border: 1px solid #f1f5f9;
    }

    .table-professional thead th {
        background: #f8fafc;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px 16px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: none;
    }

    .table-professional tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #4b5563;
    }

    .table-professional tbody tr:last-child td {
        border-bottom: none;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        color: #6b7280;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        transition: all 0.15s ease;
        text-decoration: none;
        font-size: 13px;
    }

    .btn-icon:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
        transform: none;
        box-shadow: 0 1px 2px rgba(59, 130, 246, 0.2);
    }

    /* Performance table */
    .performance-table {
        background: white;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
    }

    .performance-table th {
        background: #f8fafc;
        font-weight: 600;
        color: #374151;
        padding: 10px 12px;
        font-size: 0.8rem;
    }

    .performance-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }

    .performance-table tr:last-child td {
        border-bottom: none;
    }

    /* Loading animation */
    .loading-spinner {
        border: 2px solid #f3f4f6;
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
        margin: 0 auto 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Badge styles */
    .badge-light {
        background: #f8fafc;
        color: #4b5563;
        border: 1px solid #e5e7eb;
        font-weight: 500;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .stat-item {
            border-right: none;
            border-bottom: 1px solid #f1f5f9;
            padding: 10px 0;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .performance-metrics {
            padding: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Compact Statistics Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-white">
                <div class="card-body py-3">
                    <div class="row g-0">
                        <!-- Total Agents -->
                        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 stat-item">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon text-blue-600 me-3">
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
                            <div class="d-flex align-items-center">
                                <div class="stat-icon text-green-600 me-3">
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
                            <div class="d-flex align-items-center">
                                <div class="stat-icon text-amber-500 me-3">
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
                            <div class="d-flex align-items-center">
                                <div class="stat-icon text-cyan-600 me-3">
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
                            <div class="d-flex align-items-center">
                                <div class="stat-icon text-emerald-600 me-3">
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
                            <div class="d-flex align-items-center">
                                <div class="stat-icon text-slate-600 me-3">
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
            <div class="card border-0 bg-white">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-slate-800 fw-semibold">
                            Agent List
                        </h5>
                        <a href="{{ route('user.agents.dashboard') }}" class="btn btn-light btn-sm border">
                            <i class="fas fa-chart-bar me-1"></i>Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 mb-3" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif 

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <table class="table table-professional mb-0" id="agentsTable">
                            <thead>
                                <tr>
                                    <th width="40"></th>
                                    <th>Agent ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Company</th>
                                    <th width="100" class="text-center">Actions</th>
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
                                        <span class="badge badge-light">
                                            {{ $agent['agent_id_display'] }}
                                        </span>
                                    </td>
                                    <td class="fw-medium text-slate-800">{{ $agent['name'] }}</td>
                                    <td class="text-slate-600">{{ $agent['email'] }}</td>
                                    <td class="text-slate-600">
                                        {{ $agent['phone'] ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-light">
                                            {{ $agent['company'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('user.agents.show', $agent['id']) }}" class="btn btn-icon" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('user.agents.performance', $agent['id']) }}" class="btn btn-icon" title="Performance">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Performance Details Row -->
                                <tr class="performance-details">
                                    <td colspan="7">
                                        <div class="performance-metrics" id="performance-{{ $agent['id'] }}">
                                            <div class="text-center py-4">
                                                <div class="loading-spinner"></div>
                                                <p class="mt-2 text-slate-500 mb-0 text-sm">Loading performance data...</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-slate-500">
                                            <i class="fas fa-users fa-2x mb-3 opacity-50"></i>
                                            <p class="mb-0">No agents found</p>
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
        // Simple counter animation
        function animateCounter(elementId, finalValue, suffix = '', isDecimal = false) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            let current = 0;
            const increment = finalValue / 30;
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    current = finalValue;
                    clearInterval(timer);
                }
                
                if (isDecimal) {
                    element.textContent = current.toFixed(1) + suffix;
                } else if (suffix === '%') {
                    element.textContent = Math.floor(current) + suffix;
                } else {
                    element.textContent = Math.floor(current);
                }
            }, 40);
        }

        // Get actual values from the server
        const stats = {
            totalAgents: {{ $summary['total_agents'] ?? 0 }},
            activeAgents: {{ $summary['active_agents'] ?? 0 }},
            averageRating: {{ $summary['avg_rating'] ?? 0 }},
            totalCalls: {{ $summary['total_calls'] ?? 0 }},
            successRate: {{ $summary['success_rate'] ?? 0 }},
            avgResponseTime: {{ $summary['avg_response_time'] ?? 0 }}
        };

        // Start animations
        animateCounter('totalAgents', stats.totalAgents);
        animateCounter('activeAgents', stats.activeAgents);
        animateCounter('averageRating', parseFloat(stats.averageRating), '', true);
        animateCounter('totalCalls', stats.totalCalls);
        animateCounter('successRate', stats.successRate, '%');
        animateCounter('avgResponseTime', stats.avgResponseTime, 's');

        // Expandable row functionality
        const expandableRows = document.querySelectorAll('.expandable-row');
        
        expandableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('.btn-icon')) {
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
                
                // Load performance data if expanding
                if (!isExpanded) {
                    loadPerformanceData(agentId);
                }
            });
        });

        // Load performance data via AJAX
        function loadPerformanceData(agentId) {
            const performanceContainer = document.getElementById(`performance-${agentId}`);
            
            const url = "{{ route('agents.performance.data', ['agentId' => ':agentId']) }}".replace(':agentId', agentId);
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        performanceContainer.innerHTML = createPerformanceHTML(data.performance);
                    } else {
                        performanceContainer.innerHTML = `
                            <div class="text-center py-4 text-slate-500">
                                <i class="fas fa-exclamation-triangle fa-lg mb-3 opacity-50"></i>
                                <p class="mb-0 text-sm">Failed to load performance data</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    performanceContainer.innerHTML = `
                        <div class="text-center py-4 text-slate-500">
                            <i class="fas fa-exclamation-triangle fa-lg mb-3 opacity-50"></i>
                            <p class="mb-0 text-sm">Error loading data</p>
                        </div>
                    `;
                });
        }

        // Create performance HTML
        function createPerformanceHTML(performance) {
            const current = performance.current_scores || {};
            const history = performance.performance_history || [];
            
            return `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3 text-slate-800 fw-semibold text-sm">Current Performance</h6>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-lg-6">
                        <div class="metric-card">
                            <div class="metric-value text-blue-600">${current.overall_score ? current.overall_score.toFixed(1) : '0.0'}</div>
                            <div class="metric-label">Overall Score</div>
                            <div class="progress">
                                <div class="progress-bar bg-blue-500" style="width: ${current.overall_score || 0}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="metric-card">
                            <div class="metric-value text-green-600">${current.answer_accuracy || 0}%</div>
                            <div class="metric-label">Answer Accuracy</div>
                            <div class="progress">
                                <div class="progress-bar bg-green-500" style="width: ${current.answer_accuracy || 0}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="metric-card">
                            <div class="metric-value text-cyan-600">${current.response_speed || 0}%</div>
                            <div class="metric-label">Response Speed</div>
                            <div class="progress">
                                <div class="progress-bar bg-cyan-500" style="width: ${current.response_speed || 0}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="metric-card">
                            <div class="metric-value text-amber-500">${current.customer_satisfaction || 0}%</div>
                            <div class="metric-label">Customer Satisfaction</div>
                            <div class="progress">
                                <div class="progress-bar bg-amber-500" style="width: ${current.customer_satisfaction || 0}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                ${history.length > 0 ? `
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-3 text-slate-800 fw-semibold text-sm">Recent Performance</h6>
                        <div class="performance-table">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Overall</th>
                                        <th>Accuracy</th>
                                        <th>Speed</th>
                                        <th>Satisfaction</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${history.slice(0, 3).map(item => `
                                        <tr>
                                            <td class="fw-medium text-slate-700">${item.period || 'N/A'}</td>
                                            <td><span class="badge badge-light">${item.overall_score ? item.overall_score.toFixed(1) : '0.0'}</span></td>
                                            <td class="text-slate-600">${item.answer_accuracy || 0}%</td>
                                            <td class="text-slate-600">${item.response_speed || 0}%</td>
                                            <td class="text-slate-600">${item.customer_satisfaction || 0}%</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                ` : '<div class="text-center text-slate-500 text-sm py-3">No performance history available</div>'}
            `;
        }

        // Initialize DataTable with minimal configuration
        if ($.fn.DataTable) {
            $('#agentsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[1, 'asc']],
                language: {
                    search: "",
                    searchPlaceholder: "Search agents...",
                    lengthMenu: "_MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ agents",
                    infoEmpty: "No agents found",
                    infoFiltered: "(filtered from _MAX_ total agents)"
                }
            });
        }
    });
</script>
@endpush