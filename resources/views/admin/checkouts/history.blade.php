@extends('layouts.admin')
@section('title', 'Riwayat ' . $room->name)
@section('page-title', 'Riwayat Penghuni — ' . $room->name)

@section('content')

<div class="card">
  <div class="overflow-x-auto">
    <table class="table-base">
      <thead class="table-thead">
        <tr>
          <th class="table-th">Penyewa</th>
          <th class="table-th">HP</th>
          <th class="table-th">Check-In</th>
          <th class="table-th">Check-Out</th>
          <th class="table-th">Durasi</th>
          <th class="table-th">Status</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($bookings as $b)
        <tr class="table-tr">
          <td class="table-td font-medium text-gray-900">{{ $b->user->name }}</td>
          <td class="table-td text-sm text-gray-500">{{ $b->user->phone ?? '-' }}</td>
          <td class="table-td text-sm">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d M Y') }}</td>
          <td class="table-td text-sm">
            @if($b->check_out_date || $b->status === 'completed')
              {{ $b->check_out_date ? \Carbon\Carbon::parse($b->check_out_date)->format('d M Y') : '-' }}
            @else
              <span class="text-gray-400">—</span>
            @endif
          </td>
          <td class="table-td text-sm">{{ $b->duration_months }} bulan</td>
          <td class="table-td">
            <span class="{{ $b->status === 'completed' ? 'badge badge-gray' : ($b->status === 'approved' || $b->status === 'active' ? 'badge badge-green' : 'badge badge-yellow') }}">
              {{ $b->status_label }}
            </span>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Kamar ini belum pernah dihuni.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($bookings->hasPages())
    <div class="card-footer">{{ $bookings->links() }}</div>
  @endif
</div>

<a href="{{ route('admin.rooms.show', $room) }}"
   class="mt-4 inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600">
  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
  </svg>
  Kembali ke detail kamar
</a>

@endsection
