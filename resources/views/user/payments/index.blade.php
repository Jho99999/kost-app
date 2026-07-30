@extends('layouts.app')
@section('title', 'Tagihan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-xl font-bold text-gray-900">Tagihan Saya</h1>
      <p class="text-sm text-gray-500 mt-0.5">Semua tagihan sewa bulanan Anda</p>
    </div>
  </div>

  @if($payments->isEmpty())
    <div class="text-center py-20 text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>
      </svg>
      <p>Belum ada tagihan. Tagihan akan muncul setelah pemesanan disetujui.</p>
    </div>
  @else
    <div class="space-y-3">
      @foreach($payments as $payment)
      @php
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
        $hasProof      = (bool) $payment->proof_image;
        $needsAttention = in_array($payment->status, ['pending','overdue']) && ! $hasProof;
      @endphp
      <a href="{{ route('payments.show', $payment) }}"
         class="block bg-white rounded-xl border shadow-sm transition-all
                {{ $payment->status === 'overdue' ? 'border-red-200 hover:border-red-300' : 'border-gray-100 hover:border-blue-100 hover:shadow-md' }}
                p-4">
        <div class="flex items-center justify-between gap-4">
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <span class="font-mono text-xs text-gray-400">{{ $payment->payment_code }}</span>
              <span class="{{ $sc }}">{{ $sl }}</span>
              @if($hasProof && $payment->status !== 'paid')
                <span class="badge badge-blue">Menunggu Verifikasi</span>
              @endif
              @if($payment->notes && $payment->status !== 'paid')
                <span class="badge badge-red">Bukti Ditolak</span>
              @endif
            </div>
            <p class="font-semibold text-gray-900">
              {{ $payment->booking->room->name }}
              <span class="font-normal text-gray-400 text-sm">— Bulan ke-{{ $payment->month_number }}</span>
            </p>
            <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
              <span>Jatuh tempo: {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</span>
              <span>·</span>
              <span class="font-medium text-gray-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            @if($needsAttention)
              <span class="text-xs text-amber-600 font-medium bg-amber-50 px-2 py-1 rounded-lg">Upload bukti</span>
            @endif
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
          </div>
        </div>
      </a>
      @endforeach
    </div>

    @if($payments->hasPages())
      <div class="mt-6">{{ $payments->links() }}</div>
    @endif
  @endif

</div>
@endsection
