@extends('layouts.admin')
@section('title', 'Tambah Kamar')
@section('page-title', 'Tambah Kamar')
@section('content')

@php

$facilities = [

'WiFi',
'AC',
'Kipas Angin',

'Kasur',
'Spring Bed',
'Bantal',
'Guling',
'Sprei',

'Lemari',
'Rak Sepatu',

'Meja',
'Meja Belajar',
'Kursi',

'TV',
'Kulkas',
'Mini Kulkas',

'Dispenser',
'Rice Cooker',
'Kompor',
'Microwave',

'Kitchen Set',
'Dapur Bersama',

'Mesin Cuci',
'Laundry',

'Shower',
'Water Heater',
'Wastafel',

'Closet Duduk',
'Closet Jongkok',

'Internet LAN',

'Parkir Motor',
'Parkir Mobil',

'CCTV',
'Keamanan 24 Jam',

'Balkon',
'Jemuran',

];

@endphp

<form method="POST" action="{{ route('admin.rooms.store') }}"
      enctype="multipart/form-data">
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
                    <label class="form-label">
                        Nomor Kamar
                    </label>

                    <input
                        type="text"
                        name="room_number"
                        value="{{ old('room_number') }}"
                        placeholder="A-101"
                        class="form-input @error('room_number') ring-1 ring-red-400 @enderror">

                    @error('room_number')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
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

                    <label class="form-label">

                        Panjang (m)

                    </label>

                    <input
                        type="number"
                        step="0.1"
                        min="1"
                        name="length_m"
                        value="{{ old('length_m') }}"
                        class="form-input @error('length_m') ring-1 ring-red-400 @enderror">

                    @error('length_m')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="form-label">

                        Lebar (m)

                    </label>

                    <input
                        type="number"
                        step="0.1"
                        min="1"
                        name="width_m"
                        value="{{ old('width_m') }}"
                        class="form-input @error('width_m') ring-1 ring-red-400 @enderror">

                    @error('width_m')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                </div>

                <div>
                    <label class="form-label">
                        Luas Kamar
                    </label>

                    <input
                        type="text"
                        id="preview_area"
                        class="form-input bg-gray-100"
                        readonly>

                    <input
                        type="hidden"
                        id="size_sqm"
                        name="size_sqm"
                        value="{{ old('size_sqm') }}">
                </div>

                <div>
                    <label for="price" class="form-label">Harga / Bulan (Rp) <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        value="{{ old('price') }}"
                        min="0"
                        step="1000"
                        placeholder="400000"
                        required
                        class="form-input @error('price') ring-1 ring-red-400 @enderror">
                    @error('price')<p class="form-error">{{ $message }}</p>@enderror
                    <p
                        id="price_preview"
                        class="text-xs text-gray-500 mt-1">
                    </p>
                </div>

                <div>

                    <label class="form-label">

                        Deposit (Rp)

                    </label>

                    <input
                        type="number"
                        min="0"
                        step="1000"
                        name="deposit"
                        value="{{ old('deposit', '') }}"
                        class="form-input @error('deposit') ring-1 ring-red-400 @enderror">

                    @error('deposit')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p
                        id="deposit_preview"
                        class="text-xs text-gray-500 mt-1">
                    </p>
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

        <div class="card">

            <div class="card-header">

                <h3 class="text-sm font-semibold text-gray-700">

                    Spesifikasi Kamar

                </h3>

            </div>

            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>

                    <label class="form-label">

                        Jenis Kamar Mandi

                    </label>

                    <select
                        required
                        name="bathroom_type"
                        class="form-select @error('bathroom_type') ring-1 ring-red-400 @enderror">

                        <option value="" @selected(old('bathroom_type') === '')>
                            Pilih jenis kamar mandi…
                        </option>

                        <option value="inside"
                            @selected(old('bathroom_type') == 'inside')>
                            Dalam
                        </option>

                        <option value="outside"
                            @selected(old('bathroom_type') == 'outside')>
                            Luar
                        </option>

                        <option value="shared"
                            @selected(old('bathroom_type') == 'shared')>
                            Bersama
                        </option>

                    </select>
                    @error('bathroom_type')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="form-label">

                        Furnished

                    </label>

                    <select
                        required
                        name="furnished"
                        class="form-select @error('furnished') ring-1 ring-red-400 @enderror">

                        <option value="" @selected(old('furnished') === '')>
                            Pilih furnished…
                        </option>

                        <option value="empty"
                            @selected(old('furnished') == 'empty')>
                            Kosong
                        </option>

                        <option value="semi"
                            @selected(old('furnished') == 'semi')>
                            Semi Furnished
                        </option>

                        <option value="full"
                            @selected(old('furnished') == 'full')>
                            Full Furnished
                        </option>

                    </select>
                    @error('furnished')<p class="form-error">{{ $message }}</p>@enderror

                </div>

                <div>

                    <label class="form-label">

                        Listrik

                    </label>

                    <select
                        required
                        name="electricity_type"
                        class="form-select @error('electricity_type') ring-1 ring-red-400 @enderror">

                        <option value="" @selected(old('electricity_type') === '')>
                            Pilih listrik…
                        </option>

                        <option value="included"
                            @selected(old('electricity_type') == 'included')>
                            Termasuk
                        </option>

                        <option value="token"
                            @selected(old('electricity_type') == 'token')>
                            Token
                        </option>

                        <option value="usage"
                            @selected(old('electricity_type') == 'usage')>
                            Sesuai Pemakaian
                        </option>

                    </select>
                    @error('electricity_type')<p class="form-error">{{ $message }}</p>@enderror

                </div>

                <div>

                    <label class="form-label">

                        Air

                    </label>

                    <select
                        required
                        name="water_type"
                        class="form-select @error('water_type') ring-1 ring-red-400 @enderror">

                        <option value="" @selected(old('water_type') === '')>
                            Pilih air…
                        </option>

                        <option value="included"
                            @selected(old('water_type') == 'included')>
                            Termasuk
                        </option>

                        <option value="meter"
                            @selected(old('water_type') == 'meter')>
                            Meteran
                        </option>

                        <option value="well"
                            @selected(old('water_type') == 'well')>
                            Sumur
                        </option>

                    </select>
                    @error('water_type')<p class="form-error">{{ $message }}</p>@enderror

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
        <div
            class="card"
            x-data="photoUpload()"
            x-init="
                $el.addEventListener('alpine:destroy', () => destroy())
            ">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-gray-700">Foto Kamar</h3>
            </div>
            <div class="card-body space-y-3">

                {{-- Drop zone --}}
                <div
                    @click="$refs.input.click()"

                    @dragover.prevent

                    @drop.prevent="dropFiles($event)"

                    class="
                        border-2
                        border-dashed
                        border-gray-200
                        rounded-xl
                        p-5
                        text-center
                        cursor-pointer
                        hover:border-blue-500
                        hover:bg-blue-50
                        transition">
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

                                    <img
                                        :src="url"
                                        class="w-full h-full object-cover rounded-lg">

                                    <label
                                        class="absolute bottom-1 left-1 bg-white rounded px-2 py-1 text-xs shadow flex items-center gap-1">

                                        <input
                                            type="radio"
                                            name="cover_selector"
                                            x-model="cover"
                                            :value="i">

                                        <span>Cover</span>

                                    </label>

                                    <button
                                        type="button"
                                        @click="remove(i)"
                                        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white
                                            text-xs leading-none flex items-center justify-center
                                            opacity-0 group-hover:opacity-100 transition-opacity shadow">

                                        ×

                                    </button>

                                </div>
                            </template>

                        </div>

                        <!-- Hanya SATU hidden input -->
                        <input
                            type="hidden"
                            name="cover_image"
                            :value="cover">
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

