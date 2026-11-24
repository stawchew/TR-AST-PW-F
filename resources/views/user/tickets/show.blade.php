<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Detail Tiket</h2></x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow space-y-3">
            <div class="flex justify-between items-start">
                <div class="font-semibold text-lg">{{ $ticket->schedule->route->origin }} → {{ $ticket->schedule->route->destination }}</div>
                @include('user.tickets._status', ['status' => $ticket->status])
            </div>

            <dl class="grid grid-cols-2 gap-y-2 text-sm">
                <dt class="text-gray-500">Berangkat</dt>
                <dd>{{ $ticket->schedule->departure_at->format('D, d M Y • H:i') }}</dd>
                <dt class="text-gray-500">Jumlah penumpang</dt>
                <dd>{{ $ticket->passenger_count }}</dd>
                <dt class="text-gray-500">Harga / orang</dt>
                <dd>Rp {{ number_format($ticket->schedule->price, 0, ',', '.') }}</dd>
                <dt class="text-gray-500">Total bayar</dt>
                <dd class="font-bold">Rp {{ number_format($ticket->total_price, 0, ',', '.') }}</dd>
                <dt class="text-gray-500">Kode tiket</dt>
                <dd class="font-mono">{{ $ticket->ticket_code ?? 'Belum diterbitkan' }}</dd>
                <dt class="text-gray-500">Dipesan pada</dt>
                <dd>{{ $ticket->created_at->format('d M Y, H:i') }}</dd>
            </dl>

            @if ($ticket->payment_proof)
                <div>
                    <div class="text-sm text-gray-500 mb-1">Bukti transfer</div>
                    @if (Str::endsWith(strtolower($ticket->payment_proof), '.pdf'))
                        <a href="{{ asset('storage/' . $ticket->payment_proof) }}" target="_blank" class="text-indigo-600 text-sm">Buka PDF</a>
                    @else
                        <img src="{{ asset('storage/' . $ticket->payment_proof) }}" class="max-h-64 rounded border" alt="Bukti transfer">
                    @endif
                </div>
            @endif

            <div class="flex gap-2 pt-2">
                <a href="{{ route('user.tickets.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm">Kembali</a>
                @if ($ticket->status === 'pending')
                    <a href="{{ route('user.tickets.edit', $ticket) }}" class="px-4 py-2 bg-yellow-400 rounded-md text-sm">Edit</a>
                    <form method="POST" action="{{ route('user.tickets.destroy', $ticket) }}"
                          onsubmit="return confirm('Yakin ingin membatalkan tiket ini?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 bg-red-600 text-white rounded-md text-sm">Batalkan</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>