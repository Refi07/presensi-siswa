<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Persetujuan Izin & Sakit Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Daftar Pengajuan Izin Masuk</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3">Siswa</th>
                                <th class="p-3">Kelas</th>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Alasan</th>
                                <th class="p-3">Lampiran</th>
                                <th class="p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($permissions as $p)
                                <tr>
                                    <td class="p-3 font-bold">{{ $p->student->user->name ?? '-' }}</td>
                                    <td class="p-3">{{ $p->student->class->class_name ?? '-' }}</td>
                                    <td class="p-3">{{ $p->date }}</td>
                                    <td class="p-3 uppercase font-semibold text-xs">{{ $p->type }}</td>
                                    <td class="p-3 text-sm">{{ $p->reason }}</td>
                                    <td class="p-3">
                                        @if($p->attachment)
                                            <a href="{{ asset('storage/' . $p->attachment) }}" target="_blank" class="text-indigo-600 underline text-xs">Lihat Surat</a>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($p->status == 'pending')
                                            <div class="flex gap-2">
                                                <form action="{{ route('teacher.permissions.update', $p->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="bg-green-600 text-white text-xs px-3 py-1.5 rounded hover:bg-green-700 font-bold">Setujui</button>
                                                </form>
                                                <form action="{{ route('teacher.permissions.update', $p->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="bg-red-600 text-white text-xs px-3 py-1.5 rounded hover:bg-red-700 font-bold">Tolak</button>
                                                </form>
                                            </div>
                                        @elseif($p->status == 'approved')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">Disetujui</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500">Belum ada pengajuan izin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>