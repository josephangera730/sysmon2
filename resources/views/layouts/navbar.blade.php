<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand" href="{{ url('/') }}">
       <img src="{{ asset('logo-sidebar.png') }}" alt="Logo" height="40" class="me-2">
     
      
    </a>

    <!-- Tombol toggle (untuk responsive mobile) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

  <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarMenu">
      <!-- Menu utama di kiri -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <!-- Dashboard -->
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
              href="{{ route('dashboard') }}">
                Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}" 
              href="{{ route('report.index') }}">
                Laporan OPD
            </a>
          </li>
        
          <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle {{ request()->routeIs('jenisperangkat.*') || request()->routeIs('perangkat.*') || request()->routeIs('logperangkat.*') ? 'active' : '' }}" 
     href="#" 
     id="perangkatDropdown" 
     role="button" 
     data-bs-toggle="dropdown" 
     aria-expanded="false">
    Manajemen Perangkat
  </a>
  <ul class="dropdown-menu" aria-labelledby="perangkatDropdown">
    <li>
      <a class="dropdown-item {{ request()->routeIs('jenisperangkat.*') ? 'active' : '' }}" 
         href="{{ route('jenisperangkat.index') }}">
        Jenis Perangkat
      </a>
    </li>
    <li>
      <a class="dropdown-item {{ request()->routeIs('perangkat.*') ? 'active' : '' }}" 
         href="{{ route('perangkat.index') }}">
        Perangkat
      </a>
    </li>
    <li>
      <a class="dropdown-item {{ request()->routeIs('logperangkat.*') ? 'active' : '' }}" 
         href="{{ route('logperangkat.index') }}">
        Log Perangkat
      </a>
    </li>
  </ul>
</li>


          
      @can('isSuperadmin')
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle 
     {{ request()->routeIs('user.*') || request()->routeIs('pengaturansistem.*') ? 'active' : '' }}" 
     href="#" 
     id="administrasiDropdown" 
     role="button" 
     data-bs-toggle="dropdown" 
     aria-expanded="false">
    Manajemen Sistem
  </a>
  <ul class="dropdown-menu" aria-labelledby="administrasiDropdown">
    <li>
      <a class="dropdown-item {{ request()->routeIs('user.*') ? 'active' : '' }}" 
         href="{{ route('user.index') }}">
        Kelola Admin
      </a>
    </li>
    <li>
      <a class="dropdown-item {{ request()->routeIs('pengaturansistem.*') ? 'active' : '' }}" 
         href="{{ route('pengaturansistem.index') }}">
        Pengaturan Sistem
      </a>
    </li>
  </ul>
</li>
@endcan





          {{-- Contoh dropdown --}}
          {{--
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ request()->routeIs('laporan.*') ? 'active' : '' }}" 
              href="#" role="button" data-bs-toggle="dropdown">
                Laporan
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="">Laporan Perangkat</a></li>
              <li><a class="dropdown-item" href="">Laporan Barang</a></li>
            </ul>
          </li>
          --}}
      </ul>

      <!-- User profile di kanan -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item topbar-user dropdown hidden-caret">
          <a
            class="dropdown-toggle profile-pic"
            data-bs-toggle="dropdown"
            href="#"
            aria-expanded="false"
          >
            <div class="avatar-sm">
              <img
                src="{{ asset('assets/img/defaultfoto.png') }}"
                alt="User Avatar"
                class="avatar-img rounded-circle"
              />
            </div>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn dropdown-menu-end">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    <img
                      src="{{ asset('assets/img/defaultfoto.png') }}"
                      alt="Profile"
                      class="avatar-img rounded"
                    />
                  </div>
                  <div class="u-text">
                    <h4>{{ Auth::user()->username }}</h4>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#myProfileModal">My Profile</button>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Ganti Password</button>
                <div class="dropdown-divider"></div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="dropdown-item">Logout</button>
                </form>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
 </div>
</nav>