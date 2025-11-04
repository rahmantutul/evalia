@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Usage Analytics</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4><i class="fas fa-microphone"></i></h4>
                                    <h3>{{ $numbers['transcriptions'] ?? 0 }}</h3>
                                    <p>Transcriptions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4><i class="fas fa-volume-up"></i></h4>
                                    <h3>{{ $numbers['tts_jobs'] ?? 0 }}</h3>
                                    <p>TTS Conversions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4><i class="fas fa-robot"></i></h4>
                                    <h3>{{ $numbers['ai_generations'] ?? 0 }}</h3>
                                    <p>AI Generations</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4><i class="fas fa-headset"></i></h4>
                                    <h3>{{ $numbers['conversations'] ?? 0 }}</h3>
                                    <p>Conversations</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Usage This Week</h5>
                                </div>
                                <div class="card-body">
                                    @if(!empty($charts))
                                        <canvas id="usageChart" width="400" height="200"></canvas>
                                    @else
                                        <p class="text-muted text-center py-4">No usage data available yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Service Distribution</h5>
                                </div>
                                <div class="card-body">
                                    @if(!empty($charts))
                                        <canvas id="serviceChart" width="400" height="200"></canvas>
                                    @else
                                        <p class="text-muted text-center py-4">No service data available yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Simple chart implementation - you can enhance this with real data
document.addEventListener('DOMContentLoaded', function() {
    const usageCtx = document.getElementById('usageChart')?.getContext('2d');
    const serviceCtx = document.getElementById('serviceChart')?.getContext('2d');
    
    if (usageCtx) {
        new Chart(usageCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'API Usage',
                    data: [12, 19, 8, 15, 12, 5, 9],
                    borderColor: '#007bff',
                    tension: 0.1
                }]
            }
        });
    }
    
    if (serviceCtx) {
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Transcriptions', 'TTS', 'AI Generation', 'Conversations'],
                datasets: [{
                    data: [40, 25, 20, 15],
                    backgroundColor: ['#007bff', '#28a745', '#17a2b8', '#ffc107']
                }]
            }
        });
    }
});
</script>
@endsection
@endsection