<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Kelola Jadwal</h2></x-slot>

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
                       placeholder="Cari kota..." class="border-gray-300 rounded-md shadow-sm">
                <select name="route_id" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">Semua rute</option>
                    @foreach ($routes as $r)
                        <option value="{{ $r->id }}" @selected(request('route_id') == $r->id)>
                            {{ $r->origin }} → {{ $r->destination }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">Semua status</option>
                    <option value="upcoming" @selected(request('status') === 'upcoming')>Akan datang</option>
                    <option value="past" @selected(request('status') === 'past')>Sudah lewat</option>
                    <option value="full" @selected(request('status') === 'full')>Kursi penuh</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Cari</button>
                <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Reset</a>
            </form>
            <a href="{{ route('admin.schedules.create') }}"
               class="px-4 py-2 bg-green-600 text-white rounded-md text-center">+ Tambah Jadwal</a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Rute</th>
                        <th class="p-3">Berangkat</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3">Kursi (sisa/total)</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $s)
                        <tr class="border-t">
                            <td class="p-3">{{ $s->route->origin }} → {{ $s->route->destination }}</td>
                            <td class="p-3">{{ $s->departure_at->format('d M Y, H:i') }}</td>
                            <td class="p-3">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="{{ $s->available_seats === 0 ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $s->available_seats }}/{{ $s->total_seats }}
                                </span>
                            </td>
                            <td class="p-3 flex gap-2">
                                <a href="{{ route('admin.schedules.edit', $s) }}"
                                   class="px-3 py-1 bg-yellow-400 rounded">Edit</a>
                                <form method="POST" action="{{ route('admin.schedules.destroy', $s) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">Belum ada jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $schedules->links() }}</div>
    </div>
</x-app-layout>