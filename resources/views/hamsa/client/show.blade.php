@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-dark">Client Information</h4>
                    <p class="text-muted mb-0">Client details and assigned sales person</p>
                </div>
                <a href="#" class="btn btn-light">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Clients
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Client Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Client Details</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="client-avatar bg-danger text-white rounded-circle mx-auto mb-3">
                            A
                        </div>
                        <h5 class="text-dark mb-1">Al Futtaim Group</h5>
                        <p class="text-muted">Large conglomerate</p>
                    </div>
                    
                    <div class="client-info">
                        <div class="info-item mb-3">
                            <label class="text-muted small mb-1">Description</label>
                            <p class="mb-0">Large conglomerate looking for commercial space in Dubai. High budget investment for office buildings.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Sales Person -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Assigned Representative</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="sales-avatar bg-success text-white rounded-circle mx-auto mb-3">
                            M
                        </div>
                        <h6 class="text-dark mb-1">Mohammed Hassan</h6>
                        <p class="text-muted small mb-2">Commercial Properties Expert</p>
                        <span class="badge badge-success">Auto-matched</span>
                    </div>
                    
                    <div class="contact-info">
                        <div class="info-item mb-2">
                            <i class="fas fa-envelope text-muted mr-2"></i>
                            <span class="text-dark">m.hassan@example.com</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone text-muted mr-2"></i>
                            <span class="text-dark">+971 50 345 6789</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Criteria -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Client Requirements</h6>
                </div>
                <div class="card-body">
                    <div class="criteria-grid">
                        <div class="criteria-item">
                            <span class="badge bg-dark">Budget 5M-10M</span>
                            <small class="text-muted d-block mt-1">Budget Range</small>
                        </div>
                        <div class="criteria-item">
                            <span class="badge bg-dark">Location: Dubai</span>
                            <small class="text-muted d-block mt-1">Location</small>
                        </div>
                        <div class="criteria-item">
                            <span class="badge bg-dark">Property: Commercial</span>
                            <small class="text-muted d-block mt-1">Property Type</small>
                        </div>
                        <div class="criteria-item">
                            <span class="badge bg-dark">Client: Investor</span>
                            <small class="text-muted d-block mt-1">Client Type</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.client-avatar {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 2rem;
}
.sales-avatar {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 2rem;
}
.criteria-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}
.criteria-item {
    text-align: center;
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
}
.info-item label {
    font-weight: 500;
}
.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
}
</style>
@endpush