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
    </div> <!-- Header -->
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