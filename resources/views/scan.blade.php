<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scan QR Code Presensi Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="alert-container"></div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Arahkan Kamera ke QR Code Siswa</h3>
                <p class="text-sm text-gray-500 mb-6">Posisikan QR Code tepat di dalam kotak pemindai.</p>

                <div class="max-w-md mx-auto overflow-hidden rounded-xl border-2 border-indigo-500 shadow-md">
                    <div id="reader" style="width: 100%; min-height: 300px;"></div>
                </div>

                <!-- Input Manual Token/NISN jika kamera buram -->
                <div class="mt-8 pt-6 border-t border-gray-100 max-w-md mx-auto">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Input NISN / Token Manual</p>
                    <form id="manual-scan-form" class="flex gap-2">
                        <input type="text" id="token-input" placeholder="Masukkan NISN Siswa..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-lg transition shadow-sm">
                            Submit
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let isProcessing = false;

        function sendAttendanceData(qrToken) {
            if (isProcessing) return;
            isProcessing = true;

            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = `
                <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded-lg text-center font-bold">
                    ⏳ Memproses Presensi...
                </div>
            `;

            fetch("{{ route('attendance.scan.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_code_token: qrToken })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alertContainer.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg text-center font-bold">
                            ✅ ${data.message} (${data.student_name})
                        </div>
                    `;
                } else {
                    alertContainer.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg text-center font-bold">
                            ❌ ${data.message}
                        </div>
                    `;
                }
                setTimeout(() => { isProcessing = false; }, 3000);
            })
            .catch(error => {
                console.error("Error:", error);
                alertContainer.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg text-center font-bold">
                        ❌ Terjadi kesalahan server.
                    </div>
                `;
                isProcessing = false;
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            sendAttendanceData(decodedText);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false);
        html5QrcodeScanner.render(onScanSuccess);

        document.getElementById('manual-scan-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const tokenValue = document.getElementById('token-input').value.trim();
            if (tokenValue) {
                sendAttendanceData(tokenValue);
                document.getElementById('token-input').value = '';
            }
        });
    </script>
</x-app-layout>