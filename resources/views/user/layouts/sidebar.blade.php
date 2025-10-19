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

        <div class="startbar-menu" >
            <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
                <div class="d-flex align-items-start flex-column w-100">
                    <!-- Navigation -->
                    <ul class="navbar-nav mb-auto w-100">
                        <li class="nav-item {{ Route::is('user.home') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.home') ? 'active' : '' }}" href="{{ route('user.home') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('users.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-user-check menu-icon"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('roles.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                <i class="fas fa-lock menu-icon"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('user.group_data.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.group_data.*') ? 'active' : '' }}" href="{{ route('user.group_data.list') }}">
                                <i class="iconoir-community menu-icon"></i>
                                <span>Group</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('user.knowledgeBase.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.knowledgeBase.*') ? 'active' : '' }}" href="{{ route('user.knowledgeBase.list') }}">
                                <i class="fas fa-cloud menu-icon"></i>
                                <span>Knowledge Base</span>
                            </a>
                        </li>
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
                        {{--  <li class="nav-item {{ Route::is('user.telephonyAccounts.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.telephonyAccounts.*') ? 'active' : '' }}" href="{{ route('user.telephonyAccounts.index') }}">
                                <i class="fas fa-mobile-alt menu-icon"></i>
                                <span>Telephony Accounts</span>
                            </a>
                        </li>  --}}
                    </ul>
                </div>
            </div>
        </div>   
    </div>