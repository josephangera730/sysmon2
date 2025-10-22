var map;
var markersLayer;
var greenIcon;
var redIcon;
let lastStatus = {};
let isFirstLoad = true;

function loadData() {
    fetch('/reloaddashboard')
        .then(res => res.json())
        .then(data => {
            // =======================
            // Update card statistik
            // =======================
            document.getElementById('aktif').innerText = data.aktif ?? 0;
            document.getElementById('nonaktif').innerText = data.nonaktif ?? 0;
            document.getElementById('total').innerText = data.total ?? 0;

            // =======================
            // Perangkat Aktif
            // =======================
            const aktifContainer = document.querySelector('#card-aktif .card-body .row');
            const cardAktifHeader = document.querySelector('#card-aktif .card-header h5');
            if (aktifContainer) {
                const aktifData = data.data.filter(item => item.status == 1);
                const fragment = document.createDocumentFragment();

                if (aktifData.length > 0) {
                    aktifData.forEach(item => {
                        const divCol = document.createElement('div');
                        divCol.className = "col-6 col-md-4";
                        divCol.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-1 shadow-sm">
                                <div class="me-2 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width:28px; height:28px;">
                                    <i class="fas fa-desktop text-success"></i>
                                </div>
                                <span class="text-truncate flex-grow-1" title="${item.hostname ?? '-'}">${item.hostname ?? '-'}</span>
                                <span class="badge bg-success">Online</span>
                            </div>`;
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
                if (cardAktifHeader) cardAktifHeader.innerText = `Internet OPD Aktif (${aktifData.length})`;
            }

            // =======================
            // Perangkat Nonaktif
            // =======================
            const nonaktifContainer = document.querySelector('#card-nonaktif .card-body .row');
            const cardNonaktifHeader = document.querySelector('#card-nonaktif .card-header h5');
            if (nonaktifContainer) {
                const nonaktifData = data.data.filter(item => item.status == 0);
                const fragment = document.createDocumentFragment();

                if (nonaktifData.length > 0) {
                    nonaktifData.forEach(item => {
                        const divCol = document.createElement('div');
                        divCol.className = "col-6 col-md-4";
                        divCol.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-1 shadow-sm">
                                <div class="me-2 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width:28px; height:28px;">
                                    <i class="fas fa-desktop text-danger"></i>
                                </div>
                                <span class="text-truncate flex-grow-1" title="${item.hostname ?? '-'}">${item.hostname ?? '-'}</span>
                                <span class="badge bg-danger">Offline</span>
                            </div>`;
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
                if (cardNonaktifHeader) cardNonaktifHeader.innerText = `Internet OPD Nonaktif (${nonaktifData.length})`;
            }

            // =======================
            // Update marker di map (pakai logika dashboard B)
            // =======================
            markersLayer.clearLayers();
            let bounds = [];

            // Array untuk batch alert (dari dashboard A)
            const changedDevices = [];

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

                    // 🔔 Cek perubahan status (logika Swal dari dashboard A)
                    const id = item.id ?? item.hostname;
                    const statusSekarang = String(item.status);
                    if (lastStatus[id] !== undefined && lastStatus[id] !== statusSekarang) {
                        changedDevices.push({
                            hostname: item.hostname,
                            status: statusSekarang
                        });
                    }
                    lastStatus[id] = statusSekarang;
                }
            });

            // 🔔 Tampilkan batch alert kalau ada perubahan
            if (changedDevices.length > 0) {
                let message = changedDevices.map(d =>
                    `${d.hostname} → ${d.status === "1" ? "ONLINE ✅" : "OFFLINE ❌"}`
                ).join('<br>');

                // Swal.fire({
                //     toast: true,
                //     position: 'bottom-end',
                //     icon: 'info',
                //     title: message,
                //     showConfirmButton: false,
                //     timer: 4000,
                //     timerProgressBar: true
                // });

            }

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
        })
        .catch(err => console.error(err));
}




// ============================
// Init Map
// ============================
$(document).ready(function () {

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
