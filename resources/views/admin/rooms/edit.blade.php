@extends('layouts.admin')
@section('title', 'Edit Kamar')
@section('page-title', 'Edit Kamar')

@section('content')

@php
$facilities = [
    'WiFi','AC','Kipas Angin','Lemari','Kamar Mandi Dalam',
    'TV','Meja Kerja','Parkir Motor','Dapur Bersama','Laundry',
];
$existingFacilities = $room->facilities ?? [];
@endphp

<form method="POST" action="{{ route('admin.rooms.update', $room) }}"
      enctype="multipart/form-data" novalidate>
@csrf @method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom kiri: info utama ──────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Informasi Kamar</h3>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label for="name" class="form-label">Nama Kamar <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $room->name) }}" required
                           class="form-input @error('name') ring-1 ring-red-400 @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="form-label">Tipe <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required
                            class="form-select @error('type') ring-1 ring-red-400 @enderror">
                        @foreach(['Standard','Deluxe','VIP'] as $t)
                            <option value="{{ $t }}" @selected(old('type', $room->type) === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="form-select @error('status') ring-1 ring-red-400 @enderror">
                        <option value="available"   @selected(old('status', $room->status) === 'available')>Tersedia</option>
                        <option value="occupied"    @selected(old('status', $room->status) === 'occupied')>Terisi</option>
                        <option value="maintenance" @selected(old('status', $room->status) === 'maintenance')>Perbaikan</option>
                    </select>
                    @error('status')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="floor" class="form-label">Lantai <span class="text-red-500">*</span></label>
                    <input type="number" id="floor" name="floor"
                           value="{{ old('floor', $room->floor) }}" min="1" max="99" required
                           class="form-input @error('floor') ring-1 ring-red-400 @enderror">
                    @error('floor')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="capacity" class="form-label">Kapasitas (orang) <span class="text-red-500">*</span></label>
                    <input type="number" id="capacity" name="capacity"
                           value="{{ old('capacity', $room->capacity) }}" min="1" max="20" required
                           class="form-input @error('capacity') ring-1 ring-red-400 @enderror">
                    @error('capacity')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="size_sqm" class="form-label">Luas (m²)</label>
                    <input type="number" id="size_sqm" name="size_sqm"
                           value="{{ old('size_sqm', $room->size_sqm) }}" min="1"
                           class="form-input @error('size_sqm') ring-1 ring-red-400 @enderror">
                    @error('size_sqm')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="form-label">Harga / Bulan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price"
                           value="{{ old('price', $room->price) }}" min="1" step="1000" required
                           class="form-input @error('price') ring-1 ring-red-400 @enderror">
                    @error('price')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-textarea @error('description') ring-1 ring-red-400 @enderror">{{ old('description', $room->description) }}</textarea>
                    @error('description')<p class="form-error">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Fasilitas --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Fasilitas</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-4">
                    @foreach($facilities as $f)
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="facilities[]" value="{{ $f }}"
                               @checked(in_array($f, old('facilities', $existingFacilities)))
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-700 group-hover:text-gray-900 select-none">{{ $f }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- Kolom kanan: foto & tombol ──────────────────── --}}
    <div class="space-y-4">

        {{-- Foto yang sudah ada --}}
        @if(!empty($room->images))
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Foto Saat Ini</h3>
                <span class="text-xs text-gray-400">{{ count($room->images) }} foto</span>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-3 gap-2">
                    @foreach($room->images as $img)
                    <div class="relative group aspect-square">
                        <img src="{{ asset('storage/' . $img) }}"
                             alt="Foto kamar"
                             class="w-full h-full object-cover rounded-lg">
                        {{-- Hapus foto individu --}}
                        <form method="POST"
                              action="{{ route('admin.rooms.images.destroy', $room) }}"
                              onsubmit="return confirm('Hapus foto ini?')"
                              class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf @method('DELETE')
                            <input type="hidden" name="image" value="{{ $img }}">
                            <button type="submit"
                                    class="w-5 h-5 rounded-full bg-red-500 text-white text-xs
                                           flex items-center justify-center shadow">×</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Upload foto tambahan --}}
        <div class="card" x-data="photoUpload()">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Tambah Foto</h3>
            </div>
            <div class="card-body space-y-3">
                <div @click="$refs.input.click()"
                     class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer
                            hover:border-blue-400 hover:bg-blue-50 transition-colors">
                    <p class="text-sm text-gray-500">Klik untuk pilih foto baru</p>
                    <p class="text-xs text-gray-400 mt-0.5">Akan ditambahkan ke foto yang ada</p>
                </div>

                <input type="file" name="images[]" multiple accept="image/*"
                       x-ref="input" @change="pick($event)" class="hidden">

                @error('images')   <p class="form-error">{{ $message }}</p> @enderror
                @error('images.*') <p class="form-error">{{ $message }}</p> @enderror

                <template x-if="previews.length">
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="(url, i) in previews" :key="i">
                            <div class="relative group aspect-square">
                                <img :src="url" class="w-full h-full object-cover rounded-lg opacity-80">
                                <button type="button" @click="remove(i)"
                                        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white
                                               text-xs flex items-center justify-center
                                               opacity-0 group-hover:opacity-100 transition-opacity shadow">×</button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- Tombol --}}
        <button type="submit" class="btn btn-primary btn-lg w-full">Simpan Perubahan</button>
        <a href="{{ route('admin.rooms.show', $room) }}"
           class="btn btn-secondary btn-lg w-full text-center block">Batal</a>

    </div>
</div>

</form>

@push('scripts')
<script>
function photoUpload() {
    return {
        previews : [],
        fileList  : [],
        pick(e) {
            Array.from(e.target.files).forEach(f => {
                if (this.fileList.length < 6) {
                    this.previews.push(URL.createObjectURL(f));
                    this.fileList.push(f);
                }
            });
            this.syncInput();
        },
        remove(i) {
            URL.revokeObjectURL(this.previews[i]);
            this.previews.splice(i, 1);
            this.fileList.splice(i, 1);
            this.syncInput();
        },
        syncInput() {
            const dt = new DataTransfer();
            this.fileList.forEach(f => dt.items.add(f));
            this.$refs.input.files = dt.files;
        },
    };
}
</script>
@endpush

@endsection
