@extends('user.layouts.app')
@push('styles')
<style>
    .dashboard-header {
        background: #ffffff;
        border-bottom: 1px solid #dbe4f0;
        padding: 1.5rem 0;
        margin-bottom: 1.5rem;
    }
    
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.2s ease;
        background: #ffffff;
    }
    
    .card:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    .agent-header {
        background: linear-gradient(135deg, #ffffff 0%, #f5f8ff 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        border: 1px solid #e2eaf3;
    }
    
    .agent-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e2eaf3;
        margin-right: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    .performance-score {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .score-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #557ebb;
        color: white;
        font-weight: bold;
        font-size: 1.5rem;
        margin-left: auto;
        box-shadow: 0 4px 10px rgba(162, 155, 254, 0.3);
    }
    
    .metric-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 12px;
        background: #ffffff;
        height: 100%;
        border: 1px solid #e2eaf3;
    }
    
    .metric-title {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--secondary-color);
        margin-bottom: 0.75rem;
    }
    
    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .metric-weight {
        font-size: 0.75rem;
        background: #f1f7fd;
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 15px;
        color: var(--secondary-color);
    }
    
    .trend-indicator {
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
    }
    
    .trend-up {
        color: var(--success-color);
    }
    
    .trend-down {
        color: var(--danger-color);
    }
    
    .supervisor-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 1rem;
        border: 2px solid #e2eaf3;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
        background-color: #edf2f7;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
    
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--dark-color);
    }
    
    .chart-container {
        height: 250px;
        margin-top: 1rem;
    }
    
    .performance-table th {
        background-color: #f8fafc;
        color: var(--secondary-color);
        font-weight: 600;
        font-size: 0.875rem;
        border-top: 1px solid #e2eaf3;
        border-bottom: 2px solid #e2eaf3;
    }
    
    .performance-table td {
        font-size: 0.875rem;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
    }
    
    .badge-custom {
        padding: 0.4em 0.75em;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .bg-light-custom {
        background-color: #f8fafc;
        border-radius: 10px;
        padding: 1.25rem;
    }
     .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            font-size: 12px;
            font-style: italic;
            cursor: help;
        }
