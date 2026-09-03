<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Data Guru & Wali Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Tambah Guru Baru -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Guru Baru</h3>
                <form action="{{ route('admin.teachers.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap Guru</label>
                        <input type="text" name="name" placeholder="NIP / Nama Guru" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email Akun Login</label>
                        <input type="email" name="email" placeholder="email@gmail.com" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password Akun</label>
                        <input type="password" name="password" placeholder="••••••••" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition">
                            + Simpan Data Guru
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Guru & Fitur Pencarian -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Guru / Wali Kelas</h3>
                    <div class="w-1/3">
                        <input type="text" id="searchTeacher" placeholder="🔍 Cari nama atau email guru..." 
                            class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="teacherTable">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-gray-700 font-bold">No</th>
                                <th class="p-3 text-gray-700 font-bold">Nama Guru</th>
                                <th class="p-3 text-gray-700 font-bold">Email</th>
                                <th class="p-3 text-gray-700 font-bold">Role</th>
                                <th class="p-3 text-gray-700 font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $index => $teacher)
                                <tr class="border-b hover:bg-gray-50 teacher-row">
                                    <td class="p-3 text-gray-800">{{ $index + 1 }}</td>
                                    <td class="p-3 text-gray-800 font-semibold teacher-name">{{ $teacher->name }}</td>
                                    <td class="p-3 text-gray-800 teacher-email">{{ $teacher->email }}</td>
                                    <td class="p-3">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">Teacher</span>
                                    </td>
                                    <td class="p-3">
                                        <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus guru ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">Belum ada data guru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Script JS Filter Pencarian -->
    <script>
        document.getElementById('searchTeacher').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#teacherTable tbody .teacher-row');

            rows.forEach(row => {
                let name = row.querySelector('.teacher-name').textContent.toLowerCase();
                let email = row.querySelector('.teacher-email').textContent.toLowerCase();

                if (name.includes(filter) || email.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>