@extends('layouts.admin')
@section('title', 'Detail Check-Out')
@section('page-title', 'Detail Check-Out')

@section('content')

<div class="max-w-3xl">
  <a href="{{ route('admin.checkouts.index') }}"
     class="back-link mb-5">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>

  @php
    $checkOut = \Carbon\Carbon::parse($booking->check_in_date)->addMonths($booking->duration_months);
    $daysLeft = (int) now()->startOfDay()->diffInDays($checkOut->copy()->startOfDay(), false);
  @endphp

  {{-- Info booking --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Informasi Penghuni</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Nama</span><span class="font-medium">{{ $booking->user->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">HP</span><span class="font-medium">{{ $booking->user->phone ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kamar</span><span class="font-medium">{{ $booking->room->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">KTP</span>
          <span class="font-medium {{ $booking->user->ktp_image ? 'text-green-600' : 'text-red-500' }}">
            {{ $booking->user->ktp_image ? 'Ada' : 'Tidak Ada' }}
          </span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Masa Sewa</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Check-In</span><span class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in_date)->locale('id')->isoFormat('D MMMM YYYY') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Durasi</span><span class="font-medium">{{ $booking->duration_months }} bulan</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Estimasi Check-Out</span>
          <span class="font-medium {{ $daysLeft <= 7 ? 'text-red-600' : 'text-gray-800' }}">
            {{ $checkOut->locale('id')->isoFormat('D MMMM YYYY') }}
          </span>
        </div>
        <hr class="border-gray-100">
        <div class="flex justify-between text-base">
          <span class="font-semibold text-gray-700">Sisa Waktu</span>
          <span class="font-bold {{ $daysLeft <= 0 ? 'text-red-600' : ($daysLeft <= 7 ? 'text-orange-600' : 'text-green-600') }}">
            @if($daysLeft <= 0)
              {{ abs($daysLeft) }} hari lewat
            @else
              {{ $daysLeft }} hari
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>

  {{-- Tagihan --}}
  <div class="card mb-5">
    <div class="card-header">
      <h3 class="text-sm font-semibold text-gray-700">Riwayat Tagihan</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="table-base">
        <thead class="table-thead">
          <tr>
            <th class="table-th">Bulan</th>
            <th class="table-th">Jatuh Tempo</th>
            <th class="table-th">Jumlah</th>
            <th class="table-th">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @forelse($booking->payments as $p)
          <tr class="table-tr">
            <td class="table-td">Bulan ke-{{ $p->month_number }}</td>
            <td class="table-td text-sm">{{ \Carbon\Carbon::parse($p->due_date)->format('d M Y') }}</td>
            <td class="table-td font-medium">{{ $p->formatted_amount }}</td>
            <td class="table-td"><span class="{{ $p->status_color }}">{{ $p->status_label }}</span></td>
          </tr>
          @empty
          <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">Belum ada tagihan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Aksi --}}
  <div class="card border-blue-200" x-data="{
    showCheckOut: false,
    showExtend: false,
    extendMonths: 1
  }">
    <div class="card-header bg-blue-50">
      <h3 class="text-sm font-semibold text-blue-800">Aksi</h3>
    </div>
    <div class="card-body flex flex-wrap gap-3">

      {{-- Perpanjangan --}}
      <div class="flex-1 min-w-[250px] p-4 border border-green-200 rounded-lg bg-green-50">
        <h4 class="text-sm font-semibold text-green-800 mb-2">Perpanjang Kontrak</h4>
        <div class="flex gap-2 items-center">
          <input type="number" x-model="extendMonths" min="1" max="12" value="1"
                 class="form-input w-20 text-center" required>
          <span class="text-sm text-green-700">bulan</span>
          <button type="button" @click="showExtend = true" class="btn-success btn-sm">
            Perpanjang
          </button>
        </div>
      </div>

      {{-- Check-Out --}}
      <div class="flex-1 min-w-[250px] p-4 border border-red-200 rounded-lg bg-red-50">
        <h4 class="text-sm font-semibold text-red-800 mb-2">Check-Out</h4>
        <p class="text-xs text-red-600 mb-3">Proses check-out akan: menyelesaikan kontrak, mengosongkan kamar, dan menghapus data KTP penyewa.</p>
        <button type="button" @click="showCheckOut = true" class="btn-danger">
          Proses Check-Out
        </button>
      </div>

    </div>

    {{-- Modal Confirm Check-Out --}}
    <div x-show="showCheckOut" x-cloak class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
         @click.self="showCheckOut = false">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Check-Out</h3>
        <p class="text-sm text-gray-600 mb-4">
          Yakin ingin memproses check-out <strong>{{ $booking->user->name }}</strong> dari <strong>{{ $booking->room->name }}</strong>?
        </p>
        <div class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3 mb-5 space-y-1">
          <p>• Status booking akan menjadi <strong>Completed</strong></p>
          <p>• Kamar akan dikosongkan (status: Available)</p>
          <p>• Data KTP penyewa akan dihapus dari sistem</p>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showCheckOut = false"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Batal
          </button>
          <form method="POST" action="{{ route('admin.checkouts.process', $booking) }}">
            @csrf
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
              Ya, Check-Out
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Modal Confirm Perpanjang --}}
    <div x-show="showExtend" x-cloak class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
         @click.self="showExtend = false">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Perpanjangan</h3>
        <p class="text-sm text-gray-600 mb-4">
          Perpanjang kontrak <strong>{{ $booking->user->name }}</strong> di <strong>{{ $booking->room->name }}</strong>?
        </p>
        <div class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3 mb-5 space-y-1">
          <p>• Durasi bertambah <strong x-text="extendMonths + ' bulan'">1 bulan</strong></p>
          <p>• <span x-text="extendMonths">1</span> tagihan baru akan digenerate</p>
          <p>• Harga per bulan: <strong>Rp {{ number_format($booking->room->price, 0, ',', '.') }}</strong></p>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showExtend = false"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
            Batal
          </button>
          <form method="POST" :action="'{{ route('admin.checkouts.extend', $booking) }}'">
            @csrf
            <input type="hidden" name="additional_months" :value="extendMonths">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition">
              Ya, Perpanjang
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>

</div>

@endsection
