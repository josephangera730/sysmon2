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

    <link
      rel="icon"
      href="logo-sidebar.png"
      type="image/x-icon"
    />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    
    <!-- Leaflet Geocoder CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />



    

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
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
        #card-active .card-header {
            background-color: #1ABC9C;
            border-color: #1ABC9C;
            color: #ffffff;
        }

        

  
    </style>
    
  </head>
<body>
 

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3">
  <div class="container-fluid">
   <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
      <img src="{{ asset('logo-sidebar.png') }}" alt="Logo" height="40" class="me-2">
      <span class="fw-bold fs-5 d-none d-lg-inline">
        Sistem Monitoring Jaringan Internet OPD
      </span>

    </a>

    <div class="ms-auto d-none d-lg-flex">
      <button type="button"
        class="btn btn-outline-light btn-sm mx-1"
        data-bs-toggle="modal"
        data-bs-target="#sendReportModal">
        <i class="fas fa-paper-plane me-1"></i> Kirim Laporan
      </button>

      <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm mx-1">
        <i class="fas fa-sign-in-alt me-1"></i> Login
      </a>
    </div>

    <!-- Versi dropdown untuk HP -->
    <div class="ms-auto d-lg-none">
      <div class="dropdown">
        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
          Menu
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#sendReportModal">
              <i class="fas fa-paper-plane me-1"></i> Kirim Laporan
            </button>
          </li>
          <li>
            <a href="{{ route('login') }}" class="dropdown-item">
              <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>
          </li>
        </ul>
      </div>
    </div>


  </div>
</nav>

 <div class="container mt-5">
  <!-- Notifikasi -->
  @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i>
          <strong>Berhasil!</strong> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif
  @if(session('failed'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Gagal!</strong> {{ session('failed') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  {{-- Statistik Perangkat --}}
  <h3 class="d-block d-lg-none text-center">Sistem Monitoring Internet OPD</h3>

  <div class="row g-4 mb-4 mx-1">
    @php
      $statCards = [
        ['title'=>'Internet Aktif','id'=>'aktif','value'=>$aktif ?? 0,'icon'=>'fa-desktop','color'=>'success'],
        ['title'=>'Internet Nonaktif','id'=>'nonaktif','value'=>$nonaktif ?? 0,'icon'=>'fa-times-circle','color'=>'danger'],
        ['title'=>'Total Internet OPD','id'=>'total','value'=>$total ?? 0,'icon'=>'fa-server','color'=>'primary']
      ];
    @endphp
    @foreach($statCards as $card)
      <div class="col-sm-6 col-md-4">
        <div class="card shadow-sm h-100 border-0 rounded-4">
          <div class="card-body d-flex align-items-center">
            <div class="me-4 d-flex justify-content-center align-items-center rounded-circle bg-{{ $card['color'] }} text-white" style="width:60px; height:60px;">
              <i class="fas {{ $card['icon'] }} fa-2x"></i>
            </div>
            <div>
              <p class="mb-1 text-muted">{{ $card['title'] }}</p>
              <h4 class="mb-0 fw-bold text-{{ $card['color'] }}" id="{{ $card['id'] }}">{{ $card['value'] }}</h4>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

 {{-- Perangkat Aktif & Nonaktif --}}
  <div class="row g-4 mb-4 mx-1">
      @php
          $tables = [
            ['title'=>'Internet OPD Nonaktif','color'=>'danger','status'=>0, 'id'=>'card-nonaktif','icon'=>'fa-times-circle'],
            ['title'=>'Internet OPD Aktif','color'=>'success','status'=>1, 'id'=>'card-aktif','icon'=>'fa-check-circle'],
          ];
      @endphp

      @foreach($tables as $table)
          @php
              $items = $data->where('status', $table['status']);
          @endphp
          <div class="col-12">
              <div id="{{ $table['id'] }}" class="card shadow-sm h-100 rounded-4 border-0">
                  <div class="card-header bg-{{ $table['color'] }} text-white d-flex align-items-center">
                      <i class="fas {{ $table['icon'] }} me-2"></i>
                      <h5 class="mb-0">{{ $table['title'] }} ({{ $items->count() }})</h5>
                  </div>
                  <div class="card-body p-3">
                      @if($items->count() > 0)
                          <div class="row g-2">
                              @foreach($items as $item)
                                  <div class="col-6 col-md-4">
                                      <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-1 shadow-sm">
                                          {{-- Logo/ikon perangkat --}}
                                          <div class="me-2 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width:28px; height:28px;">
                                              <i class="fas fa-desktop text-{{ $table['color'] }}"></i>
                                          </div>
                                          {{-- Hostname --}}
                                          <span class="text-truncate flex-grow-1" title="{{ $item->hostname }}">{{ $item->hostname }}</span>
                                          {{-- Status badge --}}
                                          <span class="badge bg-{{ $table['color'] }}">
                                              {{ $table['status'] == 1 ? 'Online' : 'Offline' }}
                                          </span>
                                      </div>
                                  </div>
                              @endforeach
                          </div>
                      @else
                          <p class="text-muted text-center mb-0">Tidak ada perangkat</p>
                      @endif
                  </div>
              </div>
          </div>
      @endforeach
  </div>


  {{-- Peta Monitoring --}}
  <div class="card shadow-sm mb-4 rounded-4 border-0 ">
    <div class="card-body p-3 ">
      <h5 class="card-title mb-3 fw-bold"><i class="fas fa-map-marker-alt me-1"></i>&nbsp;Peta Geografis</h5>
      <div id="map" class="rounded-4" style="height:500px;"></div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="sendReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ route('send.report') }}" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Kirim Laporan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>Nama</label>
              <input type="text" name="pengirim" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Organisansi Pemerintahan Daerah (OPD)</label>
              <input type="text" name="opd" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Permasalah dan Keluhan</label>
              <textarea name="laporan" class="form-control" rows="3" required></textarea>
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

</div><!-- /.container -->



    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <!--   Core JS Files   -->
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    
    <!-- Bootstrap Notify -->
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>

    <script src="assets/js/plugin/jsvectormap/world.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Leaflet Geocoder JS -->
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <!-- Tambahkan JS MarkerCluster sebelum script utama -->
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>

    <script>
        $(document).ready(function () {
           $("#basic-datatables").DataTable({});
        })

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
<script src="{{ asset('js/reloadGDashboard.js') }}"></script>
<script src="{{ asset('js/trigerPing.js') }}"></script>

</body>
</html>
