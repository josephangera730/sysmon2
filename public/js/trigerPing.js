// Fungsi untuk memanggil endpoint /pingperangkat
function triggerPingJob() {
    fetch('/pingperangkat', { method: 'GET' })
        .then(response => {
            // optional: log sukses
        })
        .catch(error => {
            console.error('Gagal pingperangkat:', error);
        });
}

// Jalankan pertama kali saat halaman load
triggerPingJob();

// Jalankan terus setiap 30 detik
setInterval(triggerPingJob, 60000);