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

    /* Summary Strip */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary-gradient);
    }

    .summary-card.coaching::before { background: var(--warning-gradient); }
    .summary-card.top::before { background: var(--success-gradient); }
    .summary-card.onboarding::before { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }

    .summary-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .summary-card:hover .summary-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .icon-bg-blue { background: #eff6ff; color: #1d4ed8; }
    .icon-bg-amber { background: #fffbeb; color: #b45309; }
    .icon-bg-green { background: #f0fdf4; color: #15803d; }
    .icon-bg-cyan { background: #ecfeff; color: #0e7490; }

    .summary-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--slate-900);
        margin-bottom: 4px;
        line-height: 1;
    }

    .summary-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Filter Bar */
    .filter-bar {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 320px;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--slate-400);
        font-size: 18px;
    }

    .search-input {
        padding-left: 52px;
        border-radius: 14px;
        border: 2px solid var(--slate-100);
        height: 54px;
        width: 100%;
        transition: all 0.3s ease;
        font-weight: 500;
        background-color: var(--slate-50);
    }

    .search-input:focus {
        border-color: #0a66c2;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(10, 102, 194, 0.1);
        outline: none;
    }

    .filter-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-select {
        height: 54px;
        border-radius: 14px;
        border: 2px solid var(--slate-100);
        padding: 0 20px;
        font-size: 15px;
        font-weight: 600;
        color: var(--slate-700);
        cursor: pointer;
        min-width: 160px;
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
        border-radius: 20px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-modern thead th {
        background: var(--slate-50);
        padding: 20px 24px;
        font-size: 13px;
        font-weight: 800;
        color: var(--slate-600);
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid var(--slate-100);
    }

    .table-modern tbody td {
        padding: 22px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--slate-50);
        color: var(--slate-700);
        font-size: 15px;
    }

    .table-modern tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #fcfdfe;
    }

    .agent-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .agent-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .agent-name {
        font-weight: 700;
        color: var(--slate-900);
        font-size: 16px;
    }

    .agent-id-badge {
        font-size: 12px;
        font-weight: 700;
        color: var(--slate-500);
        background: var(--slate-100);
        padding: 2px 8px;
        border-radius: 6px;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #f1f5f9; color: #475569; }
    .status-onboarding { background: #e0f2fe; color: #075985; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .status-active .status-dot { background: #22c55e; box-shadow: 0 0 8px #22c55e; }
    .status-inactive .status-dot { background: #94a3b8; }
    .status-onboarding .status-dot { background: #0ea5e9; box-shadow: 0 0 8px #0ea5e9; }

    .score-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        margin: 0 auto;
    }

    .score-high { background: #dcfce7; color: #166534; border: 2px solid #86efac; }
    .score-medium { background: #fef3c7; color: #92400e; border: 2px solid #fde68a; }
    .score-low { background: #fee2e2; color: #991b1b; border: 2px solid #fecaca; }

    .risk-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .risk-yes { background: #fee2e2; color: #b91c1c; animation: pulse 2s infinite; }
    .risk-no { background: #f1f5f9; color: #64748b; }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .sentiment-impact {
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .impact-pos { color: #22c55e; }
    .impact-neg { color: #ef4444; }

    .action-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid var(--slate-200);
        background: #ffffff;
        color: var(--slate-600);
        text-decoration: none;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .btn-view:hover { background: #eff6ff; color: #2563eb; border-color: #dbeafe; }
    .btn-coach:hover { background: #fffbeb; color: #d97706; border-color: #fef3c7; }

    .btn-add-item {
        background: var(--primary-gradient);
        color: white;
        padding: 14px 28px;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(10, 102, 194, 0.3);
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-add-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 25px -5px rgba(10, 102, 194, 0.4);
        color: white;
    }

</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row align-items-center mb-5">
        <div class="col-md-7">
            <h1 class="fw-900 text-slate-900 mb-2">Agent Workforce</h1>
            <p class="text-slate-500 fs-18 mb-0">Manage performance, status, and intelligence across your entire agent base.</p>
        </div>
        <div class="col-md-5 text-md-end mt-4 mt-md-0">
            <a href="{{ route('users.create') }}" class="btn-add-item">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Agent</span>
            </a>
        </div>
    </div>

    <!-- Summary Strip -->
    <div class="summary-strip">
        <div class="summary-card">
            <div class="summary-icon icon-bg-blue">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="summary-value">124</div>
            <div class="summary-label">Total Agents</div>
        </div>
        <div class="summary-card coaching">
            <div class="summary-icon icon-bg-amber">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="summary-value">18</div>
            <div class="summary-label">Need Coaching</div>
        </div>
        <div class="summary-card top">
            <div class="summary-icon icon-bg-green">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="summary-value">42</div>
            <div class="summary-label">Top Performance</div>
        </div>
        <div class="summary-card onboarding">
            <div class="summary-icon icon-bg-cyan">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="summary-value">12</div>
            <div class="summary-label">Onboarding Now</div>
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
                <select class="filter-select">
                    <option value="">Role: All</option>
                    <option value="agent">Agent</option>
                    <option value="supervisor">Supervisor</option>
                </select>
                <select class="filter-select">
                    <option value="">Status: All</option>
                    <option value="active">Active</option>
                    <option value="inactive">In Active</option>
                    <option value="onboarding">On Boarding</option>
                </select>
                <select class="filter-select">
                    <option value="">Performance</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <select class="filter-select">
                    <option value="">Risk Flag</option>
                    <option value="yes">Risk: Yes</option>
                    <option value="no">Risk: No</option>
                </select>
                <select class="filter-select">
                    <option value="">Channel: All</option>
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
                    @php
                        $agentsData = [
                            ['id' => 'AGT-2024-001', 'name' => 'نادي البديري', 'supervisor' => 'محمود علي', 'status' => 'active', 'score' => 92, 'volume' => 1420, 'risk' => 0, 'impact' => 4.5, 'avatar' => '1'],
                            ['id' => 'AGT-2024-002', 'name' => 'سارة الخطيب', 'supervisor' => 'محمود علي', 'status' => 'active', 'score' => 88, 'volume' => 985, 'risk' => 0, 'impact' => 2.1, 'avatar' => '2'],
                            ['id' => 'AGT-2024-003', 'name' => 'محمود المصري', 'supervisor' => 'ليلى حسن', 'status' => 'onboarding', 'score' => 74, 'volume' => 124, 'risk' => 0, 'impact' => 1.2, 'avatar' => '3'],
                            ['id' => 'AGT-2024-004', 'name' => 'ليلى حسن', 'supervisor' => 'أحمد المناصير', 'status' => 'active', 'score' => 65, 'volume' => 2104, 'risk' => 1, 'impact' => -3.8, 'avatar' => '4'],
                            ['id' => 'AGT-2024-005', 'name' => 'أحمد المناصير', 'supervisor' => 'ليلى حسن', 'status' => 'inactive', 'score' => 82, 'volume' => 0, 'risk' => 0, 'impact' => 0, 'avatar' => '5'],
                            ['id' => 'AGT-2024-006', 'name' => 'فرح الزعبي', 'supervisor' => 'محمود علي', 'status' => 'active', 'score' => 95, 'volume' => 867, 'risk' => 0, 'impact' => 5.4, 'avatar' => '6'],
                            ['id' => 'AGT-2024-007', 'name' => 'يزن التل', 'supervisor' => 'أحمد المناصير', 'status' => 'active', 'score' => 71, 'volume' => 1560, 'risk' => 1, 'impact' => -2.2, 'avatar' => '7'],
                            ['id' => 'AGT-2024-008', 'name' => 'دينا عبيد', 'supervisor' => 'محمود علي', 'status' => 'active', 'score' => 84, 'volume' => 1120, 'risk' => 0, 'impact' => 1.8, 'avatar' => '8'],
                        ];
                    @endphp

                    @foreach($agentsData as $agent)
                    <tr>
                        <td>
                            <div class="agent-info">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($agent['name']) }}&background=random&color=fff&bold=true" class="agent-avatar" alt="">
                                <div>
                                    <div class="agent-name">{{ $agent['name'] }}</div>
                                    <div class="agent-id-badge">{{ $agent['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-700 text-slate-700">{{ $agent['supervisor'] }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $agent['status'] }}">
                                <span class="status-dot"></span>
                                {{ $agent['status'] == 'active' ? 'Active' : ($agent['status'] == 'inactive' ? 'In Active' : 'On Boarding') }}
                            </span>
                        </td>
                        <td>
                            <div class="score-circle score-{{ $agent['score'] >= 90 ? 'high' : ($agent['score'] >= 75 ? 'medium' : 'low') }}">
                                {{ $agent['score'] }}%
                            </div>
                        </td>
                        <td class="text-center fw-800 text-slate-800">
                            {{ number_format($agent['volume']) }}
                        </td>
                        <td class="text-center">
                            <span class="risk-badge risk-{{ $agent['risk'] ? 'yes' : 'no' }}">
                                <i class="fas fa-{{ $agent['risk'] ? 'exclamation-triangle' : 'check-circle' }}"></i>
                                {{ $agent['risk'] ? 'HIGH RISK' : 'NO RISK' }}
                            </span>
                        </td>
                        <td>
                            <div class="sentiment-impact {{ $agent['impact'] >= 0 ? 'impact-pos' : 'impact-neg' }}">
                                <i class="fas fa-caret-{{ $agent['impact'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($agent['impact']) }}%
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('user.agents.show', 1) }}" class="action-btn btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="action-btn btn-coach" title="Assign Coaching">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('agentSearch');
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#agentsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endpush