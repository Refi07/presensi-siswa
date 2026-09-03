<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Akun Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Tambah Orang Tua Baru -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Akun Orang Tua Baru</h3>
                <form action="{{ route('admin.parents.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Orang Tua</label>
                        <input type="text" name="name" placeholder="Nama Orang Tua" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email Login</label>
                        <input type="email" name="email" placeholder="ortu@gmail.com" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password Akun</label>
                        <input type="password" name="password" placeholder="••••••••" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition">
                            + Simpan Data Orang Tua
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Data Orang Tua & Fitur Pencarian -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Akun Orang Tua Terdaftar</h3>
                    <div class="w-1/3">
                        <input type="text" id="searchParent" placeholder="🔍 Cari nama ortu, email, atau nama anak..." 
                            class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="parentTable">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-gray-700 font-bold">No</th>
                                <th class="p-3 text-gray-700 font-bold">Nama Orang Tua</th>
                                <th class="p-3 text-gray-700 font-bold">Email Login</th>
                                <th class="p-3 text-gray-700 font-bold">Nama Anak (Siswa)</th>
                                <th class="p-3 text-gray-700 font-bold">Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parents as $index => $parent)
                                <tr class="border-b hover:bg-gray-50 parent-row">
                                    <td class="p-3 text-gray-800">{{ $index + 1 }}</td>
                                    <td class="p-3 text-gray-800 font-semibold parent-name">{{ $parent->name }}</td>
                                    <td class="p-3 text-gray-800 parent-email">{{ $parent->email }}</td>
                                    <td class="p-3 text-gray-800 font-medium child-name">
                                        @if(isset($parent->students) && $parent->students->count() > 0)
                                            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-semibold">
                                                {{ $parent->students->pluck('user.name')->join(', ') }}
                                            </span>
                                        @elseif(isset($parent->student->user))
                                            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-semibold">
                                                {{ $parent->student->user->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Belum ditautkan</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-600 text-sm">
                                        {{ \Carbon\Carbon::parse($parent->created_at)->translatedFormat('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">Belum ada data orang tua.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Script JS Filter Pencarian Instant -->
    <script>
        document.getElementById('searchParent').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#parentTable tbody .parent-row');

            rows.forEach(row => {
                let name = row.querySelector('.parent-name').textContent.toLowerCase();
                let email = row.querySelector('.parent-email').textContent.toLowerCase();
                let child = row.querySelector('.child-name').textContent.toLowerCase();

                if (name.includes(filter) || email.includes(filter) || child.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>