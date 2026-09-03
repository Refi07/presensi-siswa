<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Ringkasan Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Siswa Terdaftar</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalSiswa }}</h3>
                </div>
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Kelas</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalKelas }}</h3>
                </div>
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-purple-500">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Guru & Wali Kelas</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $totalGuru }}</h3>
                </div>
            </div>

            <!-- Panel Pengaturan Pengontrol Fitur Sistem -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Card 1: Switch Radius Lokasi GPS -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg flex flex-col justify-between gap-4 border-l-4 {{ ($locationSetting->value ?? '1') == '1' ? 'border-green-500' : 'border-amber-500' }}">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Validasi Radius Lokasi GPS Presensi</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Status saat ini: 
                            @if(($locationSetting->value ?? '1') == '1')
                                <span class="font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded-md">AKTIF (Harus di lokasi sekolah)</span>
                            @else
                                <span class="font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md">NONAKTIF (Bebas lokasi presensi)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <form action="{{ route('admin.settings.toggle-location') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-5 py-2.5 text-xs font-bold rounded-lg text-white transition shadow-sm {{ ($locationSetting->value ?? '1') == '1' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-green-600 hover:bg-green-700' }}">
                                {{ ($locationSetting->value ?? '1') == '1' ? 'Matikan Validasi Lokasi' : 'Aktifkan Validasi Lokasi' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Card 2: Switch Batas Waktu Jam Presensi (15:00 WIB) -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg flex flex-col justify-between gap-4 border-l-4 {{ ($timeSetting->value ?? '1') == '1' ? 'border-indigo-500' : 'border-amber-500' }}">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Batas Waktu Jam Presensi (15:00 WIB)</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Status saat ini: 
                            @if(($timeSetting->value ?? '1') == '1')
                                <span class="font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">AKTIF (Kunci presensi setelah 15:00 WIB)</span>
                            @else
                                <span class="font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md">NONAKTIF (Bebas jam / Mode uji coba)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <form action="{{ route('admin.settings.toggle-time') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-5 py-2.5 text-xs font-bold rounded-lg text-white transition shadow-sm {{ ($timeSetting->value ?? '1') == '1' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                                {{ ($timeSetting->value ?? '1') == '1' ? 'Matikan Batas Waktu' : 'Aktifkan Batas Waktu' }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>