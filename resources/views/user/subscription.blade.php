@extends('user.layouts.app')
@push('styles')
  
    <style>
    .discount-bar {
        background: linear-gradient(135deg, #6c63ff 0%, #4d44db 100%);
        border: none;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(255, 154, 158, 0.2);
        margin-bottom: 1.5rem;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    
    .discount-savings {
        position: relative;
        display: inline-block;
        margin-right: 15px;
    }
    
    .discount-savings-badge {
        position: absolute;
        top: -15px;
        right: 0;
        transform: translateX(50%) rotate(-15deg);
        background: white;
        color: #dc3545;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        white-space: nowrap;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        min-width: 60px;
        text-align: center;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .discount-content {
            text-align: center !important;
            margin-bottom: 1rem;
        }
        .discount-cta {
            justify-content: center !important;
        }
        .discount-savings {
            margin-right: 0;
            margin-bottom: 15px;
        }
        .discount-savings-badge {
            right: 50%;
            transform: translateX(50%) rotate(-15deg);
        }
    }

    /* Rest of your existing CSS remains the same */
    .discount-bar:before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
    }
    
    .discount-bar:hover {
        animation: none;
        transform: scale(1.01);
    }
    
    .discount-btn {
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background:  #120d5e ;

        color: #fff;
    }
    
    .discount-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        
    }
    
    .discount-emoji {
        position: absolute;
        opacity: 0.3;
        z-index: 0;
    }
</style>
  <style>
      .divider {
          width: 100%;
          height: 3px;
          background: linear-gradient(90deg, rgba(108,99,255,0) 0%, rgba(108,99,255,0.5) 50%, rgba(108,99,255,0) 100%);
          position: relative;
          overflow: hidden;
      }
      
      .divider::after {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 100%;
          background: linear-gradient(90deg, 
                        rgba(255,255,255,0) 0%, 
                        rgba(255,255,255,0.8) 50%, 
                        rgba(255,255,255,0) 100%);
          animation: shine 3s infinite;
      }
      
      @keyframes shine {
          0% { transform: translateX(-100%); }
          100% { transform: translateX(100%); }
      }
  </style>
  <style>
        /* Pricing Card Styles */
        .pricing-card {
            padding: 1rem;
            border-radius: 8px;
            background: white;
            height: 100%;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .pricing-card.popular {
            border: 2px solid #6c63ff;
            background: linear-gradient(180deg, rgba(248,249,254,1) 0%, rgba(240,242,255,1) 100%);
        }
        
        .plan-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        
        .plan-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1rem;
        }
        
        .plan-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #6c63ff;
            margin: 0.75rem 0;
        }
        
        .plan-price .price-period {
            font-size: 0.9rem;
            font-weight: 500;
            color: #64748b;
        }
        
        .most-popular-badge {
            position: absolute;
            top: -10px;
            right: 15px;
            background: #6c63ff;
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(108, 99, 255, 0.2);
        }
        
        .btn-plan {
            width: 100%;
            padding: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #64748b;
        }
        
        .btn-dark {
            background: #334155;
            color: white;
        }
        
        .btn-purple {
            background: #6c63ff;
            color: white;
        }
        
        .btn-plan:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .btn-purple:hover {
            background: #5a52d4;
            box-shadow: 0 3px 10px rgba(108, 99, 255, 0.3);
        }
        </style>
@endpush

@section('content')
<div class="container-fluid"> 
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Pricing</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item"><a href="#">Pages</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item active">Pricing</li>
                    </ol>
                </div>                                
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->                   

