@extends('supervisor.layouts.app')
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
    .top-performer-badge, .high-achiever-badge, .consistent-badge, .needs-improvement-badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.6rem;
        border-radius: 50rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .top-performer-badge {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
    }
    .high-achiever-badge {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.2);
    }
    .consistent-badge {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.2);
    }
    .needs-improvement-badge {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }
    .top-performer-badge:hover, .high-achiever-badge:hover, .consistent-badge:hover, .needs-improvement-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        color: inherit;
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
        @include('user.dashboards.evalia')
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
                callsEvaluated = 11; // Last 7 days
            } else if (period === 30) {
                callsEvaluated = 22; // Last 30 days (default)
            } else {
                callsEvaluated = 45; // Last quarter (90 days)
            }
            
            const isSupervisor = "{{ session('user.role.name') }}" === 'Supervisor';
            return {
                totalCompanies: isSupervisor ? 2 : 5,
                avgQualityScore: 86.4, // Realistic average from bell curve distribution
                callsEvaluated: callsEvaluated, // Use the calculated value
                avgResponseTime: 5.4
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
                data.push(85 + Math.random() * 12);
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
                
                labels.push(`${weekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`);
                data.push(88 + Math.random() * 8);
            }
        } else {
            // Monthly granularity
            const months = period === 90 ? 3 : 6;
            for (let i = months; i >= 1; i--) {
                const date = new Date();
                date.setMonth(now.getMonth() - i);
                
                labels.push(date.toLocaleDateString('en-US', { month: 'long' }));
                data.push(90 + Math.random() * 5);
            }
        }
        
        return { labels, data };
    },
        
        sentimentData: function() {
            return {
                positive: 78.2, // Aligned with realistic 86% avg score
                neutral: 15.8,
                negative: 6.0
            };
        },
        
        companyPerformance: function(sortBy, period) {
            // Use realistic total of 54 calls distributed across 5 companies
            const totalCalls = 54;
            
            // Each company gets 10-11 calls realistically
            const companyCalls = [11, 11, 11, 11, 10];
            
            const isSupervisor = "{{ session('user.role.name') }}" === 'Supervisor';
            let companies = [
                { name: "Social Security Jordan", id: "ssc-jordan", score: 88.2, trend: 1.4, calls: companyCalls[0] },
                { name: "Arab Bank", id: "arab-bank", score: 85.9, trend: 0.8, calls: companyCalls[1] },
                { name: "Orange Jordan", id: "orange-jo", score: 84.6, trend: 2.3, calls: companyCalls[2] },
                { name: "Manaseer Group", id: "manaseer-group", score: 87.1, trend: -0.6, calls: companyCalls[3] },
                { name: "Royal Jordanian", id: "royal-jordanian", score: 86.3, trend: 1.2, calls: companyCalls[4] }
            ];

            if (isSupervisor) {
                // Return only 2 assigned companies
                companies = companies.slice(0, 2);
            }
            
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
            // Use realistic total of 54 calls distributed across agents
            const totalCalls = 54;
            
            // Each agent gets 3-5 calls realistically
            const agentCalls = [5, 4, 4, 3, 4, 3, 3];
            
            const isSupervisor = "{{ session('user.role.name') }}" === 'Supervisor';
            let agents = [
                { 
                    id: "agent-1",
                    name: "Nadi Budiri", 
                    company: "Social Security", 
                    score: 92.3, 
                    trend: 2.2, 
                    calls: agentCalls[0],
                    avatar: "https://ui-avatars.com/api/?name=Nadi+Budiri&background=0d6efd&color=fff"
                },
                { 
                    id: "agent-2",
                    name: "Sara Khateeb", 
                    company: "Social Security", 
                    score: 89.1, 
                    trend: 1.8, 
                    calls: agentCalls[1],
                    avatar: "https://ui-avatars.com/api/?name=Sara+Khateeb&background=10B981&color=fff"
                },
                { 
                    id: "agent-3",
                    name: "Mahmoud Masri", 
                    company: "Arab Bank", 
                    score: 87.5, 
                    trend: 1.2, 
                    calls: agentCalls[2],
                    avatar: "https://ui-avatars.com/api/?name=Mahmoud+Masri&background=4F46E5&color=fff"
                },
                { 
                    id: "agent-6",
                    name: "Farah Zoubi", 
                    company: "Arab Bank", 
                    score: 91.2, 
                    trend: 3.1, 
                    calls: agentCalls[5],
                    avatar: "https://ui-avatars.com/api/?name=Farah+Zoubi&background=dc3545&color=fff"
                },
                { 
                    id: "agent-4",
                    name: "Layla Hassan", 
                    company: "Orange Jordan", 
                    score: 84.2, 
                    trend: -1.4, 
                    calls: agentCalls[3],
                    avatar: "https://ui-avatars.com/api/?name=Layla+Hassan&background=F59E0B&color=fff"
                },
                { 
                    id: "agent-5",
                    name: "Ahmed Manaseer", 
                    company: "Manaseer Group", 
                    score: 86.8, 
                    trend: 0.5, 
                    calls: agentCalls[4],
                    avatar: "https://ui-avatars.com/api/?name=Ahmed+Manaseer&background=6c757d&color=fff"
                },
                { 
                    id: "agent-7",
                    name: "Yazan Tell", 
                    company: "Royal Jordanian", 
                    score: 78.9, 
                    trend: -2.3, 
                    calls: agentCalls[6],
                    avatar: "https://ui-avatars.com/api/?name=Yazan+Tell&background=fd7e14&color=fff"
                }
            ];

            if (isSupervisor) {
                // Return only 2 agents from assigned companies (SSC and Arab Bank)
                agents = agents.filter(a => a.company === "Social Security" || a.company === "Arab Bank").slice(0, 2);
            }

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
        const periodDropdown = document.getElementById('periodDropdown');
        document.querySelectorAll('.period-filter').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.period-filter').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                currentPeriod = parseInt(this.getAttribute('data-period'));
                if (periodDropdown) periodDropdown.textContent = this.textContent;
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
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', exportDashboardData);
        }
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
        
        let trendText = '';
        if (currentPeriod === 7) {
            trendText = '+2 from last week';
        } else if (currentPeriod === 30) {
            trendText = '+5 from last month';
        } else {
            trendText = '+12 from last quarter';
        }
        
        if (kpiCards) {
            kpiCards.innerHTML = `
                <div class="col-md-3">
                    <div class="dashboard-card h-100 shadow-soft border-bottom border-primary border-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Total Companies</p>
                                    <h3 class="metric-value mb-1 fw-bold">${kpiData.totalCompanies}</h3>
                                    <small class="text-primary fw-500">${trendText}</small>
                                </div>
                                <div class="icon-circle bg-soft-primary">
                                    <i class="fas fa-building text-primary"></i>
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
                                    <p class="text-muted mb-1 small">Avg. Quality</p>
                                    <h3 class="metric-value mb-1 fw-bold text-success">${kpiData.avgQualityScore.toFixed(1)}%</h3>
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
                    <div class="dashboard-card h-100 shadow-soft border-bottom border-info border-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Analyzed Calls</p>
                                    <h3 class="metric-value mb-1 fw-bold text-info">${kpiData.callsEvaluated}</h3>
                                    <small class="text-muted"><i class="fas fa-check-circle text-info me-1"></i> ${Math.floor(kpiData.callsEvaluated * 0.3)} recently completed</small>
                                </div>
                                <div class="icon-circle bg-soft-info">
                                    <i class="fas fa-phone-alt text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="dashboard-card h-100 shadow-soft border-bottom border-warning border-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Response Time</p>
                                    <h3 class="metric-value mb-1 fw-bold text-warning">${kpiData.avgResponseTime.toFixed(1)}s</h3>
                                    <small class="trend-up"><i class="fas fa-arrow-up me-1"></i> 1.2s faster</small>
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

        const roiCards = document.getElementById('roiCards');
        if (roiCards) {
            // New Calculations based on Client Excel:
            // Inputs:
            const avgCallMinutes = 7;
            const evaluatedMinutes = kpiData.callsEvaluated * avgCallMinutes;
            const aiRatePerMin = 0.03;
            const minBillableMinutes = 5000;
            const qcSalary = 250;
            const humanDailyCapacity = 85;
            const workDaysPerMonth = 22;

            // 1. Saved Working Hours
            // Formula: (evaluated minutes / 85) = saved working days * 8 = saved hours
            const savedWorkingDays = evaluatedMinutes / humanDailyCapacity;
            const savedHours = savedWorkingDays * 8;
            
            // 2. Cost Analysis
            // AI Cost (with minimum 5000 mins)
            const billableMinutes = Math.max(evaluatedMinutes, minBillableMinutes);
            const evaliaCost = billableMinutes * aiRatePerMin;

            // Manual Cost (Scaling QC salary to the same volume)
            // One human can do (85 * 22) minutes per month for $250
            const humanMinutesPerMonth = humanDailyCapacity * workDaysPerMonth;
            const manualCostPerMin = qcSalary / humanMinutesPerMonth; // ~$0.133/min
            
            // To compare apples to apples, we see how much it would cost humans to do the AI's volume
            const manualCost = billableMinutes * manualCostPerMin;
            
            const totalSavedCost = Math.max(0, manualCost - evaliaCost);
            const roiPercentage = ((totalSavedCost / evaliaCost) * 100);

            roiCards.innerHTML = `
                <div class="col-md-4">
                    <div class="dashboard-card h-100 shadow-soft border-start border-primary border-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle bg-soft-primary me-3">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Human Time Saved</h6>
                            </div>
                            <div class="mt-2">
                                <h3 class="metric-value mb-0 fw-bold text-primary">${Math.round(savedHours)} Hours</h3>
                                <p class="text-muted small mb-0">Based on <strong>${humanDailyCapacity} min/day</strong> capacity. Equivalent to <strong>${savedWorkingDays.toFixed(1)}</strong> manual working days.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="dashboard-card h-100 shadow-soft border-start border-success border-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle bg-soft-success me-3">
                                    <i class="fas fa-hand-holding-usd text-success"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">Financial Savings</h6>
                            </div>
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">Estimated Manual:</span>
                                    <span class="fw-bold text-dark">$${manualCost.toFixed(2)}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Evalia AI Cost:</span>
                                    <span class="fw-bold text-success">$${evaliaCost.toFixed(2)}</span>
                                </div>
                                <div class="border-top pt-2 d-flex justify-content-between">
                                    <span class="fw-bold text-dark">Net Savings:</span>
                                    <h4 class="text-success fw-bold mb-0">+$${totalSavedCost.toFixed(2)}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="dashboard-card h-100 shadow-soft border-start border-info border-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon-circle bg-soft-info me-3">
                                    <i class="fas fa-chart-line text-info"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">ROI Indicator</h6>
                            </div>
                            <div class="mt-2">
                                <h3 class="metric-value mb-1 fw-bold text-info">${Math.abs(roiPercentage).toFixed(1)}%</h3>
                                <div class="progress progress-thin mb-2">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: ${Math.min(roiPercentage, 100)}%"></div>
                                </div>
                                <p class="text-muted small mb-0">Efficiency gain vs human capital. Billable minutes: <strong>${Math.round(billableMinutes)}</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Update trend chart
    function updateTrendChart() {
        const trendEl = document.getElementById('trendChart');
        if (!trendEl) return;
        
        const ctx = trendEl.getContext('2d');
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
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100,
                        grid: { drawBorder: false }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Update sentiment chart
    function updateSentimentChart() {
        const sentimentEl = document.getElementById('sentimentChart');
        if (!sentimentEl) return;
        
        const ctx = sentimentEl.getContext('2d');
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
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
        
        const sentimentBadges = document.getElementById('sentimentBadges');
        if (sentimentBadges) {
            sentimentBadges.innerHTML = `
                <span class="sentiment-badge bg-success bg-opacity-10 text-success">${sentimentData.positive}% Positive</span>
                <span class="sentiment-badge bg-warning bg-opacity-10 text-warning">${sentimentData.neutral}% Neutral</span>
                <span class="sentiment-badge bg-danger bg-opacity-10 text-danger">${sentimentData.negative}% Negative</span>
            `;
        }
    }

    // Update company table
    function updateCompanyTable() {
        const tableBody = document.querySelector('#companyTable tbody');
        if (!tableBody) return;
        
        const companies = generateDummyData.companyPerformance(currentCompanySort, currentPeriod);
        
        let html = '';
        companies.forEach(company => {
            const trendClass = company.trend > 0 ? 'trend-up' : (company.trend < 0 ? 'trend-down' : 'trend-neutral');
            const trendIcon = company.trend > 0 ? 'fa-arrow-up' : (company.trend < 0 ? 'fa-arrow-down' : 'fa-minus');
            let companyViewBaseUrl = "{{ route('user.company.view', ':id') }}".replace(':id', company.id);

            let badgeHtml = '';
            if (company.score >= 90) {
                badgeHtml = `<a href="${companyViewBaseUrl}" class="top-performer-badge ms-2"><i class="fas fa-trophy me-1"></i> Top</a>`;
            } else if (company.score >= 80) {
                badgeHtml = `<a href="${companyViewBaseUrl}" class="high-achiever-badge ms-2"><i class="fas fa-medal me-1"></i> High</a>`;
            } else if (company.score >= 70) {
                badgeHtml = `<a href="${companyViewBaseUrl}" class="consistent-badge ms-2"><i class="fas fa-star me-1"></i> Stable</a>`;
            } else {
                badgeHtml = `<a href="${companyViewBaseUrl}" class="needs-improvement-badge ms-2"><i class="fas fa-exclamation-circle me-1"></i> Alert</a>`;
            }

           html += `
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <strong>
                                <a style="color: #3f4254;" class="text-hover-primary" href="${companyViewBaseUrl}">
                                    ${company.name}
                                </a>
                            </strong>
                            ${badgeHtml}
                        </div>
                    </td>
                    <td><span class="fw-bold text-dark">${company.score}%</span></td>
                    <td><span class="${trendClass}"><i class="fas ${trendIcon} me-1 small"></i> ${Math.abs(company.trend)}%</span></td>
                    <td class="pe-4 text-secondary">${company.calls} <small class="text-muted">calls</small></td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
    }

    function updateAgentPerformance() {
        const agents = generateDummyData.agentPerformance(currentAgentSort);
        const agentContainer = document.getElementById('agentPerformance');
        
        let html = '';
        agents.forEach(agent => {
            const trendClass = agent.trend > 0 ? 'trend-up' : (agent.trend < 0 ? 'trend-down' : 'trend-neutral');
            
            let badgeHtml = '';
            if (agent.score >= 90) {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="top-performer-badge ms-2"><i class="fas fa-trophy me-1"></i> Top Performer</a>`;
            } else if (agent.score >= 80) {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="high-achiever-badge ms-2"><i class="fas fa-medal me-1"></i> High Achiever</a>`;
            } else if (agent.score >= 70) {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="consistent-badge ms-2"><i class="fas fa-star me-1"></i> Consistent</a>`;
            } else {
                badgeHtml = `<a href="{{ route('user.performance_badges') }}" class="needs-improvement-badge ms-2"><i class="fas fa-graduation-cap me-1"></i> Needs Coaching</a>`;
            }

            const agentUrl = '{{ route('user.agents.show', ['agentId' => ':id']) }}'.replace(':id', agent.id);
            const queryParams = `?name=${encodeURIComponent(agent.name)}&company=${encodeURIComponent(agent.company)}`;

            html += `
                <div class="agent-card ${agent.score >= 90 ? 'top-agent' : ''} ${agent.score < 75 ? 'low-agent' : ''} shadow-sm border-0 mb-3">
                    <div class="d-flex align-items-center">
                        <img src="${agent.avatar}" class="avatar-sm me-3 border shadow-sm">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold d-flex align-items-center">
                                <a style="color: #000;" href="${agentUrl}${queryParams}"> ${agent.name}</a> 
                                ${badgeHtml}
                            </h6>
                            <small class="text-muted">${agent.company} • ${agent.calls} calls</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 ${agent.score >= 90 ? 'text-success' : (agent.score < 75 ? 'text-danger' : '')} fw-bold">${agent.score}%</h5>
                            <small class="${trendClass}">${agent.trend > 0 ? '+' : ''}${agent.trend}%</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
            <div class="mt-4 p-3 bg-light rounded-4">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted fw-bold">Performance Distribution</small>
                    <small class="text-muted">${agents.length} agents</small>
                </div>
                <div class="progress progress-thin" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: 25%"></div>
                    <div class="progress-bar bg-primary" style="width: 55%"></div>
                    <div class="progress-bar bg-warning" style="width: 15%"></div>
                    <div class="progress-bar bg-danger" style="width: 5%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2 flex-wrap text-muted small">
                    <span><i class="fas fa-circle text-success me-1 small"></i> Exceptional (25%)</span>
                    <span><i class="fas fa-circle text-primary me-1 small"></i> High (55%)</span>
                    <span><i class="fas fa-circle text-warning me-1 small"></i> Average (15%)</span>
                    <span><i class="fas fa-circle text-danger me-1 small"></i> Poor (5%)</span>
                </div>
            </div>
        `;
        agentContainer.innerHTML = html;
    }


    function exportDashboardData() {
        const kpiData = generateDummyData.kpiData(currentPeriod);
        const companies = generateDummyData.companyPerformance('score', currentPeriod);
        const agents = generateDummyData.agentPerformance('all', currentPeriod);
        
        const wb = XLSX.utils.book_new();
        
        // Summary Sheet
        const summaryData = [
            ["Metric", "Value"],
            ["Total Companies", kpiData.totalCompanies],
            ["Avg. Quality Score", kpiData.avgQualityScore + "%"],
            ["Calls Evaluated", kpiData.callsEvaluated],
            ["Avg. Response Time", kpiData.avgResponseTime + "s"]
        ];
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(summaryData), "Summary");
        
        // Companies Sheet
        const compData = [["Company Name", "Quality Score", "Trend", "Call Volume"]];
        companies.forEach(c => compData.push([c.name, c.score + "%", c.trend + "%", c.calls]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(compData), "Companies");
        
        // Agents Sheet
        const agntData = [["Agent Name", "Company", "Score", "Trend", "Calls"]];
        agents.forEach(a => agntData.push([a.name, a.company, a.score + "%", a.trend + "%", a.calls]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(agntData), "Agents");
        
        XLSX.writeFile(wb, `Evalia_Dashboard_Export_${new Date().toISOString().split('T')[0]}.xlsx`);
    }
</script>
@endpush
