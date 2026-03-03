<div class="startbar d-print-none">
    <div class="brand d-flex justify-content-center align-items-center">
        <a href="{{ route('supervisor.dashboard') }}" class="logo text-center">
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
                    @if ((session('active_product') ?? 1) == 1)
                        @php
                            $permissions = session('permissions', []);
                            $hasManageSystem = collect($permissions)->contains('name', 'manage_system');
                        @endphp
                         <li class="nav-item {{ Request::url() == route('supervisor.dashboard') && !Request::has('sect') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::url() == route('supervisor.dashboard') && !Request::has('sect') ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}">
                                <i class="iconoir-report-columns menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::routeIs('user.group_data.list') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::routeIs('user.group_data.list') ? 'active' : '' }}" href="{{ route('user.group_data.list') }}">
                                <i class="iconoir-community menu-icon"></i>
                                <span>Groups</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::routeIs('user.company.list') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::routeIs('user.company.list') ? 'active' : '' }}" href="{{ route('user.company.list') }}">
                                <i class="icofont-bank-alt menu-icon"></i>
                                <span>Companies</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::routeIs('user.agents.index') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::routeIs('user.agents.index') ? 'active' : '' }}" href="{{ route('user.agents.index') }}">
                                <i class="fas fa-headset menu-icon"></i>
                                <span>Agents</span>
                            </a>
                        </li>
                    <!-- Kayan Navigation -->
                    @elseif(session('active_product') == 2)

                        <li class="nav-item {{ Request::url() == route('supervisor.dashboard') && !Request::has('sect') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::url() == route('supervisor.dashboard') && !Request::has('sect') ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}">
                                <i class="fas fa-home menu-icon"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'jobs' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'jobs' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=jobs">
                                <i class="fas fa-tasks menu-icon"></i>
                                <span class="menu-title">Jobs</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'tts' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'tts' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=tts">
                                <i class="fas fa-volume-up menu-icon"></i>
                                <span class="menu-title">Text to Speech</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'trans' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'trans' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=trans">
                                <i class="fas fa-language menu-icon"></i>
                                <span class="menu-title">Translation</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'voice' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'voice' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=voice">
                                <i class="fas fa-headset menu-icon"></i>
                                <span class="menu-title">Voice Agents</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'conv' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'conv' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=conv">
                                <i class="fas fa-exchange-alt menu-icon"></i>
                                <span class="menu-title">Voice Conversion</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::get('sect') == 'ai' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'ai' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=ai">
                                <i class="fas fa-robot menu-icon"></i>
                                <span class="menu-title">AI Content</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'chat' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'chat' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=chat">
                                <i class="fas fa-phone menu-icon"></i>
                                <span class="menu-title">Conversations</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::get('sect') == 'settings' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'settings' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=settings">
                                <i class="fas fa-cog menu-icon"></i>
                                <span class="menu-title">Project Settings</span>
                            </a>
                        </li>

                    @elseif(session('active_product') == 3)
                        <li class="nav-item {{ Request::get('sect') == 'bots' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'bots' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=bots">
                                <i class="fas fa-robot menu-icon"></i>
                                <span>Chatbot Bots</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'cchat' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'cchat' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=cchat">
                                <i class="fas fa-comments menu-icon"></i>
                                <span>Chat Conversations</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::get('sect') == 'train' ? 'active' : '' }}">
                            <a class="nav-link {{ Request::get('sect') == 'train' ? 'active' : '' }}" href="{{ route('supervisor.dashboard') }}?sect=train">
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

