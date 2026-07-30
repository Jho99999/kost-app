@extends('layouts.admin')
@section('title', 'Tagihan ' . $payment->payment_code)
@section('page-title', 'Detail Tagihan')

@section('content')

@php
  $isPaid    = $payment->status === 'paid';
  $hasProof  = (bool) $payment->proof_image;
  $canVerify = $hasProof && !$isPaid;
  $sc = match($payment->status) {
    'paid'    => 'badge badge-green',
    'overdue' => 'badge badge-red',
    default   => 'badge badge-yellow',
  };
  $sl = match($payment->status) {
    'paid'    => 'Lunas',
    'overdue' => 'Jatuh Tempo',
    default   => 'Belum Bayar',
  };
@endphp

{{-- Action bar --}}
<div class="flex items-center justify-between mb-6">
  <a href="{{ route('admin.payments.index') }}"
     class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>
  <span class="{{ $sc }} text-sm px-3 py-1">{{ $sl }}</span>
</div>

<div class="mb-4">
  <label class="text-xs text-gray-500">Nomor Referensi</label>
  <div class="mt-1">
    <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded font-mono text-sm text-gray-800">{{ $payment->payment_code }}</div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- Kolom kiri: detail + bukti --}}
  <div class="lg:col-span-2 space-y-5">

    {{-- Detail tagihan --}}
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Informasi Tagihan</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Kode Tagihan</span><span class="font-mono font-medium">{{ $payment->payment_code }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Penyewa</span><span class="font-medium text-gray-800">{{ $payment->user->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="text-gray-700">{{ $payment->user->email }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kamar</span><span class="font-medium text-gray-800">{{ $payment->booking->room->name }} ({{ $payment->booking->room->type }})</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tagihan Bulan ke-</span><span class="font-medium text-gray-800">{{ $payment->month_number }} dari {{ $payment->booking->duration_months }}</span></div>
        <hr class="border-gray-100">
        <div class="flex justify-between"><span class="text-gray-500">Jatuh Tempo</span>
          <span class="font-medium {{ $payment->status === 'overdue' ? 'text-red-600' : 'text-gray-800' }}">
            {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}
          </span>
        </div>
        <div class="flex justify-between text-base">
          <span class="font-semibold text-gray-700">Nominal</span>
          <span class="font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
        </div>
        @if($isPaid)
        <hr class="border-gray-100">
        <div class="flex justify-between"><span class="text-gray-500">Tanggal Lunas</span><span class="font-medium text-green-700">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : '-' }}</span></div>
        @if($payment->verifiedBy)
        <div class="flex justify-between"><span class="text-gray-500">Diverifikasi oleh</span><span class="text-gray-700">{{ $payment->verifiedBy->name }}</span></div>
        @endif
        @if($payment->verified_at)
        <div class="flex justify-between"><span class="text-gray-500">Waktu Verifikasi</span><span class="text-gray-700">{{ \Carbon\Carbon::parse($payment->verified_at)->format('d M Y, H:i') }}</span></div>
        @endif
        @endif
      </div>
    </div>

    {{-- Bukti pembayaran --}}
    @if($hasProof)
    <div class="card">
      <div class="card-header flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Bukti Pembayaran</h3>
        <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
           class="text-xs text-blue-600 hover:text-blue-700 font-medium">
          Buka di tab baru ↗
        </a>
      </div>
      <div class="card-body p-0">
        <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
           class="block hover:opacity-90 transition">
          <img src="{{ asset('storage/' . $payment->proof_image) }}"
               alt="Bukti pembayaran {{ $payment->payment_code }}"
               class="w-full max-h-96 object-contain bg-gray-50">
        </a>
      </div>
    </div>
    @else
    <div class="card">
      <div class="card-body text-center py-10 text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
        </svg>
        <p class="text-sm">Penyewa belum mengunggah bukti pembayaran.</p>
      </div>
    </div>
    @endif

  </div>

  {{-- Kolom kanan: form verifikasi --}}
  <div>
    @if($canVerify)
    <div class="card" x-data="{ action: '' }">
      <div class="card-header">
        <h3 class="text-sm font-semibold text-gray-700">Verifikasi Bukti</h3>
      </div>
      <div class="card-body space-y-4">
        <p class="text-sm text-gray-500">
          Periksa bukti pembayaran di sebelah kiri sebelum memverifikasi.
        </p>

        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}"
              x-on:submit="return action !== '' || (alert('Pilih aksi terlebih dahulu.'), false)">
          @csrf @method('PUT')
          <input type="hidden" name="action" :value="action">

          {{-- Catatan penolakan (muncul jika pilih tolak) --}}
          <div x-show="action === 'reject'" x-cloak class="mb-3">
            <label class="form-label">Alasan Penolakan <span class="text-red-500">*</span></label>
            <textarea name="notes" rows="3"
                      placeholder="cth. Nominal tidak sesuai, bukti tidak jelas, dll."
                      class="form-textarea text-sm"
                      x-bind:required="action === 'reject'"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <button type="submit" @click="action = 'approve'"
                    class="btn btn-primary btn-sm py-2.5">
              Lunas
            </button>
            <button type="submit" @click="action = 'reject'"
                    class="btn btn-danger btn-sm py-2.5">
              Tolak
            </button>
          </div>
        </form>

        <div class="text-xs text-gray-400 space-y-1">
          <p><strong>Lunas</strong> — tandai sebagai terbayar, kirim konfirmasi ke penyewa.</p>
          <p><strong>Tolak</strong> — hapus bukti, minta penyewa upload ulang dengan catatan alasan.</p>
        </div>
      </div>
    </div>

    @elseif($isPaid)
    <div class="card">
      <div class="card-body text-center py-8">
        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
          </svg>
        </div>
        <p class="font-semibold text-gray-800">Sudah Lunas</p>
        <p class="text-sm text-gray-400 mt-1">Tagihan ini sudah diverifikasi.</p>
      </div>
    </div>

    @else
    <div class="card">
      <div class="card-body text-center py-8 text-sm text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        Menunggu penyewa mengunggah bukti pembayaran.
      </div>
    </div>
    @endif
  </div>

</div>

@endsection
