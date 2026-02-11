    <div class="startbar d-print-none">
        <div class="brand d-flex justify-content-center align-items-center">
            <a href="{{ route('agent.dashboard') }}" class="logo text-center">
                <span>
                    <img src="{{ asset('/') }}assets/images/preview.png" alt="logo-small" class="logo-sm mx-auto">
                </span>
                <span>
                    <img src="{{ asset('/') }}assets/images/logo.png" height="100" alt="logo-large" class="logo-lg logo-light mx-auto">
                    <img src="{{ asset('/') }}assets/images/logo.png" height="100" alt="logo-large" class="logo-lg logo-dark mx-auto">
                </span>
            </a>
        </div>

        <div class="startbar-menu">
            <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
                <div class="d-flex align-items-start flex-column w-100">
                    <!-- Navigation -->
                    <ul class="navbar-nav mb-auto w-100">
                        <li class="nav-item {{ Route::is('agent.dashboard') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('agent.dashboard') ? 'active' : '' }}" href="{{ route('agent.dashboard') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('agent.coaching') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('agent.coaching') ? 'active' : '' }}" href="{{ route('agent.coaching') }}">
                                <i class="fas fa-graduation-cap menu-icon"></i>
                                <span>Coaching & Growth</span>
                            </a>
                        </li>
                    </ul><!--end navbar-nav--->
                    
                </div>
            </div><!--end startbar-collapse-->
        </div>   
    </div>