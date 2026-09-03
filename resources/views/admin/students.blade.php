<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Data Siswa') }}
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

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Import Excel / CSV -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-2">📁 Import Data Siswa via Excel / CSV</h3>
                <p class="text-xs text-gray-500 mb-4">
                    Format header kolom file Excel wajib: <b>nisn, nama_siswa, email_siswa, kelas, nama_ortu, email_ortu, no_wa_ortu</b>
                </p>
                <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-center">
                    @csrf
                    <input type="file" name="file" required class="w-full sm:w-2/3 text-sm border-gray-300 rounded-lg p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition shadow-sm">
                        Upload & Import Excel
                    </button>
                </form>
            </div>

            <!-- Form Tambah Data Siswa Baru Manual -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Data Siswa Baru (Manual)</h3>
                <form action="{{ route('admin.students.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap Siswa</label>
                        <input type="text" name="name" placeholder="Nama Siswa" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">NISN Siswa</label>
                        <input type="text" name="nisn" placeholder="Contoh: 0012345678" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email Login Siswa</label>
                        <input type="email" name="email" placeholder="siswa@gmail.com" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password Akun</label>
                        <input type="password" name="password" placeholder="••••••••" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kelas</label>
                        <select name="class_id" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Akun Orang Tua (Opsional)</label>
                        <select name="parent_id" id="parent_select" placeholder="🔍 Cari nama/email ortu..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Akun Orang Tua --</option>
                            @foreach($parents as $parent)
                                @php
                                    $childrenNames = $parent->students->pluck('user.name')->filter()->implode(', ');
                                @endphp
                                <option value="{{ $parent->id }}" data-children="{{ $childrenNames }}">
                                    {{ $parent->name }} ({{ $parent->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">No. WhatsApp Ortu (Aktif)</label>
                        <input type="text" name="parent_phone" placeholder="Contoh: 081234567890" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition shadow-sm">
                            + Simpan Data Siswa
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Siswa & Fitur Pencarian -->
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Siswa Terdaftar</h3>
                    <div class="w-full md:w-1/3">
                        <input type="text" id="searchStudent" placeholder="🔍 Cari siswa, NISN, kelas, ortu, no WA..." 
                            class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="studentTable">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-gray-700 font-bold">No</th>
                                <th class="p-3 text-gray-700 font-bold">NISN</th>
                                <th class="p-3 text-gray-700 font-bold">Nama Siswa</th>
                                <th class="p-3 text-gray-700 font-bold">Kelas</th>
                                <th class="p-3 text-gray-700 font-bold">Orang Tua</th>
                                <th class="p-3 text-gray-700 font-bold">No. WA Orang Tua</th>
                                <th class="p-3 text-gray-700 font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                <tr class="border-b hover:bg-gray-50 student-row">
                                    <td class="p-3 text-gray-800">{{ $index + 1 }}</td>
                                    <td class="p-3 text-gray-800 font-mono student-nisn">{{ $student->nisn }}</td>
                                    <td class="p-3 text-gray-800 font-semibold student-name">{{ $student->user->name ?? 'N/A' }}</td>
                                    <td class="p-3 text-gray-800 student-class">
                                        <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded">
                                            {{ $student->class->class_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-800 text-sm font-medium student-parent">
                                        {{ $student->parent->name ?? '-' }}
                                    </td>
                                    <td class="p-3 text-gray-800 font-mono text-sm student-phone">
                                        {{ $student->parent_phone ?? '-' }}
                                    </td>
                                    <td class="p-3">
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus siswa ini beserta akun loginnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500">Belum ada data siswa terdaftar.</td>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Simpan Peta Data Anak Sebelum Tom Select Diinisialisasi
            let parentChildrenMap = {};
            document.querySelectorAll('#parent_select option').forEach(opt => {
                if (opt.value) {
                    parentChildrenMap[opt.value] = {
                        name: opt.text,
                        children: opt.getAttribute('data-children') || ''
                    };
                }
            });

            // 2. Inisialisasi Tom Select
            let parentSelectElem = document.getElementById('parent_select');
            if (parentSelectElem) {
                let ts = new TomSelect("#parent_select", {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    onChange: function(value) {
                        if (!value || !parentChildrenMap[value]) return;
                        
                        let parentData = parentChildrenMap[value];
                        if (parentData.children && parentData.children.trim() !== "") {
                            let confirmMessage = `⚠️ PERHATIAN:\n\nAkun Orang Tua (${parentData.name}) ini sudah terhubung dengan siswa:\n👉 ${parentData.children}\n\nApakah Anda yakin ingin menghubungkan akun orang tua ini dengan siswa baru ini juga?`;
                            
                            if (!confirm(confirmMessage)) {
                                this.clear();
                            }
                        }
                    }
                });
            }

            // 3. Script JS Filter Pencarian Instant Tabel
            let searchInput = document.getElementById('searchStudent');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let rows = document.querySelectorAll('#studentTable tbody .student-row');

                    rows.forEach(row => {
                        let name = row.querySelector('.student-name').textContent.toLowerCase();
                        let nisn = row.querySelector('.student-nisn').textContent.toLowerCase();
                        let className = row.querySelector('.student-class').textContent.toLowerCase();
                        let parent = row.querySelector('.student-parent').textContent.toLowerCase();
                        let phone = row.querySelector('.student-phone').textContent.toLowerCase();

                        if (name.includes(filter) || nisn.includes(filter) || className.includes(filter) || parent.includes(filter) || phone.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>