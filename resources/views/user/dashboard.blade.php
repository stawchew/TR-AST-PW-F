<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard</h2></x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded shadow flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>Halo, <span class="font-semibold">{{ auth()->user()->name }}</span>! Mau berangkat ke mana hari ini?</div>
            <a href="{{ route('user.schedules.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-center">Cari Jadwal</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500">Total Pesanan</div>
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500">Menunggu Verifikasi</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500">Terverifikasi</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['verified'] }}</div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500">Ditolak / Dibatalkan</div>
                <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] + $stats['cancelled'] }}</div>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Total Pengeluaran (tiket terverifikasi)</div>
            <div class="text-2xl font-bold">Rp {{ number_format($stats['spent'], 0, ',', '.') }}</div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <div class="p-4 font-semibold border-b flex justify-between">
                <span>Pesanan Terbaru</span>
                <a href="{{ route('user.tickets.index') }}" class="text-sm text-indigo-600">Lihat semua</a>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Rute</th>
                        <th class="p-3">Berangkat</th>
                        <th class="p-3">Total</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestTickets as $t)
                        <tr class="border-t">
                            <td class="p-3">
                                <a href="{{ route('user.tickets.show', $t) }}" class="text-indigo-600">
                                    {{ $t->schedule->route->origin }} → {{ $t->schedule->route->destination }}
                                </a>
                            </td>
                            <td class="p-3">{{ $t->schedule->departure_at->format('d M Y, H:i') }}</td>
                            <td class="p-3">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                            <td class="p-3">@include('user.tickets._status', ['status' => $t->status])</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>