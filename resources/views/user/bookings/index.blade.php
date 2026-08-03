@extends('layouts.app')
@section('title', 'Riwayat Pemesanan')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold text-gray-900">Riwayat Pemesanan</h1>
      <p class="text-sm text-gray-500 mt-0.5">Semua pengajuan pemesanan kamar Anda</p>
    </div>
    <a href="{{ route('rooms.index') }}" class="btn btn-primary btn-sm">Cari Kamar</a>
  </div>

  @if($bookings->isEmpty())
    <div class="text-center py-20">
      <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
      </svg>
      <p class="text-gray-400">Belum ada pemesanan.</p>
      <a href="{{ route('rooms.index') }}" class="back-link mt-2">
        Lihat kamar tersedia →
      </a>
    </div>
  @else
    <div class="space-y-3">
      @foreach($bookings as $booking)
      @php
        $statusClass = match($booking->status) {
          'pending'  => 'badge badge-yellow',
          'approved' => 'badge badge-green',
          'rejected' => 'badge badge-red',
          'expired'  => 'badge badge-gray',
          default    => 'badge badge-gray',
        };
        $statusLabel = match($booking->status) {
          'pending'  => 'Menunggu',
          'approved' => 'Disetujui',
          'rejected' => 'Ditolak',
          'expired'  => 'Kadaluarsa',
          default    => ucfirst($booking->status),
        };
      @endphp
      <a href="{{ route('bookings.show', $booking) }}"
         class="block bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-100 transition-all p-4">
        <div class="flex items-center justify-between gap-4">
          <div class="min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-mono text-xs text-gray-400">{{ $booking->booking_code }}</span>
              <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <p class="font-semibold text-gray-900 truncate">{{ $booking->room->name }}</p>
            <p class="text-sm text-gray-500 mt-0.5">
              {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}
              &middot; {{ $booking->duration_months }} bulan
              &middot; {{ $booking->room->formatted_price }}/bln
            </p>
          </div>
          <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
          </svg>
        </div>
      </a>
      @endforeach
    </div>

    @if($bookings->hasPages())
      <div class="mt-6">{{ $bookings->links() }}</div>
    @endif
  @endif

</div>
@endsection
