<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print:hidden">
            {{ __('Laporan & Rekap Presensi') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            .print\:hidden { display: none !important; }
            body { background-color: white !important; }
            .shadow-sm, .rounded-lg { box-shadow: none !important; border-radius: 0 !important; }
        }
    </style>

    <div class="py-12 print:py-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg print:hidden">
                <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Presensi</label>
                        <input type="date" name="date" value="{{ $selectedDate }}" class="mt-1 border-gray-300 rounded-md w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Filter Kelas</label>
                        <select name="class_id" class="mt-1 border-gray-300 rounded-md w-full">
                            <option value="" {{ empty($selectedClass) ? 'selected' : '' }}>-- Semua Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (string)$selectedClass === (string)$class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-semibold text-xs w-full">
                            🔍 Filter Data
                        </button>

                        <a href="{{ route('admin.reports.pdf', ['date' => $selectedDate, 'class_id' => $selectedClass]) }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 font-semibold text-xs flex items-center justify-center gap-1 w-full text-center">
                            📄 Download PDF
                        </a>

                        <button type="button" onclick="window.print()" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-800 font-semibold text-xs w-full">
                            🖨️ Print
                        </button>
                    </div>
                </form>
            </div>

            <div class="hidden print:block text-center mb-6">
                <h1 class="text-2xl font-bold uppercase">Laporan Presensi Siswa</h1>
                <p class="text-sm">Sistem Informasi Presensi Digital HadirKu</p>
                <hr class="my-2 border-gray-400">
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg print:p-0">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">
                        Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-300">
                                <th class="p-3 border border-gray-300">No</th>
                                <th class="p-3 border border-gray-300">NISN</th>
                                <th class="p-3 border border-gray-300">Nama Siswa</th>
                                <th class="p-3 border border-gray-300">Kelas</th>
                                <th class="p-3 border border-gray-300">Jam Masuk</th>
                                <th class="p-3 border border-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $index => $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="p-3 border border-gray-300">{{ $index + 1 }}</td>
                                    <td class="p-3 border border-gray-300 font-mono">{{ $item->student->nisn ?? '-' }}</td>
                                    <td class="p-3 border border-gray-300 font-semibold">{{ $item->student->user->name ?? 'N/A' }}</td>
                                    <td class="p-3 border border-gray-300">{{ $item->student->class->class_name ?? 'Tanpa Kelas' }}</td>
                                    <td class="p-3 border border-gray-300 font-mono">{{ $item->time_in }} WIB</td>
                                    <td class="p-3 border border-gray-300">
                                        @if($item->status === 'hadir')
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded print:bg-transparent print:text-black print:font-bold">HADIR</span>
                                        @elseif($item->status === 'terlambat')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5 rounded print:bg-transparent print:text-black print:font-bold">TERLAMBAT</span>
                                        @elseif($item->status === 'izin' || $item->status === 'sakit')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded print:bg-transparent print:text-black print:font-bold">{{ strtoupper($item->status) }}</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded print:bg-transparent print:text-black print:font-bold">ALFA</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500 border border-gray-300">Belum ada data presensi pada tanggal dan kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="hidden print:flex justify-between mt-12 text-sm">
                    <div></div>
                    <div class="text-center">
                        <p>Penanggung Jawab / Admin,</p>
                        <br><br><br>
                        <p class="font-bold underline">{{ Auth::user()->name }}</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>