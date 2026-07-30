@extends('layouts.admin')
@section('title', 'Check-Out')
@section('page-title', 'Check-Out Penghuni')

@section('content')

{{-- Filter / Search --}}
<div class="card mb-5">
  <div class="card-body py-3">
    <form method="GET" class="flex flex-wrap items-center gap-2">
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Cari nama penyewa atau kamar…"
             class="form-input flex-1 min-w-48">
      <button type="submit" class="btn-primary btn-sm">Cari</button>
      @if(request('search'))
        <a href="{{ route('admin.checkouts.index') }}" class="btn-secondary btn-sm">Reset</a>
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
          <th class="table-th">Kamar</th>
          <th class="table-th">Penyewa</th>
          <th class="table-th">Check-In</th>
          <th class="table-th">Check-Out</th>
          <th class="table-th">Sisa Hari</th>
          <th class="table-th">Status</th>
          <th class="table-th"></th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($bookings as $b)
          @php
            $checkOut = \Carbon\Carbon::parse($b->check_in_date)->addMonths($b->duration_months);
            $daysLeft = now()->diffInDays($checkOut, false);
          @endphp
        <tr class="table-tr">
          <td class="table-td font-medium">{{ $b->room->name }}</td>
          <td class="table-td">
            <p class="text-sm font-medium text-gray-900">{{ $b->user->name }}</p>
            <p class="text-xs text-gray-400">{{ $b->user->phone }}</p>
          </td>
          <td class="table-td text-sm">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d M Y') }}</td>
          <td class="table-td text-sm">{{ $checkOut->format('d M Y') }}</td>
          <td class="table-td">
            @if($daysLeft <= 0)
              <span class="badge badge-red">Overdue</span>
            @elseif($daysLeft <= 7)
              <span class="badge badge-red">{{ $daysLeft }} hari</span>
            @elseif($daysLeft <= 30)
              <span class="badge badge-yellow">{{ $daysLeft }} hari</span>
            @else
              <span class="text-sm text-gray-500">{{ $daysLeft }} hari</span>
            @endif
          </td>
          <td class="table-td">
            <span class="{{ $b->status === 'approved' ? 'badge badge-yellow' : 'badge badge-green' }}">
              {{ $b->status_label }}
            </span>
          </td>
          <td class="table-td">
            <a href="{{ route('admin.checkouts.show', $b) }}"
               class="text-sm text-blue-600 hover:text-blue-800 font-medium">Detail →</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">
            Tidak ada penghuni aktif saat ini.
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
