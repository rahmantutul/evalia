@extends('user.layouts.app')
@push('styles')
  <style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        --glass-effect: rgba(255, 255, 255, 0.15);
        --glass-border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    
    
    .dashboard-card {
        border: none;
        border-radius: 16px;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    }
    
    .dashboard-card:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    
    .gradient-card-header {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .gradient-card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
    }
    
    .usage-widget {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }
    
    .circular-progress {
        position: relative;
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }
    
    .circular-progress svg {
        width: 100%;
        height: 100%;
    }
    
    .circular-progress circle {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    
    .circular-progress .progress-bg {
        stroke: #f0f2f7;
    }
    
    .circular-progress .progress-bar {
        stroke: url(#progress-gradient);
        stroke-dasharray: 226;
        stroke-dashoffset: calc(226 - (226 * var(--progress)) / 100);
        animation: progress-animation 1.5s ease-out forwards;
    }
    
    @keyframes progress-animation {
        from { stroke-dashoffset: 226; }
    }
    
    .bot-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border-left: 4px solid;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }
    
    .bot-card:hover {
        transform: translateY(-3px);
    }
    
    .bot-card.active {
        border-left-color: #43e97b;
    }
    
    .bot-card.inactive {
        border-left-color: #f5576c;
    }
    
    .bot-avatar {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    
    .floating-action-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        z-index: 100;
        transition: all 0.3s ease;
    }
    
    .floating-action-btn:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    }
    
    .glow-effect {
        position: relative;
    }
    
    .glow-effect::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(90deg, 
            rgba(255,255,255,0) 0%, 
            rgba(255,255,255,0.3) 50%, 
            rgba(255,255,255,0) 100%);
        animation: glow-animation 2.5s infinite;
        z-index: 1;
    }
    
    @keyframes glow-animation {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid"> 
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">All Assistants</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Evalia</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item"><a href="#">Pages</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item active">Assistants</li>
                    </ol>
                </div>                                
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->                   
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
                                    <h3 class="mb-0"><a href="{{ route('user.bot.overview') }}">Support Assistant</a></h3>
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
       
</div>
@endsection

@push('scripts')

@endpush