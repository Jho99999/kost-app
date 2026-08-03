@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

  {{-- Greeting ──────────────────────────────────────── --}}
  <div class="mb-7">
    <h1 class="text-xl font-bold text-gray-900">
      Selamat datang, {{ auth()->user()->name }} 👋
    </h1>
    <p class="text-sm text-gray-500 mt-0.5">
      {{ now()->isoFormat('dddd, D MMMM Y') }}
    </p>
  </div>

  {{-- Alert: ada tagihan jatuh tempo / perlu upload bukti ─ --}}
  @if($overdueCount > 0)
  <div class="alert alert-error mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
    </svg>
    <div>
      <p class="font-medium">Anda memiliki {{ $overdueCount }} tagihan yang sudah jatuh tempo.</p>
      <a href="{{ route('payments.index') }}" class="back-link mt-2">Lihat tagihan →</a>
    </div>
  </div>
  @endif

  {{-- Status Section ─────────────────────────────────── --}}
  @if($activeBooking)
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">

    {{-- Booking aktif --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-semibold text-gray-700">Pemesanan Aktif</h2>
        <span class="{{ $activeBooking->status_color }}">{{ $activeBooking->status_label }}</span>
      </div>
      <p class="font-bold text-gray-900 text-lg">{{ $activeBooking->room->name }}</p>
      <p class="text-sm text-gray-500 mt-0.5">
        {{ $activeBooking->room->type }} · Lantai {{ $activeBooking->room->floor }}
      </p>
      <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
        <div>
          <p class="text-gray-400 text-xs">Tanggal masuk</p>
          <p class="font-medium text-gray-700">{{ $activeBooking->check_in_date->format('d M Y') }}</p>
        </div>
        <div>
          <p class="text-gray-400 text-xs">Estimasi keluar</p>
          <p class="font-medium text-gray-700">{{ $activeBooking->check_out_date->format('d M Y') }}</p>
        </div>
      </div>
      <a href="{{ route('bookings.show', $activeBooking) }}"
         class="back-link mt-4">
        Lihat detail pemesanan →
      </a>
    </div>

    {{-- Tagihan berikutnya --}}
    @if($nextPayment)
    <div class="bg-white rounded-xl border {{ $nextPayment->status === 'overdue' ? 'border-red-200' : 'border-gray-100' }} shadow-sm p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-semibold text-gray-700">Tagihan Berikutnya</h2>
        <span class="{{ $nextPayment->status_color }}">{{ $nextPayment->status_label }}</span>
      </div>
      <p class="font-bold text-gray-900 text-2xl">{{ $nextPayment->formatted_amount }}</p>
      <div class="mt-2 text-sm space-y-1">
        <div class="flex items-center justify-between">
          <span class="text-gray-500">Bulan ke-</span>
          <span class="font-medium">{{ $nextPayment->month_number }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-gray-500">Jatuh tempo</span>
          <span class="font-medium {{ $nextPayment->status === 'overdue' ? 'text-red-600' : 'text-gray-700' }}">
            {{ $nextPayment->due_date->format('d M Y') }}
          </span>
        </div>
        @if($nextPayment->proof_image && $nextPayment->status !== 'paid')
          <p class="text-xs text-blue-600 mt-1">Bukti sudah diunggah — menunggu verifikasi admin.</p>
        @endif
      </div>
      @if($nextPayment->status !== 'paid')
        <a href="{{ route('payments.show', $nextPayment) }}"
           class="mt-4 btn btn-primary btn-sm w-full text-center block">
          {{ $nextPayment->proof_image ? 'Lihat Status Tagihan' : 'Bayar Sekarang' }}
        </a>
      @else
        <p class="mt-4 text-xs text-green-600 font-medium text-center">✓ Tagihan ini sudah lunas</p>
      @endif
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center justify-center text-center">
      <div>
        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        <p class="text-sm text-gray-500">Semua tagihan sudah lunas.</p>
      </div>
    </div>
    @endif

  </div>
  @endif

  {{-- Kamar tersedia ──────────────────────────────────── --}}
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-base font-semibold text-gray-800">Kamar Tersedia</h2>
    <a href="{{ route('rooms.index') }}"
       class="back-link">Lihat semua →</a>
  </div>

  @if($availableRooms->isEmpty())
    <div class="text-center py-12 text-gray-400">
      <p class="text-sm">Tidak ada kamar yang tersedia saat ini.</p>
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($availableRooms as $room)
      <a href="{{ route('rooms.show', $room) }}"
         class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all overflow-hidden">

        <div class="relative h-40 bg-slate-100 overflow-hidden">
          @if(!empty($room->images))
            <img src="{{ asset('storage/' . $room->images[0]) }}"
                 alt="{{ $room->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
          @else
            <div class="w-full h-full flex items-center justify-center">
              <svg class="w-8 h-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
              </svg>
            </div>
          @endif
          <span class="absolute top-2 left-2 badge badge-gray">{{ $room->type }}</span>
        </div>

        <div class="p-4">
          <p class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $room->name }}</p>
          <p class="text-xs text-gray-400 mt-0.5">Lantai {{ $room->floor }} · {{ $room->capacity }} orang</p>
          <p class="text-blue-700 font-bold mt-2">{{ $room->formatted_price }}
            <span class="text-gray-400 font-normal text-xs">/ bulan</span>
          </p>
        </div>
      </a>
      @endforeach
    </div>

    @if(!$activeBooking)
    <div class="mt-6 text-center">
      <a href="{{ route('rooms.index') }}" class="btn btn-primary btn-lg">
        Cari dan Pesan Kamar
      </a>
    </div>
    @endif
  @endif

</div>
@endsection
