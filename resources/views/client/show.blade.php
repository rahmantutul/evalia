@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Client Details</h4>
                <div>
                    <a href="{{ route('client.edit', $client['id']) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Client Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="text-primary">{{ $client['name'] }}</h5>
                    <p class="text-muted">{{ $client['description'] }}</p>
                    
                    <div class="mt-4">
                        <h6>Assigned Sales Person</h6>
                        <div class="d-flex align-items-center mt-2">
                            <div class="avatar bg-primary rounded-circle mr-3 d-flex align-items-center justify-content-center text-white"
                                 style="width: 50px; height: 50px;">
                                <span class="font-weight-bold">{{ substr($salesPerson['name'], 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-weight-bold">{{ $salesPerson['name'] }}</div>
                                <small class="text-muted">{{ $salesPerson['email'] }}</small>
                                <br>
                                <small class="text-muted">{{ $salesPerson['phone'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Sales Person Matching Results</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sales Person</th>
                                    <th>Matching Score</th>
                                    <th>Percentage</th>
                                    <th>Matching Criteria</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allMatches as $match)
                                <tr class="{{ $match['salesperson']['id'] == $client['assigned_salesperson_id'] ? 'table-success' : '' }}">
                                    <td>
                                        <div class="font-weight-bold">{{ $match['salesperson']['name'] }}</div>
                                        <small class="text-muted">{{ $match['salesperson']['email'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary badge-pill" style="font-size: 1.1em;">
                                            {{ $match['score'] }}/{{ count($criteria) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $match['percentage'] > 70 ? 'bg-success' : ($match['percentage'] > 40 ? 'bg-warning' : 'bg-danger') }}"
                                                 style="width: {{ $match['percentage'] }}%">
                                                {{ number_format($match['percentage'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($match['matching_criteria'] as $criteria)
                                        <span class="badge badge-info mb-1">{{ $criteria['title'] }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar {
    font-size: 1.2rem;
}
.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
    font-size: 0.8rem;
    line-height: 20px;
}
</style>
@endpush