@extends('layouts.admin')
@section('title', 'Manajemen Pembayaran')
@section('page-title', 'Manajemen Pembayaran')

@section('content')

{{-- Summary cards ─────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
  <div class="card">
    <div class="card-body flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-bold text-gray-900">{{ $summary['pending'] }}</p>
        <p class="text-xs text-gray-500">Belum Bayar</p>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-bold text-blue-700">{{ $summary['awaiting'] }}</p>
        <p class="text-xs text-gray-500">Menunggu Verifikasi</p>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-bold text-red-700">{{ $summary['overdue'] }}</p>
        <p class="text-xs text-gray-500">Jatuh Tempo</p>
      </div>
    </div>
  </div>
</div>

{{-- Filter ────────────────────────────────────────── --}}
<div class="card mb-5">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('admin.payments.index') }}"
          class="flex flex-wrap items-center gap-2">
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Kode pembayaran, nama, atau kamar…"
             class="form-input flex-1 min-w-48">
      <select name="status" class="form-select w-36">
        <option value="">Semua Status</option>
        <option value="pending" @selected(request('status') === 'pending')>Belum Bayar</option>
        <option value="paid"    @selected(request('status') === 'paid')>Lunas</option>
        <option value="overdue" @selected(request('status') === 'overdue')>Jatuh Tempo</option>
      </select>
      <select name="proof" class="form-select w-44">
        <option value="">Semua Bukti</option>
        <option value="uploaded" @selected(request('proof') === 'uploaded')>Sudah Upload Bukti</option>
        <option value="empty"    @selected(request('proof') === 'empty')>Belum Upload Bukti</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm h-9 px-4">Cari</button>
      @if(request()->hasAny(['search','status','proof']))
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm h-9 px-3">Reset</a>
      @endif
    </form>
  </div>
</div>

{{-- Tabel ─────────────────────────────────────────── --}}
<div class="card">
  <div class="overflow-x-auto">
    <table class="table-base">
      <thead class="table-thead">
        <tr>
          <th class="table-th">Kode</th>
          <th class="table-th">Penyewa</th>
          <th class="table-th">Kamar</th>
          <th class="table-th">Bulan ke-</th>
          <th class="table-th">Jatuh Tempo</th>
          <th class="table-th">Nominal</th>
          <th class="table-th">Bukti</th>
          <th class="table-th">Status</th>
          <th class="table-th"></th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($payments as $payment)
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
        @endphp
        <tr class="table-tr">
          <td class="table-td font-mono text-xs text-gray-500">{{ $payment->payment_code }}</td>
          <td class="table-td">
            <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
          </td>
          <td class="table-td text-sm text-gray-700">{{ $payment->booking->room->name }}</td>
          <td class="table-td text-center text-sm text-gray-600">{{ $payment->month_number }}</td>
          <td class="table-td text-sm {{ $payment->status === 'overdue' ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            {{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}
          </td>
          <td class="table-td font-medium text-gray-800">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
          </td>
          <td class="table-td">
            @if($payment->proof_image)
              @if($payment->status === 'paid')
                <span class="text-xs text-green-600 font-medium">✓ Terverifikasi</span>
              @else
                <span class="text-xs text-blue-600 font-medium">Perlu Cek</span>
              @endif
            @else
              <span class="text-xs text-gray-400">—</span>
            @endif
          </td>
          <td class="table-td"><span class="{{ $sc }}">{{ $sl }}</span></td>
          <td class="table-td">
            <a href="{{ route('admin.payments.show', $payment) }}"
               class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="px-6 py-14 text-center text-sm text-gray-400">
            Tidak ada tagihan yang sesuai filter.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($payments->hasPages())
    <div class="card-footer">{{ $payments->links() }}</div>
  @endif
</div>

@endsection
