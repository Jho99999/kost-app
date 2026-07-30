@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Stat cards baris 1: Ringkasan Operasional ────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">

  @php
  $cards = [
    ['label' => 'Kamar',         'value' => $stats['available_rooms'] . '/' . $stats['total_rooms'] . ' kosong', 'sub' => $stats['total_rooms'] - $stats['available_rooms'] . ' terisi', 'color' => 'green',  'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
    ['label' => 'Pemesanan Baru',  'value' => $stats['pending_bookings'], 'color' => 'yellow', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ['label' => 'Penghuni Aktif',     'value' => $stats['active_bookings'],  'color' => 'green',  'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ['label' => 'Cek Pembayaran',  'value' => $stats['awaiting_verify'],  'color' => 'blue',   'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
  ];
  @endphp

  @foreach($cards as $card)
  @php
    $bg   = "bg-{$card['color']}-50";
    $ico  = "text-{$card['color']}-500";
    $val  = "text-{$card['color']}-700";
  @endphp
  <div class="card">
    <div class="card-body">
      <div class="flex items-start justify-between gap-2">
        <div>
          <p class="text-2xl font-bold {{ $val }}">{{ $card['value'] }}</p>
          <p class="text-xs text-gray-500 mt-0.5 leading-tight">{{ $card['label'] }}</p>
          @if(!empty($card['sub']))
          <p class="text-xs text-gray-400 mt-0.5">{{ $card['sub'] }}</p>
          @endif
        </div>
        <div class="w-9 h-9 rounded-lg {{ $bg }} flex items-center justify-center flex-shrink-0">
          <svg class="w-4.5 h-4.5 {{ $ico }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
  @endforeach

</div>

{{-- ── Stat cards baris 2: Keuangan & Bisnis ─────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

  {{-- Omset Bulan Ini --}}
  <div class="card">
    <div class="card-body">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
          </svg>
        </div>
        <div>
          <p class="text-lg font-bold text-gray-900">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
          <p class="text-xs text-gray-500">Omset Bulan Ini</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Tingkat Hunian --}}
  <div class="card">
    <div class="card-body">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605"/>
          </svg>
        </div>
        <div>
          <p class="text-lg font-bold text-gray-900">{{ $occupancyRate }}%</p>
          <p class="text-xs text-gray-500">Tingkat Hunian</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Rata-rata Lama Sewa --}}
  <div class="card">
    <div class="card-body">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
          </svg>
        </div>
        <div>
          <p class="text-lg font-bold text-gray-900">{{ $avgDuration ? number_format($avgDuration, 1) : '-' }} bln</p>
          <p class="text-xs text-gray-500">Rata-rata Lama Sewa</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Tagihan Overdue --}}
  <div class="card">
    <div class="card-body">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
          </svg>
        </div>
        <div>
          <p class="text-lg font-bold text-red-700">{{ $overdueCount }}</p>
          <p class="text-xs text-gray-500">Tagihan Overdue</p>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ── Grafik ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">

  {{-- Grafik Pemasukan --}}
  <div class="card">
    <div class="card-header">
      <h3 class="text-sm font-semibold text-gray-700">Pemasukan 6 Bulan</h3>
    </div>
    <div class="card-body">
      <canvas id="revenueChart" height="140"></canvas>
    </div>
  </div>

  {{-- Grafik Penghuni Baru --}}
  <div class="card">
    <div class="card-header">
      <h3 class="text-sm font-semibold text-gray-700">Penghuni Baru 6 Bulan</h3>
    </div>
    <div class="card-body">
      <canvas id="tenantChart" height="140"></canvas>
    </div>
  </div>

</div>

