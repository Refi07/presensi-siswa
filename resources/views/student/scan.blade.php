<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scan Presensi Mandiri Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Presensi Mandiri Siswa</h3>
                <p class="text-xs text-gray-500 mb-4">Arahkan kamera ke QR Code Sekolah atau masukkan kode secara manual.</p>

                <!-- Status Notifikasi Lokasi GPS & Respon -->
                <div id="gpsStatus" class="p-3 mb-4 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
                    📍 Sedang mendeteksi lokasi GPS Anda...
                </div>

                <div id="alertBox" class="hidden p-4 mb-4 rounded-lg text-sm font-semibold text-center"></div>

                <!-- Live Camera Scanner Container -->
                <div class="relative overflow-hidden rounded-xl border-4 border-indigo-500 max-w-sm mx-auto shadow-md mb-6">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-4 text-xs text-gray-400 font-semibold">ATAU INPUT MANUALLY</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Form Input Manual Kode Kelas/QR -->
                <form id="manualForm" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="qr_code_token" id="qrTokenInput" required placeholder="MASUKKAN KODE KELAS / QR" 
                            class="w-full text-center tracking-widest uppercase text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition shadow-sm">
                        Kirim Presensi Sekarang
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Script HTML5-QRCode & Geolocation -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let userLat = null;
        let userLng = null;

        // 1. Dapatkan Lokasi GPS Siswa
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;

                    let gpsBox = document.getElementById('gpsStatus');
                    gpsBox.className = 'p-3 mb-4 rounded-lg text-xs font-semibold bg-green-50 text-green-700';
                    gpsBox.innerHTML = '✅ Lokasi GPS berhasil didapatkan!';
                },
                (error) => {
                    let gpsBox = document.getElementById('gpsStatus');
                    gpsBox.className = 'p-3 mb-4 rounded-lg text-xs font-semibold bg-red-50 text-red-700';
                    gpsBox.innerHTML = '⚠️ Gagal mendeteksi lokasi GPS! Pastikan izin lokasi aktif.';
                }
            );
        }

        function showAlert(message, type) {
            let box = document.getElementById('alertBox');
            box.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
            if (type === 'success') {
                box.classList.add('bg-green-100', 'text-green-700');
            } else {
                box.classList.add('bg-red-100', 'text-red-700');
            }
            box.innerText = message;
        }

        // Fungsi kirim presensi (Kamera & Manual)
        let isProcessing = false;

        function submitAttendance(tokenValue) {
            if (isProcessing) return;
            isProcessing = true;

            showAlert('⏳ Memproses presensi...', 'success');

            fetch("{{ route('student.scan.process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    qr_code_token: tokenValue,
                    latitude: userLat,
                    longitude: userLng
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('✅ ' + data.message, 'success');
                } else {
                    showAlert('❌ ' + (data.message || 'Presensi gagal!'), 'error');
                }
                setTimeout(() => { isProcessing = false; }, 3000);
            })
            .catch(err => {
                showAlert('❌ Terjadi kesalahan koneksi server!', 'error');
                setTimeout(() => { isProcessing = false; }, 3000);
            });
        }

        // Handle Form Submit Manual via AJAX
        document.getElementById('manualForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let token = document.getElementById('qrTokenInput').value;
            submitAttendance(token);
        });

        // 2. Scan Kamera Otomatis
        function onScanSuccess(decodedText, decodedResult) {
            submitAttendance(decodedText);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: { width: 220, height: 220 } }, false
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-app-layout>