<div class="sidebar" data-background-color="dark">
    <!-- Sidebar Logo -->
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="/logo-sidebar.png"
                     alt="Logo Aplikasi"
                     class="navbar-brand"
                     height="50px" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
    </div>

    <!-- Sidebar Content -->
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <!-- SUPERADMIN -->
                @can('isSuperadmin')
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Manajemen</h4>
                    </li>


                    <li class="nav-item {{ request()->routeIs('perangkat*') ? 'active' : '' }}">
                        <a href="{{ route('perangkat.index') }}">
                            <i class="fas fa-server"></i>
                            <p>Perangkat</p>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('logperangkat*') ? 'active' : '' }}">
                        <a href="{{ route('logperangkat.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Log Perangkat</p>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('user*') ? 'active' : '' }}">
                        <a href="{{ route('user.index') }}">
                            <i class="fas fa-users-cog"></i>
                            <p>Data User</p>
                        </a>
                    </li>
                @endcan

                <!-- ADMIN -->
                @can('isAdmin')
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Manajemen</h4>
                    </li>

                    <li class="nav-item {{ request()->routeIs('jenisperangkat*') ? 'active' : '' }}">
                        <a href="{{ route('jenisperangkat.index') }}">
                            <i class="fas fa-sitemap"></i>
                            <p>Jenis Perangkat</p>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('perangkat*') ? 'active' : '' }}">
                        <a href="{{ route('perangkat.index') }}">
                            <i class="fas fa-server"></i>
                            <p>Perangkat</p>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('logperangkat*') ? 'active' : '' }}">
                        <a href="{{ route('logperangkat.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Log Perangkat</p>
                        </a>
                    </li>
                @endcan

                <!-- TEKNISI -->
                @can('isTeknisi')
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Menu</h4>
                    </li>

                    <li class="nav-item {{ request()->routeIs('perangkat*') ? 'active' : '' }}">
                        <a href="{{ route('perangkat.index') }}">
                            <i class="fas fa-network-wired"></i>
                            <p>Perangkat</p>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('logperangkat*') ? 'active' : '' }}">
                        <a href="{{ route('logperangkat.index') }}">
                            <i class="fas fa-history"></i>
                            <p>Log Perangkat</p>
                        </a>
                    </li>
                @endcan

            </ul>
        </div>
    </div>
</div>
