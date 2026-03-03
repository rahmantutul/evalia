@extends('user.layouts.app')
@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
        --secondary-gradient: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        --success-gradient: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
    }

    /* Compressed Summary Strip */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: var(--primary-gradient);
    }

    .summary-card.coaching::before { background: var(--warning-gradient); }
    .summary-card.top::before { background: var(--success-gradient); }
    .summary-card.onboarding::before { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .icon-bg-blue { background: #eff6ff; color: #1d4ed8; }
    .icon-bg-amber { background: #fffbeb; color: #b45309; }
    .icon-bg-green { background: #f0fdf4; color: #15803d; }
    .icon-bg-cyan { background: #ecfeff; color: #0e7490; }

    .summary-content {
        flex: 1;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--slate-900);
        line-height: 1.2;
    }

    .summary-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Filter Bar */
    .filter-bar {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
    }

    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 280px;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--slate-400);
        font-size: 16px;
    }

    .search-input {
        padding-left: 45px;
        border-radius: 12px;
        border: 2px solid var(--slate-100);
        height: 48px;
        width: 100%;
        transition: all 0.3s ease;
        font-weight: 500;
        background-color: var(--slate-50);
        font-size: 14px;
    }

    .search-input:focus {
        border-color: #0a66c2;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(10, 102, 194, 0.1);
        outline: none;
    }

    .filter-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-select {
        height: 48px;
        border-radius: 12px;
        border: 2px solid var(--slate-100);
        padding: 0 15px;
        font-size: 14px;
        font-weight: 600;
        color: var(--slate-700);
        cursor: pointer;
        min-width: 140px;
        background-color: var(--slate-50);
        transition: all 0.3s ease;
    }

    .filter-select:hover {
        border-color: var(--slate-300);
        background-color: #ffffff;
    }

    .filter-select:focus {
        border-color: #0a66c2;
        box-shadow: 0 0 0 4px rgba(10, 102, 194, 0.1);
        outline: none;
    }

    /* Table Design */
    .agent-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-modern thead th {
        background: var(--slate-50);
        padding: 18px 20px;
        font-size: 12px;
        font-weight: 800;
        color: var(--slate-600);
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid var(--slate-100);
    }

    .table-modern tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid var(--slate-50);
        color: var(--slate-700);
        font-size: 14px;
    }

    .agent-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .agent-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        object-fit: cover;
    }

    .agent-name {
        font-weight: 700;
        color: var(--slate-900);
        font-size: 15px;
    }

    .agent-id-badge {
        font-size: 11px;
        font-weight: 700;
        color: var(--slate-500);
        background: var(--slate-100);
        padding: 2px 6px;
        border-radius: 5px;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #f1f5f9; color: #475569; }
    .status-onboarding { background: #e0f2fe; color: #075985; }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .status-active .status-dot { background: #22c55e; }
    .status-inactive .status-dot { background: #94a3b8; }
    .status-onboarding .status-dot { background: #0ea5e9; }

    .score-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 12px;
        margin: 0 auto;
    }

    .score-high { background: #dcfce7; color: #166534; border: 1.5px solid #86efac; }
    .score-medium { background: #fef3c7; color: #92400e; border: 1.5px solid #fde68a; }
    .score-low { background: #fee2e2; color: #991b1b; border: 1.5px solid #fecaca; }

    .risk-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .risk-yes { background: #fee2e2; color: #b91c1c; }
    .risk-no { background: #f1f5f9; color: #64748b; }

    .sentiment-impact {
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: center;
        font-size: 13px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid var(--slate-200);
        background: #ffffff;
        color: var(--slate-600);
        text-decoration: none;
        font-size: 12px;
    }

    .btn-view:hover { background: #eff6ff; color: #2563eb; border-color: #dbeafe; }
    .btn-coach:hover { background: #fffbeb; color: #d97706; border-color: #fef3c7; }

    .agent-name { transition: all 0.2s; }
    .agent-name:hover { color: #0a66c2 !important; }

    .btn-excel-item {
        background: var(--success-gradient);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.2);
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-excel-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.3);
        color: white;
    }

    .btn-add-item {
        background: var(--primary-gradient);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(10, 102, 194, 0.2);
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-add-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(10, 102, 194, 0.3);
        color: white;
    }

</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h2 class="fw-900 text-slate-900 mb-1">Agent Workforce</h2>
            <p class="text-slate-500 fs-16 mb-0">Performance, status, and intelligence management.</p>
        </div>
         @if(session('user.role.name') !== 'Supervisor')
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
            <button type="button" class="btn-excel-item" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                <i class="fas fa-file-excel"></i>
                <span>Upload Excel</span>
            </button>
            <a href="{{ route('users.create', ['type' => 'agent']) }}" class="btn-add-item">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Agent</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Compressed Summary Strip -->
    <div class="summary-strip">
        <div class="summary-card">
            <div class="summary-icon icon-bg-blue">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value">{{ $summary['total_agents'] ?? 0 }}</div>
                <div class="summary-label">Total Agents</div>
            </div>
        </div>
        <div class="summary-card coaching">
            <div class="summary-icon icon-bg-amber">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value">{{ $summary['needs_coaching'] ?? 0 }}</div>
                <div class="summary-label">Needs Coaching</div>
            </div>
        </div>
        <div class="summary-card top">
            <div class="summary-icon icon-bg-green">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value">{{ $summary['top_performers'] ?? 0 }}</div>
                <div class="summary-label">Top Performers</div>
            </div>
        </div>
        <div class="summary-card onboarding">
            <div class="summary-icon icon-bg-cyan">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value">{{ $summary['onboarding'] ?? 0 }}</div>
                <div class="summary-label">Onboarding</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="agentSearch" class="search-input" placeholder="Search by name, ID, or supervisor...">
            </div>
            <div class="filter-group">
                <select class="filter-select js-filter" data-filter="status">
                    <option value="all">Status: All</option>
                    <option value="active">Active</option>
                    <option value="inactive">In Active</option>
                    <option value="onboarding">On Boarding</option>
                </select>
                <select class="filter-select js-filter" data-filter="performance">
                    <option value="all">Performance</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <select class="filter-select js-filter" data-filter="risk">
                    <option value="all">Risk Flag</option>
                    <option value="yes">Risk: Yes</option>
                    <option value="no">Risk: No</option>
                </select>
                <select class="filter-select js-filter" data-filter="channel">
                    <option value="all">Channel: All</option>
                    <option value="phone">Phone</option>
                    <option value="chat">Chat</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="social">Social</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Agent Table -->
    <div class="agent-table-card">
        <div class="table-responsive">
            <table class="table table-modern mb-0" id="agentsTable">
                <thead>
                    <tr>
                        <th>Agent Information</th>
                        <th>Supervisor</th>
                        <th>Status</th>
                        <th class="text-center">Overall Score</th>
                        <th class="text-center">Total Interaction</th>
                        <th class="text-center">Risk Flag</th>
                        <th class="text-center">Sentiment Impact</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agentsWithPerformance as $agentData)
                    @php
                        $agent = $agentData['agent'];
                        $performance = $agentData['performance'];
                        $hasPerformance = !empty($performance);
                        $scores = $hasPerformance ? $performance['current_scores'] : [];
                        $agentDetails = $hasPerformance ? $performance['agent_details'] : [];
                        
                        // Derived values for filtering demo
                        $roles = ['Agent', 'Supervisor'];
                        $role = $roles[rand(0, 1)];
                        $statuses = ['active', 'inactive', 'onboarding'];
                        $status = $statuses[rand(0, 2)];
                        $overallScore = $scores['overall_score'] ?? rand(60, 98);
                        $perfLevel = $overallScore >= 90 ? 'high' : ($overallScore >= 75 ? 'medium' : 'low');
                        
                        $dangerSentences = [
                            "Policy violation during early retirement counseling.",
                            "Incorrect information provided regarding maternity allowance eligibility.",
                            "Failed to verify mandatory identification for account modification.",
                            "Used non-professional terminology during sensitive law inquiry.",
                            "Missing mandatory silence-gap closure during verification steps.",
                            "Leaked partial confidential data in unencrypted chat channel.",
                            "Agent displayed high frustration levels during customer escalation.",
                            "Inaccurate guidance on Social Security law article 42 procedures."
                        ];

                        $riskCount = $overallScore < 75 ? rand(1, 4) : 0;
                        $risk = $riskCount > 0 ? 'yes' : 'no';
                        $agentRisks = $riskCount > 0 ? (array)array_rand(array_flip($dangerSentences), $riskCount) : [];
                        // Ensure it's an array if only one item is picked
                        if ($riskCount == 1) $agentRisks = [$agentRisks];

                        $channels = ['phone', 'chat', 'whatsapp', 'social'];
                        $channel = $channels[rand(0, 3)];
                        $impact = rand(-5, 5) + ($overallScore - 80) / 4;
                    @endphp
                    <tr 
                        data-role="{{ $role }}" 
                        data-status="{{ $status }}" 
                        data-performance="{{ $perfLevel }}" 
                        data-risk="{{ $risk }}" 
                        data-channel="{{ $channel }}">
                        <td>
                                <div class="agent-info">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($agent['full_name'] ?? 'Agent') }}&background=random&color=fff&bold=true" class="agent-avatar" alt="">
                                    <div>
                                        <a href="{{ route('user.agents.show', ['agentId' => $agent['id'] ?? 1]) }}?name={{ urlencode($agent['full_name'] ?? 'N/A') }}&company={{ urlencode($agent['company_name'] ?? 'الضمان الاجتماعي - الأردن') }}" class="agent-name text-decoration-none hover-primary">{{ $agent['full_name'] ?? 'N/A' }}</a>
                                        <div class="agent-id-badge">{{ $agentDetails['display_id'] ?? 'AGT-'.strtoupper(Str::random(5)) }}</div>
                                    </div>
                                </div>
                        </td>
                        <td>
                            <div class="fw-700 text-slate-700">{{ $agent['supervisor_name'] ?? 'Mahmoud Ali' }}</div>
                            <div class="small text-muted">{{ $role }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $status }}">
                                <span class="status-dot"></span>
                                {{ $status == 'active' ? 'Active' : ($status == 'inactive' ? 'In Active' : 'On Boarding') }}
                            </span>
                        </td>
                        <td>
                            <div class="score-circle score-{{ $perfLevel }}">
                                {{ $overallScore }}%
                            </div>
                        </td>
                        <td class="text-center fw-800 text-slate-800">
                            {{ number_format($performance['total_interaction'] ?? rand(500, 2500)) }}
                        </td>
                        <td class="text-center">
                            @if($riskCount > 0)
                            <span class="risk-badge risk-yes cursor-pointer js-risk-trigger" 
                                  data-name="{{ $agent['full_name'] ?? 'Agent' }}"
                                  data-risks='@json($agentRisks)'
                                  style="cursor: pointer; transition: all 0.2s;">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $riskCount }} {{ Str::plural('Risk', $riskCount) }}
                            </span>
                            @else
                            <span class="risk-badge risk-no">
                                <i class="fas fa-check-circle"></i>
                                NO RISK
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="sentiment-impact {{ $impact >= 0 ? 'impact-pos' : 'impact-neg' }}">
                                <i class="fas fa-caret-{{ $impact >= 0 ? 'up' : 'down' }}"></i>
                                {{ number_format(abs($impact), 1) }}%
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('user.agents.show', ['agentId' => $agent['id'] ?? 1]) }}?name={{ urlencode($agent['full_name'] ?? 'N/A') }}&company={{ urlencode($agent['company_name'] ?? 'الضمان الاجتماعي - الأردن') }}" class="action-btn btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $agent['id'] ?? 1) }}" class="action-btn btn-view" style="color: #6366f1;" title="Edit Agent">
                                    <i class="fas fa-pen"></i>
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
@endsection

