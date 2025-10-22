@extends('layouts.main')

@section('main')

<h3 class="mt-3 mb-4 fw-bold">Internet OPD Kota Pariaman</h3>

{{-- Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('failed') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 d-flex align-items-start flex-column" role="alert">
        <div class="d-flex align-items-center w-100 mb-2">
            <i class="fas fa-times-circle me-2"></i>
            <div>
                {{-- Pesan failed --}}
                @if(session('failed'))
                    {{ session('failed') }}
                @endif

                {{-- Pesan error validasi --}}
                @if ($errors->any())
                    <ul class="mb-0 ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif


<script>
document.addEventListener('DOMContentLoaded', function () {

    // Create Modal Map
   var createMap, createMarker;

    $('#createModal').on('shown.bs.modal', function () {
        if (!createMap) {
            createMap = L.map('create-map').setView([-0.786528, 100.654013], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(createMap);

            // Tambah geocoder search box
            var geocoder = L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on('markgeocode', function(e) {
                var bbox = e.geocode.bbox;
                var center = e.geocode.center;

                createMap.fitBounds(bbox);

                createMarker.setLatLng(center);

                $('#create-latitude').val(center.lat.toFixed(6));
                $('#create-longitude').val(center.lng.toFixed(6));
            })
            .addTo(createMap);

            createMarker = L.marker([-0.786528, 100.654013], { draggable:true }).addTo(createMap);

            createMarker.on('dragend', function(e) {
                var latlng = e.target.getLatLng();
                $('#create-latitude').val(latlng.lat.toFixed(6));
                $('#create-longitude').val(latlng.lng.toFixed(6));
            });

            createMap.on('click', function(e) {
                createMarker.setLatLng(e.latlng);
                $('#create-latitude').val(e.latlng.lat.toFixed(6));
                $('#create-longitude').val(e.latlng.lng.toFixed(6));
            });

            // Set nilai awal input
            $('#create-latitude').val(-0.786528);
            $('#create-longitude').val(100.654013);
        }


        // Setelah modal tampil, refresh ukuran peta
        setTimeout(function() {
            createMap.invalidateSize();
        }, 10);
    });
});
</script>

<div>
    <button class="btn btn-dx mt-2 mb-5" data-bs-toggle="modal" data-bs-target="#createModal">
         Tambah 
    </button>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('perangkat.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Perangkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Hostname</label>
                        <input type="text" name="hostname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>IP Address</label>
                        <input type="text" name="ip_address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>MAC Address</label>
                        <input type="text" name="mac_address" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Latitude</label>
                        <input type="text" name="latitude" id="create-latitude" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Longitude</label>
                        <input type="text" name="longitude" id="create-longitude" class="form-control" readonly>
                    </div>
                    <div id="create-map" style="height: 300px;"></div>
                    {{-- <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-dx">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Statistik Perangkat --}}
<div class="row g-4 mb-4">
    @php
        $statCards = [
            ['title'=>'Internet OPD Aktif','id'=>'aktif','value'=>$aktif ?? 0,'icon'=>'fa-network-wired','color'=>'success'],
            ['title'=>'Internet OPD Nonaktif','id'=>'nonaktif','value'=>$nonaktif ?? 0,'icon'=>'fa-times-circle','color'=>'danger'],
            ['title'=>'Total Internet OPD','id'=>'total','value'=>$total ?? 0,'icon'=>'fa-server','color'=>'primary']
        ];
    @endphp

    @foreach($statCards as $card)
        <div class="col-sm-6 col-md-4">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-light">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas {{ $card['icon'] }} fa-3x text-{{ $card['color'] }}"></i>
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
<div class="row g-4 mb-4">
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
                                    <a href="/perangkat/{{ $item->id }}" class="text-dark">
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

                                    </a>
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
<div class="card shadow-sm mb-4 rounded-4">
    <div class="card-body p-3">
        <h5 class="card-title mb-3 fw-bold">Peta Geografis</h5>
        <div id="map" class="rounded-4" style="height:500px; width:100%; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#datatable-aktif').DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false
        });
        $('#datatable-nonaktif').DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false
        });
    });
</script>
@endpush
