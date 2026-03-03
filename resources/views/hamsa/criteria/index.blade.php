@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row m-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Criteria Management</h4>
                    <p class="text-muted mb-0">Manage your matching criteria for sales people and clients</p>
                </div>
                <a href="{{ route('criteria.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-2"></i>Add Criteria
                </a>
            </div>
        </div>
    </div>

    <!-- Criteria List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Criteria Name</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th width="100" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $criterion)
                                <tr>
                                    <td>
                                        <div class="font-weight-600 text-dark">{{ $criterion['title'] }}</div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $criterion['description'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border text-black">{{ $criterion['type'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="#" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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
.font-weight-600 {
    font-weight: 600;
}
.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}
</style>
@endpush