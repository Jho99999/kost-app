@extends('layouts.admin')
@section('title', 'Manajemen Pemesanan')
@section('page-title', 'Manajemen Pemesanan')

@section('content')

<div class="flex items-center justify-between mb-6">
  <p class="text-sm text-gray-500">Total <strong class="text-gray-700">{{ $bookings->total() }}</strong> pemesanan</p>
</div>

{{-- Filter --}}
<div class="card mb-5">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('admin.bookings.index') }}"
          class="flex flex-wrap items-center gap-2">
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Kode booking atau nama penyewa…"
             class="form-input flex-1 min-w-48">
      <select name="status" class="form-select w-36">
        <option value="">Semua Status</option>
        <option value="pending"  @selected(request('status') === 'pending')>Menunggu</option>
        <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
        <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
        <option value="expired"  @selected(request('status') === 'expired')>Kadaluarsa</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm h-9 px-4">Filter</button>
      @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm h-9 px-3">Reset</a>
      @endif
    </form>
  </div>
</div>

{{-- Tabel --}}
<div class="card">
  <div class="overflow-x-auto">
    <table class="table-base">
      <thead class="table-thead">
        <tr>
          <th class="table-th">Kode</th>
          <th class="table-th">Penyewa</th>
          <th class="table-th">Kamar</th>
          <th class="table-th">Tanggal Masuk</th>
          <th class="table-th">Durasi</th>
          <th class="table-th">Status</th>
          <th class="table-th">Diajukan</th>
          <th class="table-th"></th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($bookings as $booking)
        @php
          $sc = match($booking->status) {
            'pending'  => 'badge badge-yellow',
            'approved' => 'badge badge-green',
            'rejected' => 'badge badge-red',
            'expired'  => 'badge badge-gray',
            default    => 'badge badge-gray',
          };
          $sl = match($booking->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'expired'  => 'Kadaluarsa',
            default    => ucfirst($booking->status),
          };
        @endphp
        <tr class="table-tr">
          <td class="table-td font-mono text-xs text-gray-500">{{ $booking->booking_code }}</td>
          <td class="table-td">
            <p class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</p>
            <p class="text-xs text-gray-400">{{ $booking->user->email }}</p>
          </td>
          <td class="table-td">
            <p class="text-sm text-gray-800">{{ $booking->room->name }}</p>
            <p class="text-xs text-gray-400">{{ $booking->room->type }}</p>
          </td>
          <td class="table-td text-sm text-gray-600">
            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}
          </td>
          <td class="table-td text-sm text-gray-600">{{ $booking->duration_months }} bln</td>
          <td class="table-td"><span class="{{ $sc }}">{{ $sl }}</span></td>
          <td class="table-td text-xs text-gray-400">{{ $booking->created_at->format('d/m/Y') }}</td>
          <td class="table-td">
            <a href="{{ route('admin.bookings.show', $booking) }}"
               class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-6 py-14 text-center text-sm text-gray-400">
            Belum ada pemesanan masuk.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($bookings->hasPages())
    <div class="card-footer">{{ $bookings->links() }}</div>
  @endif
</div>

@endsection
