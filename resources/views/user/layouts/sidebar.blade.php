<div class="startbar d-print-none">
    <div class="brand d-flex justify-content-center align-items-center">
        <a href="{{ route('user.home') }}" class="logo text-center">
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
                    <!-- Evalia Navigation -->
                    <!-- Evalia Navigation -->
                        
                        @if(auth()->user()->isAgent())
                        <li class="nav-item {{ Route::is('agent.dashboard') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('agent.dashboard') ? 'active' : '' }}" href="{{ route('agent.dashboard') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Agent Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('agent.coaching') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('agent.coaching') ? 'active' : '' }}" href="{{ route('agent.coaching') }}">
                                <i class="fas fa-graduation-cap menu-icon"></i>
                                <span>My Coaching</span>
                            </a>
                        </li>
                        @else
                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor() || auth()->user()->isStaff())
                        <li class="nav-item {{ Route::is('user.home') || Route::is('supervisor.dashboard') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.home') ? 'active' : '' }}" href="{{ route('user.home') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>{{ auth()->user()->isSupervisor() ? 'Supervisor Dashboard' : 'Dashboard' }}</span>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                        
                        <li class="nav-item {{ Route::is('roles.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <i class="fas fa-lock menu-icon"></i>
                                <span>Roles & Permissions</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('users.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-user-check menu-icon"></i>
                                <span>Users</span>
                            </a>
                        </li>

                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                        <li class="nav-item {{ Route::is('user.company.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.company.*') ? 'active' : '' }}" href="{{ route('user.company.list') }}">
                                <i class="icofont-bank-alt menu-icon"></i>
                                <span>Companies</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Route::is('user.agents.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.agents.index.*') ? 'active' : '' }}" href="{{ route('user.agents.index') }}">
                                <i class="fas fa-headset menu-icon"></i>
                                <span>Agents</span>
                            </a>
                        </li>
                        @endif
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="{{ asset('documentation.html') }}" target="_blank">
                                <i class="fas fa-book menu-icon"></i>
                                <span>Documentation</span>
                            </a>
                        </li>
                </ul>
            </div>
        </div>
    </div>   
</div>