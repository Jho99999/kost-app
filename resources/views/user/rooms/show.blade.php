@extends('layouts.app')
@section('title', $room->name . ' — Detail Kamar')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('rooms.index') }}" class="hover:text-gray-600">Kamar</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">{{ $room->name }}</span>
  </nav>

  @php
      $isAvailable = $room->status === 'available';
  @endphp

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom konten: galeri + deskripsi + fasilitas ── --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Galeri foto --}}
      @if(!empty($room->images))
      @php $imgUrls = array_map(fn($p) => asset('storage/'.$p), $room->images); @endphp
      <div x-data="{ active: 0, imgs: {{ json_encode($imgUrls) }} }" class="space-y-2">
        <div class="rounded-xl overflow-hidden bg-gray-100">
          <img :src="imgs[active]"
               alt="{{ $room->name }}"
               class="w-full h-72 sm:h-96 object-cover transition-opacity duration-150">
        </div>
        @if(count($imgUrls) > 1)
        <div class="flex gap-2 overflow-x-auto pb-1">
          <template x-for="(src, i) in imgs" :key="i">
            <button type="button" @click="active = i"
                    :class="active === i ? 'ring-2 ring-blue-500 ring-offset-1' : 'opacity-60 hover:opacity-90'"
                    class="rounded-lg overflow-hidden flex-shrink-0 transition focus:outline-none">
              <img :src="src" class="w-16 h-16 object-cover">
            </button>
          </template>
        </div>
        @endif
      </div>
      @else
      <div class="rounded-xl bg-slate-100 h-64 flex items-center justify-center text-slate-300">
        <div class="text-center">
          <svg class="w-12 h-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
          </svg>
          <p class="text-sm">Belum ada foto</p>
        </div>
      </div>
      @endif

      {{-- Deskripsi --}}
      @if($room->description)
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-gray-800 mb-2">Tentang Kamar</h2>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $room->description }}</p>
      </div>
      @endif

      {{-- Fasilitas --}}
      @if(!empty($room->facilities))
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-gray-800 mb-3">Fasilitas</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          @foreach($room->facilities as $f)
          <div class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
            </svg>
            {{ $f }}
          </div>
          @endforeach
        </div>
      </div>
      @endif

    </div>

    {{-- Sidebar: harga + pesan ──────────────────────── --}}
    <div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sticky top-24 space-y-4">

        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="badge badge-gray">{{ $room->type }}</span>
            @if($isAvailable)
              <span class="badge badge-green">Tersedia</span>
            @else
              <span class="badge badge-blue">Terisi</span>
            @endif
          </div>
          <h1 class="text-lg font-bold text-gray-900">{{ $room->name }}</h1>
        </div>

        <div>
          <p class="text-3xl font-extrabold text-blue-700">{{ $room->formatted_price }}</p>
          <p class="text-sm text-gray-400">per bulan</p>
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
        </dl>

        <hr class="border-gray-100">

        {{-- Tombol pemesanan ─────────────────────── --}}
        @if(!$isAvailable)
          {{-- Kamar terisi --}}
          <button disabled class="btn btn-secondary btn-lg w-full opacity-50 cursor-not-allowed">
            Kamar Sedang Terisi
          </button>
          <p class="text-xs text-gray-400 text-center">Kamar ini sedang ditempati.</p>

        @elseif($activeBooking)
          {{-- User sudah punya booking aktif --}}
          <button disabled class="btn btn-secondary btn-lg w-full opacity-50 cursor-not-allowed">
            Tidak Dapat Memesan
          </button>
          <p class="text-xs text-gray-400 text-center">
            Anda masih memiliki
            <a href="{{ route('bookings.show', $activeBooking) }}" class="text-blue-600 hover:underline">pemesanan aktif</a>.
          </p>

        @else
          {{-- Bisa pesan --}}
          <a href="{{ route('bookings.create', $room) }}"
             class="btn btn-primary btn-lg w-full text-center block">
            Ajukan Pemesanan
          </a>
          <p class="text-xs text-gray-400 text-center">
            Pemesanan diproses setelah admin menyetujui.
          </p>
        @endif

        <a href="{{ route('rooms.index') }}"
           class="block text-center text-sm text-gray-400 hover:text-gray-600 transition-colors">
          ← Lihat kamar lain
        </a>

      </div>
    </div>

  </div>
</div>
@endsection
