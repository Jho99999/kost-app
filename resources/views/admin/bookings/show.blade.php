@extends('layouts.admin')
@section('title', 'Pemesanan ' . $booking->booking_code)
@section('page-title', 'Detail Pemesanan')

@section('content')

@php
  $isPending = $booking->status === 'pending';
  $sc = match($booking->status) {
    'pending'  => 'badge badge-yellow',
    'approved' => 'badge badge-green',
    'rejected' => 'badge badge-red',
    'expired'  => 'badge badge-gray',
    default    => 'badge badge-gray',
  };
  $sl = match($booking->status) {
    'pending'  => 'Menunggu Persetujuan',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
    'expired'  => 'Kadaluarsa',
    default    => ucfirst($booking->status),
  };
  $checkOut = \Carbon\Carbon::parse($booking->check_in_date)
                ->addMonths($booking->duration_months)->subDay();
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <a href="{{ route('admin.bookings.index') }}"
     class="back-link">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>
  <span class="{{ $sc }} text-sm px-3 py-1">{{ $sl }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- Kolom kiri: detail --}}
  <div class="lg:col-span-2 space-y-5">

    {{-- Info penyewa --}}
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Data Penyewa</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Nama</span><span class="font-medium text-gray-800">{{ $booking->user->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="text-gray-700">{{ $booking->user->email }}</span></div>
        @if($booking->user->phone)
        <div class="flex justify-between"><span class="text-gray-500">Telepon</span><span class="text-gray-700">{{ $booking->user->phone }}</span></div>
        @endif
      </div>
    </div>

    {{-- Info kamar + rincian sewa --}}
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Rincian Pemesanan</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Kode Booking</span><span class="font-mono font-medium text-gray-800">{{ $booking->booking_code }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kamar</span><span class="font-medium text-gray-800">{{ $booking->room->name }} ({{ $booking->room->type }})</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Lantai</span><span class="text-gray-700">{{ $booking->room->floor }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Harga/bulan</span><span class="font-medium text-gray-800">{{ $booking->room->formatted_price }}</span></div>
        <hr class="border-gray-100">
        <div class="flex justify-between"><span class="text-gray-500">Tanggal masuk</span><span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Estimasi keluar</span><span class="text-gray-700">{{ $checkOut->format('d M Y') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Durasi</span><span class="font-medium text-gray-800">{{ $booking->duration_months }} bulan</span></div>
        <div class="flex justify-between text-base">
          <span class="font-semibold text-gray-700">Total sewa</span>
          <span class="font-bold text-gray-900">Rp {{ number_format($booking->room->price * $booking->duration_months, 0, ',', '.') }}</span>
        </div>
        <hr class="border-gray-100">
        <div class="flex justify-between"><span class="text-gray-500">Diajukan</span><span class="text-gray-700">{{ $booking->created_at->format('d M Y, H:i') }}</span></div>
        @if($booking->approvedBy)
        <div class="flex justify-between">
          <span class="text-gray-500">Diproses oleh</span>
          <span class="text-gray-700">{{ $booking->approvedBy->name }}</span>
        </div>
        @endif
        @if($booking->notes)
        <div>
          <span class="text-gray-500">Catatan</span>
          <p class="mt-1 text-gray-700 bg-gray-50 rounded-lg p-2 text-xs">{{ $booking->notes }}</p>
        </div>
        @endif
      </div>
    </div>

    {{-- Jadwal tagihan (hanya jika sudah approved) --}}
    @if($booking->status === 'approved' && $booking->payments->isNotEmpty())
    <div class="card">
      <div class="card-header flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Tagihan yang Digenerate</h3>
        <span class="text-xs text-gray-400">{{ $booking->payments->count() }} tagihan</span>
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
              $pc = match($payment->status) {
                'paid'    => 'badge badge-green',
                'overdue' => 'badge badge-red',
                default   => 'badge badge-yellow',
              };
              $pl = match($payment->status) {
                'paid'    => 'Lunas',
                'overdue' => 'Jatuh Tempo',
                default   => 'Belum Bayar',
              };
            @endphp
            <tr class="table-tr">
              <td class="table-td text-center text-gray-600">{{ $payment->month_number }}</td>
              <td class="table-td text-gray-700">{{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</td>
              <td class="table-td font-medium text-gray-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
              <td class="table-td"><span class="{{ $pc }}">{{ $pl }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

  </div>

  {{-- Kolom kanan: form approve/reject --}}
  <div>
    @if($isPending)
    <div class="card" x-data="{ action: '' }">
      <div class="card-header">
        <h3 class="text-sm font-semibold text-gray-700">Proses Pemesanan</h3>
      </div>
      <div class="card-body space-y-4">
        <p class="text-sm text-gray-500">
          Setujui pemesanan ini untuk otomatis meng-generate
          <strong>{{ $booking->duration_months }} tagihan bulanan</strong>
          dan mengunci status kamar menjadi <em>Terisi</em>.
        </p>

        <form method="POST" action="{{ route('admin.bookings.update', $booking) }}"
              x-on:submit="return action !== '' || (alert('Pilih aksi terlebih dahulu.'), false)">
          @csrf @method('PUT')
          <input type="hidden" name="action" :value="action">

          {{-- Notes (muncul jika pilih tolak) --}}
          <div x-show="action === 'reject'" x-cloak class="mb-3">
            <label class="form-label">Alasan Penolakan <span class="text-red-500">*</span></label>
            <textarea name="notes" rows="3"
                      placeholder="Tulis alasan penolakan yang jelas untuk penyewa…"
                      class="form-textarea text-sm"
                      x-bind:required="action === 'reject'"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <button type="submit"
                    @click="action = 'approve'"
                    class="btn btn-primary btn-sm py-2.5">
              Setujui
            </button>
            <button type="submit"
                    @click="action = 'reject'"
                    class="btn btn-danger btn-sm py-2.5">
              Tolak
            </button>
          </div>
        </form>

        <p class="text-xs text-gray-400">
          Tindakan ini tidak dapat dibatalkan setelah diproses.
        </p>
      </div>
    </div>
    @else
    <div class="card">
      <div class="card-body text-center py-8">
        <p class="text-sm text-gray-400">Pemesanan ini sudah diproses.</p>
        <span class="{{ $sc }} mt-2 inline-block">{{ $sl }}</span>
      </div>
    </div>
    @endif
  </div>

</div>

@endsection
