@extends('layouts.app')
@section('title', 'Kamar Tersedia')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Header ──────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Cari Kamar</h1>
        <p class="text-sm text-gray-500 mt-0.5">Temukan kamar yang sesuai kebutuhan Anda</p>
    </div>

    {{-- Filter bar ───────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('rooms.index') }}"
              class="flex flex-wrap gap-3 items-end">

            <div class="flex-1 min-w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                <select name="type" class="form-select w-full">
                    <option value="">Semua Tipe</option>
                    @foreach(['Standard','Deluxe','VIP'] as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1">Harga Maks (Rp)</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}"
                       placeholder="Semua harga" min="1" step="1"
                       class="form-input w-full">
            </div>

            <div class="flex-1 min-w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1">Urutan</label>
                <select name="sort" class="form-select w-full">
                    <option value="">Default (lantai)</option>
                    <option value="price_asc"  @selected(request('sort') === 'price_asc')>Harga: Termurah</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga: Termahal</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary h-9 px-4 text-sm">Cari</button>
                @if(request()->hasAny(['type','max_price','sort']))
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary h-9 px-3 text-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Jumlah hasil ──────────────────────────────────── --}}
    <p class="text-sm text-gray-500 mb-4">
        Menampilkan <strong class="text-gray-700">{{ $rooms->total() }}</strong> kamar
        @if(request('type')) dengan tipe <strong>{{ request('type') }}</strong> @endif
    </p>

    {{-- Grid kamar ────────────────────────────────────── --}}
    @if($rooms->isEmpty())
        <div class="text-center py-20">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
            </svg>
            <p class="text-gray-400 text-sm">Tidak ada kamar yang sesuai filter.</p>
            <a href="{{ route('rooms.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-1 inline-block">Tampilkan semua kamar</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($rooms as $room)
            @php
                $isAvailable = $room->status === 'available';
            @endphp
            <a href="{{ route('rooms.show', $room) }}"
               class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all overflow-hidden flex flex-col">

                {{-- Foto --}}
                <div class="relative h-44 bg-slate-100 overflow-hidden flex-shrink-0">
                    @if(!empty($room->images[0]))
                        <img src="{{ asset('storage/' . $room->images[0]) }}"
                             alt="{{ $room->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Badge status --}}
                    <div class="absolute top-2.5 left-2.5">
                        @if($isAvailable)
                            <span class="badge badge-green text-xs px-2 py-0.5 shadow-sm">Tersedia</span>
                        @else
                            <span class="badge badge-blue text-xs px-2 py-0.5 shadow-sm">Terisi</span>
                        @endif
                    </div>

                    {{-- Badge tipe --}}
                    <div class="absolute top-2.5 right-2.5">
                        <span class="badge badge-gray text-xs px-2 py-0.5 shadow-sm">{{ $room->type }}</span>
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">
                        {{ $room->name }}
                    </h3>

                    <div class="flex items-center gap-3 text-xs text-gray-400 mt-1">
                        <span>Lantai {{ $room->floor }}</span>
                        <span>·</span>
                        <span>{{ $room->capacity }} orang</span>
                        @if($room->size_sqm)
                            <span>·</span>
                            <span>{{ $room->size_sqm }} m²</span>
                        @endif
                    </div>

                    {{-- Fasilitas --}}
                    @if(!empty($room->facilities))
                    <div class="flex flex-wrap gap-1 mt-3">
                        @foreach(array_slice($room->facilities, 0, 3) as $f)
                            <span class="text-xs bg-gray-50 text-gray-500 border border-gray-100 rounded px-1.5 py-0.5">{{ $f }}</span>
                        @endforeach
                        @if(count($room->facilities) > 3)
                            <span class="text-xs text-gray-400">+{{ count($room->facilities) - 3 }} lagi</span>
                        @endif
                    </div>
                    @endif

                    <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $room->formatted_price }}</p>
                            <p class="text-xs text-gray-400">/ bulan</p>
                        </div>
                        <span class="back-link btn-sm">Lihat →</span>
                    </div>
                </div>

            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($rooms->hasPages())
            <div class="mt-8">
                {{ $rooms->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
