<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Dashboard | Creative AI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="creative ai" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/normal.png">
    
    <!-- CSS Files -->
    <link href="{{ asset('/') }}assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}assets/libs/mobius1-selectr/selectr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}assets/libs/huebee/huebee.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/') }}assets/libs/vanillajs-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css" />
    
    @stack('styles')
</head>

<body>
    @include('admin.layouts.topbar')
    @include('admin.layouts.sidebar')

    <div class="startbar-overlay d-print-none"></div>

    <div class="page-wrapper">
        <div class="page-content">
            @yield('content')
            @include('admin.layouts.footer')
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>
    <script src="{{ asset('/') }}assets/js/pages/index.init.js"></script>
    <script src="{{ asset('/') }}assets/libs/mobius1-selectr/selectr.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/huebee/huebee.pkgd.min.js"></script>
    <script src="{{ asset('/') }}assets/libs/vanillajs-datepicker/js/datepicker-full.min.js"></script>
    <script src="{{ asset('/') }}assets/js/moment.js"></script>
    <script src="{{ asset('/') }}assets/libs/imask/imask.min.js"></script>
    <script src="{{ asset('/') }}assets/js/pages/forms-advanced.js"></script>
    <script src="{{ asset('/') }}assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: @json(session('success')),
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json(session('error')),
                timer: 4000,
                showConfirmButton: true,
                position: 'center'
            });
        </script>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const switcher = document.querySelector('.bot-switcher');
        const btn = document.querySelector('.bot-switcher-btn');
        
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            switcher.classList.toggle('active');
        });
        
        document.addEventListen~er('click', function() {
            switcher.classList.remove('active');
        });
    });
    </script>

    @stack('scripts')
</body>
</html>