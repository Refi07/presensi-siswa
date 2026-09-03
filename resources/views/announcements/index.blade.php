<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengumuman & Informasi Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Buat Pengumuman (Hanya Admin & Guru) -->
            @if(in_array(Auth::user()->role, ['admin', 'teacher']))
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-gray-900">Buat Pengumuman Baru</h3>
                    <form action="{{ route('announcements.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
                            <input type="text" name="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
                            <textarea name="content" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                            Terbitkan
                        </button>
                    </form>
                </div>
            @endif

            <!-- Daftar Pengumuman -->
            <div class="space-y-4">
                @forelse($announcements as $item)
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $item->title }}</h3>
                                <p class="text-xs text-gray-500 mb-3">
                                    Oleh: <span class="font-semibold">{{ $item->author->name ?? 'Admin' }}</span> | {{ $item->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if(in_array(Auth::user()->role, ['admin', 'teacher']))
                                <form action="{{ route('announcements.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold">Hapus</button>
                                </form>
                            @endif
                        </div>
                        <p class="text-gray-700 whitespace-pre-line">{{ $item->content }}</p>
                    </div>
                @empty
                    <div class="bg-white p-6 text-center text-gray-500 rounded-lg">
                        Belum ada pengumuman terbaru.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>