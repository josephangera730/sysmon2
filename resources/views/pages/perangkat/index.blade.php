@extends('layouts.main')

@section('main')
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

    // Edit Modal Maps
    @foreach ($data as $item)
    $('#editModal{{ $item->id }}').on('shown.bs.modal', function () {
        if (!window['editMap{{ $item->id }}']) {
            // Init map
            window['editMap{{ $item->id }}'] = L.map('edit-map-{{ $item->id }}').setView(
                [{{ $item->latitude ?? '-0.786528' }}, {{ $item->longitude ?? '100.654013' }}], 13
            );
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(window['editMap{{ $item->id }}']);

            // Simpan marker di window
            window['editMarker{{ $item->id }}'] = L.marker(
                [{{ $item->latitude ?? '-0.786528' }}, {{ $item->longitude ?? '100.654013' }}],
                { draggable: true }
            ).addTo(window['editMap{{ $item->id }}']);

            // Event drag marker
            window['editMarker{{ $item->id }}'].on('dragend', function(e) {
                var latlng = e.target.getLatLng();
                $('#edit-latitude-{{ $item->id }}').val(latlng.lat.toFixed(6));
                $('#edit-longitude-{{ $item->id }}').val(latlng.lng.toFixed(6));
            });

            // Event klik map
            window['editMap{{ $item->id }}'].on('click', function(e) {
                window['editMarker{{ $item->id }}'].setLatLng(e.latlng);
                $('#edit-latitude-{{ $item->id }}').val(e.latlng.lat.toFixed(6));
                $('#edit-longitude-{{ $item->id }}').val(e.latlng.lng.toFixed(6));
            });

            // Geocoder
            L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on('markgeocode', function(e) {
                var bbox = e.geocode.bbox;
                var center = e.geocode.center;

                window['editMap{{ $item->id }}'].fitBounds(bbox);
                window['editMarker{{ $item->id }}'].setLatLng(center);

                $('#edit-latitude-{{ $item->id }}').val(center.lat.toFixed(6));
                $('#edit-longitude-{{ $item->id }}').val(center.lng.toFixed(6));
            })
            .addTo(window['editMap{{ $item->id }}']);

            // Set nilai awal input
            $('#edit-latitude-{{ $item->id }}').val({{ $item->latitude ?? '-0.786528' }});
            $('#edit-longitude-{{ $item->id }}').val({{ $item->longitude ?? '100.654013' }});
        }

        // Biar map nggak ketutup / ngecil
        setTimeout(function() {
            window['editMap{{ $item->id }}'].invalidateSize();
        }, 10);
    });
    @endforeach


    // Detail Modal Maps
    @foreach ($data as $item)
    $('#detailModal{{ $item->id }}').on('shown.bs.modal', function () {
        if (!window['detailMap{{ $item->id }}']) {
            window['detailMap{{ $item->id }}'] = L.map('detail-map-{{ $item->id }}').setView(
                [{{ $item->latitude ?? '-0.786528' }}, {{ $item->longitude ?? '100.654013' }}], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(window['detailMap{{ $item->id }}']);

            // Marker tidak draggable
            window['detailMarker{{ $item->id }}'] = L.marker(
                [{{ $item->latitude ?? '-0.786528' }}, {{ $item->longitude ?? '100.654013' }}],
                { draggable: false }
            ).addTo(window['detailMap{{ $item->id }}']);

            // Tambah geocoder search box (untuk navigasi lihat lokasi lain)
            L.Control.geocoder({
                defaultMarkGeocode: false
            })
            .on('markgeocode', function(e) {
                var bbox = e.geocode.bbox;
                var center = e.geocode.center;

                window['detailMap{{ $item->id }}'].fitBounds(bbox);
                window['detailMarker{{ $item->id }}'].setLatLng(center);
            })
            .addTo(window['detailMap{{ $item->id }}']);
        }

        // Supaya peta tidak ngecil
        setTimeout(function() {
            window['detailMap{{ $item->id }}'].invalidateSize();
        }, 10);
    });
    @endforeach


});
</script>


<h4>Perangkat</h4>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('failed'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('failed') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


<div>
    <!-- Tombol Tambah -->
    <button class="btn btn-dx mt-2 mb-4" data-bs-toggle="modal" data-bs-target="#createModal">
         Tambah
    </button>

    <!-- Tombol Import -->
    {{-- <button class="btn btn-dx mt-2 mb-4" data-bs-toggle="modal" data-bs-target="#importModal">
         Import
    </button> --}}
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-dx text-white">
        <h5 class="modal-title" id="importModalLabel">Import Perangkat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/importperangkat" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="file" class="form-label">Pilih file Excel</label>
            <input class="form-control" type="file" id="file" name="file" accept=".xlsx,.xls" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-dx rounded-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dx rounded-4">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>


<table class="table" name="ajax-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Organisasi Pemerintahan Daerah (OPD)</th>
            <th>Ip address</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->hostname }}</td>
                <td>{{ $item->ip_address }}</td>
                <td>
                    @if($item->status == 1)
                        <span class="badge bg-dx">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('perangkat.show', $item->id) }}" class="btn btn-sm btn-dx">
                        <i class="fas fa-eye"></i>
                    </a>

                    <button class="btn btn-sm btn-dx" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                        <i class="fas fa-edit"></i>
                     </button>
                   @can('isSuperadmin')
                       
                    <form action="{{ route('perangkat.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Apa anda yakin ingin menghapus data ini?')" class="btn btn-sm btn-dx">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                        
                    
                </td>
            </tr>

            {{-- Modal Edit --}}
            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('perangkat.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Organisasi Pemerintahan Daerah (OPD)</label>
                                    <input type="text" name="hostname" value="{{ $item->hostname }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>IP Address (Router)</label>
                                    <input type="text" name="ip_address" value="{{ $item->ip_address }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>MAC Address (Boleh Kosong)</label>
                                    <input type="text" name="mac_address" value="{{ $item->mac_address }}" class="form-control">
                                </div>
                               <div class="mb-3">
                                    <label>Latitude</label>
                                    <input type="text" name="latitude" id="edit-latitude-{{ $item->id }}" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" id="edit-longitude-{{ $item->id }}" class="form-control" readonly>
                                </div>
                                <div id="edit-map-{{ $item->id }}" style="height: 300px;"></div>
                                {{-- <div class="mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Nonaktif</option>
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
        @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Modal Create --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('perangkat.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Organisasi Pemerintahan Daerah (OPD)</label>
                        <input type="text" name="hostname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>IP Address (Router)</label>
                        <input type="text" name="ip_address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>MAC Address (Boleh Kosong)</label>
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


@endsection
