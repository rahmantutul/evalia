    <div class="startbar d-print-none">
        <div class="brand d-flex justify-content-center align-items-center">
            <a href="{{ route('home') }}" class="logo text-center">
                <span>
                    <img src="{{ asset('/') }}assets/images/normal.png" alt="logo-small" class="logo-sm mx-auto">
                </span>
                <span>
                    <img src="{{ asset('/') }}assets/images/crtvai.png" height="100" alt="logo-large" class="logo-lg logo-light mx-auto">
                    <img src="{{ asset('/') }}assets/images/crtvai.png" height="100" alt="logo-large" class="logo-lg logo-dark mx-auto">
                </span>
            </a>
        </div>

        <div class="startbar-menu" >
            <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
                <div class="d-flex align-items-start flex-column w-100">
                    <!-- Navigation -->
                    <ul class="navbar-nav mb-auto w-100">
                        <li class="nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('admins.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">
                                <i class="fas fa-user menu-icon"></i>
                                <span>Admins</span>
                            </a>
                        </li>
                       <li class="nav-item {{ Route::is('plans.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('plans.*') ? 'active' : '' }}" href="{{ route('admin.plans.index') }}">
                                <i class="fas fa-tags menu-icon"></i>
                                <span>Price Plan</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('features.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('features.*') ? 'active' : '' }}" href="{{ route('admin.features.index') }}">
                                <i class="fas fa-star menu-icon"></i>
                                <span>Features</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('admin.settings.edit') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('admin.settings.edit') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
                                <i class="fas fa-cogs menu-icon"></i>
                                <span>System Configuration</span>
                            </a>
                        </li>
                    </ul><!--end navbar-nav--->
                    
                </div>
            </div><!--end startbar-collapse-->
        </div>   
    </div>