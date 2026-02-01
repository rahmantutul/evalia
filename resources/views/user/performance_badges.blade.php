@extends('user.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="fw-bold text-dark mb-2">Performance Achievement System</h1>
                <p class="text-muted">Understanding our quality assurance grading and recognition system</p>
                <div class="mx-auto bg-primary opacity-25 rounded" style="height: 4px; width: 60px;"></div>
            </div>

            <div class="row g-4">
                <!-- Top Performer -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px; transition: transform 0.3s ease;">
                        <div class="card-body p-4 position-relative">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-icon bg-success bg-opacity-10 text-success p-3 rounded-circle me-3">
                                    <i class="fas fa-trophy fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-success">Top Performer</h4>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill mt-1">Excellent Level</span>
                                </div>
                            </div>
                            <p class="text-secondary">Awarded to agents and companies maintaining an average quality score above <strong>90%</strong>. This represents a commitment to excellence, adherence to all protocols, and exceptional customer communication.</p>
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark small text-uppercase tracking-wider">Key Requirements:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Quality Score ≥ 90%</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Zero major compliance failures</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> High positive sentiment feedback</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- High Achiever -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-icon bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                                    <i class="fas fa-medal fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-primary">High Achiever</h4>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill mt-1">Good Level</span>
                                </div>
                            </div>
                            <p class="text-secondary">Recognizes reliable performance with quality scores between <strong>80% and 89%</strong>. High Achievers consistently deliver quality results with minimal areas for improvement.</p>
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark small text-uppercase tracking-wider">Key Requirements:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Quality Score 80% - 89%</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Consistent performance metrics</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Strong adherence to core values</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consistent Performer -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-icon bg-info bg-opacity-10 text-info p-3 rounded-circle me-3">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-info">Consistent</h4>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill mt-1">Average Level</span>
                                </div>
                            </div>
                            <p class="text-secondary">Awarded for scores between <strong>70% and 79%</strong>. This indicates a stable performance that meets basic standards but has potential for growth through targeted training.</p>
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark small text-uppercase tracking-wider">Key Requirements:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Quality Score 70% - 79%</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Meets minimum quality benchmarks</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-info me-2"></i> Active participation in QA sessions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Needs Coaching -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge-icon bg-danger bg-opacity-10 text-danger p-3 rounded-circle me-3">
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-danger">Needs Coaching</h4>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill mt-1">Priority Level</span>
                                </div>
                            </div>
                            <p class="text-secondary">Assigned when quality scores fall below <strong>70%</strong>. This triggers an automated coaching workflow to identify bottlenecks and provide necessary support for improvement.</p>
                            <div class="mt-4">
                                <h6 class="fw-bold text-dark small text-uppercase tracking-wider">Improvement Plan:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-info-circle text-danger me-2"></i> Detailed performance audit</li>
                                    <li class="mb-2"><i class="fas fa-info-circle text-danger me-2"></i> 1-on-1 coaching sessions</li>
                                    <li class="mb-2"><i class="fas fa-info-circle text-danger me-2"></i> Re-evaluation after 2 weeks</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

    .badge-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tracking-wider {
        letter-spacing: 0.05em;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
    }
</style>
@endsection
