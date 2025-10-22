@extends('layouts.main')

@section('main')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let editMap, editMarker;

    // Modal Edit → saat dibuka
    $('#editModal').on('shown.bs.modal', function () {
        let lat = {{ $perangkat->latitude ?? '-0.786528' }};
        let lng = {{ $perangkat->longitude ?? '100.654013' }};

        if (!editMap) {
            editMap = L.map('edit-map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(editMap);

            editMarker = L.marker([lat, lng], { draggable: true }).addTo(editMap);

            // drag marker → update input
            editMarker.on('dragend', function(e) {
                let pos = e.target.getLatLng();
                $('#edit-latitude').val(pos.lat.toFixed(6));
                $('#edit-longitude').val(pos.lng.toFixed(6));
            });

            // klik map → pindah marker
            editMap.on('click', function(e) {
                editMarker.setLatLng(e.latlng);
                $('#edit-latitude').val(e.latlng.lat.toFixed(6));
                $('#edit-longitude').val(e.latlng.lng.toFixed(6));
            });

            // Geocoder
            L.Control.geocoder({ defaultMarkGeocode: false })
                .on('markgeocode', function(e) {
                    let center = e.geocode.center;
                    editMap.fitBounds(e.geocode.bbox);
                    editMarker.setLatLng(center);
                    $('#edit-latitude').val(center.lat.toFixed(6));
                    $('#edit-longitude').val(center.lng.toFixed(6));
                })
                .addTo(editMap);

            // Set nilai awal input
            $('#edit-latitude').val(lat);
            $('#edit-longitude').val(lng);
        } else {
            // kalau sudah pernah dibuat → reset posisi
            editMap.setView([lat, lng], 13);
            editMarker.setLatLng([lat, lng]);
        }

        setTimeout(() => editMap.invalidateSize(), 200);
    });

    
});
</script>

<h3 class="mb-3">Detail Internet OPD</h3>

<div class="card shadow-sm mb-4">
    
    <div class="card-body">
        <div class="row">
            <!-- Kolom kiri (Detail Info) -->
            <div class="col-md-6">
                <ul class="list-group">
                    <li class="list-group-item"><strong>OPD:&nbsp;</strong> {{ $perangkat->hostname }}</li>
                    <li class="list-group-item"><strong>IP Address:&nbsp;</strong> {{ $perangkat->ip_address }}</li>
                    <li class="list-group-item"><strong>MAC Address:&nbsp;</strong> {{ $perangkat->mac_address ?? '-' }}</li>
                    <li class="list-group-item"><strong>Latitude:&nbsp;</strong> {{ $perangkat->latitude ?? '-' }}</li>
                    <li class="list-group-item"><strong>Longitude:&nbsp;</strong> {{ $perangkat->longitude ?? '-' }}</li>
                </ul>
                <button class="btn btn-dx float-end my-2" data-bs-toggle="modal" data-bs-target="#editModal">
                    <i class="fas fa-edit"></i>
                    Ubah Data
                </button>

            </div>

            <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('perangkat.update', $perangkat->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Nama Perangkat</label>
                                    <input type="text" name="hostname" value="{{ $perangkat->hostname }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>IP Address</label>
                                    <input type="text" name="ip_address" value="{{ $perangkat->ip_address }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>MAC Address</label>
                                    <input type="text" name="mac_address" value="{{ $perangkat->mac_address }}" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Latitude</label>
                                    <input type="text" name="latitude" id="edit-latitude" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" id="edit-longitude" class="form-control" readonly>
                                </div>
                                <div id="edit-map" style="height: 300px;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dx" data-bs-dismiss="modal">Batal</button>
                                <button class="btn btn-dx">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <!-- Kolom kanan (Map) -->
            <div class="col-md-6">
                <div class="border rounded" style="height: 400px; overflow: hidden;">
                    <div id="detail-map-{{ $perangkat->id }}" style="height: 100%; width:100%;"></div>
                </div>
            </div>
            {{-- Debug Script --}}
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    try {
                        
                        var lat = {{ $perangkat->latitude ?? -0.786528 }};
                        var lng = {{ $perangkat->longitude ?? 100.654013 }};
                        var map = L.map('detail-map-{{ $perangkat->id }}').setView([lat, lng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>'
                        }).addTo(map);

                        L.marker([lat, lng], { draggable: false }).addTo(map);

                        setTimeout(() => map.invalidateSize(), 300);
                    } catch (err) {
                        console.error("❌ Error saat render map:", err);
                    }
                });
            </script>
        </div>
    </div>
