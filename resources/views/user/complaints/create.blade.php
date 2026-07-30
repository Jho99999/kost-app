@extends('layouts.app')
@section('title', 'Buat Aduan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('complaints.index') }}" class="hover:text-gray-600">Aduan</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Buat Baru</span>
  </nav>

  <h1 class="text-xl font-bold text-gray-900 mb-6">Buat Aduan Baru</h1>

  <form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
    @csrf

    {{-- Kamar --}}
    <div>
      <label for="room_id" class="form-label">Kamar <span class="text-red-500">*</span></label>
      <select name="room_id" id="room_id" class="form-select w-full" required>
        <option value="">— Pilih Kamar —</option>
        @foreach($rooms as $room)
          <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->name }}</option>
        @endforeach
      </select>
      @if($rooms->isEmpty())
        <p class="text-xs text-amber-600 mt-1">Tidak ada kamar aktif. Anda harus memiliki booking aktif untuk membuat aduan.</p>
      @endif
      @error('room_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    {{-- Judul --}}
    <div>
      <label for="title" class="form-label">Judul Aduan <span class="text-red-500">*</span></label>
      <input type="text" id="title" name="title" value="{{ old('title') }}"
             placeholder="Contoh: Keran kamar mandi bocor"
             class="form-input" required>
      @error('title')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    {{-- Deskripsi --}}
    <div>
      <label for="description" class="form-label">Deskripsi <span class="text-red-500">*</span></label>
      <textarea id="description" name="description" rows="4"
                placeholder="Jelaskan masalah yang Anda alami..."
                class="form-input" required>{{ old('description') }}</textarea>
      @error('description')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    {{-- Foto --}}
    <div x-data="{ preview: null }">
      <label for="image" class="form-label">Foto Bukti</label>
      <input type="file" id="image" name="image" accept="image/jpeg,image/png"
             @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
             class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                    file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100">
      <p class="text-xs text-gray-400 mt-1">Format: JPG/PNG, maks 2MB. Opsional.</p>
      @error('image')<p class="form-error">{{ $message }}</p>@enderror

      <template x-if="preview">
        <div class="mt-3 rounded-xl overflow-hidden border border-blue-100">
          <img :src="preview" class="w-full max-h-48 object-contain bg-gray-50">
        </div>
      </template>
    </div>

    <button type="submit" class="btn-primary w-full">
      Kirim Aduan
    </button>
  </form>

</div>
@endsection