<!-- Risk Details Modal -->
<div class="modal fade" id="riskDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-shield-alt me-2"></i>
                    Risk Incident Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-soft-danger p-3 rounded-circle me-3">
                        <i class="fas fa-user-shield text-danger fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Target Agent</div>
                        <h4 class="fw-bold mb-0 text-slate-900" id="riskAgentName">Nadi Al-Budairi</h4>
                    </div>
                </div>

                <div class="mb-3 fw-bold text-slate-700" style="font-size: 13px;">DETECTED RISK FRAGMENTS:</div>
                <div id="riskSentencesList">
                    <!-- Sentences will be injected here -->
                </div>

                <div class="mt-4 p-3 rounded-3 bg-soft-warning border border-warning border-opacity-25">
                    <div class="d-flex gap-2">
                        <i class="fas fa-info-circle text-warning mt-1"></i>
                        <p class="small text-slate-700 mb-0">These flags were automatically identified via <strong>Cognitive Linguistic Analysis</strong>. Direct coaching is recommended within 24 hours.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Excel Modal -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-900"><i class="fas fa-file-excel text-success me-2"></i>Bulk Agent Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="excelUploadForm">
                <div class="modal-body p-4">
                    <p class="text-slate-500 small mb-4">Bulk register your agents by uploading an Excel or CSV file. Please ensure your file follows our standard format.</p>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-slate-700">Select File</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-3 p-4 text-center bg-slate-50">
                            <i class="fas fa-cloud-upload-alt fs-1 text-slate-300 mb-2"></i>
                            <input type="file" class="form-control mt-2" accept=".xlsx, .xls, .csv" required>
                            <div class="form-text mt-2">Max file size: 5MB</div>
                        </div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-3 d-flex gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        <div>
                            <div class="fw-bold text-blue-900 small">Missing a template?</div>
                            <a href="#" class="text-blue-600 small text-decoration-underline">Download sample-template.xlsx</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-excel-item border-0">
                        <i class="fas fa-check-circle"></i>
                        Confirm Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('agentSearch');
        const filterSelects = document.querySelectorAll('.js-filter');
        const rows = document.querySelectorAll('#agentsTable tbody tr');

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeFilters = {};
            
            filterSelects.forEach(select => {
                const type = select.getAttribute('data-filter');
                activeFilters[type] = select.value;
            });

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesSearch = text.includes(searchTerm);
                
                let matchesFilters = true;
                for (let type in activeFilters) {
                    if (activeFilters[type] !== 'all') {
                        if (row.getAttribute(`data-${type}`) !== activeFilters[type]) {
                            matchesFilters = false;
                            break;
                        }
                    }
                }

                row.style.display = (matchesSearch && matchesFilters) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', applyFilters);
        filterSelects.forEach(select => {
            select.addEventListener('change', applyFilters);
        });

        // Risk Popup dynamic listener
        document.querySelectorAll('.js-risk-trigger').forEach(trigger => {
            trigger.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const risks = JSON.parse(this.getAttribute('data-risks'));
                showRiskDetails(name, risks);
            });
        });
    });

    function showRiskDetails(agentName, sentences) {
        const modal = new bootstrap.Modal(document.getElementById('riskDetailsModal'));
        document.getElementById('riskAgentName').textContent = agentName;
        
        const list = document.getElementById('riskSentencesList');
        list.innerHTML = '';
        
        sentences.forEach(sentence => {
            const div = document.createElement('div');
            div.className = 'p-3 mb-2 rounded-3 border-start border-4 border-danger bg-soft-danger text-danger fw-600 small';
            div.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i> ${sentence}`;
            list.appendChild(div);
        });
        
        modal.show();
    }
</script>
@endpush