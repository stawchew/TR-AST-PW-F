<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Tiket Saya</h2></x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kota / kode tiket..." class="border-gray-300 rounded-md shadow-sm">
            <select name="status" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Semua status</option>
                @foreach (['pending' => 'Pending', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Cari</button>
            <a href="{{ route('user.tickets.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Reset</a>
        </form>

        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Rute</th>
                        <th class="p-3">Berangkat</th>
                        <th class="p-3">Penumpang</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Kode Tiket</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $t)
                        <tr class="border-t">
                            <td class="p-3">{{ $t->schedule->route->origin }} → {{ $t->schedule->route->destination }}</td>
                            <td class="p-3">{{ $t->schedule->departure_at->format('d M Y, H:i') }}</td>
                            <td class="p-3">{{ $t->passenger_count }}</td>
                            <td class="p-3">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                            <td class="p-3">@include('user.tickets._status', ['status' => $t->status])</td>
                            <td class="p-3 font-mono">{{ $t->ticket_code ?? '-' }}</td>
                            <td class="p-3 flex gap-2">
                                <a href="{{ route('user.tickets.show', $t) }}" class="px-3 py-1 bg-gray-200 rounded">Detail</a>
                                @if ($t->status === 'pending')
                                    <a href="{{ route('user.tickets.edit', $t) }}" class="px-3 py-1 bg-yellow-400 rounded">Edit</a>
                                    <form method="POST" action="{{ route('user.tickets.destroy', $t) }}"
                                          onsubmit="return confirm('Yakin ingin membatalkan tiket ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded">Batalkan</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-gray-500">Belum ada tiket.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $tickets->links() }}</div>
    </div>
</x-app-layout>