</style>
@endpush
@section('content')
<div class="container-fluid pb-5 mt-4">
    <!-- Agent Header -->
    <div class="agent-header">
        
        <img src="https://cdn-icons-png.flaticon.com/512/69/69890.png" class="agent-avatar" alt="Agent Avatar">
        <div class="flex-grow-1">
            <h2 class="h4 mb-1">Abdullah Al Hasib</h2>
            <p class="text-muted mb-1">Senior Customer Support Agent</p>
            <div class="d-flex gap-2">
                <span class="badge bg-light text-dark">ID: AG-7892</span>
                <span class="badge bg-light text-dark">Company A</span>
                <span class="badge bg-light text-dark">Joined: Jan 15, 2022</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-3 text-end">
                <div class="performance-score">83</div>
                <p class="text-muted mb-0 small">Overall Score</p>
                <span class="badge bg-success trend-indicator">
                    <i class="fas fa-arrow-up me-1"></i> 2.5%
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Performance Metrics -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <h4 class="card-title mb-0">Performance Metrics</h4>
                        <div class="info-icon" data-bs-toggle="tooltip" title="Calculation Details: (Response Speed × 0.3) + (Answer Accuracy × 0.5) + (Customer Satisfaction × 0.2) ">i</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="metric-card">
                                <div class="metric-title">Response Speed</div>
                                <div class="metric-value">80</div>
                                <span class="metric-weight">Weight: 30%</span>
                                <div class="mt-2">
                                    <span class="trend-indicator trend-up">
                                        <i class="fas fa-arrow-up me-1"></i> 5%
                                    </span>
                                </div>
                                <div class="chart-container">
                                    <canvas id="responseSpeedChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="metric-card">
                                <div class="metric-title">Answer Accuracy</div>
                                <div class="metric-value">90</div>
                                <span class="metric-weight">Weight: 50%</span>
                                <div class="mt-2">
                                    <span class="trend-indicator trend-up">
                                        <i class="fas fa-arrow-up me-1"></i> 3.2%
                                    </span>
                                </div>
                                <div class="chart-container">
                                    <canvas id="accuracyChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="metric-card">
                                <div class="metric-title">Customer Satisfaction</div>
                                <div class="metric-value">70</div>
                                <span class="metric-weight">Weight: 20%</span>
                                <div class="mt-2">
                                    <span class="trend-indicator trend-down">
                                        <i class="fas fa-arrow-down me-1"></i> 2.1%
                                    </span>
                                </div>
                                <div class="chart-container">
                                    <canvas id="satisfactionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance History -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Performance History</h5>
                    <div class="chart-container">
                        <canvas id="performanceHistoryChart"></canvas>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table performance-table">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Response Speed</th>
                                    <th>Answer Accuracy</th>
                                    <th>Customer Satisfaction</th>
                                    <th>Overall Score</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">July 2023</td>
                                    <td>80 <span class="trend-indicator trend-up"><i class="fas fa-arrow-up"></i></span></td>
                                    <td>90 <span class="trend-indicator trend-up"><i class="fas fa-arrow-up"></i></span></td>
                                    <td>70 <span class="trend-indicator trend-down"><i class="fas fa-arrow-down"></i></span></td>
                                    <td><span class="badge badge-custom" style="background-color: #557ebb; color: white;">83</span></td>
                                    <td><span class="trend-indicator trend-up"><i class="fas fa-arrow-up"></i> 2.5%</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">June 2023</td>
                                    <td>75</td>
                                    <td>85</td>
                                    <td>80</td>
                                    <td><span class="badge badge-custom" style="background-color: #557ebb; color: white;">81</span></td>
                                    <td><span class="trend-indicator trend-up"><i class="fas fa-arrow-up"></i> 1.2%</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">May 2023</td>
                                    <td>85</td>
                                    <td>80</td>
                                    <td>75</td>
                                    <td><span class="badge badge-custom" style="background-color: #557ebb; color: white;">80</span></td>
                                    <td><span class="trend-indicator trend-up"><i class="fas fa-arrow-up"></i> 5.3%</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">April 2023</td>
                                    <td>70</td>
                                    <td>85</td>
                                    <td>65</td>
                                    <td><span class="badge badge-custom" style="background-color: #557ebb; color: white;">76</span></td>
                                    <td><span class="trend-indicator trend-down"><i class="fas fa-arrow-down"></i> 3.8%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Supervisor Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Supervisor Information</h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://cdn-icons-png.flaticon.com/512/69/69890.png" class="supervisor-img" alt="Supervisor">
                        <div>
                            <h6 class="mb-0">Sarah Johnson</h6>
                            <p class="text-muted small mb-0">Customer Support Manager</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="badge badge-custom" style="background-color: #557ebb; color: white;"><i class="fas fa-star me-1"></i> Rating: 4.6</span>
                    </div>
                    <p class="mb-1 small"><i class="fas fa-envelope me-2 text-muted"></i> sarah.johnson@company.com</p>
                    <p class="mb-3 small"><i class="fas fa-phone me-2 text-muted"></i> +1 (555) 123-4567</p>
                </div>
            </div>

            <!-- Performance Comparison -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Performance Comparison</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Your Performance</span>
                            <span class="small fw-semibold">83%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 83%; background-color: #557ebb;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Team Average</span>
                            <span class="small fw-semibold">82%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 82%; background-color: #588cfd;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Company Average</span>
                            <span class="small fw-semibold">79%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 79%; background-color: #74b9ff;"></div>
                        </div>
                    </div>
                    <div class="chart-container mt-3">
                        <canvas id="comparisonChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Weight Settings -->
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">Performance Weights</h5>
                    <p class="text-muted small">These weights are set at the company level and applied to all agents.</p>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Response Speed</span>
                            <span class="small fw-semibold">30%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 30%; background-color: #557ebb;"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Answer Accuracy</span>
                            <span class="small fw-semibold">50%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 50%; background-color: #557ebb;"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Customer Satisfaction</span>
                            <span class="small fw-semibold">20%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 20%; background-color: #557ebb;"></div>
                        </div>
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
        // Soft Blue Variations
        const blue1 = { border: 'rgba(100, 149, 237, 0.7)', bg: 'rgba(100, 149, 237, 0.15)' }; // Cornflower
        const blue2 = { border: 'rgba(135, 206, 250, 0.7)', bg: 'rgba(135, 206, 250, 0.15)' }; // Light Sky
        const blue3 = { border: 'rgba(173, 216, 230, 0.7)', bg: 'rgba(173, 216, 230, 0.15)' }; // Light Blue
        const blue4 = { border: 'rgba(176, 196, 222, 0.7)', bg: 'rgba(176, 196, 222, 0.4)' }; // Light Steel
        const blue5 = { border: 'rgba(135, 206, 250, 0.7)', bg: 'rgba(135, 206, 250, 0.15)' }; // Light Sky for radar

        // Response Speed Chart
        new Chart(document.getElementById('responseSpeedChart'), {
            type: 'line',
            data: {
                labels: ['Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Response Speed',
                    data: [70, 85, 75, 80],
                    borderColor: blue1.border,
                    backgroundColor: blue1.bg,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:false,min:60,max:100}} }
        });

        // Accuracy Chart
        new Chart(document.getElementById('accuracyChart'), {
            type: 'line',
            data: {
                labels: ['Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Accuracy',
                    data: [85, 80, 85, 90],
                    borderColor: blue2.border,
                    backgroundColor: blue2.bg,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:false,min:70,max:100}} }
        });

        // Satisfaction Chart
        new Chart(document.getElementById('satisfactionChart'), {
            type: 'line',
            data: {
                labels: ['Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Satisfaction',
                    data: [65, 75, 80, 70],
                    borderColor: blue3.border,
                    backgroundColor: blue3.bg,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:false,min:60,max:90}} }
        });

        // Performance History Chart
        new Chart(document.getElementById('performanceHistoryChart'), {
            type: 'bar',
            data: {
                labels: ['April 2023', 'May 2023', 'June 2023', 'July 2023'],
                datasets: [
                    {
                        label: 'Response Speed',
                        data: [70, 85, 75, 80],
                        backgroundColor: 'rgba(100, 149, 237, 0.4)'
                    },
                    {
                        label: 'Answer Accuracy',
                        data: [85, 80, 85, 90],
                        backgroundColor: 'rgba(135, 206, 250, 0.4)'
                    },
                    {
                        label: 'Customer Satisfaction',
                        data: [65, 75, 80, 70],
                        backgroundColor: blue4.bg
                    }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales:{x:{stacked:false}, y:{stacked:false,beginAtZero:true,max:100}} }
        });

        // Comparison Chart
        new Chart(document.getElementById('comparisonChart'), {
            type: 'radar',
            data: {
                labels: ['Response Speed', 'Answer Accuracy', 'Customer Satisfaction', 'Resolution Time', 'Professionalism'],
                datasets: [
                    {
                        label: 'Your Performance',
                        data: [80, 90, 70, 75, 85],
                        backgroundColor: blue5.bg,
                        borderColor: blue5.border,
                        pointBackgroundColor: 'rgba(135, 206, 250, 0.9)'
                    },
                    {
                        label: 'Team Average',
                        data: [75, 85, 75, 70, 80],
                        backgroundColor: blue5.bg,
                        borderColor: blue5.border,
                        pointBackgroundColor: 'rgba(135, 206, 250, 0.9)'
                    }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } } } }
        });
    });
</script>


@endpush