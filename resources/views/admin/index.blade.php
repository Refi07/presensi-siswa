<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Rekap Presensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Laporan -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Kelas</label>
                        <select name="class_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClass == $c->id ? 'selected' : '' }}>
                                    {{ $c->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Bulan & Tahun</label>
                        <input type="month" name="month" value="{{ $selectedMonth }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-lg transition shadow-sm w-full">
                            Tampilkan
                        </button>

                        @if($selectedClass)
                            <a href="{{ route('admin.reports.print', ['class_id' => $selectedClass, 'month' => $selectedMonth]) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-4 py-2 rounded-lg transition shadow-sm flex items-center justify-center w-full text-center">
                                Cetak / Simpan PDF
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Data Laporan -->
            @if($selectedClass)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-gray-900">
                        Rekap Presensi Kelas {{ $className }} (Bulan {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }})
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b text-gray-800 text-sm bg-gray-50">
                                    <th class="py-3 px-2 text-center w-12">No</th>
                                    <th class="py-3 px-4 w-32">Tanggal</th>
                                    <th class="py-3 px-4">Nama Siswa</th>
                                    <th class="py-3 px-4 text-center w-32">Jam Masuk</th>
                                    <th class="py-3 px-4 text-center w-32">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($attendances as $index => $row)
                                    <tr>
                                        <td class="py-3 px-2 text-center font-medium text-gray-700">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4 font-mono text-gray-800">{{ $row->date }}</td>
                                        <td class="py-3 px-4 font-bold text-gray-900">{{ $row->student->user->name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-center text-gray-700">{{ $row->time_in ?? '-' }}</td>
                                        <td class="py-3 px-4 text-center font-bold uppercase text-xs">
                                            @if($row->status === 'hadir')
                                                <span class="text-green-600">HADIR</span>
                                            @elseif($row->status === 'terlambat')
                                                <span class="text-yellow-600">TERLAMBAT</span>
                                            @elseif($row->status === 'izin' || $row->status === 'sakit')
                                                <span class="text-blue-600">{{ strtoupper($row->status) }}</span>
                                            @else
                                                <span class="text-red-600">ALFA</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-gray-500">Tidak ada data presensi pada bulan ini.</td>
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