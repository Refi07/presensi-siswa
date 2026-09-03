<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scan QR Code Presensi Siswa') }}
        </h2>
    </x-slot>

    <!-- Library HTML5 QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center">
                <h3 class="text-lg font-bold mb-4 text-gray-700">Arahkan Kamera ke QR Code Siswa</h3>
                
                <!-- Container Kamera Scan -->
                <div id="reader" class="w-full max-w-md mx-auto overflow-hidden rounded-lg border-2 border-indigo-500"></div>

                <!-- Alert Status Presensi -->
                <div id="resultAlert" class="mt-6 hidden p-4 rounded-lg font-semibold text-center text-lg"></div>
            </div>
        </div>
    </div>

    <script>
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;

            fetch("{{ route('attendance.scan.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_code_token: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                let alertBox = document.getElementById('resultAlert');
                alertBox.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800', 'bg-red-100', 'text-red-800');

                if (data.status === 'success') {
                    alertBox.classList.add('bg-green-100', 'text-green-800');
                    alertBox.innerText = "✅ " + data.message;
                } else if (data.status === 'warning') {
                    alertBox.classList.add('bg-yellow-100', 'text-yellow-800');
                    alertBox.innerText = "⚠️ " + data.message;
                } else {
                    alertBox.classList.add('bg-red-100', 'text-red-800');
                    alertBox.innerText = "❌ " + data.message;
                }

                // Jeda 3 detik sebelum scan berikutnya
                setTimeout(() => {
                    isProcessing = false;
                }, 3000);
            })
            .catch(error => {
                console.error("Error:", error);
                isProcessing = false;
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: { width: 250, height: 250 } }, /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-app-layout>