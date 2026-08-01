@extends('layouts.admin')
@section('title', 'Manajemen Kamar')
@section('page-title', 'Manajemen Kamar')

@section('content')

{{-- Header ──────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <p class="text-sm text-gray-500">Total <strong class="text-gray-700">{{ $rooms->total() }}</strong> kamar terdaftar</p>
    <a href="{{ route('admin.rooms.create') }}"
   class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg bg-blue-600 text-white text-sm font-medium leading-none hover:bg-blue-700 transition">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    <span class="leading-none">Tambah Kamar</span>
    </a>
</div>

{{-- Filter ───────────────────────────────────────────── --}}
<div class="card mb-5">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.rooms.index') }}"
              class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama kamar…"
                   class="form-input flex-1 min-w-40">

            <select name="type" class="form-select w-36">
                <option value="">Semua Tipe</option>
                @foreach(['Standard','Deluxe','VIP'] as $t)
                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                @endforeach
            </select>

            <select name="status" class="form-select w-36">
                <option value="">Semua Status</option>
                <option value="available"   @selected(request('status') === 'available')>Tersedia</option>
                <option value="occupied"    @selected(request('status') === 'occupied')>Terisi</option>
                <option value="maintenance" @selected(request('status') === 'maintenance')>Perbaikan</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm h-9 px-4">Filter</button>

            @if(request()->hasAny(['search','type','status']))
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary btn-sm h-9 px-3">Reset</a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel ────────────────────────────────────────────── --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="table-base">
            <thead class="table-thead">
                <tr>
                    <th class="table-th">Kamar</th>
                    <th class="table-th">Tipe</th>
                    <th class="table-th">Lantai</th>
                    <th class="table-th">Kapasitas</th>
                    <th class="table-th">Harga / Bulan</th>
                    <th class="table-th">Status</th>
                    <th class="table-th"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($rooms as $room)
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
                <tr class="table-tr">
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            {{-- Thumbnail --}}
                            @if(!empty($room->images[0]))
                                <img src="{{ asset('storage/' . $room->images[0]) }}"
                                     alt="{{ $room->name }}"
                                     class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $room->name }}</p>
                                @if($room->size_sqm)
                                    <p class="text-xs text-gray-400">{{ $room->size_sqm }} m²</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="table-td">
                        <span class="badge badge-gray">{{ $room->type }}</span>
                    </td>
                    <td class="table-td text-gray-600">Lantai {{ $room->floor }}</td>
                    <td class="table-td text-gray-600">{{ $room->capacity }} orang</td>
                    <td class="table-td font-semibold text-gray-800">{{ $room->formatted_price }}</td>
                    <td class="table-td">
                        <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.rooms.show', $room) }}"
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat</a>
                            <a href="{{ route('admin.rooms.edit', $room) }}"
                               class="text-sm text-amber-600 hover:text-amber-800 font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}"
                                  onsubmit="return confirm('Hapus kamar {{ addslashes($room->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-sm text-red-600 hover:text-red-800 font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-14 text-center">
                        <div class="flex flex-col items-center gap-2 text-gray-400">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                            </svg>
                            <p class="text-sm">Belum ada kamar terdaftar.</p>
                            <a href="{{ route('admin.rooms.create') }}"
                               class="text-sm text-blue-600 hover:text-blue-700 font-medium">Tambah kamar pertama →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rooms->hasPages())
        <div class="card-footer">
            {{ $rooms->links() }}
        </div>
    @endif
</div>

@endsection
