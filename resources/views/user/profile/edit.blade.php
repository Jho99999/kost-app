@php
    $user = auth()->user();
@endphp

@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- ── Header ───────────────────────────────────────── --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xl select-none">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Profil Saya</h1>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">

        {{-- ── Kolom kiri: Foto KTP ─────────────────────── --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-3">Foto KTP</h2>

                {{-- Preview KTP --}}
                @if ($user->ktp_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $user->ktp_image) }}"
                             alt="KTP {{ $user->name }}"
                             class="w-full rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">
                            Diunggah: {{ $user->ktp_uploaded_at?->isoFormat('D MMMM Y, HH:mm') ?? '-' }}
                        </p>
                    </div>

                    {{-- Tombol ganti / hapus KTP --}}
                    <div class="flex gap-2">
                        <label class="btn-secondary btn-sm cursor-pointer flex-1 text-center">
                            <input type="file" name="ktp_image" accept="image/jpeg,image/png"
                                   form="form-ktp" class="hidden" onchange="document.getElementById('form-ktp').submit()">
                            Ganti
                        </label>
                        <form method="POST" action="{{ route('profile.ktp.delete') }}"
                              onsubmit="return confirm('Hapus foto KTP?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                @else
                    {{-- Belum ada KTP --}}
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                        <p class="text-sm text-gray-400 mb-3">Belum upload KTP</p>
                        <label class="btn-primary btn-sm cursor-pointer inline-block">
                            <input type="file" name="ktp_image" accept="image/jpeg,image/png"
                                   form="form-ktp" class="hidden" onchange="document.getElementById('form-ktp').submit()">
                            Upload KTP
                        </label>
                    </div>
                @endif

                {{-- Hidden form upload KTP --}}
                <form id="form-ktp" method="POST" action="{{ route('profile.ktp.upload') }}" enctype="multipart/form-data" class="hidden">
                    @csrf
                </form>

                <p class="text-xs text-gray-400 mt-3 text-center">Format: JPG/PNG, maks 2MB</p>
            </div>
        </div>

        {{-- ── Kolom kanan: Form data diri + password ───── --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Form Data Diri --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-4">Data Diri</h2>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf @method('PUT')

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               class="form-input {{ $errors->has('name') ? 'input-error' : '' }}"
                               required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label for="phone" class="form-label">Nomor HP / WhatsApp</label>
                        <input type="tel" id="phone" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="form-input {{ $errors->has('phone') ? 'input-error' : '' }}"
                               required>
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pekerjaan --}}
                    <div>
                        <label for="occupation" class="form-label">Pekerjaan</label>
                        <input type="text" id="occupation" name="occupation"
                               value="{{ old('occupation', $user->occupation) }}"
                               placeholder="Misal: Mahasiswa, Karyawan, dll"
                               class="form-input {{ $errors->has('occupation') ? 'input-error' : '' }}">
                        @error('occupation') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select id="gender" name="gender"
                                class="form-select {{ $errors->has('gender') ? 'input-error' : '' }}">
                            <option value="">— Pilih —</option>
                            <option value="L" {{ old('gender', $user->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $user->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Agama --}}
                    <div>
                        <label for="religion" class="form-label">Agama</label>
                        <select id="religion" name="religion"
                                class="form-select {{ $errors->has('religion') ? 'input-error' : '' }}">
                            <option value="">— Pilih —</option>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $r)
                                <option value="{{ $r }}" {{ old('religion', $user->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                        @error('religion') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nomor KTP --}}
                    <div>
                        <label for="id_card" class="form-label">Nomor KTP</label>
                        <input type="text" id="id_card" name="id_card"
                               value="{{ old('id_card', $user->id_card) }}"
                               placeholder="16 digit nomor KTP"
                               class="form-input {{ $errors->has('id_card') ? 'input-error' : '' }}">
                        @error('id_card') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary">
                        Simpan Data Diri
                    </button>
                
            </div>

            {{-- Form Ganti Password --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-800 mb-1">Ganti Password</h2>
                <p class="text-xs text-gray-400 mb-4">Kosongkan jika tidak ingin mengganti password.</p>

                    {{-- Password saat ini --}}
                    <div>
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password"
                               class="form-input {{ $errors->has('current_password') ? 'input-error' : '' }}">
                        @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password baru --}}
                    <div>
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" id="password" name="password"
                               class="form-input {{ $errors->has('password') ? 'input-error' : '' }}">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Konfirmasi --}}
                    <div>
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-input">
                    </div>

                    <button type="submit" class="btn-primary">
                        Ganti Password
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
