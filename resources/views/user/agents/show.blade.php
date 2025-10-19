@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="col d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Agent Details</h4>
                        <div class="btn-group">
                            <a href="{{ route('user.agents.performance', $agent['agent_details']['id'] ?? '') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-chart-line me-1"></i>Performance
                            </a>
                            <a href="{{ route('user.agents.dashboard') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Agent Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Agent Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Agent ID:</td>
                                            <td class="fw-bold">
                                                <span class="badge bg-primary">{{ $agent['agent_details']['display_id'] ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Full Name:</td>
                                            <td class="fw-bold">{{ $agent['agent_details']['name'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Position:</td>
                                            <td class="fw-bold">{{ $agent['agent_details']['position'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Company:</td>
                                            <td class="fw-bold">{{ $agent['agent_details']['company_name'] ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Current Performance -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Current Performance</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($agent['current_scores']))
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="50%">Overall Score:</td>
                                            <td class="fw-bold">
                                                <span class="badge bg-primary fs-6">
                                                    {{ number_format($agent['current_scores']['overall_score'] ?? 0, 1) }}/100
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Answer Accuracy:</td>
                                            <td class="fw-bold">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $agent['current_scores']['answer_accuracy'] ?? 0 }}%;" 
                                                         aria-valuenow="{{ $agent['current_scores']['answer_accuracy'] ?? 0 }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $agent['current_scores']['answer_accuracy'] ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Response Speed:</td>
                                            <td class="fw-bold">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                         style="width: {{ $agent['current_scores']['response_speed'] ?? 0 }}%;" 
                                                         aria-valuenow="{{ $agent['current_scores']['response_speed'] ?? 0 }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $agent['current_scores']['response_speed'] ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Customer Satisfaction:</td>
                                            <td class="fw-bold">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                         style="width: {{ $agent['current_scores']['customer_satisfaction'] ?? 0 }}%;" 
                                                         aria-valuenow="{{ $agent['current_scores']['customer_satisfaction'] ?? 0 }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $agent['current_scores']['customer_satisfaction'] ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    @else
                                    <p class="text-muted text-center">No performance data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Weights -->
                    @if(isset($agent['performance_weights']))
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Performance Weights</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h4>{{ number_format(($agent['performance_weights']['answer_accuracy'] ?? 0) * 100) }}%</h4>
                                                    <p class="mb-0">Answer Accuracy</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <h4>{{ number_format(($agent['performance_weights']['response_speed'] ?? 0) * 100) }}%</h4>
                                                    <p class="mb-0">Response Speed</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body">
                                                    <h4>{{ number_format(($agent['performance_weights']['customer_satisfaction'] ?? 0) * 100) }}%</h4>
                                                    <p class="mb-0">Customer Satisfaction</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Quick Stats -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Agent ID Information</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-0">
                                        <strong>Internal ID:</strong> {{ $agent['agent_details']['id'] ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection