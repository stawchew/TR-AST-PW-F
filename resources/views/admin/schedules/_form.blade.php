@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium">Rute</label>
        <select name="route_id" class="w-full border-gray-300 rounded-md">
            <option value="">-- Pilih rute --</option>
            @foreach ($routes as $r)
                <option value="{{ $r->id }}" @selected(old('route_id', $schedule->route_id ?? '') == $r->id)>
                    {{ $r->origin }} → {{ $r->destination }}
                </option>
            @endforeach
        </select>
        @error('route_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Waktu Keberangkatan</label>
        <input type="datetime-local" name="departure_at"
               value="{{ old('departure_at', isset($schedule) ? $schedule->departure_at->format('Y-m-d\TH:i') : '') }}"
               class="w-full border-gray-300 rounded-md">
        @error('departure_at') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Harga Tiket (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $schedule->price ?? '') }}"
               class="w-full border-gray-300 rounded-md">
        @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Total Kursi</label>
        <input type="number" name="total_seats" value="{{ old('total_seats', $schedule->total_seats ?? '') }}"
               class="w-full border-gray-300 rounded-md">
        @error('total_seats') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Simpan</button>
        <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Batal</a>
    </div>
</div>