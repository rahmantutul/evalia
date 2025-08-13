@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-6 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">                      
                            <h4 class="card-title">Recent Analyses</h4>                      
                        </div><!--end col-->
                        <div class="col-auto"> 
                            <div class="dropdown">
                                <a href="#" class="btn bt btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icofont-calendar fs-5 me-1"></i> This Year<i class="las la-angle-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Today</a>
                                    <a class="dropdown-item" href="#">Last Week</a>
                                    <a class="dropdown-item" href="#">Last Month</a>
                                    <a class="dropdown-item" href="#">This Year</a>
                                </div>
                            </div>               
                        </div><!--end col-->
                    </div>  <!--end row-->                                  
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">ID</th>
                                    <th class="border-top-0">Status</th>
                                    <th class="border-top-0">Entry Date</th>
                                    <th class="border-top-0">Action</th>
                                </tr><!--end tr-->
                            </thead>
                            <tbody>
                                <tr>                                                        
                                    <td>
                                        <h6 class="m-0">3fcd1431fa6a4654899051a00a88f64</h6>
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary px-2">Active</span></td>
                                    <td>08/10/2025, 08:31:40</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary rounded-circle me-1" title="View Details">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning rounded-circle" title="Re-evaluate">
                                            <i class="las la-sync-alt"></i>
                                        </a>
                                    </td>
                                </tr>                        
                            </tbody>
                        </table> <!--end table-->                                               
                    </div><!--end /div-->
                </div><!--end card-body--> 
            </div>
        </div>       
    </div>
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
