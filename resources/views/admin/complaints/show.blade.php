@extends('layouts.admin')
@section('title', 'Detail Aduan')
@section('page-title', 'Detail Aduan')

@section('content')

<div class="max-w-3xl">
  <a href="{{ route('admin.complaints.index') }}"
     class="back-link mb-5">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
    </svg>
    Kembali
  </a>

  <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
      <h1 class="text-lg font-bold text-gray-900">{{ $complaint->title }}</h1>
      <p class="text-xs text-gray-400 mt-0.5">Oleh {{ $complaint->user->name }} — {{ $complaint->room->name }}</p>
    </div>
    <span class="{{ $complaint->status_color }} text-sm px-3 py-1">{{ $complaint->status_label }}</span>
  </div>

  {{-- Detail --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Informasi</h3></div>
      <div class="card-body space-y-2 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Penyewa</span><span class="font-medium">{{ $complaint->user->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">HP</span><span class="font-medium">{{ $complaint->user->phone ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Kamar</span><span class="font-medium">{{ $complaint->room->name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span class="text-gray-700">{{ $complaint->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        @if($complaint->resolved_at)
        <div class="flex justify-between"><span class="text-gray-500">Selesai</span><span class="text-green-700">{{ $complaint->resolved_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Deskripsi Aduan</h3></div>
      <div class="card-body">
        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
      </div>
    </div>
  </div>

  {{-- Foto --}}
  @if($complaint->image)
  <div class="card mb-5">
    <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Foto Bukti</h3></div>
    <div class="card-body">
      <a href="{{ $complaint->image_url }}" target="_blank"
         class="block rounded-xl overflow-hidden border border-gray-100 hover:opacity-90 transition max-w-md">
        <img src="{{ $complaint->image_url }}" alt="" class="w-full max-h-80 object-contain bg-gray-50">
      </a>
    </div>
  </div>
  @endif

  {{-- Form Respon --}}
  <div class="card border-blue-200">
    <div class="card-header bg-blue-50">
      <h3 class="text-sm font-semibold text-blue-800">Respon Admin</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-4">
        @csrf @method('PUT')

        <div>
          <label class="form-label">Status</label>
          <div class="flex flex-wrap gap-2">
            @foreach(['pending' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="status" value="{{ $val }}"
                     {{ $complaint->status == $val ? 'checked' : '' }}
                     class="text-blue-600 focus:ring-blue-500">
              <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
            @endforeach
          </div>
        </div>

        <div>
          <label for="admin_notes" class="form-label">Catatan (opsional)</label>
          <textarea id="admin_notes" name="admin_notes" rows="3"
                    class="form-input" placeholder="Contoh: Sudah diperbaiki, keran diganti...">{{ old('admin_notes', $complaint->admin_notes) }}</textarea>
        </div>

        <button type="submit" class="btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</div>

@endsection
