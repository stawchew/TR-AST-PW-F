@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium">Kota Asal</label>
        <input type="text" name="origin" value="{{ old('origin', $route->origin ?? '') }}"
               class="w-full border-gray-300 rounded-md">
        @error('origin') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Kota Tujuan</label>
        <input type="text" name="destination" value="{{ old('destination', $route->destination ?? '') }}"
               class="w-full border-gray-300 rounded-md">
        @error('destination') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Durasi (menit)</label>
        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $route->duration_minutes ?? '') }}"
               class="w-full border-gray-300 rounded-md">
        @error('duration_minutes') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Gambar (maks 2MB)</label>
        @if (!empty($route->image))
            <img src="{{ asset('storage/' . $route->image) }}" class="w-40 h-28 object-cover rounded mb-2">
        @endif
        <input type="file" name="image" accept="image/*">
        @error('image') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Simpan</button>
        <a href="{{ route('admin.routes.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Batal</a>
    </div>
</div>