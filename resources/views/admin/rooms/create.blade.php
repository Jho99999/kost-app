@extends('layouts.admin')
@section('title', 'Tambah Kamar')
@section('page-title', 'Tambah Kamar')

@section('content')

@php
$facilities = [
    'WiFi','AC','Kipas Angin','Lemari','Kamar Mandi Dalam',
    'TV','Meja Kerja','Parkir Motor','Dapur Bersama','Laundry',
];
@endphp

<form method="POST" action="{{ route('admin.rooms.store') }}"
      enctype="multipart/form-data" novalidate>
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom kiri: info utama ──────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Informasi dasar --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Informasi Kamar</h3>
            </div>
            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label for="name" class="form-label">Nama Kamar <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           placeholder="cth. Kamar 101" required
                           class="form-input @error('name') ring-1 ring-red-400 @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="form-label">Tipe <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required
                            class="form-select @error('type') ring-1 ring-red-400 @enderror">
                        <option value="">Pilih tipe…</option>
                        @foreach(['Standard','Deluxe','VIP'] as $t)
                            <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                            class="form-select @error('status') ring-1 ring-red-400 @enderror">
                        <option value="available"   @selected(old('status','available') === 'available')>Tersedia</option>
                        <option value="occupied"    @selected(old('status') === 'occupied')>Terisi</option>
                        <option value="maintenance" @selected(old('status') === 'maintenance')>Perbaikan</option>
                    </select>
                    @error('status')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="floor" class="form-label">Lantai <span class="text-red-500">*</span></label>
                    <input type="number" id="floor" name="floor"
                           value="{{ old('floor', 1) }}" min="1" max="99" required
                           class="form-input @error('floor') ring-1 ring-red-400 @enderror">
                    @error('floor')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="capacity" class="form-label">Kapasitas (orang) <span class="text-red-500">*</span></label>
                    <input type="number" id="capacity" name="capacity"
                           value="{{ old('capacity', 1) }}" min="1" max="20" required
                           class="form-input @error('capacity') ring-1 ring-red-400 @enderror">
                    @error('capacity')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="size_sqm" class="form-label">Luas (m²)</label>
                    <input type="number" id="size_sqm" name="size_sqm"
                           value="{{ old('size_sqm') }}" min="1" placeholder="Opsional"
                           class="form-input @error('size_sqm') ring-1 ring-red-400 @enderror">
                    @error('size_sqm')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="form-label">Harga / Bulan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price"
                           value="{{ old('price') }}" min="1" step="1000" required
                           placeholder="800000"
                           class="form-input @error('price') ring-1 ring-red-400 @enderror">
                    @error('price')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Deskripsi singkat tentang kamar…"
                              class="form-textarea @error('description') ring-1 ring-red-400 @enderror">{{ old('description') }}</textarea>
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
                               @checked(in_array($f, old('facilities', [])))
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-700 group-hover:text-gray-900 select-none">{{ $f }}</span>
                    </label>
                    @endforeach
                </div>
                @error('facilities')<p class="form-error mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>

    {{-- Kolom kanan: foto & tombol ──────────────────── --}}
    <div class="space-y-4">

        {{-- Upload foto --}}
        <div class="card" x-data="photoUpload()">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Foto Kamar</h3>
            </div>
            <div class="card-body space-y-3">

                {{-- Drop zone --}}
                <div @click="$refs.input.click()"
                     class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center cursor-pointer
                            hover:border-blue-400 hover:bg-blue-50 transition-colors">
                    <svg class="w-7 h-7 text-gray-300 mx-auto mb-1.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Klik untuk memilih foto</p>
                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG, WebP · maks. 2 MB/foto · maks. 6 foto</p>
                </div>

                {{-- Input tersembunyi --}}
                <input type="file" name="images[]" multiple accept="image/*"
                       x-ref="input" @change="pick($event)" class="hidden">

                @error('images')   <p class="form-error">{{ $message }}</p> @enderror
                @error('images.*') <p class="form-error">{{ $message }}</p> @enderror

                {{-- Pratinjau --}}
                <template x-if="previews.length">
                    <div>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(url, i) in previews" :key="i">
                                <div class="relative group aspect-square">
                                    <img :src="url" class="w-full h-full object-cover rounded-lg">
                                    <button type="button" @click="remove(i)"
                                            class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white
                                                   text-xs leading-none flex items-center justify-center
                                                   opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                        ×
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-400 mt-2" x-text="`${previews.length} foto dipilih`"></p>
                    </div>
                </template>

            </div>
        </div>

        {{-- Tombol --}}
        <button type="submit" class="btn btn-primary btn-lg w-full">Simpan Kamar</button>
        <a href="{{ route('admin.rooms.index') }}"
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
            const incoming = Array.from(e.target.files);
            incoming.forEach(f => {
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
            // Tulis ulang file input agar sesuai dengan fileList saat ini
            const dt = new DataTransfer();
            this.fileList.forEach(f => dt.items.add(f));
            this.$refs.input.files = dt.files;
        },
    };
}
</script>
@endpush

@endsection
