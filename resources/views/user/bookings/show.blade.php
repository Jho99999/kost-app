@extends('layouts.app')
@section('title', 'Detail Pemesanan — ' . $booking->booking_code)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('bookings.index') }}" class="hover:text-gray-600">Pemesanan</a>
    <span>/</span>
    <span class="font-mono text-gray-700">{{ $booking->booking_code }}</span>
  </nav>

  @php
    $statusClass = match($booking->status) {
      'pending'  => 'badge badge-yellow',
      'approved' => 'badge badge-green',
      'rejected' => 'badge badge-red',
      'expired'  => 'badge badge-gray',
      default    => 'badge badge-gray',
    };
    $statusLabel = match($booking->status) {
      'pending'  => 'Menunggu Persetujuan',
      'approved' => 'Disetujui',
      'rejected' => 'Ditolak',
      'expired'  => 'Kadaluarsa',
      default    => ucfirst($booking->status),
    };
    $checkOut = \Carbon\Carbon::parse($booking->check_in_date)
                  ->addMonths($booking->duration_months)
                  ->subDay();
  @endphp

  {{-- Header status --}}
  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h1 class="text-xl font-bold text-gray-900">Detail Pemesanan</h1>
      <p class="text-sm font-mono text-gray-400 mt-0.5">{{ $booking->booking_code }}</p>
    </div>
    <span class="{{ $statusClass }} text-sm px-3 py-1">{{ $statusLabel }}</span>
  </div>

  {{-- Banner status --}}
  @if($booking->status === 'pending')
  <div class="alert alert-warning mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
    Pemesanan sedang menunggu persetujuan admin. Anda akan menerima email setelah diproses.
  </div>
  @elseif($booking->status === 'rejected')
  <div class="alert alert-error mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
    </svg>
    <div>
      Pemesanan ditolak.
      @if($booking->notes)
        <span class="block text-sm mt-0.5">Catatan admin: <em>{{ $booking->notes }}</em></span>
      @endif
    </div>
  </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Detail kamar --}}
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Kamar</h3></div>
      <div class="card-body space-y-2 text-sm">
        <p class="font-semibold text-gray-900 text-base">{{ $booking->room->name }}</p>
        <p class="text-gray-500">{{ $booking->room->type }} · Lantai {{ $booking->room->floor }}</p>
        <hr class="border-gray-100">
        <div class="flex justify-between"><span class="text-gray-500">Harga/bulan</span><span class="font-medium">{{ $booking->room->formatted_price }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kapasitas</span><span class="font-medium">{{ $booking->room->capacity }} orang</span></div>
      </div>
    </div>

    {{-- Detail booking --}}
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Rincian Sewa</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Tanggal masuk</span><span class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Estimasi keluar</span><span class="font-medium">{{ $checkOut->format('d M Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Durasi</span><span class="font-medium">{{ $booking->duration_months }} bulan</span></div>
        <hr class="border-gray-100">
        <div class="flex justify-between text-base">
          <span class="font-semibold text-gray-700">Total sewa</span>
          <span class="font-bold text-gray-900">Rp {{ number_format($booking->room->price * $booking->duration_months, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between"><span class="text-gray-500">Diajukan</span><span class="font-medium">{{ $booking->created_at->format('d M Y, H:i') }}</span></div>
      </div>
    </div>

  </div>

  {{-- Jadwal tagihan (hanya muncul jika approved) --}}
  @if($booking->status === 'approved' && $booking->payments->isNotEmpty())
  <div class="card mt-5">
    <div class="card-header flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-700">Jadwal Tagihan</h3>
      <a href="{{ route('payments.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
        Kelola pembayaran →
      </a>
    </div>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead class="table-thead">
          <tr>
            <th class="table-th">Bulan ke-</th>
            <th class="table-th">Jatuh Tempo</th>
            <th class="table-th">Nominal</th>
            <th class="table-th">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @foreach($booking->payments as $payment)
          @php
            $pClass = match($payment->status) {
              'paid'    => 'badge badge-green',
              'overdue' => 'badge badge-red',
              default   => 'badge badge-yellow',
            };
            $pLabel = match($payment->status) {
              'paid'    => 'Lunas',
              'overdue' => 'Jatuh Tempo',
              default   => 'Belum Bayar',
            };
          @endphp
          <tr class="table-tr">
            <td class="table-td text-center text-gray-600">{{ $payment->month_number }}</td>
            <td class="table-td text-gray-700">{{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</td>
            <td class="table-td font-medium text-gray-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            <td class="table-td"><span class="{{ $pClass }}">{{ $pLabel }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  <div class="mt-6">
    <a href="{{ route('bookings.index') }}"
       class="back-link">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
      </svg>
      Kembali ke riwayat pemesanan
    </a>
  </div>

</div>
@endsection
