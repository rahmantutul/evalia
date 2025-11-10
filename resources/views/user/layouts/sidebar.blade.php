<div class="startbar d-print-none">
    <div class="brand d-flex justify-content-center align-items-center">
        <a href="{{ route('user.home') }}" class="logo text-center">
            <span>
                <img src="{{ asset('/') }}assets/images/preview.png" alt="logo-small" class="logo-sm mx-auto">
            </span>
            <span>
                <img src="{{ asset('/') }}assets/images/PNG-BLACK-BUJbO_jP.png" height="100" alt="logo-large" class="logo-lg logo-light mx-auto">
                <img src="{{ asset('/') }}assets/images/PNG-BLACK-BUJbO_jP.png" height="100" alt="logo-large" class="logo-lg logo-dark mx-auto">
            </span>
        </a>
    </div>

    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <!-- Navigation -->
                <ul class="navbar-nav mb-auto w-100">
                    <!-- Evalia Navigation -->
                    @if ((session('active_product') ?? 1) == 1)
                        <li class="nav-item {{ Route::is('user.home') ? 'active' : '' }}">
                            <a class="nav-link {{ Route::is('user.home') ? 'active' : '' }}" href="{{ route('user.home') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                         <!-- Common Navigation Items -->
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
                    <!-- Kayan Navigation -->
                    @elseif(session('active_product') == 2)
                    <!-- Hamsa AI Services -->
                    <li class="nav-item {{ Request::is('hamsa/dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/dashboard') }}">
                            <i class="fas fa-home menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('hamsa/jobs*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/jobs') }}">
                            <i class="fas fa-tasks menu-icon"></i>
                            <span class="menu-title">Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('hamsa/tts*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/tts') }}">
                            <i class="fas fa-volume-up menu-icon"></i>
                            <span class="menu-title">Text to Speech</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('hamsa/translate*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/translate') }}">
                            <i class="fas fa-language menu-icon"></i>
                            <span class="menu-title">Translation</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('hamsa/voice-agents*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/voice-agents') }}">
                            <i class="fas fa-headset menu-icon"></i>
                            <span class="menu-title">Voice Agents</span>
                        </a>
                    </li>
                    {{--  <li class="nav-item {{ Request::is('hamsa/transcribe*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/transcribe') }}">
                            <i class="fas fa-microphone-alt menu-icon"></i>
                            <span class="menu-title">Speech to Text</span>
                        </a>
                    </li>  --}}
                  



                    <li class="nav-item {{ Request::is('hamsa/sts*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/sts') }}">
                            <i class="fas fa-exchange-alt menu-icon"></i>
                            <span class="menu-title">Voice Conversion</span>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('hamsa/ai*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/ai/generate') }}">
                            <i class="fas fa-robot menu-icon"></i>
                            <span class="menu-title">AI Content</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::is('hamsa/conversations*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/conversations') }}">
                            <i class="fas fa-phone menu-icon"></i>
                            <span class="menu-title">Conversations</span>
                        </a>
                    </li>

                    {{--  <li class="nav-item {{ Request::is('hamsa/usage*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/usage') }}">
                            <i class="fas fa-chart-bar menu-icon"></i>
                            <span class="menu-title">Usage Analytics</span>
                        </a>
                    </li>  --}}

                    <li class="nav-item {{ Request::is('hamsa/project*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/hamsa/project') }}">
                            <i class="fas fa-cog menu-icon"></i>
                            <span class="menu-title">Project Settings</span>
                        </a>
                    </li>

                    <!-- Chatbot Navigation -->
                    @elseif(session('active_product') == 3)
                        <li class="nav-item {{ Route::is('chatbot.bots') ? 'active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-robot menu-icon"></i>
                                <span>Chatbot Bots</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('chatbot.conversations') ? 'active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-comments menu-icon"></i>
                                <span>Chat Conversations</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('chatbot.training') ? 'active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-brain menu-icon"></i>
                                <span>Bot Training</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>   
</div>