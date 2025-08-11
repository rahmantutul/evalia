@extends('user.layouts.app')

@push('styles')
<style>
    /* Modern Glassmorphism Design */
    .bot-usage-container {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 15px 50px rgba(108, 99, 255, 0.15);
        padding: 2rem;
        overflow: hidden;
        position: relative;
    }

    /* Floating Particles Background */
    .bot-usage-container::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 30% 30%, 
            rgba(108, 99, 255, 0.05) 0%, 
            transparent 50%),
            radial-gradient(circle at 70% 70%, 
            rgba(72, 187, 120, 0.05) 0%, 
            transparent 50%);
        animation: float 20s infinite alternate ease-in-out;
        z-index: -1;
    }

    @keyframes float {
        0% { transform: translate(0, 0); }
        50% { transform: translate(-5%, 5%); }
        100% { transform: translate(5%, -5%); }
    }

    /* Header Section */
    .bot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(108, 99, 255, 0.1);
    }

    .bot-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .bot-avatar {
        width: 100px;
        height: 100px;
        border-radius: 24px;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 10px 30px rgba(108, 99, 255, 0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .bot-avatar:hover {
        transform: rotate(5deg) scale(1.05);
        box-shadow: 0 15px 40px rgba(108, 99, 255, 0.3);
    }

    .bot-meta h2 {
        margin: 0;
        font-weight: 800;
        color: #1a202c;
        font-size: 2rem;
        letter-spacing: -0.5px;
        background: linear-gradient(90deg, #6c63ff, #4d44db);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        text-shadow: 0 2px 10px rgba(108, 99, 255, 0.1);
    }

    .bot-status {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1.5rem;
        border-radius: 24px;
        font-size: 0.9rem;
        font-weight: 700;
        margin-top: 0.8rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .status-active {
        background: linear-gradient(135deg, rgba(72, 187, 120, 0.2) 0%, rgba(72, 187, 120, 0.1) 100%);
        color: #2b6cb0;
        border: 1px solid rgba(72, 187, 120, 0.3);
    }

    .bot-actions {
        display: flex;
        gap: 1rem;
    }

    .action-btn {
        padding: 0.8rem 1.8rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        cursor: pointer;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .action-btn::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(255,255,255,0.2), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .action-btn:hover::after {
        opacity: 1;
    }

    .btn-clone {
        background: linear-gradient(135deg, rgba(108, 99, 255, 0.2) 0%, rgba(108, 99, 255, 0.1) 100%);
        color: #6c63ff;
        border: 1px solid rgba(108, 99, 255, 0.3);
    }

    .btn-clone:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(108, 99, 255, 0.2);
    }

    .btn-inactive {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0.1) 100%);
        color: #dd6b20;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .btn-inactive:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.2);
    }

    .btn-delete {
        background: linear-gradient(135deg, rgba(245, 101, 101, 0.2) 0%, rgba(245, 101, 101, 0.1) 100%);
        color: #c53030;
        border: 1px solid rgba(245, 101, 101, 0.3);
    }

    .btn-delete:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(245, 101, 101, 0.2);
    }
</style>
<style>
    /* Compact Stats Cards with Full Gradient Backgrounds */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(108, 99, 255, 0.1);
        border: 1px solid rgba(108, 99, 255, 0.1);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* Optional subtle shine effect on hover */
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 99, 255, 0.15);
    }

    .stat-title {
        font-size: 1rem;
        color: #4a5568;
        font-weight: 600;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-title i {
        font-size: 1.1rem;
        color: #6c63ff;
    }

    .stat-content {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        line-height: 1.2;
    }

    .stat-change {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: rgba(255, 255, 255, 0.7);
    }

    .change-up {
        color: #38a169;
    }

    .change-down {
        color: #e53e3e;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .stat-value {
            font-size: 1.4rem;
        }
    }
