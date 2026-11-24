@php
    $map = [
        'pending'   => ['Pending',   'bg-yellow-100 text-yellow-800'],
        'verified'  => ['Terverifikasi', 'bg-green-100 text-green-800'],
        'rejected'  => ['Ditolak',   'bg-red-100 text-red-800'],
        'cancelled' => ['Dibatalkan', 'bg-gray-200 text-gray-700'],
    ];
    [$label, $class] = $map[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-700'];
@endphp
<span class="px-2 py-1 rounded text-xs font-semibold {{ $class }}">{{ $label }}</span>