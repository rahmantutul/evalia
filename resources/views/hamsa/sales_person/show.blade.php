@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Client Details</h4>
                <a href="{{ route('client.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Client Info -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Al Futtaim Group</h5>
                    <p class="text-muted">Large company looking for commercial space in Dubai.</p>
                    
                    <h6>Client Requirements:</h6>
                    <div class="mb-3">
                        <span class="badge badge-primary bg-dark">Budget 5M-10M</span>
                        <span class="badge badge-primary bg-dark">Dubai Location</span>
                        <span class="badge badge-primary bg-dark">Commercial Property</span>
                        <span class="badge badge-primary bg-dark">Investor</span>
                    </div>
                </div>
            </div>

            <!-- Assigned Sales Person -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <div class="bg-dark text-white rounded-circle mx-auto mb-2 d-inline-flex align-items-center justify-content-center"
                         style="width: 50px; height: 50px; font-size: 1.2rem;">
                        M
                    </div>
                    <h6 class="mb-1">Mohammed Hassan</h6>
                    <p class="text-muted small mb-2">Commercial Properties Expert</p>
                    <div class="small">
                        <div><i class="fas fa-envelope"></i> m.hassan@example.com</div>
                        <div><i class="fas fa-phone"></i> +971503456789</div>
                    </div>
                    <span class="badge badge-success mt-2">Auto-matched</span>
                </div>
            </div>
        </div>

        <!-- Matching Results -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Sales Person Matches</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sales Person</th>
                                    <th width="80">Match</th>
                                    <th width="120">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-success">
                                    <td>
                                        <strong>Mohammed Hassan</strong><br>
                                        <small class="text-muted">Commercial Expert</small>
                                    </td>
                                    <td class="text-center">4/4</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 100%">100%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>David Chen</strong><br>
                                        <small class="text-muted">International Clients</small>
                                    </td>
                                    <td class="text-center">3/4</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 75%">75%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Ahmed Al Mansoori</strong><br>
                                        <small class="text-muted">Senior Consultant</small>
                                    </td>
                                    <td class="text-center">2/4</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: 50%">50%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Sarah Johnson</strong><br>
                                        <small class="text-muted">Luxury Specialist</small>
                                    </td>
                                    <td class="text-center">1/4</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" style="width: 25%">25%</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection