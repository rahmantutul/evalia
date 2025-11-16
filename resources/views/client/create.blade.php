@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('client.index') }}" class="btn btn-outline-secondary btn-sm mr-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="mb-0 text-dark">Add New Client</h5>
                            <p class="text-muted mb-0 small">Create client profile and define requirements for optimal matching</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('client.store') }}" method="POST">
                        @csrf
                        
                        <!-- Client Information -->
                        <div class="row mb-4">
                            <div class="col-12 mb-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-building mr-2"></i>Client Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Client Name</label>
                                    <input type="text" class="form-control border" name="name" placeholder="Enter client name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Sales Representative</label>
                                    <select class="form-control border" name="assigned_salesperson_id">
                                        <option value="">Auto-assign (Recommended)</option>
                                        @foreach($salesPeople as $person)
                                        <option value="{{ $person['id'] }}">{{ $person['name'] }} - {{ $person['description'] }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Leave empty for automatic best-match assignment</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Client Description</label>
                                    <textarea class="form-control border" name="description" rows="3" placeholder="Brief description of the client and their needs"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Client Requirements -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-bullseye mr-2"></i>Client Requirements</h6>
                                <p class="text-muted small mb-3">Select criteria that match client requirements for optimal sales representative matching</p>
                                
                                <div class="row">
                                    @foreach($criteria as $criterion)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="requirement_{{ $criterion['id'] }}" 
                                                   name="criteria[]" value="{{ $criterion['id'] }}">
                                            <label class="custom-control-label small text-dark" for="requirement_{{ $criterion['id'] }}">
                                                {{ $criterion['title'] }}
                                                <small class="d-block text-muted">{{ $criterion['type'] }}</small>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-5 pt-4 border-top">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('client.index') }}" class="btn btn-outline-secondary mr-3 px-4">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save mr-2"></i>Create Client
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 8px;
}
.form-control {
    border-radius: 6px;
    border: 1px solid #d1d3e2;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.1);
}
.custom-control-input:checked ~ .custom-control-label::before {
    border-color: #4e73df;
    background-color: #4e73df;
}
.font-weight-600 {
    font-weight: 600;
}
.btn {
    border-radius: 6px;
    font-weight: 500;
}
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 8px 10px;
}
</style>
@endpush