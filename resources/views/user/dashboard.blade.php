@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card gradient-card-header glow-effect">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="text-white mb-1">Support assistant Dashboard</h2>
                        <p class="text-white-50 mb-0">Manage your AI voice assistants</p>
                    </div>
                    <a class="col-md-4 text-md-end" href="{{ route('user.subscription') }}">
                        <div class="badge bg-white text-primary p-2 px-3 rounded-pill">
                            <i class="fas fa-crown me-2"></i> Pro Plan
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Stats with Circular Progress -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card">
                <div class="usage-widget">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="circular-progress">
                                <svg viewBox="0 0 100 100">
                                    <defs>
                                        <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#667eea" />
                                            <stop offset="100%" stop-color="#764ba2" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-bg" cx="50" cy="50" r="40" />
                                    <circle class="progress-bar" cx="50" cy="50" r="40" style="--progress: 65" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <h5 class="mb-0">65%</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <h6 class="text-muted mb-1">Minutes Used</h6>
                            <h4 class="mb-1">325/500</h4>
                            <small class="text-muted">175 minutes remaining</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card">
                <div class="usage-widget">
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="circular-progress">
                                <svg viewBox="0 0 100 100">
                                    <defs>
                                        <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#43e97b" />
                                            <stop offset="100%" stop-color="#38f9d7" />
                                        </linearGradient>
                                    </defs>
                                    <circle class="progress-bg" cx="50" cy="50" r="40" />
                                    <circle class="progress-bar" cx="50" cy="50" r="40" style="--progress: 25" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <h5 class="mb-0">25%</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <h6 class="text-muted mb-1">Messages Used</h6>
                            <h4 class="mb-1">250/1000</h4>
                            <small class="text-muted">750 messages remaining</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="dashboard-card h-100" style="background: var(--warning-gradient); color: white;">
                <div class="d-flex flex-column h-100 p-4">
                    <div class="mb-auto">
                        <h5 class="mb-1">Upgrade to Premium</h5>
                        <p class="small mb-3">Unlock unlimited bots and advanced features</p>
                    </div>
                    <a href="{{ route('user.subscription') }}" class="btn btn-light btn-sm align-self-start">Upgrade Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Voice Bots Grid -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="row g-3 p-4">
                    <!-- Bot Card 1 -->
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="bot-card active p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bot-avatar me-3" style="background: var(--primary-gradient)">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><a href="{{ route('user.bot.overview') }}">Support Assistant</a></h3>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Status</small>
                                <span class="badge bg-soft-success text-success">Active</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <small class="text-muted">Created</small>
                                <small>2 hours ago</small>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="bot-status-badges">
                                    <a href="{{route('user.bot.overview')}}" class="badge bg-primary bg-opacity-10 text-primary me-2" title="Clone">
                                        <i class="fas fa-copy me-1"></i> Clone
                                    </a>
                                    <a href="#" class="badge bg-warning bg-opacity-10 text-warning me-2" title="Deactivate">
                                        <i class="fas fa-pause me-1"></i> Deactivate
                                    </a>
                                    <a href="#" class="badge bg-danger bg-opacity-10 text-danger" title="Delete">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bot Card 2 -->
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="bot-card inactive p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bot-avatar me-3" style="background: var(--primary-gradient)">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"> <a href="{{ route('user.bot.overview') }}">Sales Bot</a></h3>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Status</small>
                                <span class="badge bg-soft-danger text-danger">Inactive</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <small class="text-muted">Created</small>
                                <small>3 days ago</small>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 100%"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="bot-status-badges">
                                    <a href="{{route('user.bot.overview')}}" class="badge bg-primary bg-opacity-10 text-primary me-2" title="Clone">
                                        <i class="fas fa-copy me-1"></i> Clone
                                    </a>
                                    <a href="#" class="badge bg-warning bg-opacity-10 text-warning me-2" title="Deactivate">
                                        <i class="fas fa-pause me-1"></i> Deactivate
                                    </a>
                                    <a href="#" class="badge bg-danger bg-opacity-10 text-danger" title="Delete">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add more bot cards here -->
                    
                    <!-- Create New Bot Card -->
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="dashboard-card h-100" style="background: rgba(245, 247, 254, 0.5); border: 2px dashed #d1d7f0;">
                            <div class="d-flex flex-column align-items-center justify-content-center p-4 text-center h-100">
                                <div class="avatar-lg mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-plus text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="mb-1">Create New Bot</h5>
                                <p class="text-muted small mb-3">Design your custom voice assistant</p>
                                <a href="{{ route('user.bot.create') }}" class="btn btn-primary btn-sm">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating Action Button -->
    <a href="{{ route('user.bot.create') }}" class="floating-action-btn">
        <i class="fas fa-plus"></i>
    </a>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Animate elements on scroll
        function animateOnScroll() {
            $('.dashboard-card').each(function() {
                var cardTop = $(this).offset().top;
                var windowBottom = $(window).scrollTop() + $(window).height();
                
                if (cardTop < windowBottom) {
                    $(this).css('opacity', '1');
                }
            });
        }
        
        // Initialize animation
        $('.dashboard-card').css('opacity', '0');
        $(window).on('scroll', animateOnScroll);
        animateOnScroll();
        
        // Add ripple effect to buttons
        $('.btn').on('click', function(e) {
            var x = e.pageX - $(this).offset().left;
            var y = e.pageY - $(this).offset().top;
            var ripple = $('<span class="ripple-effect"></span>');
            
            ripple.css({
                left: x,
                top: y
            }).appendTo($(this));
            
            setTimeout(function() {
                ripple.remove();
            }, 1000);
        });
    });
</script>
@endpush
