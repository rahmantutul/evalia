@extends('agent.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-dashboard.css') }}">
<style>
    /* Explicitly matching user dashboard styles */
    .dashboard-card {
        border-radius: 12px;
        border: none;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    .dashboard-card.clickable {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .dashboard-card.clickable:hover {
        transform: translateY(-2px);
    }
    .agent-card.clickable {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .agent-card.clickable:hover {
        background-color: rgba(220, 53, 69, 0.08);
        transform: scale(1.01);
    }
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .metric-value {
        font-size: 1.75rem;
        letter-spacing: -0.02em;
    }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1) !important; }
    
    .text-primary { color: #0d6efd !important; }
    .text-success { color: #198754 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-info { color: #0dcaf0 !important; }

    .chart-container {
        position: relative;
        height: 300px;
        padding: 1.25rem;
    }
    
    .agent-card {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        border: 1px solid #f1f1f1;
    }
    .agent-card.focus-needed {
        background-color: rgba(220, 53, 69, 0.05);
        border-left: 4px solid #dc3545;
    }
    .agent-card.good-work {
        background-color: rgba(25, 135, 84, 0.05);
        border-left: 4px solid #198754;
    }
    .agent-card.info-tip {
        background-color: rgba(13, 110, 253, 0.05);
        border-left: 4px solid #0d6efd;
    }

    .interaction-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 50rem;
        font-size: 0.75rem;
    }

    .score-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6c757d;
        border-top: none;
        padding: 12px 15px;
    }
    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    .page-header h4 {
        color: #1e293b;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Agent Performance Dashboard</h4>
                <p class="text-muted mb-0">Overview of your quality metrics and improvement areas</p>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Last 7 Days
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="periodDropdown">
                        <li><a class="dropdown-item period-filter active" href="#" data-period="7">Last 7 Days</a></li>
                        <li><a class="dropdown-item period-filter" href="#" data-period="30">Last 30 Days</a></li>
                        <li><a class="dropdown-item period-filter" href="#" data-period="90">Last Quarter</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                    <i class="fas fa-download me-1"></i> My Report
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-primary border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold uppercase">Overall Score</p>
                            <h3 class="metric-value mb-1 fw-bold text-primary" id="overallScore">8.7</h3>
                            <small class="text-success fw-bold" id="scoreTrend"><i class="fas fa-arrow-up"></i> +0.3 vs prev</small>
                        </div>
                        <div class="icon-circle bg-soft-primary">
                            <i class="fas fa-star text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card clickable h-100 shadow-soft border-bottom border-danger border-3" onclick="showComplianceAlerts()">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold uppercase">Compliance Alert</p>
                            <h3 class="metric-value mb-1 fw-bold text-danger" id="complianceCount">3</h3>
                            <small class="text-muted">Cases Need Attention</small>
                        </div>
                        <div class="icon-circle bg-soft-danger">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-success border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold uppercase">Sentiment Avg</p>
                            <h3 class="metric-value mb-1 fw-bold text-success" id="sentimentScore">4.2</h3>
                            <small class="text-muted">Positive Performance</small>
                        </div>
                        <div class="icon-circle bg-soft-success">
                            <i class="fas fa-smile text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft border-bottom border-info border-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold uppercase">Calls Volume</p>
                            <h3 class="metric-value mb-1 fw-bold text-info" id="callsCount">87</h3>
                            <small class="text-muted" id="callsTime">Today: 12 handled</small>
                        </div>
                        <div class="icon-circle bg-soft-info">
                            <i class="fas fa-phone text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Performance Score Trend</h6>
                        <span class="badge bg-soft-success text-success" id="trendIndicator">
                            <i class="fas fa-arrow-up me-1"></i> Improving
                        </span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Today's Focus Panel</h6>
                </div>
                <div class="card-body p-3 pt-0">
                    <div class="agent-card info-tip">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 small">Reduce Call Time</h6>
                            <i class="fas fa-clock text-primary"></i>
                        </div>
                        <p class="text-muted small mb-0">Current avg is 8.5m. Target is under 7m to improve throughput.</p>
                    </div>

                    <div class="agent-card focus-needed clickable" onclick="showComplianceAlerts()">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 small">Compliance Note</h6>
                            <i class="fas fa-exclamation-circle text-danger"></i>
                        </div>
                        <p class="text-muted small mb-0">3 calls missing closing script. Review evaluation notes ASAP.</p>
                    </div>

                    <div class="agent-card good-work">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 small">Sentiment Win</h6>
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <p class="text-muted small mb-0">Excellent rapport building today. 90% positive sentiment rate.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactions Table -->
    <div class="row g-4">
        <div class="col-12">
            <div class="dashboard-card shadow-soft h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Recent Interactions & Evaluations</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="interactionsTable">
                            <thead>
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Customer ID</th>
                                    <th>Type</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated dynamically -->
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
    let currentPeriod = 7;
    let trendChart = null;

    const dataGenerators = {
        getKpis: (period) => {
            const factor = period / 7;
            return {
                score: (8.5 + Math.random() * 0.5).toFixed(1),
                trend: (0.2 + Math.random() * 0.3).toFixed(1),
                compliance: Math.floor(3 * factor),
                sentiment: (4.0 + Math.random() * 0.5).toFixed(1),
                calls: Math.floor(87 * factor),
                today: 12
            };
        },
        getTrendData: (period) => {
            const labels = [];
            const data = [];
            const now = new Date();
            for (let i = period - 1; i >= 0; i--) {
                const d = new Date();
                d.setDate(now.getDate() - i);
                if (period <= 7) labels.push(d.toLocaleDateString('en-US', { weekday: 'short' }));
                else labels.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                data.push(80 + Math.random() * 15);
            }
            return { labels, data };
        },
        getInteractions: () => {
            return [
                { 
                    id: 'CUS-9821', type: 'Call', duration: '5m 45s', status: 'Evaluated', score: 92, time: '10:15 AM', date: 'Today',
                    summary: {
                        strengths: ['Excellent personalized greeting', 'Clear explanation of technical terms', 'Properly verified identity'],
                        improvements: ['None observed'],
                        tip: 'Keep up the great energy, it really reflects in the sentiment score!'
                    }
                },
                { 
                    id: 'CUS-4432', type: 'Chat', duration: '12m 10s', status: 'Evaluated', score: 89, time: '09:40 AM', date: 'Today',
                    summary: {
                        strengths: ['Fast response time', 'Accurate product knowledge', 'Professional sign-off'],
                        improvements: ['Slightly informal opening'],
                        tip: 'Try using the standard brand greeting for chats to maintain consistency.'
                    }
                },
                { 
                    id: 'CUS-1120', type: 'Call', duration: '7m 32s', status: 'Evaluated', score: 85, time: '02:15 PM', date: 'Yesterday',
                    summary: {
                        strengths: ['Professional tone throughout', 'Effective use of active listening'],
                        improvements: ['Minor verification steps missed', 'Closing script was slightly rushed'],
                        tip: 'Take an extra 10 seconds at the end to ensure the customer feels fully supported.'
                    }
                },
                { 
                    id: 'CUS-3301', type: 'Call', duration: '4m 15s', status: 'Evaluated', score: 72, time: '11:20 AM', date: 'Yesterday',
                    summary: {
                        strengths: ['Efficient handle time', 'Accurate information provided'],
                        improvements: ['Lacked empathy during complaint phase', 'Failed to use customer name during interaction'],
                        tip: 'Try to mirror the customer\'s pace more effectively to build better rapport.'
                    }
                },
                { 
                    id: 'CUS-5564', type: 'Chat', duration: '9m 50s', status: 'Evaluated', score: 88, time: '04:10 PM', date: 'Feb 8',
                    summary: {
                        strengths: ['Fast response times', 'Correct use of canned responses'],
                        improvements: ['Grammar could be more polished'],
                        tip: 'Double check the spelling of customer-specific technical terms.'
                    }
                }
            ];
        }
    };

    function updateDashboard() {
        const kpis = dataGenerators.getKpis(currentPeriod);
        document.getElementById('overallScore').textContent = kpis.score;
        document.getElementById('scoreTrend').innerHTML = `<i class="fas fa-arrow-up"></i> +${kpis.trend} vs prev`;
        document.getElementById('complianceCount').textContent = kpis.compliance;
        document.getElementById('sentimentScore').textContent = kpis.sentiment;
        document.getElementById('callsCount').textContent = kpis.calls;
        document.getElementById('callsTime').textContent = `Today: ${kpis.today} handled`;

        updateChart();
        updateTable();
    }

    function updateChart() {
        const trend = dataGenerators.getTrendData(currentPeriod);
        const ctx = document.getElementById('trendChart').getContext('2d');
        if (trendChart) trendChart.destroy();
        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [{
                    label: 'Score',
                    data: trend.data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.05)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    intersect: false,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { 
                        min: 0, 
                        max: 100, 
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { stepSize: 20, font: { size: 10 } }
                    }
                }
            }
        });
    }

    let currentInteractions = [];

    function updateTable() {
        currentInteractions = dataGenerators.getInteractions();
        const tbody = document.querySelector('#interactionsTable tbody');
        tbody.innerHTML = '';
        currentInteractions.forEach((item, index) => {
            const scoreClass = !item.score ? '' : 
                             item.score >= 90 ? 'bg-soft-success text-success' : 
                             item.score >= 80 ? 'bg-soft-primary text-primary' : 
                             item.score >= 70 ? 'bg-soft-warning text-warning' : 
                             'bg-soft-danger text-danger';
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-4">
                    <div class="fw-bold small text-dark">${item.date}</div>
                    <div class="text-muted extra-small">${item.time}</div>
                </td>
                <td class="fw-bold text-dark fs-13">${item.id}</td>
                <td>
                    <span class="small font-500">
                        <i class="fas ${item.type === 'Call' ? 'fa-phone-alt' : 'fa-comment-alt'} me-1 text-muted"></i>
                        ${item.type}
                    </span>
                </td>
                <td class="small text-muted">${item.duration}</td>
                <td><span class="interaction-badge ${item.status === 'Evaluated' ? 'badge-evaluated' : 'badge-pending'}">${item.status}</span></td>
                <td>
                    ${item.score ? `<div class="score-circle ${scoreClass}">${item.score}</div>` : '<span class="text-muted">—</span>'}
                </td>
                <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-white border px-2 shadow-xs" onclick="viewEvaluatorNote(${index})">
                        <i class="fas fa-external-link-alt text-muted fs-11"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function viewEvaluatorNote(index) {
        const item = currentInteractions[index];
        if (!item || !item.summary) return;

        Swal.fire({
            title: `<h5 class="fw-bold mb-0 text-primary">Call Evaluation: ${item.id}</h5>`,
            html: `
                <div class="text-start mt-3">
                    <p class="text-muted small mb-4">Hello! Here is the detailed breakdown of your performance for this interaction. Great to see your commitment to quality!</p>
                    
                    <div class="p-3 mb-4 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block text-muted extra-small uppercase fw-bold">Performance Score</span>
                            <h4 class="mb-0 fw-bold ${item.score >= 85 ? 'text-success' : 'text-warning'}">${item.score}/100</h4>
                        </div>
                        <div class="score-circle ${item.score >= 85 ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning'}" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="fas ${item.score >= 85 ? 'fa-check' : 'fa-info'}"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold small text-success d-flex align-items-center mb-2">
                             <span class="icon-circle bg-soft-success me-2" style="width: 24px; height: 24px; font-size: 10px;"><i class="fas fa-thumbs-up"></i></span>
                             What you did great:
                        </h6>
                        <div class="ps-4">
                            ${item.summary.strengths.map(s => `<div class="small mb-1 text-dark">• ${s}</div>`).join('')}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold small text-danger d-flex align-items-center mb-2">
                             <span class="icon-circle bg-soft-danger me-2" style="width: 24px; height: 24px; font-size: 10px;"><i class="fas fa-chart-line"></i></span>
                             How to improve:
                        </h6>
                        <div class="ps-4">
                            ${item.summary.improvements.map(i => `<div class="small mb-1 text-dark">• ${i}</div>`).join('')}
                        </div>
                    </div>

                    <div class="p-3 border-start border-4 border-info bg-soft-info rounded">
                        <h6 class="fw-bold small text-info mb-1"><i class="fas fa-lightbulb me-1"></i> Pro-Tip for Success:</h6>
                        <p class="mb-0 small text-dark-50">${item.summary.tip}</p>
                    </div>
                </div>
            `,
            confirmButtonText: 'I understand, thank you!',
            confirmButtonColor: '#0d6efd',
            showCloseButton: true,
            width: '480px',
            customClass: {
                popup: 'rounded-4 border-0 shadow-lg'
            }
        });
    }

    function showComplianceAlerts() {
        Swal.fire({
            title: '<h5 class="fw-bold text-danger mb-0">Compliance Attention Required</h5>',
            html: `
                <div class="text-start mt-3">
                    <div class="alert alert-soft-danger border-0 small mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> You have <strong>3 critical compliance issues</strong> that need immediate review.
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex align-items-start border-0 px-0">
                            <i class="fas fa-circle text-danger mt-1 me-2" style="font-size: 8px;"></i>
                            <div>
                                <span class="fw-bold">Missing Closing Script:</span>
                                <p class="text-muted mb-0">Interaction #CUS-9821 missed the mandatory legal disclaimer at the end of the call.</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-start border-0 px-0">
                            <i class="fas fa-circle text-danger mt-1 me-2" style="font-size: 8px;"></i>
                            <div>
                                <span class="fw-bold">Verification Failure:</span>
                                <p class="text-muted mb-0">Customer identity was not fully verified before sharing account details in #CUS-4432.</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-start border-0 px-0">
                            <i class="fas fa-circle text-danger mt-1 me-2" style="font-size: 8px;"></i>
                            <div>
                                <span class="fw-bold">Tone of Voice:</span>
                                <p class="text-muted mb-0">Unprofessional response detected during interaction #CUS-1120 at the 4-minute mark.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            showCloseButton: true,
            confirmButtonText: 'I Understand',
            confirmButtonColor: '#dc3545'
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateDashboard();
        document.querySelectorAll('.period-filter').forEach(el => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.period-filter').forEach(i => i.classList.remove('active'));
                el.classList.add('active');
                currentPeriod = parseInt(el.dataset.period);
                document.getElementById('periodDropdown').textContent = el.textContent;
                updateDashboard();
            });
        });
    });
</script>
@endpush
