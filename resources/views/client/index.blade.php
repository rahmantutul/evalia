@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Client Management</h4>
                <a href="{{ route('client.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Client
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($clients as $client)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title text-primary mb-0">{{ $client['name'] }}</h5>
                        @if($client['auto_assigned'])
                        <span class="badge badge-success">Auto-assigned</span>
                        @else
                        <span class="badge badge-info">Manual</span>
                        @endif
                    </div>
                    
                    <p class="text-muted small mb-3">{{ $client['description'] }}</p>
                    
                    <div class="salesperson-info mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-light rounded-circle mr-2 d-flex align-items-center justify-content-center">
                                <small class="font-weight-bold text-dark">
                                    {{ substr($client['salesperson_name'], 0, 1) }}
                                </small>
                            </div>
                            <div>
                                <div class="font-weight-bold">{{ $client['salesperson_name'] }}</div>
                                <small class="text-muted">Assigned Sales Person</small>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group w-100">
                        <a href="{{ route('client.show', $client['id']) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('client.edit', $client['id']) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-outline-danger btn-sm flex-fill">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endpush