<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa - Kartu Digital Presensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Widget Pengumuman Sekolah Terbaru -->
            @php
                $latestAnnouncements = \App\Models\Announcement::latest()->take(3)->get();
            @endphp

            @if($latestAnnouncements->count() > 0)
                <div class="bg-indigo-50 border-l-4 border-indigo-600 p-4 rounded-r-lg shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-md font-bold text-indigo-900">📢 Pengumuman Sekolah Terbaru</h3>
                        <a href="{{ route('announcements.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach($latestAnnouncements as $ann)
                            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-xs">
                                <h4 class="font-bold text-sm text-gray-800">{{ $ann->title }}</h4>
                                <p class="text-xs text-gray-600 line-clamp-2 mt-1">{{ $ann->content }}</p>
                                <span class="text-[10px] text-gray-400 mt-2 block">{{ $ann->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!$student)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    ⚠️ Profil Siswa Anda belum dikonfigurasi oleh Admin.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Kartu Presensi Digital & QR Code -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 text-center flex flex-col items-center justify-center">
                        <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full uppercase mb-3">Kartu Pelajar Digital</span>
                        <h3 class="text-xl font-bold text-gray-800">{{ $student->user->name ?? 'Siswa' }}</h3>
                        <p class="text-sm text-gray-500 mb-4">NISN: {{ $student->nisn }} | Kelas: {{ $student->class->class_name ?? '-' }}</p>

                        <!-- Tampilan QR Code -->
                        <div class="bg-gray-50 p-4 rounded-xl border-2 border-dashed border-indigo-300 inline-block mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ $student->qr_code_token }}" alt="QR Code Siswa" class="mx-auto rounded-md shadow-sm">
                        </div>
                        
                        <p class="text-xs text-gray-500 mb-4">Tunjukkan QR Code ini ke scanner kamera sekolah untuk presensi masuk.</p>

                        <!-- Tombol Scan Mandiri GPS -->
                        <a href="{{ route('student.scan') }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm flex items-center justify-center gap-2">
                            📍 Scan Presensi GPS
                        </a>
                    </div>

                    <!-- Statistik & Riwayat Kehadiran -->
                    <div class="md:col-span-2 space-y-6">
                        
                        <!-- Ringkasan Kehadiran & Kode Unik Kelas -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                                <p class="text-xs text-gray-500 font-semibold uppercase">Total Kehadiran</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalHadir }} Hari</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
                                <p class="text-xs text-gray-500 font-semibold uppercase">Total Terlambat</p>
                                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalTerlambat }} Hari</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-indigo-500">
                                <p class="text-xs text-gray-500 font-semibold uppercase">Kode Kelas Hari Ini</p>
                                <p class="text-2xl font-mono font-bold text-indigo-600 mt-1 tracking-wider">{{ $todayCode ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Riwayat Terbaru -->
                        <div class="bg-white p-6 shadow-sm rounded-lg">
                            <h3 class="text-lg font-bold mb-4">Riwayat Kehadiran Kamu</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b bg-gray-50 text-sm text-gray-600">
                                            <th class="p-3">Tanggal</th>
                                            <th class="p-3">Jam Masuk</th>
                                            <th class="p-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @forelse($attendances as $item)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 font-semibold">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y') }}</td>
                                                <td class="p-3 font-mono">{{ $item->time_in ?? '-' }} WIB</td>
                                                <td class="p-3">
                                                    @if($item->status === 'hadir')
                                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">HADIR</span>
                                                    @elseif($item->status === 'terlambat')
                                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">TERLAMBAT</span>
                                                    @elseif($item->status === 'izin' || $item->status === 'sakit')
                                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ strtoupper($item->status) }}</span>
                                                    @else
                                                        <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">ALFA</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="p-4 text-center text-gray-500">Belum ada data presensi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>