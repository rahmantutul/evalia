@extends('user.layouts.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-dashboard.css') }}">
<style>
    .btn-period.active {
        background-color: #0d6efd;
        color: white;
    }
    .trend-up {
        color: #198754;
    }
    .trend-down {
        color: #dc3545;
    }
    .trend-neutral {
        color: #6c757d;
    }
    .dashboard-card {
        border-radius: 12px;
        border: none;
        background-color: #fff;
    }
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chart-container {
        position: relative;
        height: 300px;
        padding: 1.25rem;
    }
    .sentiment-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 50rem;
        font-size: 0.75rem;
    }
    .agent-card {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        border: 1px solid #f1f1f1;
    }
    .agent-card.top-agent {
        background-color: rgba(25, 135, 84, 0.05);
        border-left: 4px solid #198754;
    }
    .agent-card.low-agent {
        background-color: rgba(220, 53, 69, 0.05);
        border-left: 4px solid #dc3545;
    }
    .top-performer-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        background-color: #198754;
        color: white;
        border-radius: 0.25rem;
    }
    .needs-improvement-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        background-color: #dc3545;
        color: white;
        border-radius: 0.25rem;
    }
    .progress-thin {
        height: 8px;
    }
    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
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
                        <li><a class="dropdown-item period-filter active" href="#" data-period="30">Last 30 Days</a></li>
                        <li><a class="dropdown-item period-filter" href="#" data-period="7">Last 7 Days</a></li>
                        <li><a class="dropdown-item period-filter" href="#" data-period="90">Last Quarter</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center" id="exportBtn">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4" id="kpiCards">
        <!-- Cards will be updated dynamically -->
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="dashboard-card h-100 shadow-soft">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Quality Score Trend</h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-period active" data-granularity="daily">Daily</button>
                            <button class="btn btn-period" data-granularity="weekly">Weekly</button>
                            <button class="btn btn-period" data-granularity="monthly">Monthly</button>
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
                <div class="d-flex justify-content-center gap-2 mt-3 pb-3" id="sentimentBadges">
                    <!-- Will be updated dynamically -->
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
                                <li><a class="dropdown-item company-sort active" href="#" data-sort="score">By Score</a></li>
                                <li><a class="dropdown-item company-sort" href="#" data-sort="volume">By Call Volume</a></li>
                                <li><a class="dropdown-item company-sort" href="#" data-sort="improvement">By Improvement</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="companyTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Company</th>
                                    <th>Score</th>
                                    <th>Trend</th>
                                    <th class="pe-4">Calls</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated dynamically -->
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
                                <li><a class="dropdown-item agent-sort active" href="#" data-sort="top">Top Performers</a></li>
                                <li><a class="dropdown-item agent-sort" href="#" data-sort="needs-improvement">Needs Improvement</a></li>
                                <li><a class="dropdown-item agent-sort" href="#" data-sort="all">All Agents</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3" id="agentPerformance">
                    <!-- Will be populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // Dummy data generators
    const generateDummyData = {
        kpiData: function(period) {
            return {
                totalCompanies: 24 + Math.floor(Math.random() * 5),
                avgQualityScore: 85 + Math.random() * 10,
                callsEvaluated: 1000 + Math.floor(Math.random() * 500),
                avgResponseTime: 10 + Math.random() * 8
            };
        },
        
        trendData: function(granularity, period) {
            let labels = [];
            let data = [];
            
            if (granularity === 'daily') {
                const days = period === 7 ? 7 : 30;
                for (let i = days; i >= 1; i--) {
                    labels.push(`Day ${i}`);
                    data.push(80 + Math.random() * 15);
                }
            } else if (granularity === 'weekly') {
                const weeks = period === 7 ? 1 : period === 30 ? 4 : 12;
                for (let i = weeks; i >= 1; i--) {
                    labels.push(`Week ${i}`);
                    data.push(80 + Math.random() * 15);
                }
            } else {
                const months = period === 90 ? 3 : 6;
                for (let i = months; i >= 1; i--) {
                    labels.push(`Month ${i}`);
                    data.push(80 + Math.random() * 15);
                }
            }
            
            return { labels, data };
        },
        
        sentimentData: function() {
            const positive = 60 + Math.random() * 15;
            const neutral = 20 + Math.random() * 10;
            const negative = 100 - positive - neutral;
            
            return {
                positive: parseFloat(positive.toFixed(1)),
                neutral: parseFloat(neutral.toFixed(1)),
                negative: parseFloat(negative.toFixed(1))
            };
        },
        
        companyPerformance: function(sortBy) {
            const companies = [
                { name: "TechCorp Inc.",id: "hassan",  score: 94.2, trend: 3.2, calls: 187 },
                { name: "Global Solutions",id: "hassan",  score: 89.5, trend: 1.1, calls: 156 },
                { name: "Innovate LLC", id: "hassan",  score: 87.8, trend: 0.0, calls: 132 },
                { name: "DataSystems",  id: "hassan", score: 76.4, trend: -2.4, calls: 98 },
                { name: "FutureTech", id: "hassan", score: 91.2, trend: 2.1, calls: 143 },
                { name: "CloudMasters", id: "hassan", score: 83.7, trend: -1.2, calls: 121 }
            ];
            
            // Sort based on selection
            if (sortBy === 'score') {
                companies.sort((a, b) => b.score - a.score);
            } else if (sortBy === 'volume') {
                companies.sort((a, b) => b.calls - a.calls);
            } else if (sortBy === 'improvement') {
                companies.sort((a, b) => b.trend - a.trend);
            }
            
            return companies;
        },
        
        agentPerformance: function(sortBy) {
            const agents = [
                { 
                    name: "Sarah Lee", 
                    
                    company: "TechCorp Inc.", 
                    score: 97.4, 
                    trend: 4.2, 
                    calls: 187,
                    avatar: "https://ui-avatars.com/api/?name=Sarah+Lee&background=10B981&color=fff"
                },
                { 
                    name: "Michael Chen", 
                   
                    company: "Global Solutions", 
                    score: 93.1, 
                    trend: 1.8, 
                    calls: 156,
                    avatar: "https://ui-avatars.com/api/?name=Michael+Chen&background=4F46E5&color=fff"
                },
                { 
                    name: "David Kim", 
                    
                    company: "DataSystems", 
                    score: 72.5, 
                    trend: -3.1, 
                    calls: 98,
                    avatar: "https://ui-avatars.com/api/?name=David+Kim&background=EF4444&color=fff"
                },
                { 
                    name: "Emma Wilson", 
                    
                    company: "FutureTech", 
                    score: 95.2, 
                    trend: 2.8, 
                    calls: 134,
                    avatar: "https://ui-avatars.com/api/?name=Emma+Wilson&background=8B5CF6&color=fff"
                },
                { 
                    name: "James Brown", 
                    
                    company: "CloudMasters", 
                    score: 88.3, 
                    trend: -0.5, 
                    calls: 112,
                    avatar: "https://ui-avatars.com/api/?name=James+Brown&background=F59E0B&color=fff"
                }
            ];
            
            // Sort based on selection
            if (sortBy === 'top') {
                agents.sort((a, b) => b.score - a.score);
                return agents.slice(0, 3);
            } else if (sortBy === 'needs-improvement') {
                agents.sort((a, b) => a.score - b.score);
                return agents.slice(0, 3);
            } else {
                agents.sort((a, b) => b.score - a.score);
                return agents;
            }
        }
    };

    // Chart instances
    let trendChart = null;
    let sentimentChart = null;
    
    // Current filters
    let currentPeriod = 30;
    let currentGranularity = 'daily';
    let currentCompanySort = 'score';
    let currentAgentSort = 'top';

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        updateDashboard();
        setupEventListeners();
    });

    // Set up event listeners
    function setupEventListeners() {
        // Period filter
        document.querySelectorAll('.period-filter').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.period-filter').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentPeriod = parseInt(this.getAttribute('data-period'));
                document.getElementById('periodDropdown').textContent = this.textContent;
                updateDashboard();
            });
        });

        // Granularity buttons
        document.querySelectorAll('.btn-period').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-period').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentGranularity = this.getAttribute('data-granularity');
                updateTrendChart();
            });
        });

        // Company sort
        document.querySelectorAll('.company-sort').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.company-sort').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentCompanySort = this.getAttribute('data-sort');
                updateCompanyTable();
            });
        });

        // Agent sort
        document.querySelectorAll('.agent-sort').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.agent-sort').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentAgentSort = this.getAttribute('data-sort');
                updateAgentPerformance();
            });
        });

        // Export button
        document.getElementById('exportBtn').addEventListener('click', exportDashboardData);
    }

    // Update entire dashboard
    function updateDashboard() {
        updateKpiCards();
        updateTrendChart();
        updateSentimentChart();
        updateCompanyTable();
        updateAgentPerformance();
    }

    // Update KPI cards
    function updateKpiCards() {
        const kpiData = generateDummyData.kpiData(currentPeriod);
        const kpiCards = document.getElementById('kpiCards');
        
        kpiCards.innerHTML = `
            <div class="col-md-3">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Total Companies</p>
                                <h3 class="metric-value mb-1">${kpiData.totalCompanies}</h3>
                                <small class="text-muted">+${Math.floor(Math.random() * 5)} from last month</small>
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
                                <h3 class="metric-value mb-1">${kpiData.avgQualityScore.toFixed(1)}%</h3>
                                <small class="trend-up"><i class="fas fa-arrow-up me-1"></i> ${(Math.random() * 3).toFixed(1)}% improvement</small>
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
                                <h3 class="metric-value mb-1">${kpiData.callsEvaluated}</h3>
                                <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> ${Math.floor(Math.random() * 100)} this week</small>
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
                                <h3 class="metric-value mb-1">${kpiData.avgResponseTime.toFixed(1)}s</h3>
                                <small class="trend-down"><i class="fas fa-arrow-down me-1"></i> ${(Math.random() * 2).toFixed(1)}s faster</small>
                            </div>
                            <div class="icon-circle bg-soft-warning">
                                <i class="fas fa-stopwatch text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Update trend chart
    function updateTrendChart() {
        const ctx = document.getElementById('trendChart').getContext('2d');
        const trendData = generateDummyData.trendData(currentGranularity, currentPeriod);
        
        if (trendChart) {
            trendChart.destroy();
        }
        
        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Quality Score',
                    data: trendData.data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Update sentiment chart
    function updateSentimentChart() {
        const ctx = document.getElementById('sentimentChart').getContext('2d');
        const sentimentData = generateDummyData.sentimentData();
        
        if (sentimentChart) {
            sentimentChart.destroy();
        }
        
        sentimentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [sentimentData.positive, sentimentData.neutral, sentimentData.negative],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
        
        // Update sentiment badges
        document.getElementById('sentimentBadges').innerHTML = `
            <span class="sentiment-badge bg-success bg-opacity-10 text-success">${sentimentData.positive}% Positive</span>
            <span class="sentiment-badge bg-warning bg-opacity-10 text-warning">${sentimentData.neutral}% Neutral</span>
            <span class="sentiment-badge bg-danger bg-opacity-10 text-danger">${sentimentData.negative}% Negative</span>
        `;
    }

    // Update company table
    function updateCompanyTable() {
        const companies = generateDummyData.companyPerformance(currentCompanySort);
        const tableBody = document.querySelector('#companyTable tbody');
        
        let html = '';
        companies.forEach(company => {
            const trendClass = company.trend > 0 ? 'trend-up' : (company.trend < 0 ? 'trend-down' : 'trend-neutral');
            const trendIcon = company.trend > 0 ? 'fa-arrow-up' : (company.trend < 0 ? 'fa-arrow-down' : 'fa-minus');
            let companyViewBaseUrl = "{{ route('user.company.view', ':id') }}"; 
           html += `
                <tr>
                    <td class="ps-4">
                        <strong>
                            <a style="color: #000;" href="${companyViewBaseUrl.replace(':id', company.id)}">
                                ${company.name}
                            </a>
                        </strong>
                    </td>
                    <td><span class="fw-bold">${company.score}%</span></td>
                    <td><span class="${trendClass}"><i class="fas ${trendIcon} me-1"></i> ${Math.abs(company.trend)}%</span></td>
                    <td class="pe-4">${company.calls}</td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
    }

    // Update agent performance
    function updateAgentPerformance() {
        const agents = generateDummyData.agentPerformance(currentAgentSort);
        const agentContainer = document.getElementById('agentPerformance');
        
        let html = '';
        
        agents.forEach(agent => {
            const isTopPerformer = agent.score >= 90;
            const isLowPerformer = agent.score < 75;
            const trendClass = agent.trend > 0 ? 'trend-up' : (agent.trend < 0 ? 'trend-down' : 'trend-neutral');
            const scoreClass = isTopPerformer ? 'text-success' : (isLowPerformer ? 'text-danger' : '');
            
            html += `
                <div class="agent-card ${isTopPerformer ? 'top-agent' : ''} ${isLowPerformer ? 'low-agent' : ''}">
                    <div class="d-flex align-items-center">
                        <img src="${agent.avatar}" class="avatar-sm me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><a style="color: #000;" href="{{ route('user.agents.index') }}"> ${agent.name}</a> ${isTopPerformer ? '<span class="top-performer-badge ms-2">Top Performer</span>' : ''} ${isLowPerformer ? '<span class="needs-improvement-badge ms-2">Needs Coaching</span>' : ''}</h6>
                            <small class="text-muted">${agent.company} • ${agent.calls} calls</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 ${scoreClass}">${agent.score}%</h5>
                            <small class="${trendClass}">${agent.trend > 0 ? '+' : ''}${agent.trend}%</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        // Add performance distribution
        html += `
            <div class="mt-4">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Performance Distribution</small>
                    <small class="text-muted">${agents.length} agents</small>
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
        `;
        
        agentContainer.innerHTML = html;
    }

    // Export dashboard data to Excel
    function exportDashboardData() {
        // Prepare data for export
        const kpiData = generateDummyData.kpiData(currentPeriod);
        const trendData = generateDummyData.trendData(currentGranularity, currentPeriod);
        const sentimentData = generateDummyData.sentimentData();
        const companies = generateDummyData.companyPerformance(currentCompanySort);
        const agents = generateDummyData.agentPerformance(currentAgentSort);
        
        // Create workbook
        const wb = XLSX.utils.book_new();
        
        // Add KPIs sheet
        const kpiSheetData = [
            ['Metric', 'Value', 'Trend'],
            ['Total Companies', kpiData.totalCompanies, `+${Math.floor(Math.random() * 5)} from last month`],
            ['Average Quality Score', `${kpiData.avgQualityScore.toFixed(1)}%`, `${(Math.random() * 3).toFixed(1)}% improvement`],
            ['Calls Evaluated', kpiData.callsEvaluated, `${Math.floor(Math.random() * 100)} this week`],
            ['Average Response Time', `${kpiData.avgResponseTime.toFixed(1)}s`, `${(Math.random() * 2).toFixed(1)}s faster`]
        ];
        const kpiSheet = XLSX.utils.aoa_to_sheet(kpiSheetData);
        XLSX.utils.book_append_sheet(wb, kpiSheet, 'KPIs');
        
        // Add trend data sheet
        const trendSheetData = [['Period', 'Quality Score']];
        trendData.labels.forEach((label, index) => {
            trendSheetData.push([label, trendData.data[index]]);
        });
        const trendSheet = XLSX.utils.aoa_to_sheet(trendSheetData);
        XLSX.utils.book_append_sheet(wb, trendSheet, 'Quality Trend');
        
        // Add sentiment data sheet
        const sentimentSheetData = [
            ['Sentiment', 'Percentage'],
            ['Positive', `${sentimentData.positive}%`],
            ['Neutral', `${sentimentData.neutral}%`],
            ['Negative', `${sentimentData.negative}%`]
        ];
        const sentimentSheet = XLSX.utils.aoa_to_sheet(sentimentSheetData);
        XLSX.utils.book_append_sheet(wb, sentimentSheet, 'Sentiment Analysis');
        
        // Add company performance sheet
        const companySheetData = [['Company', 'Score', 'Trend', 'Calls']];
        companies.forEach(company => {
            companySheetData.push([company.name, `${company.score}%`, `${company.trend > 0 ? '+' : ''}${company.trend}%`, company.calls]);
        });
        const companySheet = XLSX.utils.aoa_to_sheet(companySheetData);
        XLSX.utils.book_append_sheet(wb, companySheet, 'Company Performance');
        
        // Add agent performance sheet
        const agentSheetData = [['Agent', 'Company', 'Score', 'Trend', 'Calls']];
        agents.forEach(agent => {
            agentSheetData.push([agent.name, agent.company, `${agent.score}%`, `${agent.trend > 0 ? '+' : ''}${agent.trend}%`, agent.calls]);
        });
        const agentSheet = XLSX.utils.aoa_to_sheet(agentSheetData);
        XLSX.utils.book_append_sheet(wb, agentSheet, 'Agent Performance');
        
        // Export the workbook
        const fileName = `Quality_Dashboard_Export_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, fileName);
    }
</script>
@endpush