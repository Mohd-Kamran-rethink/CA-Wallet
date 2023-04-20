<style>
    .os-scrollbar-horizontal {
        display: none
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <a href="{{url('/dashboard')}}" class="brand-link">
            <img @if (isset($settings)&&$settings->project_logo) src="{{ asset('Data/Project/' . $settings->project_logo) }}"
                @else
                src="https://imgs.search.brave.com/jjizMxNTRgX8Jd1PNu7XXsh0-_jVVpSJF-bVeHWJZ_c/rs:fit:860:900:1/g:ce/aHR0cHM6Ly93d3cu/a2luZHBuZy5jb20v/cGljYy9tLzc4LTc4/NjIwN191c2VyLWF2/YXRhci1wbmctdXNl/ci1hdmF0YXItaWNv/bi1wbmctdHJhbnNw/YXJlbnQucG5n" @endif
                alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{ $settings->project_name ?? 'AdminLTE' }}</span>
        </a>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item  ">
                    <a href="{{ url('/dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if((session('user')->role=='manager')&&session('user')->is_admin=='Yes')
                <li class="nav-item ">
                    <a href="{{ url('/managers') }}"
                        class="nav-link {{ Request::is('managers') || Request::is('managers/add') || Request::is('managers/edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Managers

                        </p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ url('/agents') }}"
                        class="nav-link {{ Request::is('agents') || Request::is('agents/add') || Request::is('agents/edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Agents

                        </p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ url('/sources') }}"
                        class="nav-link {{ Request::is('sources') || Request::is('sources/add') || Request::is('sources/edit') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-th-list"></i>
                        <p>
                            Sources

                        </p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="{{ url('/statuses') }}"
                        class="nav-link {{ Request::is('statuses') || Request::is('statuses/add') || Request::is('statuses/edit') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-th-list"></i>
                        <p>
                            Status
                        </p>
                    </a>
                </li>
                @endif

                <li class="nav-item ">
                    <a href="{{ url('/leads') }}"
                        class="nav-link {{ Request::is('leads') || Request::is('leads/add') || Request::is('leads/edit') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-tasks"></i>
                        <p>
                            Leads

                        </p>
                    </a>
                </li>
               
            </ul>


        </nav>
    </div>
</aside>
