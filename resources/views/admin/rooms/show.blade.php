@extends('layouts.admin')
@section('title', $room->name)
@section('page-title', $room->name)

@section('content')

@php
$statusClass = match($room->status) {
    'available'   => 'badge badge-green',
    'occupied'    => 'badge badge-blue',
    'maintenance' => 'badge badge-yellow',
    default       => 'badge badge-gray',
};
$statusLabel = match($room->status) {
    'available'   => 'Tersedia',
    'occupied'    => 'Terisi',
    'maintenance' => 'Perbaikan',
    default       => ucfirst($room->status),
};
@endphp

{{-- Action bar ───────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.rooms.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-secondary btn-sm">Edit Kamar</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Galeri foto ──────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="card">
            <div class="card-body p-0 overflow-hidden rounded-xl">
                @if(!empty($room->images))
                    <div x-data="{ active: 0 }">
                        <img :src="images[active]"
                             x-init="$el.setAttribute('src', images[0])"
                             :src="images[active]"
                             alt="{{ $room->name }}"
                             class="w-full h-72 object-cover"
                             x-bind:src="'{{ asset('storage/') }}/' + images[active]">
                        {{-- JS: inject images array --}}
                        <div x-data="{ images: {{ json_encode($room->images) }} }" class="hidden"></div>
                    </div>
                    @php $imgs = $room->images; @endphp
                    {{-- Fallback non-JS render + Alpine gallery --}}
                    <div x-data="{ active: 0, imgs: {{ json_encode(array_map(fn($p) => asset('storage/'.$p), $imgs)) }} }">
                        <img :src="imgs[active]" alt="{{ $room->name }}" class="w-full h-72 object-cover">
                        @if(count($imgs) > 1)
                        <div class="flex gap-2 p-3 bg-gray-50">
                            <template x-for="(src, i) in imgs" :key="i">
                                <button type="button" @click="active = i"
                                        class="focus:outline-none rounded-lg overflow-hidden transition"
                                        :class="active === i ? 'ring-2 ring-blue-500' : 'opacity-60 hover:opacity-100'">
                                    <img :src="src" class="w-14 h-14 object-cover">
                                </button>
                            </template>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-56 bg-slate-100 flex items-center justify-center">
                        <div class="text-center text-gray-300">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                            <p class="text-sm">Belum ada foto</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Deskripsi --}}
        @if($room->description)
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Deskripsi</h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-gray-600 leading-relaxed">{{ $room->description }}</p>
            </div>
        </div>
        @endif

        {{-- Riwayat pemesanan --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Pemesanan Terbaru</h3>
            </div>
            @if($room->bookings->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead class="table-thead">
                        <tr>
                            <th class="table-th">Kode</th>
                            <th class="table-th">Penyewa</th>
                            <th class="table-th">Check-in</th>
                            <th class="table-th">Durasi</th>
                            <th class="table-th">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($room->bookings as $booking)
                        @php
                            $bc = match($booking->status) {
                                'approved' => 'badge badge-green',
                                'pending'  => 'badge badge-yellow',
                                'rejected' => 'badge badge-red',
                                'expired'  => 'badge badge-gray',
                                default    => 'badge badge-gray',
                            };
                            $bl = match($booking->status) {
                                'approved' => 'Disetujui',
                                'pending'  => 'Menunggu',
                                'rejected' => 'Ditolak',
                                'expired'  => 'Kadaluarsa',
                                default    => ucfirst($booking->status),
                            };
                        @endphp
                        <tr class="table-tr">
                            <td class="table-td font-mono text-xs text-gray-500">{{ $booking->booking_code }}</td>
                            <td class="table-td text-sm text-gray-800">{{ $booking->user->name ?? '-' }}</td>
                            <td class="table-td text-sm text-gray-600">
                                {{ $booking->check_in_date?->format('d/m/Y') }}
                            </td>
                            <td class="table-td text-sm text-gray-600">{{ $booking->duration_months }} bln</td>
                            <td class="table-td"><span class="{{ $bc }}">{{ $bl }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="card-body text-center py-8 text-sm text-gray-400">
                    Belum ada pemesanan untuk kamar ini.
                </div>
            @endif
        </div>

    </div>

    {{-- Sidebar: detail kamar ────────────────────────── --}}
    <div class="space-y-4">
        <div class="card">
            <div class="card-body space-y-4">

                <div class="flex items-center justify-between">
                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                    <span class="badge badge-gray">{{ $room->type }}</span>
                </div>

                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $room->formatted_price }}</p>
                    <p class="text-xs text-gray-400">per bulan</p>
                </div>

                <hr class="border-gray-100">

                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Lantai</dt>
                        <dd class="font-medium text-gray-800">{{ $room->floor }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kapasitas</dt>
                        <dd class="font-medium text-gray-800">{{ $room->capacity }} orang</dd>
                    </div>
                    @if($room->size_sqm)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Luas</dt>
                        <dd class="font-medium text-gray-800">{{ $room->size_sqm }} m²</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Foto</dt>
                        <dd class="font-medium text-gray-800">{{ count($room->images ?? []) }} foto</dd>
                    </div>
                </dl>

            </div>
        </div>

        {{-- Fasilitas --}}
        @if(!empty($room->facilities))
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Fasilitas</h3>
            </div>
            <div class="card-body">
                <div class="flex flex-wrap gap-1.5">
                    @foreach($room->facilities as $f)
                        <span class="badge badge-gray">{{ $f }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Aksi --}}
        <a href="{{ route('admin.rooms.edit', $room) }}"
           class="btn btn-primary w-full text-center block">Edit Kamar</a>

        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}"
              onsubmit="return confirm('Hapus kamar {{ addslashes($room->name) }}? Semua foto dan data terkait akan hilang.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger w-full">Hapus Kamar</button>
        </form>

    </div>
</div>

@endsection
