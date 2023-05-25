<style>
    .os-scrollbar-horizontal {
        display: none
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <a href="{{ url('/dashboard') }}" class="brand-link">
            <img @if (isset($settings) && $settings->project_logo) src="{{ asset('Data/Project/' . $settings->project_logo) }}"
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
                @if ($userData->role == 'manager')
                    <li class="nav-item ">
                        <a href="{{ url('/agents') }}"
                            class="nav-link {{ Request::is('agents') || Request::is('agents/add') || Request::is('agents/edit') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Agents

                            </p>
                        </a>
                    </li>
                    @if ($userData->is_admin == 'Yes')
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
                            <a href="{{ url('/sources') }}"
                                class="nav-link {{ Request::is('sources') || Request::is('sources/add') || Request::is('sources/edit') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-folder "></i>
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
                @endif
{{-- main leads --}}
                <li class="nav-item ">
                    <a href="{{ url('/leads') }}"
                        class="nav-link {{ Request::is('leads') || Request::is('leads/add') || Request::is('leads/edit') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-tasks"></i>
                        <p>
                            Leads

                        </p>
                    </a>
                </li>
                @if ($userData->role == 'manager')
                {{-- duplicate leads --}}
                <li class="nav-item ">
                    <a href="{{ url('/leads/duplicate') }}"
                        class="nav-link {{ Request::is('leads/duplicate')? 'active' : '' }}">
                        <i class="nav-icon  fa fa-clone"></i>
                        <p>
                           Duplicate Leads

                        </p>
                    </a>
                </li>
                @endif
                {{-- followup  --}}
                <li
                    class="nav-item {{ Request::is('leads/demoid') || Request::is('leads/callback') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ Request::is('leads/demoid') || Request::is('leads/callback') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-th"></i>
                        <p>
                            Follow Up
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="display: {{ Request::is('leads/demoid') || Request::is('leads/callback') ? 'block' : 'none' }}">
                        <li class="nav-item">
                            <a href="{{ url('leads/demoid') }}"
                                class="nav-link {{ Request::is('leads/demoid') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-table"></i>
                                <p>
                                    Demo Id
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/leads/idcreated') }}"
                                class="nav-link {{ Request::is('reports/leads') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-table"></i>
                                <p>
                                    ID Created
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('leads/callback') }}"
                                class="nav-link {{ Request::is('reports/leads') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-table"></i>
                                <p>
                                    Call back
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @if ($userData->id == 1)
                    <li class="nav-item ">
                        <a href="{{ url('/leads/approval') }}"
                            class="nav-link {{ Request::is('leads/approval') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-th"></i>
                            <p>
                                Leads For Approval

                            </p>
                        </a>
                    </li>
                @endif
                {{-- clients --}}
                @if ($userData->role == 'agent')
                    {{-- <li class="nav-item ">
                        <a href="{{ url('/clients') }}"
                            class="nav-link {{ Request::is('clients') || Request::is('clients/add') || Request::is('clients/edit') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                My Clients

                            </p>
                        </a>
                    </li> --}}
                @endif
                <li class="nav-item ">
                    <a href="{{ url('/attendance') }}"
                        class="nav-link {{ Request::is('attendance') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-calendar"></i>
                        <p>
                            Attendance
                        </p>
                    </a>
                </li>

                {{-- reports --}}
                @if ($userData->role == 'manager')
                    <li
                        class="nav-item {{ Request::is('reports/leads') || Request::is('reports/deposits') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::is('reports/leads') || Request::is('reports/deposits') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Reports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display: {{ Request::is('reports/leads') || Request::is('reports/deposits') ? 'block' : 'none' }}">
                            <li class="nav-item">
                                <a href="{{ url('/reports/leads') }}"
                                    class="nav-link {{ Request::is('reports/leads') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-table"></i>

                                    <p>
                                        Leads
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="{{ url('/reports/deposits') }}"
                                    class="nav-link {{ Request::is('reports/deposits') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-table"></i>

                                    <p>
                                        Deposits
                                    </p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif
            </ul>


        </nav>
    </div>
</aside>
