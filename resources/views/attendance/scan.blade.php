<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scanner Presensi QR Code') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Container Kamera Scanner -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Arahkan QR Code Siswa ke Kamera</h3>
                <p class="text-xs text-gray-500 mb-4">Pastikan lokasi GPS aktif jika validasi radius dinyalakan oleh Admin.</p>

                <!-- Status Notifikasi -->
                <div id="alertBox" class="hidden p-4 mb-4 rounded-lg text-sm font-semibold text-center"></div>

                <!-- Element Video Kamera -->
                <div class="relative overflow-hidden rounded-xl border-4 border-indigo-500 max-w-sm mx-auto shadow-md">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <div class="mt-4 flex justify-center gap-2 text-xs text-gray-400">
                    <span>📍 Lat: <span id="latDisplay">Mencari...</span></span> | 
                    <span>Long: <span id="lngDisplay">Mencari...</span></span>
                </div>
            </div>

        </div>
    </div>

    <!-- Script HTML5-QRCode & GeoLocation -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let userLat = null;
        let userLng = null;

        // Ambil Koordinat GPS User
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;
                    document.getElementById('latDisplay').innerText = userLat.toFixed(5);
                    document.getElementById('lngDisplay').innerText = userLng.toFixed(5);
                },
                (error) => {
                    console.warn("Gagal mendapatkan lokasi GPS: ", error.message);
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

        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;

            showAlert('⏳ Memproses presensi...', 'success');

            fetch("{{ route('attendance.scan.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    qr_code_token: decodedText,
                    latitude: userLat,
                    longitude: userLng
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('✅ ' + data.message, 'success');
                } else {
                    showAlert('❌ ' + data.message, 'error');
                }
                setTimeout(() => { isProcessing = false; }, 3000);
            })
            .catch(err => {
                showAlert('❌ Terjadi kesalahan koneksi server!', 'error');
                setTimeout(() => { isProcessing = false; }, 3000);
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: { width: 250, height: 250 } }, /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-app-layout>