{{-- ── Tabel bawah: 3 kolom ──────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Kolom 1: Booking Terbaru --}}
  <div class="card">
    <div class="card-header flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-700">Booking Terbaru</h3>
      <a href="{{ route('admin.bookings.index') }}"
         class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead class="table-thead">
          <tr>
            <th class="table-th">Penyewa</th>
            <th class="table-th">Kamar</th>
            <th class="table-th">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @forelse($recentBookings as $b)
          <tr class="table-tr">
            <td class="table-td">
              <p class="text-sm font-medium text-gray-900">{{ $b->user->name }}</p>
              <p class="text-xs font-mono text-gray-400">{{ $b->booking_code }}</p>
            </td>
            <td class="table-td text-sm text-gray-700">{{ $b->room->name }}</td>
            <td class="table-td">
              <span class="{{ $b->status_color }}">{{ $b->status_label }}</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada booking.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Kolom 2: Kontrak Akan Habis --}}
  <div class="card">
    <div class="card-header flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-700">Kontrak Akan Habis</h3>
      <span class="text-xs text-gray-400">{{ $expiringBookings->count() }} dalam 30 hari</span>
    </div>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead class="table-thead">
          <tr>
            <th class="table-th">Penyewa</th>
            <th class="table-th">Kamar</th>
            <th class="table-th">Sisa</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @forelse($expiringBookings as $b)
            @php
              $checkOut = \Carbon\Carbon::parse($b->check_in_date)->addMonths($b->duration_months);
              $daysLeft = (int) now()->startOfDay()->diffInDays($checkOut->copy()->startOfDay(), false);
            @endphp
          <tr class="table-tr">
            <td class="table-td">
              <p class="text-sm font-medium text-gray-900">{{ $b->user->name }}</p>
              <p class="text-xs font-mono text-gray-400">{{ $b->booking_code }}</p>
            </td>
            <td class="table-td text-sm text-gray-700">{{ $b->room->name }}</td>
            <td class="table-td">
              <span class="badge {{ $daysLeft <= 7 ? 'badge-red' : 'badge-yellow' }}">
                {{ $daysLeft }} hari
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">
              Tidak ada kontrak akan habis.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Kolom 3: Tagihan Menunggu Verifikasi --}}
  <div class="card">
    <div class="card-header flex items-center justify-between">
      <h3 class="text-sm font-semibold text-gray-700">Tagihan Menunggu Verifikasi</h3>
      <a href="{{ route('admin.payments.index', ['proof' => 'uploaded']) }}"
         class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead class="table-thead">
          <tr>
            <th class="table-th">Penyewa</th>
            <th class="table-th">Kamar</th>
            <th class="table-th">Jatuh Tempo</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @forelse($pendingPayments as $p)
          <tr class="table-tr">
            <td class="table-td">
              <a href="{{ route('admin.payments.show', $p) }}"
                 class="text-sm font-medium text-blue-600 hover:text-blue-800">
                {{ $p->user->name }}
              </a>
              <p class="text-xs font-mono text-gray-400">{{ $p->payment_code }}</p>
            </td>
            <td class="table-td text-sm text-gray-700">{{ $p->booking->room->name }}</td>
            <td class="table-td text-sm {{ $p->status === 'overdue' ? 'text-red-600 font-medium' : 'text-gray-600' }}">
              {{ \Carbon\Carbon::parse($p->due_date)->format('d M Y') }}
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">
              Tidak ada tagihan menunggu verifikasi.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ── Grafik Pemasukan ──
  const revCtx = document.getElementById('revenueChart');
  if (revCtx) {
    new Chart(revCtx, {
      type: 'bar',
      data: {
        labels: {!! json_encode(array_column($revenueChart, 'label')) !!},
        datasets: [{
          label: 'Pemasukan (Rp)',
          data: {!! json_encode(array_column($revenueChart, 'total')) !!},
          backgroundColor: '#3b82f6',
          borderRadius: 6,
          barPercentage: 0.6,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: v => 'Rp' + (v/1000).toFixed(0) + 'rb'
            }
          }
        }
      }
    });
  }

  // ── Grafik Penghuni Baru ──
  const tenCtx = document.getElementById('tenantChart');
  if (tenCtx) {
    new Chart(tenCtx, {
      type: 'bar',
      data: {
        labels: {!! json_encode(array_column($tenantChart, 'label')) !!},
        datasets: [{
          label: 'Penghuni Baru',
          data: {!! json_encode(array_column($tenantChart, 'total')) !!},
          backgroundColor: '#10b981',
          borderRadius: 6,
          barPercentage: 0.6,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  }
});
</script>
@endpush

@endsection