</div>
<!-- Perangkat Terkoneksi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Perangkat Terkoneksi</h3>
    <!-- Tombol untuk buka modal tambah -->
    <button class="btn btn-dx" data-bs-toggle="modal" data-bs-target="#tambahPerangkatModal">
        + Tambah Perangkat
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered" name="detailPerangkat">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Perangkat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->namaperangkat }}</td>
                <td>
                    <span id="status-badge-{{ $item->id }}"
                        class="badge {{ $item->status == 1 ? 'bg-dx' : 'bg-danger' }}">
                        {{ $item->status == 1 ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
    
    
                <td>
                    <!-- Tombol Detail -->
                    <button class="btn btn-sm btn-dx" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                        <i class="fas fa-eye"></i>
                    </button>
    
                    <!-- Tombol Edit -->
                    <button class="btn btn-sm btn-dx" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
    
                    <!-- Tombol Hapus -->
                    <form action="{{ route('detailperangkat.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Yakin hapus data ini?')" class="btn btn-sm btn-dx">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
    
            <!-- Modal Detail -->
            <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Perangkat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Nama Perangkat:&nbsp;</strong> {{ $item->namaperangkat }}
                                </li>
                                <li class="list-group-item">
                                    <strong>IP Address:&nbsp;</strong> {{ $item->ip_address }}
                                </li>
                                <li class="list-group-item">
                                    <strong>MAC Address:&nbsp;</strong> {{ $item->mac_address ?? 'Tidak Diketahui' }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Jenis Perangkat:&nbsp;</strong> {{ $item->perangkat->jenisperangkat ?? '-' }}
                                </li>
                            </ul>
    
                        </div>
                    </div>
                </div>
            </div>
    
          <!-- Modal Edit -->
<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('detailperangkat.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Perangkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- Nama Perangkat -->
                    <div class="mb-3">
                        <label class="form-label">Nama Perangkat</label>
                        <input type="text" name="namaperangkat" value="{{ $item->namaperangkat }}" class="form-control" required>
                    </div>

                    <!-- IP Address -->
                    <div class="mb-3">
                        <label class="form-label">IP Address</label>
                        <input type="text" name="ip_address" value="{{ $item->ip_address }}" class="form-control" >
                    </div>

                    <!-- MAC Address -->
                    <div class="mb-3">
                        <label class="form-label">MAC Address</label>
                        <input type="text" name="mac_address" value="{{ $item->mac_address }}" class="form-control" >
                    </div>

                    <!-- Jenis Perangkat -->
                    <div class="mb-3">
                        <label class="form-label">Jenis Perangkat</label>
                        <select name="jenisperangkat_id" class="form-select" required>
                            @foreach($jenisperangkat as $jenis)
                                <option value="{{ $jenis->id }}" {{ $jenis->id == $item->jenisperangkat_id ? 'selected' : '' }}>
                                    {{ $jenis->jenisperangkat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dx">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

            @endforeach
        </tbody>
    </table>

</div>

<!-- Modal Create -->
<div class="modal fade" id="tambahPerangkatModal" tabindex="-1" aria-labelledby="tambahPerangkatLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('detailperangkat.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Perangkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- Nama Perangkat -->
                    <div class="mb-3">
                        <label for="namaperangkat" class="form-label">Nama Perangkat</label>
                        <input type="text" name="namaperangkat" id="namaperangkat" class="form-control" required>
                    </div>

                    <!-- IP Address -->
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address</label>
                        <input type="text" name="ip_address" id="ip_address" class="form-control">
                    </div>

                    <!-- MAC Address -->
                    <div class="mb-3">
                        <label for="mac_address" class="form-label">MAC Address</label>
                        <input type="text" name="mac_address" id="mac_address" class="form-control">
                    </div>

                    <!-- Jenis Perangkat -->
                    <div class="mb-3">
                        <label for="jenisperangkat_id" class="form-label">Jenis Perangkat</label>
                        <select name="jenisperangkat_id" id="jenisperangkat_id" class="form-select" required>
                            @foreach($jenisperangkat as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->jenisperangkat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hidden Perangkat ID -->
                    <input type="hidden" name="perangkat_id" value="{{ $perangkat->id }}">

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dx">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
let lastDetailStatus = {}; // tracking status sebelumnya
let detailTable; // simpan instance DataTable

function refreshDetail(perangkatId) {
    fetch(`/reloaddetailperangkat/${perangkatId}`)
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data)) return;

            let $table = $('[name="detailPerangkat"]');
            if ($table.length === 0) return;

            // Kalau DataTable belum ada → inisialisasi
            if (!$.fn.DataTable.isDataTable($table)) {
                detailTable = $table.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10
                });
            }

            // Clear data lama
            detailTable.clear();

            // Tambahkan data baru
            data.forEach((item, index) => {
                let id = item.id;
                let statusSekarang = item.status;

                // Badge status
                let statusBadge = statusSekarang == 1
                    ? `<span class="badge bg-dx" id="status-badge-${id}">Aktif</span>`
                    : `<span class="badge bg-danger" id="status-badge-${id}">Nonaktif</span>`;

                // Tombol aksi
                let aksi = `
                    <!-- Tombol Detail -->
                    <button class="btn btn-sm btn-dx" data-bs-toggle="modal" data-bs-target="#detailModal${id}">
                        <i class="fas fa-eye"></i>
                    </button>

                    <!-- Tombol Edit -->
                    <button class="btn btn-sm btn-dx" data-bs-toggle="modal" data-bs-target="#editModal${id}">
                        <i class="fas fa-edit"></i>
                    </button>

                    <!-- Tombol Hapus -->
                    <form action="/detailperangkat/${id}" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button onclick="return confirm('Yakin hapus data ini?')" class="btn btn-sm btn-dx">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                `;

                // Tambah baris ke DataTable
                detailTable.row.add([
                    index + 1,
                    item.namaperangkat ?? '-',
                    statusBadge,
                    aksi
                ]);

                // Tracking status
                if (lastDetailStatus[id] === undefined) {
                    lastDetailStatus[id] = statusSekarang;
                } else if (lastDetailStatus[id] !== statusSekarang) {
                    lastDetailStatus[id] = statusSekarang;
                    // Bisa taruh notifikasi di sini
                }
            });

            // Redraw tabel
            detailTable.draw();
        })
        .catch(err => console.error("Gagal refresh detail:", err));
}

// Ambil perangkatId dari Blade
let perangkatId = @json($perangkat->id ?? null);
if (perangkatId) {
    refreshDetail(perangkatId); // load pertama
    setInterval(() => refreshDetail(perangkatId), 30000); // refresh tiap 5 detik
}
</script>







@endsection
