<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Cari Jadwal</h2></x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <form method="GET" class="flex flex-wrap gap-2 mb-6">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kota asal / tujuan..." class="border-gray-300 rounded-md shadow-sm">
            <select name="route_id" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Semua rute</option>
                @foreach ($routes as $r)
                    <option value="{{ $r->id }}" @selected(request('route_id') == $r->id)>
                        {{ $r->origin }} → {{ $r->destination }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="border-gray-300 rounded-md shadow-sm">
            <select name="availability" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Semua kursi</option>
                <option value="available" @selected(request('availability') === 'available')>Masih tersedia</option>
            </select>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Cari</button>
            <a href="{{ route('user.schedules.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Reset</a>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($schedules as $s)
                <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                    @if ($s->route->image)
                        <img src="{{ asset('storage/' . $s->route->image) }}" class="w-full h-36 object-cover" alt="">
                    @else
                        <div class="w-full h-36 bg-gray-200 flex items-center justify-center text-gray-400 text-sm">Tanpa gambar</div>
                    @endif
                    <div class="p-4 flex-1 flex flex-col gap-1">
                        <div class="font-semibold text-lg">{{ $s->route->origin }} → {{ $s->route->destination }}</div>
                        <div class="text-sm text-gray-600">{{ $s->departure_at->format('D, d M Y • H:i') }}</div>
                        <div class="text-sm text-gray-600">Durasi ± {{ intdiv($s->route->duration_minutes, 60) }} jam {{ $s->route->duration_minutes % 60 }} menit</div>
                        <div class="font-bold mt-1">Rp {{ number_format($s->price, 0, ',', '.') }} <span class="text-xs font-normal text-gray-500">/ orang</span></div>
                        <div class="text-sm {{ $s->available_seats === 0 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            Sisa kursi: {{ $s->available_seats }}/{{ $s->total_seats }}
                        </div>
                        <div class="mt-auto pt-3">
                            @if ($s->available_seats > 0)
                                <a href="{{ route('user.tickets.create', $s) }}"
                                   class="block text-center px-4 py-2 bg-green-600 text-white rounded-md">Pesan Tiket</a>
                            @else
                                <span class="block text-center px-4 py-2 bg-gray-200 text-gray-500 rounded-md">Kursi Penuh</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-8 rounded shadow text-center text-gray-500">Tidak ada jadwal yang cocok.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $schedules->links() }}</div>
    </div>
</x-app-layout>