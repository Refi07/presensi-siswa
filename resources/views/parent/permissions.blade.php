<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan Izin / Sakit Anak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Pengajuan -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Form Pengajuan Izin/Sakit</h3>
                <form action="{{ route('parent.permissions.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih Anak</label>
                        <select name="student_id" class="mt-1 border-gray-300 rounded-md w-full" required>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Izin</label>
                        <input type="date" name="date" class="mt-1 border-gray-300 rounded-md w-full" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="type" class="mt-1 border-gray-300 rounded-md w-full" required>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Foto Surat Dokter / Surat Izin (Opsional)</label>
                        <input type="file" name="attachment" accept="image/*" class="mt-1 border-gray-300 rounded-md w-full">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Alasan / Keterangan</label>
                        <textarea name="reason" rows="3" class="mt-1 border-gray-300 rounded-md w-full" required placeholder="Tuliskan alasan izin..."></textarea>
                    </div>

                    <div class="md:col-span-2 text-right">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-semibold">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Riwayat Pengajuan -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Riwayat Pengajuan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3">Anak</th>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Alasan</th>
                                <th class="p-3">Lampiran</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($permissions as $p)
                                <tr>
                                    <td class="p-3 font-bold">{{ $p->student->user->name ?? '-' }}</td>
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
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">Menunggu</span>
                                        @elseif($p->status == 'approved')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">Disetujui</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">Belum ada pengajuan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>