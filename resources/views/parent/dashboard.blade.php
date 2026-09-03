<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portal Orang Tua - Monitoring Kehadiran Anak') }}
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
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    Akun Orang Tua Anda belum dihubungkan ke data Siswa manapun. Silakan hubungi Pihak Sekolah/Admin.
                </div>
            @else
                <!-- Info Anak -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-bold text-gray-900">Anak: {{ $student->user->name ?? 'N/A' }}</h3>
                    <p class="text-gray-700 font-medium">NISN: {{ $student->nisn }} | Kelas: {{ $student->class->class_name ?? '-' }}</p>
                </div>

                <!-- Ringkasan Statistik -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-gray-500 text-sm font-bold uppercase">TOTAL HADIR</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalHadir }} Hari</p>
                    </div>
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-gray-500 text-sm font-bold uppercase">TERLAMBAT</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalTerlambat }} Hari</p>
                    </div>
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-gray-500 text-sm font-bold uppercase">IZIN / SAKIT</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalIzin }} Hari</p>
                    </div>
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <p class="text-gray-500 text-sm font-bold uppercase">TANPA KETERANGAN</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $totalAlfa }} Hari</p>
                    </div>
                </div>

                <!-- Riwayat Presensi -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-gray-900">Riwayat Kehadiran</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="p-2 text-gray-700 font-bold">Tanggal</th>
                                    <th class="p-2 text-gray-700 font-bold">Jam Masuk</th>
                                    <th class="p-2 text-gray-700 font-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $item)
                                    <tr class="border-b">
                                        <td class="p-2 text-gray-800">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}</td>
                                        <td class="p-2 text-gray-800">{{ $item->time_in ?? '-' }} WIB</td>
                                        <td class="p-2">
                                            @if($item->status === 'hadir')
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded">HADIR</span>
                                            @elseif($item->status === 'terlambat')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded">TERLAMBAT</span>
                                            @elseif($item->status === 'izin' || $item->status === 'sakit')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">{{ strtoupper($item->status) }}</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded">ALFA</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-500">Belum ada riwayat presensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>