@extends('layouts.admin')
@section('title', 'Aduan')
@section('page-title', 'Aduan Penghuni')

@section('content')

{{-- Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
  <div class="card">
    <div class="card-body flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-bold text-yellow-700">{{ $summary['pending'] }}</p>
        <p class="text-xs text-gray-500">Menunggu</p>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-bold text-blue-700">{{ $summary['diproses'] }}</p>
        <p class="text-xs text-gray-500">Sedang Diproses</p>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-bold text-green-700">{{ $summary['selesai'] }}</p>
        <p class="text-xs text-gray-500">Selesai</p>
      </div>
    </div>
  </div>
</div>

{{-- Filter --}}
<div class="card mb-5">
  <div class="card-body py-3">
    <form method="GET" class="flex flex-wrap items-center gap-2">
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Cari judul aduan, nama, atau kamar…"
             class="form-input flex-1 min-w-48">
      <select name="status" class="form-select w-36">
        <option value="">Semua</option>
        <option value="pending"  @selected(request('status') === 'pending')>Menunggu</option>
        <option value="diproses" @selected(request('status') === 'diproses')>Diproses</option>
        <option value="selesai"  @selected(request('status') === 'selesai')>Selesai</option>
        <option value="ditolak"  @selected(request('status') === 'ditolak')>Ditolak</option>
      </select>
      <button type="submit" class="btn-primary btn-sm">Cari</button>
      @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.complaints.index') }}" class="btn-secondary btn-sm">Reset</a>
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
          <th class="table-th">Tanggal</th>
          <th class="table-th">Penyewa</th>
          <th class="table-th">Kamar</th>
          <th class="table-th">Aduan</th>
          <th class="table-th">Status</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($complaints as $c)
        <tr class="table-tr cursor-pointer" onclick="window.location='{{ route('admin.complaints.show', $c) }}'">
          <td class="table-td text-xs text-gray-400 whitespace-nowrap">{{ $c->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
          <td class="table-td">
            <p class="text-sm font-medium text-gray-900">{{ $c->user->name }}</p>
            <p class="text-xs text-gray-400">{{ $c->user->phone }}</p>
          </td>
          <td class="table-td text-sm text-gray-700">{{ $c->room->name }}</td>
          <td class="table-td">
            <p class="text-sm font-medium text-gray-900">{{ $c->title }}</p>
            <p class="text-xs text-gray-400 line-clamp-1">{{ $c->description }}</p>
          </td>
          <td class="table-td"><span class="{{ $c->status_color }}">{{ $c->status_label }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada aduan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($complaints->hasPages())
    <div class="card-footer">{{ $complaints->links() }}</div>
  @endif
</div>

@endsection
