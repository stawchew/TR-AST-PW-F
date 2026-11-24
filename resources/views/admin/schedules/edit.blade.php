<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Edit Jadwal</h2></x-slot>
    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}"
              class="bg-white p-6 shadow rounded-lg">
            @method('PUT')
            @include('admin.schedules._form')
        </form>
    </div>
</x-app-layout>