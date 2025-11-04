@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks"></i> Jobs</h3>
                </div>
                <div class="card-body">
                    @if(!empty($jobs))
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Job ID</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                    <tr>
                                        <td><code>{{ substr($job['id'], 0, 12) }}...</code></td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ ucfirst($job['type']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info', 
                                                    'completed' => 'success',
                                                    'failed' => 'danger'
                                                ][$job['status']] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">
                                                {{ ucfirst($job['status']) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($job['created_at'])->format('M j, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ url('/hamsa/jobs/' . $job['id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h4>No jobs found</h4>
                            <p class="text-muted">Your jobs will appear here once you start processing audio or generating content.</p>
                            <a href="{{ url('/hamsa/transcribe') }}" class="btn btn-primary">
                                <i class="fas fa-microphone"></i> Start Transcription
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection