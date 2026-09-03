<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Data Kelas') }}
        </h2>
    </x-slot>

    <!-- Tom Select CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Tambah Kelas Baru -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Kelas Baru</h3>
                <form action="{{ route('admin.classes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kelas</label>
                        <input type="text" name="class_name" placeholder="Contoh: XI RPL 1" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Wali Kelas (Opsional)</label>
                        <select name="teacher_id" id="teacher_select" placeholder="🔍 Cari Wali Kelas..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition">
                            + Simpan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Kelas & Fitur Pencarian -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Kelas</h3>
                    <div class="w-1/3">
                        <input type="text" id="searchClass" placeholder="🔍 Cari nama kelas atau wali kelas..." 
                            class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="classTable">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-gray-700 font-bold">No</th>
                                <th class="p-3 text-gray-700 font-bold">Nama Kelas</th>
                                <th class="p-3 text-gray-700 font-bold">Wali Kelas</th>
                                <th class="p-3 text-gray-700 font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $index => $class)
                                <tr class="border-b hover:bg-gray-50 class-row">
                                    <td class="p-3 text-gray-800">{{ $index + 1 }}</td>
                                    <td class="p-3 text-gray-800 font-semibold class-name">{{ $class->class_name }}</td>
                                    <td class="p-3 text-gray-800 class-teacher">{{ $class->teacher->name ?? '-' }}</td>
                                    <td class="p-3">
                                        <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">Belum ada data kelas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Tom Select JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <!-- Script Inisialisasi Tom Select & Filter Pencarian Tabel -->
    <script>
        // Inisialisasi Searchable Dropdown
        new TomSelect("#teacher_select", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        // Script Filter Tabel Kelas
        document.getElementById('searchClass').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#classTable tbody .class-row');

            rows.forEach(row => {
                let className = row.querySelector('.class-name').textContent.toLowerCase();
                let teacherName = row.querySelector('.class-teacher').textContent.toLowerCase();

                if (className.includes(filter) || teacherName.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>