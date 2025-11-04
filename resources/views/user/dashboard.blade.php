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
    @if (session('active_product') == 1)
        @include('user.dashboards.evalia')
    @elseif (session('active_product') == 2)
        @include('user.dashboards.kayan')
    @elseif (session('active_product') == 3)
        @include('user.dashboards.chatbot')
    @else
        @include('user.dashboards.evalia')
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // Fixed data generators
    const generateDummyData = {
        kpiData: function(period) {
            // Calculate calls based on period
            let callsEvaluated;
            if (period === 7) {
                callsEvaluated = 12; // Last 7 days
            } else if (period === 30) {
                callsEvaluated = 53; // Last 30 days (default)
            } else {
                callsEvaluated = 158; // Last quarter (90 days)
            }
            
            return {
                totalCompanies: 7,
                avgQualityScore: 89.2,
                callsEvaluated: callsEvaluated,
                avgResponseTime: 12.3
            };
        },
        
    trendData: function(granularity, period) {
        let labels = [];
        let data = [];
        
        const now = new Date();
        
        if (granularity === 'daily') {
            const days = period === 7 ? 7 : 30;
            for (let i = days; i >= 1; i--) {
                const date = new Date();
                date.setDate(now.getDate() - i);
                
                // Show day name for recent dates, full date for older ones
                if (i <= 7) {
                    labels.push(date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }));
                } else {
                    labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                }
                data.push(80 + Math.random() * 15);
            }
        } else if (granularity === 'weekly') {
            const weeks = period === 7 ? 1 : period === 30 ? 4 : 12;
            for (let i = weeks; i >= 1; i--) {
                const date = new Date();
                date.setDate(now.getDate() - (i * 7));
                
                const weekStart = new Date(date);
                weekStart.setDate(date.getDate() - date.getDay());
                
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                
                // Format based on whether weeks span different months
                if (weekStart.getMonth() === weekEnd.getMonth()) {
                    labels.push(`${weekStart.toLocaleDateString('en-US', { month: 'short' })} ${weekStart.getDate()}-${weekEnd.getDate()}`);
                } else {
                    labels.push(`${weekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`);
                }
                data.push(80 + Math.random() * 15);
            }
        } else {
            // Monthly granularity
            const months = period === 90 ? 3 : 6;
            for (let i = months; i >= 1; i--) {
                const date = new Date();
                date.setMonth(now.getMonth() - i);
                
                // Include year if it's different from current year
                const showYear = date.getFullYear() !== now.getFullYear();
                labels.push(date.toLocaleDateString('en-US', { 
                    month: 'long', 
                    year: showYear ? 'numeric' : undefined 
                }));
                data.push(80 + Math.random() * 15);
            }
        }
        
        return { labels, data };
    },
        
        sentimentData: function() {
            return {
                positive: 72.5,
                neutral: 18.3,
                negative: 9.2
            };
        },
        
        companyPerformance: function(sortBy, period) {
            // Calculate total calls based on period
            let totalCalls;
            if (period === 7) {
                totalCalls = 12;
            } else if (period === 30) {
                totalCalls = 53;
            } else {
                totalCalls = 158;
            }
            
            // Distribute calls between 7 companies
            const companyCalls = this.distributeCalls(totalCalls, 7);
            
            const companies = [
                { name: "TechCorp Inc.", id: "hassan", score: 94.2, trend: 3.2, calls: companyCalls[0] },
                { name: "Global Solutions", id: "hassan", score: 89.5, trend: 1.1, calls: companyCalls[1] },
                { name: "Innovate LLC", id: "hassan", score: 87.8, trend: 0.0, calls: companyCalls[2] },
                { name: "DataSystems", id: "hassan", score: 76.4, trend: -2.4, calls: companyCalls[3] },
                { name: "FutureTech", id: "hassan", score: 91.2, trend: 2.1, calls: companyCalls[4] },
                { name: "CloudMasters", id: "hassan", score: 83.7, trend: -1.2, calls: companyCalls[5] },
                { name: "NextGen Corp", id: "hassan", score: 88.9, trend: 1.5, calls: companyCalls[6] }
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
        
        agentPerformance: function(sortBy, period) {
            // Calculate total calls based on period
            let totalCalls;
            if (period === 7) {
                totalCalls = 12;
            } else if (period === 30) {
                totalCalls = 53;
            } else {
                totalCalls = 158;
            }
            
            // Distribute calls between agents
            const agentCalls = this.distributeCalls(totalCalls, 5);
            
            const agents = [
                { 
                    name: "Sarah Lee", 
                    company: "TechCorp Inc.", 
                    score: 97.4, 
                    trend: 4.2, 
                    calls: agentCalls[0],
                    avatar: "https://ui-avatars.com/api/?name=Sarah+Lee&background=10B981&color=fff"
                },
                { 
                    name: "Michael Chen", 
                    company: "Global Solutions", 
                    score: 93.1, 
                    trend: 1.8, 
                    calls: agentCalls[1],
                    avatar: "https://ui-avatars.com/api/?name=Michael+Chen&background=4F46E5&color=fff"
                },
                { 
                    name: "David Kim", 
                    company: "DataSystems", 
                    score: 72.5, 
                    trend: -3.1, 
                    calls: agentCalls[2],
                    avatar: "https://ui-avatars.com/api/?name=David+Kim&background=EF4444&color=fff"
                },
                { 
                    name: "Emma Wilson", 
                    company: "FutureTech", 
                    score: 95.2, 
                    trend: 2.8, 
                    calls: agentCalls[3],
                    avatar: "https://ui-avatars.com/api/?name=Emma+Wilson&background=8B5CF6&color=fff"
                },
                { 
                    name: "James Brown", 
                    company: "CloudMasters", 
                    score: 88.3, 
                    trend: -0.5, 
                    calls: agentCalls[4],
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
        },
        
        // Helper function to distribute calls between entities
        distributeCalls: function(totalCalls, numberOfEntities) {
            const calls = [];
            let remainingCalls = totalCalls;
            
            // Distribute calls with some variation
            for (let i = 0; i < numberOfEntities - 1; i++) {
                // Allocate between 10% and 25% of remaining calls
                const maxAllocation = Math.min(Math.floor(remainingCalls * 0.25), Math.floor(remainingCalls / (numberOfEntities - i)));
                const minAllocation = Math.max(1, Math.floor(remainingCalls * 0.1));
                
                const allocated = Math.floor(Math.random() * (maxAllocation - minAllocation + 1)) + minAllocation;
                calls.push(allocated);
                remainingCalls -= allocated;
            }
            
            // Add remaining calls to the last entity
            calls.push(remainingCalls);
            
            return calls;
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
        
        // Calculate trend text based on period
        let trendText = '';
        if (currentPeriod === 7) {
            trendText = '+2 from last week';
        } else if (currentPeriod === 30) {
            trendText = '+2 from last month';
        } else {
            trendText = '+5 from last quarter';
        }
        
        kpiCards.innerHTML = `
            <div class="col-md-3">
                <div class="dashboard-card h-100 shadow-soft">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Total Companies</p>
                                <h3 class="metric-value mb-1">${kpiData.totalCompanies}</h3>
                                <small class="text-muted">${trendText}</small>
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
                                <h3 class="metric-value mb-1">${kpiData.callsEvaluated}</h3>
                                <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> ${Math.floor(kpiData.callsEvaluated * 0.3)} this period</small>
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
                                <small class="trend-down"><i class="fas fa-arrow-down me-1"></i> 1.2s faster</small>
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
        const companies = generateDummyData.companyPerformance(currentCompanySort, currentPeriod);
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
        const agents = generateDummyData.agentPerformance(currentAgentSort, currentPeriod);
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
        const companies = generateDummyData.companyPerformance(currentCompanySort, currentPeriod);
        const agents = generateDummyData.agentPerformance(currentAgentSort, currentPeriod);
        
        // Create workbook
        const wb = XLSX.utils.book_new();
        
        // Add KPIs sheet
        const kpiSheetData = [
            ['Metric', 'Value', 'Trend'],
            ['Total Companies', kpiData.totalCompanies, `+2 from last period`],
            ['Average Quality Score', `${kpiData.avgQualityScore.toFixed(1)}%`, `2.1% improvement`],
            ['Calls Evaluated', kpiData.callsEvaluated, `${Math.floor(kpiData.callsEvaluated * 0.3)} this period`],
            ['Average Response Time', `${kpiData.avgResponseTime.toFixed(1)}s`, `1.2s faster`]
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