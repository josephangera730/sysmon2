let lastStatus = {};

var map;
var markersLayer;
var greenIcon;
var redIcon;


// Fungsi cek status perangkat + alert
function cekPerangkat() {
    fetch("/checkperangkat")
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data.perangkat) || data.perangkat.length === 0) {
                return;
            }

            let $table = $('[name="ajax-table"]');
            let hasTable = $table.length > 0;

            // 🔔 tampung semua perubahan status
            let changedDevices = [];

            if (hasTable) {
                if ($.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().clear().destroy();
                }

                let tbody = $table.find('tbody');
                tbody.empty();

                data.perangkat.forEach((item, index) => {
                    let id = item.id;
                    let statusSekarang = item.status; // '1' = online, '0' = offline

                    // Status badge
                    let statusBadge = statusSekarang === "1"
                        ? '<span class="badge bg-dx">Aktif</span>'
                        : '<span class="badge bg-danger">Nonaktif</span>';

                    // Tombol Action
                    let actionButtons = `
                        <a href="/perangkat/${id}" class="btn btn-sm btn-dx">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-sm btn-dx" data-bs-toggle="modal" data-bs-target="#editModal${id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="/perangkat/${id}" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button onclick="return confirm('Apa anda yakin ingin menghapus data ini?')" class="btn btn-sm btn-dx">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    `;

                    // Row tabel
                    let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.hostname}</td>
                            <td>${item.ip_address}</td>
                            <td>${statusBadge}</td>
                            <td>${actionButtons}</td>
                        </tr>
                    `;
                    tbody.append(row);

                    // Cek perubahan status
                    if (lastStatus[id] === undefined) {
                        lastStatus[id] = statusSekarang;
                    } else if (lastStatus[id] !== statusSekarang) {
                        changedDevices.push({
                            hostname: item.hostname,
                            status: statusSekarang
                        });
                        lastStatus[id] = statusSekarang;
                        if (typeof loadData === "function") {
                            loadData();
                        }
                    }
                });

                $table.DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10
                });

            } else {
                // Kalau tabel nggak ada, tetap cek perubahan status
                data.perangkat.forEach(item => {
                    let id = item.id;
                    let statusSekarang = item.status;

                    if (lastStatus[id] === undefined) {
                        lastStatus[id] = statusSekarang;
                    } else if (lastStatus[id] !== statusSekarang) {
                        changedDevices.push({
                            hostname: item.hostname,
                            status: statusSekarang
                        });
                        lastStatus[id] = statusSekarang;
                    }
                });
            }

            // 🔔 Batch Alert sekali saja
            // if (changedDevices.length > 0) {
            //     let message = changedDevices.map(d =>
            //         `${d.hostname} → ${d.status === "1" ? "ONLINE ✅" : "OFFLINE ❌"}`
            //     ).join('<br>');

            //    Swal.fire({
            //         toast: true,
            //         position: 'bottom-end',
            //         icon: 'info',
            //         title: message,
            //         showConfirmButton: false,
            //         timer: 4000,
            //         timerProgressBar: true
            //     });
            // }
        })
        .catch(err => console.error("Gagal cek perangkat:", err));
}




// ============================
// Fungsi load data dashboard + peta
// ============================
let isFirstLoad = true; // flag load pertama

function loadData() {
    fetch('/reloaddashboard')
        .then(res => res.json())
        .then(data => {
            // =======================
            // Update card statistik (aman, cek dulu)
            // =======================
            const aktifEl = document.getElementById('aktif');
            const nonaktifEl = document.getElementById('nonaktif');
            const totalEl = document.getElementById('total');

            if (aktifEl) aktifEl.innerText = data.aktif ?? 0;
            if (nonaktifEl) nonaktifEl.innerText = data.nonaktif ?? 0;
            if (totalEl) totalEl.innerText = data.total ?? 0;

            // =======================
            // Perangkat Aktif
            // =======================
            const aktifContainer = document.querySelector('#card-aktif .card-body .row');
            const cardAktifHeader = document.querySelector('#card-aktif .card-header h5');
            if (aktifContainer && cardAktifHeader) {
                const aktifData = data.data.filter(item => item.status == 1);
                const fragment = document.createDocumentFragment();

                if (aktifData.length > 0) {
                    aktifData.forEach(item => {
                        const divCol = document.createElement('div');
                        divCol.className = "col-6 col-md-4";
                        divCol.innerHTML = `
                         <a href="/perangkat/${item.id}" class="text-dark">
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-1 shadow-sm">
                                <div class="me-2 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width:28px; height:28px;">
                                    <i class="fas fa-desktop text-success"></i>
                                </div>
                                <span class="text-truncate flex-grow-1" title="${item.hostname ?? '-'}">${item.hostname ?? '-'}</span>
                                <span class="badge bg-success">Online</span>
                            </div>
                            </a>
                            `;
                        fragment.appendChild(divCol);
                    });
                } else {
                    const divEmpty = document.createElement('div');
                    divEmpty.className = "col-12 text-center text-muted";
                    divEmpty.innerText = "Tidak ada perangkat";
                    fragment.appendChild(divEmpty);
                }

                aktifContainer.innerHTML = "";
                aktifContainer.appendChild(fragment);
                cardAktifHeader.innerText = `Internet OPD Aktif (${aktifData.length})`;
            }

            // =======================
            // Perangkat Nonaktif
            // =======================
            const nonaktifContainer = document.querySelector('#card-nonaktif .card-body .row');
            const cardNonaktifHeader = document.querySelector('#card-nonaktif .card-header h5');
            if (nonaktifContainer && cardNonaktifHeader) {
                const nonaktifData = data.data.filter(item => item.status == 0);
                const fragment = document.createDocumentFragment();

                if (nonaktifData.length > 0) {
                    nonaktifData.forEach(item => {
                        const divCol = document.createElement('div');
                        divCol.className = "col-6 col-md-4";
                        divCol.innerHTML = `
                         <a href="/perangkat/${item.id}" class="text-dark">
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-1 shadow-sm">
                                <div class="me-2 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width:28px; height:28px;">
                                    <i class="fas fa-desktop text-danger"></i>
                                </div>
                                <span class="text-truncate flex-grow-1" title="${item.hostname ?? '-'}">${item.hostname ?? '-'}</span>
                                <span class="badge bg-danger">Offline</span>
                            </div>
                        </a>`;
                        fragment.appendChild(divCol);
                    });
                } else {
                    const divEmpty = document.createElement('div');
                    divEmpty.className = "col-12 text-center text-muted";
                    divEmpty.innerText = "Tidak ada perangkat";
                    fragment.appendChild(divEmpty);
                }

                nonaktifContainer.innerHTML = "";
                nonaktifContainer.appendChild(fragment);
                cardNonaktifHeader.innerText = `Internet OPD Nonaktif (${nonaktifData.length})`;
            }

            // =======================
            // Update marker di map
            // =======================
            if (typeof markersLayer !== 'undefined') {
                markersLayer.clearLayers();
                let bounds = [];

                data.data.forEach(item => {
                    if (item.latitude && item.longitude) {
                        let icon = item.status == 1 ? greenIcon : redIcon;

                        let marker = L.marker([item.latitude, item.longitude], { icon: icon })
                            .bindPopup(
                                "<b>" + (item.hostname ?? "Perangkat") + "</b><br>" +
                                "Status: " + (item.status == 1 ? "Aktif ✅" : "Nonaktif ❌")
                            );

                        markersLayer.addLayer(marker);

                        if (item.hostname) {
                            marker.bindTooltip(item.hostname, {
                                permanent: true,
                                direction: 'top',
                                offset: [0, -35],
                                className: 'leaflet-popup-always'
                            });
                        }

                        bounds.push([item.latitude, item.longitude]);
                    }
                });

                // Zoom otomatis hanya sekali
                if (bounds.length > 0 && isFirstLoad) {
                    let targetBounds = L.latLngBounds(bounds);
                    map.flyToBounds(targetBounds, {
                        padding: [50, 50],
                        maxZoom: 16,
                        animate: true,
                        duration: 1.5
                    });
                    isFirstLoad = false;
                }
            }
        })
        .catch(err => console.error(err));
}

// // jalankan tiap 5 detik
// setInterval(loadData, 5000);





// ============================
// Init ketika halaman siap
// ============================
$(document).ready(function () {
    // Jalankan cek perangkat berkala
    cekPerangkat();
    setInterval(cekPerangkat, 60000);

    // ============================
    // Inisialisasi Peta (jika ada)
    // ============================
    if ($('#map').length) {
        map = L.map('map').setView([-2.5, 118], 5);

        // Tambahkan layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>'
        }).addTo(map);

        // Custom Icon
        greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        redIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        markersLayer = L.layerGroup().addTo(map);

    

      
        setInterval(loadData, 60000);
    }
});