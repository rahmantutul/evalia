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
                        <li class="nav-item {{ Route::is('home') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('user.bot.*') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.bot.*') ? 'active' : '' }}" href="{{ route('user.bot.list') }}">
                                <i class="fas fa-bolt menu-icon"></i>
                                <span>Your assistants</span>
                            </a>
                        </li> 
                         <li class="nav-item {{ Route::is('user.support') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.support') ? 'active' : '' }}" href="{{ route('user.support') }}">
                                <i class="fas fa-headset menu-icon"></i>
                                <span>Contact Support</span>
                            </a>
                        </li>
                        <br>
                        <li class="nav-item {{ Route::is('user.bot.*') ? 'active' : '' }}">
                             @if(Route::is('user.bot.overview') || Route::is('user.bot.create') || Route::is('user.bot.inbox'))
                                <ul class="" style="list-style: none; background: #fff;">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('user.bot.overview') }}">
                                            <i class="fas fa-chart-pie menu-icon"></i>
                                            <span>Statistics</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('user.bot.inbox') }}">
                                            <i class="fas fa-inbox menu-icon"></i>
                                            <span>Inbox</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('user.bot.create') }}">
                                            <i class="fas fa-cog menu-icon"></i>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                </ul>
                            @endif
                        </li>
                        
                    </ul>

                    <div class="update-msg text-center d-block"  style="position: absolute; bottom: 50px;"> 
                        <div class="d-flex justify-content-center align-items-center thumb-lg update-icon-box  rounded-circle mx-auto">
                            <img src="{{ asset('/') }}assets/images/extra/P9aqUeKFvc.gif" alt="" class="d-inline-block me-1 rounded" height="50">
                        </div>                   
                        <h5 class="mt-1">Creative AI</h5>
                        <p class="mb-3 text-muted">Your artificial voice agent.</p>
                        <a href="{{ route('user.subscription') }}" class="btn bg-black text-white shadow-sm rounded-pill">Upgrade your plan 
                            <img src="{{ asset('/') }}assets/images/extra/giphy.gif" style="height: 25px; margin-left: 8px; display: inline-block; vertical-align: middle;">
                        </a>
                    </div>
                </div>
            </div>
        </div>   
    </div>