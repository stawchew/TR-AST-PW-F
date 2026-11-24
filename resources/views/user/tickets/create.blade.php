<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Pesan Tiket</h2></x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow mb-4">
            <div class="font-semibold text-lg">{{ $schedule->route->origin }} → {{ $schedule->route->destination }}</div>
            <div class="text-sm text-gray-600">{{ $schedule->departure_at->format('D, d M Y • H:i') }}</div>
            <div class="text-sm text-gray-600">Harga: Rp {{ number_format($schedule->price, 0, ',', '.') }} / orang • Sisa kursi: {{ $schedule->available_seats }}</div>
        </div>

        <form method="POST" action="{{ route('user.tickets.store', $schedule) }}" enctype="multipart/form-data"
              class="bg-white p-6 rounded shadow space-y-4"
              x-data="{ qty: {{ (int) old('passenger_count', 1) }}, price: {{ $schedule->price }} }">
            @csrf

            <div>
                <x-input-label for="passenger_count" value="Jumlah Penumpang" />
                <x-text-input id="passenger_count" name="passenger_count" type="number" min="1"
                              max="{{ $schedule->available_seats }}" class="mt-1 block w-full"
                              x-model.number="qty" required />
                <x-input-error :messages="$errors->get('passenger_count')" class="mt-2" />
            </div>

            <div class="p-3 bg-gray-50 rounded">
                Total bayar (estimasi):
                <span class="font-bold" x-text="'Rp ' + (qty * price || 0).toLocaleString('id-ID')"></span>
                <div class="text-xs text-gray-500">Total akhir dihitung ulang oleh server.</div>
            </div>

            <div>
                <x-input-label for="payment_proof" value="Bukti Transfer (JPG/PNG/PDF, maks 2 MB)" />
                <input id="payment_proof" name="payment_proof" type="file" accept=".jpg,.jpeg,.png,.pdf"
                       class="mt-1 block w-full text-sm" required>
                <x-input-error :messages="$errors->get('payment_proof')" class="mt-2" />
            </div>

            <div class="flex gap-2">
                <x-primary-button>Pesan Sekarang</x-primary-button>
                <a href="{{ route('user.schedules.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm">Kembali</a>
            </div>
        </form>
    </div>
</x-app-layout>