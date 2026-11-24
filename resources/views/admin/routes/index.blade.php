<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Kelola Rute</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kota asal/tujuan..."
                       class="border-gray-300 rounded-md shadow-sm">
                <select name="origin" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">Semua kota asal</option>
                    @foreach ($origins as $origin)
                        <option value="{{ $origin }}" @selected(request('origin') === $origin)>{{ $origin }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Cari</button>
                <a href="{{ route('admin.routes.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Reset</a>
            </form>
            <a href="{{ route('admin.routes.create') }}"
               class="px-4 py-2 bg-green-600 text-white rounded-md text-center">+ Tambah Rute</a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Gambar</th>
                        <th class="p-3">Rute</th>
                        <th class="p-3">Durasi</th>
                        <th class="p-3">Jadwal</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($routes as $route)
                        <tr class="border-t">
                            <td class="p-3">
                                @if ($route->image)
                                    <img src="{{ asset('storage/' . $route->image) }}" class="w-20 h-14 object-cover rounded">
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 font-medium">{{ $route->origin }} → {{ $route->destination }}</td>
                            <td class="p-3">{{ intdiv($route->duration_minutes, 60) }} jam {{ $route->duration_minutes % 60 }} menit</td>
                            <td class="p-3">{{ $route->schedules_count }}</td>
                            <td class="p-3 flex gap-2">
                                <a href="{{ route('admin.routes.edit', $route) }}"
                                   class="px-3 py-1 bg-yellow-400 rounded">Edit</a>
                                <form method="POST" action="{{ route('admin.routes.destroy', $route) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus rute ini? Jadwal terkait ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada data rute.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $routes->links() }}</div>
    </div>
</x-app-layout>