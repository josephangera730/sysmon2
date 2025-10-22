<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Sistem Monitoring Jaringan Internet OPD Kota Pariaman</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('logo-sidebar.png') }}" type="image/x-icon" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" />

    <!-- Font Awesome (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <style>
        #map {
          width: 100%;
          height: 400px; /* kasih tinggi biar peta tampil */
        }

        .leaflet-popup-always {
            background: white;
            border: 1px solid #999;
            border-radius: 5px;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            color: #333;
            box-shadow: 0 3px 8px rgba(0,0,0,0.3);
            white-space: nowrap;
        }

        .btn-dx {
            background-color: #1ABC9C;
            border-color: #1ABC9C;
            color: #ffffff;
        }
        .btn-dx:hover,
        .btn-dx:focus,
        .btn-dx:active {
            background-color: #111524;
            border-color: #111524;
            color: #ffffff;
        }
        .bg-dx {
            background-color: #1ABC9C;
            border-color: #1ABC9C;
            color: #ffffff;
        }
        .nav-link.active {
            background-color: #8e9098 !important;
            border-color: #8e9098 !important;
            color: #ffffff !important;
        }
        /* Active item lebih soft */
        .dropdown-menu .dropdown-item.active {
            background-color: #cfd0d4 !important;
            border-color: #cfd0d4 !important;
            color: #ffffff !important;
        }

    </style>
</head>

<body>

<!-- Navbar -->
@include('layouts.navbar')

    <div class="card p-5 m-5">
        @yield('main')

    </div>

        <!-- Modal My Profile -->
<div class="modal fade" id="myProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">My Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Isi profil user, misal nama, email, dsb -->
        <p><strong>Username:</strong> {{ auth()->user()->username }}</p>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        <p><strong>Role:</strong> {{ auth()->user()->role }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('profile.update') }}" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="{{ auth()->user()->username }}" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dx">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ganti Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('profile.password') }}" method="POST">
    @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ganti Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Password Lama</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dx">Simpan</button>
        </div>
      </div>
    </form>

  </div>
</div>


 <!--   Core JS Files   -->
<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

<!-- SweetAlert2 (CDN tetap) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Leaflet JS (CDN tetap) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Geocoder JS (CDN tetap) -->
<script src="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.js"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });

    $(document).ready(function () {
        $("#datatable-aktif").DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: false
        });

        $("#datatable-nonaktif").DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: false
        });
    });
</script>

<script>
    window.Laravel = {
        user: @json(Auth::user()),
    };
</script>

<script src="{{ asset('js/cekperangkat.js') }}"></script>
<script src="{{ asset('js/trigerPing.js') }}"></script>

</body>
</html>
