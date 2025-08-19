@extends('user.layouts.app')
@push('styles')
<style>
    :root {
        --primary: #4F46E5;
        --primary-light: #6366F1;
        --primary-dark: #4338CA;
        --success: #10B981;
        --success-light: #34D399;
        --warning: #F59E0B;
        --warning-light: #FBBF24;
        --danger: #EF4444;
        --danger-light: #F87171;
        --dark: #1F2937;
        --dark-light: #6B7280;
        --light: #F9FAFB;
        --light-gray: #F3F4F6;
    }
    
    .dashboard-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }
    
    .metric-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        font-family: 'Inter', sans-serif;
    }
    
    .trend-up { color: var(--success); }
    .trend-down { color: var(--danger); }
    .trend-neutral { color: var(--dark-light); }
    
    .chart-container {
        height: 280px;
        position: relative;
        padding: 0 1rem 1rem;
    }
    
    .top-performer-badge {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
    }
    
    .needs-improvement-badge {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
    }
    
    .avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .progress-thin {
        height: 6px;
        border-radius: 3px;
    }
    
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-soft-primary { background: rgba(79, 70, 229, 0.1); }
    .bg-soft-success { background: rgba(16, 185, 129, 0.1); }
    .bg-soft-info { background: rgba(59, 130, 246, 0.1); }
    .bg-soft-warning { background: rgba(245, 158, 11, 0.1); }
    .bg-soft-danger { background: rgba(239, 68, 68, 0.1); }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    }
    
    .table-sm th, .table-sm td {
        padding: 0.75rem 1rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: var(--light-gray);
    }
    
    .btn-period {
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: var(--dark-light);
        font-weight: 500;
    }
    
    .btn-period.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .sentiment-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .shadow-soft {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    
    .agent-card {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .agent-card:hover {
        background: var(--light-gray);
    }
    
    .top-agent {
        border-left: 3px solid var(--success);
    }
    
    .low-agent {
        border-left: 3px solid var(--danger);
    }
    
    .dropdown-toggle::after {
        display: none;
    }
    
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 0.5rem;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    
    .page-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Quality Assurance Dashboard</h4>
                <p class="text-muted mb-0">Comprehensive overview of call quality metrics and agent performance</p>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Last 30 Days
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="periodDropdown">
                        <li><a class="dropdown-item active" href="#">Last 30 Days</a></li>
                        <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                        <li><a class="dropdown-item" href="#">Last Quarter</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Custom Range</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Companies</p>
                            <h3 class="metric-value mb-1">24</h3>
                            <small class="text-muted">+3 from last month</small>
                        </div>
                        <div class="icon-circle bg-soft-primary">
                            <i class="fas fa-building text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Avg. Quality Score</p>
                            <h3 class="metric-value mb-1">87.4%</h3>
                            <small class="trend-up"><i class="fas fa-arrow-up me-1"></i> 2.1% improvement</small>
                        </div>
                        <div class="icon-circle bg-soft-success">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Calls Evaluated</p>
                            <h3 class="metric-value mb-1">1,248</h3>
                            <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> 328 this week</small>
                        </div>
                        <div class="icon-circle bg-soft-info">
                            <i class="fas fa-phone-alt text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Avg. Response Time</p>
                            <h3 class="metric-value mb-1">12.4s</h3>
                            <small class="trend-down"><i class="fas fa-arrow-down me-1"></i> 1.2s faster</small>
                        </div>
                        <div class="icon-circle bg-soft-warning">
                            <i class="fas fa-stopwatch text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Quality Score Trend</h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-period active">Daily</button>
                            <button class="btn btn-period">Weekly</button>
                            <button class="btn btn-period">Monthly</button>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">Customer Sentiment Analysis</h6>
                </div>
                <div class="chart-container">
                    <canvas id="sentimentChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3 pb-3">
                    <span class="sentiment-badge bg-success bg-opacity-10 text-success">68% Positive</span>
                    <span class="sentiment-badge bg-warning bg-opacity-10 text-warning">25% Neutral</span>
                    <span class="sentiment-badge bg-danger bg-opacity-10 text-danger">7% Negative</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tables -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Company Performance</h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="companySortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                By Score
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="companySortDropdown">
                                <li><a class="dropdown-item active" href="#">By Score</a></li>
                                <li><a class="dropdown-item" href="#">By Call Volume</a></li>
                                <li><a class="dropdown-item" href="#">By Improvement</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Company</th>
                                    <th>Score</th>
                                    <th>Trend</th>
                                    <th class="pe-4">Calls</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4"><strong>TechCorp Inc.</strong></td>
                                    <td><span class="fw-bold">94.2%</span></td>
                                    <td><span class="trend-up"><i class="fas fa-arrow-up me-1"></i> 3.2%</span></td>
                                    <td class="pe-4">187</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Global Solutions</td>
                                    <td>89.5%</td>
                                    <td><span class="trend-up"><i class="fas fa-arrow-up me-1"></i> 1.1%</span></td>
                                    <td class="pe-4">156</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Innovate LLC</td>
                                    <td>87.8%</td>
                                    <td><span class="trend-neutral"><i class="fas fa-minus me-1"></i> 0.0%</span></td>
                                    <td class="pe-4">132</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">DataSystems</td>
                                    <td>76.4%</td>
                                    <td><span class="trend-down"><i class="fas fa-arrow-down me-1"></i> 2.4%</span></td>
                                    <td class="pe-4">98</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Agent Performance</h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="agentSortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Top Performers
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="agentSortDropdown">
                                <li><a class="dropdown-item active" href="#">Top Performers</a></li>
                                <li><a class="dropdown-item" href="#">Needs Improvement</a></li>
                                <li><a class="dropdown-item" href="#">All Agents</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Top Agent -->
                    <div class="agent-card top-agent ">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Lee&background=10B981&color=fff" class="avatar-sm me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Sarah Lee <span class="top-performer-badge ms-2">Top Performer</span></h6>
                                <small class="text-muted">TechCorp Inc. • 187 calls</small>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0 text-success">97.4%</h5>
                                <small class="trend-up">+4.2%</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mid Agent -->
                    <div class="agent-card">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=Michael+Chen&background=4F46E5&color=fff" class="avatar-sm me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Michael Chen</h6>
                                <small class="text-muted">Global Solutions • 156 calls</small>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0">93.1%</h5>
                                <small class="trend-up">+1.8%</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Low Performer -->
                    <div class="agent-card low-agent">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=David+Kim&background=EF4444&color=fff" class="avatar-sm me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">David Kim <span class="needs-improvement-badge ms-2">Needs Coaching</span></h6>
                                <small class="text-muted">DataSystems • 98 calls</small>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0 text-danger">72.5%</h5>
                                <small class="trend-down">-3.1%</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Performance Distribution</small>
                            <small class="text-muted">100 agents</small>
                        </div>
                        <div class="progress progress-thin">
                            <div class="progress-bar bg-success" style="width: 20%"></div>
                            <div class="progress-bar bg-primary" style="width: 60%"></div>
                            <div class="progress-bar bg-warning" style="width: 15%"></div>
                            <div class="progress-bar bg-danger" style="width: 5%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">Top 20%</small>
                            <small class="text-primary">Mid 60%</small>
                            <small class="text-warning">Low 15%</small>
                            <small class="text-danger">Bottom 5%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Jan 1', 'Jan 5', 'Jan 10', 'Jan 15', 'Jan 20', 'Jan 25', 'Jan 30'],
            datasets: [{
                label: 'Quality Score',
                data: [85, 86, 87, 88, 87, 89, 90],
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4F46E5',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    displayColors: false,
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return 'Score: ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: false, 
                    min: 80, 
                    max: 100,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.03)'
                    },
                    ticks: {
                        padding: 10
                    }
                },
                x: { 
                    grid: { 
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        padding: 10
                    }
                }
            }
        }
    });

    // Sentiment Chart
    const sentimentCtx = document.getElementById('sentimentChart').getContext('2d');
    new Chart(sentimentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [68, 25, 7],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    bodyFont: { size: 12 },
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush