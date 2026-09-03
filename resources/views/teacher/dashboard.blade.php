<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Guru & Kelola Presensi Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$myClass)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    Anda belum ditugaskan sebagai Wali Kelas di kelas manapun. Hubungi Admin.
                </div>
            @else
                <!-- Banner Kode Unik Presensi Kelas Hari Ini -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-6 rounded-xl shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold">Kode Presensi Kelas Hari Ini</h3>
                        <p class="text-xs text-indigo-200">Bagikan kode unik ini ke siswa kelas {{ $myClass->class_name }} untuk absen mandiri.</p>
                    </div>
                    <div class="bg-white/10 border border-white/20 backdrop-blur-md px-6 py-2 rounded-lg text-center">
                        <span class="text-3xl font-extrabold tracking-widest font-mono text-yellow-300">{{ $todayClassCode }}</span>
                    </div>
                </div>

                <!-- Status Kunci Edit -->
                @if($isLocked)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                        <div class="flex">
                            <div>
                                <p class="text-sm text-yellow-700 font-bold">
                                    Sistem Pengubahan Presensi Terkunci
                                </p>
                                <p class="text-xs text-yellow-600 mt-1">
                                    Batas waktu pengisian/pengubahan presensi harian ({{ $lockTime }} WIB) telah lewat. Fitur ubah status manual dinonaktifkan.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info Kelas -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Wali Kelas: {{ $myClass->class_name }}</h3>
                        <p class="text-gray-700 font-medium">Tanggal Hari Ini: {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <a href="{{ route('attendance.scan') }}" class="bg-white border border-gray-300 text-indigo-600 hover:bg-gray-50 px-4 py-2 rounded-md font-semibold text-sm shadow-sm flex items-center gap-2">
                        Buka Scan QR Presensi
                    </a>
                </div>

                <!-- Tabel Presensi Siswa -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-gray-900">
                        Daftar Presensi Siswa Kelas {{ $myClass->class_name }} Hari Ini
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b text-gray-800 text-sm">
                                    <th class="py-3 px-2 text-center w-12">No</th>
                                    <th class="py-3 px-4 w-36">NISN</th>
                                    <th class="py-3 px-4">Nama Siswa</th>
                                    <th class="py-3 px-4 text-center w-32">Jam Masuk</th>
                                    <th class="py-3 px-4 text-center w-40">Status Saat Ini</th>
                                    <th class="py-3 px-4 text-center w-52">Ubah Status Manual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($students as $index => $student)
                                    @php
                                        $attendance = $todayAttendances->get($student->id);
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-2 text-center text-gray-700 font-medium">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4 text-gray-800 font-mono">{{ $student->nisn }}</td>
                                        <td class="py-3 px-4 text-gray-900 font-bold">{{ $student->user->name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-center text-gray-700">
                                            {{ $attendance ? $attendance->time_in . ' WIB' : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if(!$attendance)
                                                <span class="text-gray-500 font-medium">Belum Absen</span>
                                            @elseif($attendance->status === 'hadir')
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded">HADIR</span>
                                            @elseif($attendance->status === 'terlambat')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-1 rounded">TERLAMBAT</span>
                                            @elseif($attendance->status === 'izin' || $attendance->status === 'sakit')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded">{{ strtoupper($attendance->status) }}</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-1 rounded">ALFA</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($isLocked)
                                                <span class="text-gray-400 text-xs italic">Terkunci</span>
                                            @else
                                                <form action="{{ route('teacher.attendance.manual') }}" method="POST" class="inline-flex gap-1 justify-center">
                                                    @csrf
                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                    <button type="submit" name="status" value="hadir" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-2 py-1 rounded">Hadir</button>
                                                    <button type="submit" name="status" value="izin" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-2 py-1 rounded">Izin</button>
                                                    <button type="submit" name="status" value="alfa" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-2 py-1 rounded">Alfa</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-500">Belum ada siswa terdaftar di kelas ini.</td>
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