<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <a href="index3.html" class="brand-link">
            <img src="https://imgs.search.brave.com/jjizMxNTRgX8Jd1PNu7XXsh0-_jVVpSJF-bVeHWJZ_c/rs:fit:860:900:1/g:ce/aHR0cHM6Ly93d3cu/a2luZHBuZy5jb20v/cGljYy9tLzc4LTc4/NjIwN191c2VyLWF2/YXRhci1wbmctdXNl/ci1hdmF0YXItaWNv/bi1wbmctdHJhbnNw/YXJlbnQucG5n"
                alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">{{ $settings->project_name ?? 'AdminLTE' }}</span>
        </a>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item  ">
                    <a href="{{ url('admin/dashboard') }}"
                        class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li
                    class="nav-item {{ Request::is('managers') || Request::is('managers/add') || Request::is('managers/edit') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Managers
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="display: {{ Request::is('managers') || Request::is('managers/add') || Request::is('managers/edit') ? 'block' : '' }}">
                        <li class="nav-item active">
                            <a href="{{ url('/managers/add') }}"
                                class="nav-link  {{ Request::is('managers/add') ? 'active' : '' }}">
                                <i class="far fa fa-user-plus nav-icon"></i>
                                <p>Add Manager</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/managers') }}"
                                class="nav-link {{ Request::is('managers') ? 'active' : '' }}">
                                <i class="far fa fa-table nav-icon"></i>
                                <p>All Managers</p>
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>

        </nav>
    </div>
</aside>