</style>
<style>
    /* Chart Section Styles */
    .chart-container {
        margin-top: 2rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(108, 99, 255, 0.08);
        border: 1px solid rgba(108, 99, 255, 0.1);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .chart-period {
        display: flex;
        gap: 0.5rem;
        background: rgba(108, 99, 255, 0.05);
        padding: 0.25rem;
        border-radius: 12px;
    }

    .period-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        background: transparent;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .period-btn:hover {
        background: rgba(108, 99, 255, 0.1);
        color: #6c63ff;
    }

    .period-btn.active {
        background: rgba(108, 99, 255, 0.2);
        color: #6c63ff;
    }

    .chart-wrapper {
        height: 300px;
        position: relative;
    }

    /* Modern Chart Styling */
    .chart-js-container {
        position: relative;
        height: 100%;
        width: 100%;
    }

    /* Tooltip Styling */
    .chart-tooltip {
        position: absolute;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(108, 99, 255, 0.2);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        pointer-events: none;
        z-index: 100;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .tooltip-header {
        font-weight: 700;
        color: #6c63ff;
        margin-bottom: 0.25rem;
    }

    .tooltip-value {
        font-weight: 600;
        color: #2d3748;
    }

    /* Legend Styling */
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        transition: opacity 0.3s ease;
    }

    .legend-item:hover .legend-color {
        opacity: 0.8;
        transform: scale(1.1);
    }

    /* Animation for chart bars */
    @keyframes barRise {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@section('content')
<div class="container-fluid">
    <div class="bot-usage-container">
        <!-- Header Section -->
        <div class="bot-header">
            <div class="bot-info">
                <img src="{{ asset('/') }}assets/images/extra/P9aqUeKFvc.gif" class="bot-avatar" alt="Bot Avatar">
                <div class="bot-meta">
                    <h2>Support Assistant Pro</h2>
                    <span class="bot-status status-active">
                        Active <i class="fas fa-bolt ml-1"></i>
                    </span>
                </div>
            </div>
            <div class="bot-actions">
                <a href="{{route('user.bot.overview')}}" class="action-btn btn-clone">
                    <i class="fas fa-copy"></i> Clone Bot
                </a>
                <button class="action-btn btn-inactive">
                    <i class="fas fa-power-off"></i> Deactivate
                </button>
                <button class="action-btn btn-delete">
                    <i class="fas fa-trash"></i> Delete Bot
                </button>
            </div>
        </div>

        <!-- Stats Cards - Compact Version -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">
                    <i class="fas fa-exchange-alt"></i> Total Interactions
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">15,842</h3>
                    <div class="stat-change change-up">
                        <i class="fas fa-arrow-up"></i> 24%
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-title">
                    <i class="fas fa-microphone"></i> Voice Minutes
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">2,156</h3>
                    <div class="stat-change change-up">
                        <i class="fas fa-arrow-up"></i> 18%
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-title">
                    <i class="fas fa-comment-alt"></i> Messages
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">11,487</h3>
                    <div class="stat-change change-up">
                        <i class="fas fa-arrow-up"></i> 12%
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">Monthly Usage Analytics</h3>
                <div class="chart-period">
                    <button class="period-btn active" data-period="week">7D</button>
                    <button class="period-btn" data-period="month">1M</button>
                    <button class="period-btn" data-period="quarter">3M</button>
                    <button class="period-btn" data-period="year">1Y</button>
                </div>
            </div>
            <div class="chart-wrapper">
                <div class="chart-js-container">
                    <canvas id="usageChart"></canvas>
                    <div class="chart-tooltip"></div>
                </div>
            </div>
            <div class="chart-legend" id="chartLegend"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample data - replace with your actual data
        const monthlyData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            interactions: [1245, 2156, 3487, 2892, 4156, 5045, 4842, 5231, 4789, 5123, 5897, 6245],
            voiceMinutes: [845, 1256, 1587, 1892, 2156, 3045, 2842, 3231, 2789, 3123, 3897, 4245],
            messages: [945, 1556, 2487, 1892, 3156, 4045, 3842, 4231, 3789, 4123, 4897, 5245]
        };

        // Chart configuration
        const ctx = document.getElementById('usageChart').getContext('2d');
        const usageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.labels,
                datasets: [
                    {
                        label: 'Interactions',
                        data: monthlyData.interactions,
                        backgroundColor: 'rgba(108, 99, 255, 0.7)',
                        borderColor: 'rgba(108, 99, 255, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(108, 99, 255, 0.9)',
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    },
                    {
                        label: 'Voice Minutes',
                        data: monthlyData.voiceMinutes,
                        backgroundColor: 'rgba(72, 187, 120, 0.7)',
                        borderColor: 'rgba(72, 187, 120, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(72, 187, 120, 0.9)',
                        animation: {
                            duration: 1000,
                            delay: 200,
                            easing: 'easeOutQuart'
                        }
                    },
                    {
                        label: 'Messages',
                        data: monthlyData.messages,
                        backgroundColor: 'rgba(245, 158, 11, 0.7)',
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(245, 158, 11, 0.9)',
                        animation: {
                            duration: 1000,
                            delay: 400,
                            easing: 'easeOutQuart'
                        }
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false,
                        external: function(context) {
                            const tooltip = document.querySelector('.chart-tooltip');
                            
                            // Hide if no tooltip
                            if (context.tooltip.opacity === 0) {
                                tooltip.style.opacity = 0;
                                return;
                            }
                            
                            // Set tooltip content
                            const datasetLabel = context.tooltip.dataPoints[0].dataset.label || '';
                            const value = context.tooltip.dataPoints[0].raw;
                            const label = context.tooltip.dataPoints[0].label;
                            
                            tooltip.innerHTML = `
                                <div class="tooltip-header">${datasetLabel}</div>
                                <div class="tooltip-value">${label}: ${value.toLocaleString()}</div>
                            `;
                            
                            // Position tooltip
                            const chartRect = context.chart.canvas.getBoundingClientRect();
                            tooltip.style.left = chartRect.left + context.tooltip.caretX + 'px';
                            tooltip.style.top = chartRect.top + context.tooltip.caretY + 'px';
                            tooltip.style.opacity = 1;
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#718096'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(108, 99, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#718096',
                            callback: function(value) {
                                return value >= 1000 ? (value/1000) + 'k' : value;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    onComplete: function() {
                        // Create custom legend
                        const legendContainer = document.getElementById('chartLegend');
                        legendContainer.innerHTML = this.data.datasets.map((dataset, i) => {
                            return `
                                <div class="legend-item" data-index="${i}">
                                    <div class="legend-color" style="background-color: ${dataset.backgroundColor}"></div>
                                    <span>${dataset.label}</span>
                                </div>
                            `;
                        }).join('');
                        
                        // Add click event to legend items
                        document.querySelectorAll('.legend-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const index = this.getAttribute('data-index');
                                const meta = usageChart.getDatasetMeta(index);
                                meta.hidden = meta.hidden === null ? !usageChart.data.datasets[index].hidden : null;
                                usageChart.update();
                            });
                        });
                    }
                }
            }
        });

        // Period button functionality
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Here you would typically fetch new data based on the period
                // For this example, we'll just show all data
                usageChart.data.labels = monthlyData.labels;
                usageChart.data.datasets[0].data = monthlyData.interactions;
                usageChart.data.datasets[1].data = monthlyData.voiceMinutes;
                usageChart.data.datasets[2].data = monthlyData.messages;
                usageChart.update();
            });
        });
    });
</script>
@endpush