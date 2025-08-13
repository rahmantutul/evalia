@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">                      
                            <h4 class="card-title">Company List</h4>                      
                        </div><!--end col-->
                    </div>  <!--end row-->                                  
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                   <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>Action <span><a href="{{ route('user.company.create') }}" class="btn btn-sm btn-primary d-block float-end">+ Create New</a></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                    <tr>
                                        <td>{{ $company['id'] }}</td>
                                        <td>{{ $company['name'] }}</td>
                                        <td>
                                            <a href="{{ route('user.company.view',$company['id']) }}" class="btn btn-sm btn-primary">
                                               <i class="fas fa-eye"></i> View Details
                                            </a>
                                            <form action="{{ route('user.company.delete', $company['id']) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
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