document.addEventListener('DOMContentLoaded', () => {


    const length = document.querySelector('[name=length_m]');
    const width  = document.querySelector('[name=width_m]');
    const area   = document.getElementById('preview_area');
    if (!length || !width || !area) {
        return;
    }
    const roomNumber = document.querySelector('[name=room_number]');
    const priceInput = document.getElementById('price');
    const depositInput = document.querySelector('[name=deposit]');

    const pricePreview = document.getElementById('price_preview');
    const depositPreview = document.getElementById('deposit_preview');
    
    function rupiah(v){

        if (v === '' || v === null) {
            return '';
        }

        return 'Rp ' + Number(v).toLocaleString('id-ID');

    }

    function updateMoney(){

        pricePreview.textContent =
            priceInput.value
                ? rupiah(priceInput.value) + ' / bulan'
                : '';

        depositPreview.textContent =
            depositInput.value
                ? 'Deposit : ' + rupiah(depositInput.value)
                : '';

    }

    if(priceInput){

        priceInput.addEventListener('input', updateMoney);
    }
    if(depositInput){
        depositInput.addEventListener('input', updateMoney);
    }

    updateMoney();
        if (roomNumber) {

        roomNumber.value = roomNumber.value.toUpperCase();

        roomNumber.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });

    }
    const hiddenArea = document.getElementById('size_sqm');
    if (!hiddenArea) {
        return;
    }
    function calculateArea() {

        const l = parseFloat(length.value) || 0;
        const w = parseFloat(width.value) || 0;

        if (l > 0 && w > 0) {

            const total = l * w;

            area.value =
            `${l} × ${w} = ${total.toFixed(2)} m²`;

            hiddenArea.value =
            total.toFixed(2);

        } else {

            area.value = '';

            hiddenArea.value = '';

        }
    }

    length.addEventListener('input', calculateArea);
    width.addEventListener('input', calculateArea);

    calculateArea();

});
console.log('script loaded');
function photoUpload() {

    const allowed = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];
    return {
        previews : [],
        fileList  : [],
        cover: 0,
        
        addFile(file) {

            if (this.fileList.length >= 6) {
                alert('Maksimal 6 foto.');
                return;
            }

            if (!allowed.includes(file.type)) {
                alert('Format harus JPG, PNG atau WebP.');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran maksimal 2 MB.');
                return;
            }

            const exists = this.fileList.some(f =>
                f.name === file.name &&
                f.size === file.size &&
                f.lastModified === file.lastModified
            );

            if (exists) {
                return;
            }

            this.fileList.push(file);
            this.previews.push(URL.createObjectURL(file));

            if (this.fileList.length === 1) {
                this.cover = 0;
            }
        },
        remove(i){

            URL.revokeObjectURL(this.previews[i]);

            this.previews.splice(i,1);
            this.fileList.splice(i,1);

            if (this.fileList.length === 0) {
                this.cover = 0;
            }
            else if (this.cover === i) {
                this.cover = 0;
            }
            else if (this.cover > i) {
                this.cover--;
            }

            this.syncInput();

        }, // <-- WAJIB
        pick(e) {

            Array.from(e.target.files).forEach(file => this.addFile(file));

            this.syncInput();

        },
        syncInput() {

            const dt = new DataTransfer();

            this.fileList.forEach(f => dt.items.add(f));

            this.$refs.input.files = dt.files;

            console.log("input files =", this.$refs.input.files);
            console.log("length =", this.$refs.input.files.length);

        },

        dropFiles(e) {
            Array.from(e.dataTransfer.files).forEach(file => this.addFile(file));

            this.syncInput();
        },

        destroy() {
            this.previews.forEach(url => URL.revokeObjectURL(url));
        }

    };
}
</script>
@endpush

@endsection
