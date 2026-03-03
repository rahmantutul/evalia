@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('sales_person.index') }}" class="btn btn-outline-secondary btn-sm mr-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="mb-0 text-dark">Add Sales Representative</h5>
                            <p class="text-muted mb-0 small">Create new team member and define expertise areas</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('sales_person.store') }}" method="POST">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12 mb-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-user-circle mr-2"></i>Personal Information</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Full Name</label>
                                    <input type="text" class="form-control border" name="name" placeholder="Enter full name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Email Address</label>
                                    <input type="email" class="form-control border" name="email" placeholder="email@company.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Phone Number</label>
                                    <input type="tel" class="form-control border" name="phone" placeholder="+1 (555) 123-4567">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-600 text-dark mb-2">Position / Title</label>
                                    <input type="text" class="form-control border" name="description" placeholder="Senior Sales Executive">
                                </div>
                            </div>
                        </div>

                        <!-- Expertise Areas -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-chart-line mr-2"></i>Areas of Expertise</h6>
                                <p class="text-muted small mb-3">Select the criteria this representative specializes in for automatic client matching</p>
                                
                                <div class="row">
                                    @foreach($criteria as $criterion)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="expertise_{{ $criterion['id'] }}" 
                                                   name="criteria[]" value="{{ $criterion['id'] }}">
                                            <label class="custom-control-label small text-dark" for="expertise_{{ $criterion['id'] }}">
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
                                    <a href="{{ route('sales_person.index') }}" class="btn btn-outline-secondary mr-3 px-4">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save mr-2"></i>Create Representative
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
</style>
@endpush