<div class="row ">
    <div class="col-12">
        <div class="discount-bar p-4">
            <!-- Decorative emojis -->
            <div class="discount-emoji" style="top: 10px; right: 20px; font-size: 1.5rem;">✨</div>
            {{--  <div class="discount-emoji" style="bottom: 10px; left: 20px; font-size: 1.5rem;">🔥</div>  --}}
            
            <div class="row align-items-center">
                <div class="col-md-8 discount-content">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="font-size: 2rem;">🎉</div>
                        <div>
                            <h2 class="text-white mb-1">Limited Time Offer!</h2>
                            <p class="text-white mb-0">Upgrade to <strong>Pro Plan</strong> and get <strong>40% OFF</strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-end discount-cta">
                        {{--  <div class="discount-savings">
                            <div class="text-white text-decoration-line-through" style="font-size: 1.25rem;">$999/year</div>
                            <div class="discount-savings-badge">SAVE $400</div>
                        </div>  --}}
                        <a href="#" class="btn btn-white btn-lg rounded-pill px-4 discount-btn ">
                            40% OFF !!
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row justify-content-center">
  
        <div class="col-12">
          <div class="divider" style="width: 100%; height: 3px; background: linear-gradient(90deg, rgba(108,99,255,0) 0%, rgba(108,99,255,0.5) 50%, rgba(108,99,255,0) 100%);"></div>
        </div>
        <!-- Feature Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>
                        <h2 class="text-center fw-bold mb-2">Pick your plan</h2>
                        <p class="text-center text-muted">Select the plan that best matches your goals</p>
                    </th>
                    <th>
                        <div class="pricing-card position-relative">
                            <div class="plan-title">Free</div>
                            <div class="plan-subtitle">Exploring Creative AI</div>
                            <div class="plan-price">SAR 0<span class="price-period">/month</span></div>
                            <button class="btn btn-plan btn-secondary">Current Plan</button>
                        </div>
                    </th>
                    <th>
                        <div class="pricing-card position-relative">
                            <div class="plan-title">Starter</div>
                            <div class="plan-subtitle">For small teams</div>
                            <div class="plan-price">SAR 299<span class="price-period">/month</span></div>
                            <button class="btn btn-plan btn-dark">Upgrade</button>
                        </div>
                    </th>
                    <th>
                        <div class="pricing-card popular position-relative">
                            <div class="most-popular-badge">Most Popular</div>
                            <div class="plan-title">Business</div>
                            <div class="plan-subtitle">Growing teams</div>
                            <div class="plan-price">SAR 999<span class="price-period">/month</span></div>
                            <button class="btn btn-plan btn-purple">Upgrade</button>
                        </div>
                    </th>
                    <th>
                        <div class="pricing-card position-relative">
                            <div class="plan-title">Premium</div>
                            <div class="plan-subtitle">Advanced features</div>
                            <div class="plan-price">SAR 2,999<span class="price-period">/month</span></div>
                            <button class="btn btn-plan btn-dark">Upgrade</button>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>No. of Text Responses</td>
                    <td>100</td>
                    <td>2,000</td>
                    <td>7,500</td>
                    <td>30,000</td>
                </tr>
                <tr>
                    <td>No. of Voice Minutes</td>
                    <td>50</td><td>500</td>
                    <td>1,500</td>
                    <td>5,500</td>
                </tr>
                <tr>
                    <td>No. of AI Agents</td>
                    <td>1</td>
                    <td>2</td>
                    <td>5</td>
                    <td>15</td>
                </tr>
                <tr>
                    <td>Voice Conversations Recordings</td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                </tr>
                <tr>
                    <td>Integrations</td>
                    <td>Basic</td>
                    <td>Basic</td>
                    <td>Advanced</td>
                    <td>Advanced</td>
                </tr>
                <tr>
                    <td>White Labeled</td>
                    <td>200</td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                </tr>
                <tr>
                    <td>Analytics & Reporting</td>
                    <td>75</td>
                    <td>Basic</td>
                    <td>Advanced</td>
                    <td>Advanced</td>
                </tr>
                <tr>
                    <td>API Access</td>
                    <td>5</td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                </tr>
                <tr>
                    <td>Onboarding & Training</td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/right.png" alt=""></td>
                </tr>
                <tr>
                    <td>SLAs</td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                    <td><img style="height:20px;" src="{{ asset('/') }}assets/images/cross.png" alt=""></td>
                </tr>
            </tbody>
            </table>
        </div>
                                                                
    </div>        
</div>
@endsection

@push('scripts')

@endpush