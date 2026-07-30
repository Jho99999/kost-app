@extends('layouts.app')
@section('title', 'Ajukan Pemesanan — ' . $room->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('rooms.index') }}" class="hover:text-gray-600">Kamar</a>
    <span>/</span>
    <a href="{{ route('rooms.show', $room) }}" class="hover:text-gray-600">{{ $room->name }}</a>
    <span>/</span>
    <span class="text-gray-700 font-medium">Pesan</span>
  </nav>

  {{-- Warning: sudah punya booking aktif --}}
  @if($activeBooking)
  <div class="alert alert-warning mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
    </svg>
    <div>
      <p class="font-medium">Anda masih memiliki pemesanan aktif.</p>
      <p class="text-sm mt-0.5">
        Booking <span class="font-mono font-semibold">{{ $activeBooking->booking_code }}</span>
        ({{ $activeBooking->room->name }}) sedang dalam status
        <span class="font-medium">{{ $activeBooking->status === 'pending' ? 'menunggu persetujuan' : 'aktif' }}</span>.
        Pemesanan baru tidak dapat diajukan sampai booking ini selesai.
      </p>
    </div>
  </div>
  @endif

  {{-- Warning: KTP belum diupload --}}
  @if($ktpMissing)
  <div class="alert alert-error mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
    </svg>
    <div>
      <p class="font-medium">Data KTP belum lengkap</p>
      <p class="text-sm mt-0.5">
        Anda harus <a href="{{ route('profile.edit') }}" class="font-semibold underline">melengkapi foto KTP di halaman profil</a> terlebih dahulu sebelum dapat melakukan pemesanan kamar.
      </p>
    </div>
  </div>
  @endif

  <h1 class="text-xl font-bold text-gray-900 mb-6">Ajukan Pemesanan</h1>

  {{-- Info kamar (read-only) --}}
  <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-center gap-4">
    @if(!empty($room->images))
      <img src="{{ asset('storage/' . $room->images[0]) }}"
           alt="{{ $room->name }}"
           class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
    @endif
    <div>
      <p class="font-semibold text-gray-900">{{ $room->name }}</p>
      <p class="text-sm text-gray-500">{{ $room->type }} · Lantai {{ $room->floor }} · {{ $room->capacity }} orang</p>
      <p class="text-blue-700 font-bold mt-0.5">{{ $room->formatted_price }} <span class="font-normal text-sm text-gray-400">/ bulan</span></p>
    </div>
  </div>

  {{-- Form --}}
  <form method="POST" action="{{ route('bookings.store') }}"
        x-data="bookingCalc({{ (int)$room->price }})"
        @if($activeBooking) onsubmit="return false;" @endif>
    @csrf
    <input type="hidden" name="room_id" value="{{ $room->id }}">

    <div class="space-y-5">

      {{-- Error umum (misal: sudah punya booking aktif dari validasi) --}}
      @if($errors->has('general'))
        <div class="alert alert-warning">{{ $errors->first('general') }}</div>
      @endif

      {{-- Tanggal masuk --}}
      <div>
        <label for="check_in_date" class="form-label">
          Tanggal Masuk <span class="text-red-500">*</span>
        </label>
        <input type="date" id="check_in_date" name="check_in_date"
               x-model="checkIn"
               value="{{ old('check_in_date') }}"
               min="{{ date('Y-m-d') }}"
               required
               class="form-input @error('check_in_date') ring-1 ring-red-400 @enderror">
        @error('check_in_date')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      {{-- Durasi --}}
      <div>
        <label for="duration_months" class="form-label">
          Durasi Sewa <span class="text-red-500">*</span>
        </label>
        <select id="duration_months" name="duration_months"
                x-model="duration"
                required
                class="form-select @error('duration_months') ring-1 ring-red-400 @enderror">
          @for($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" @selected(old('duration_months', 1) == $i)>
              {{ $i }} bulan
            </option>
          @endfor
        </select>
        @error('duration_months')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      {{-- Ringkasan kalkulasi (Alpine.js live) --}}
      <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-2.5 text-sm">
        <h3 class="font-semibold text-gray-700 text-base mb-3">Ringkasan Pemesanan</h3>

        <div class="flex justify-between text-gray-600">
          <span>Harga per bulan</span>
          <span class="font-medium" x-text="formatRp(price)">{{ $room->formatted_price }}</span>
        </div>
        <div class="flex justify-between text-gray-600">
          <span>Durasi</span>
          <span class="font-medium" x-text="duration + ' bulan'">1 bulan</span>
        </div>
        <div class="flex justify-between text-gray-600">
          <span>Tanggal masuk</span>
          <span class="font-medium" x-text="checkIn ? formatDate(checkIn) : '—'">—</span>
        </div>
        <div class="flex justify-between text-gray-600">
          <span>Estimasi keluar</span>
          <span class="font-medium" x-text="checkOutDate">—</span>
        </div>
        <hr class="border-gray-200 my-1">
        <div class="flex justify-between text-gray-900 font-bold text-base">
          <span>Total Sewa</span>
          <span x-text="formatRp(price * duration)">{{ $room->formatted_price }}</span>
        </div>
        <p class="text-xs text-gray-400">
          Tagihan dibayar per bulan. Setelah disetujui, jadwal tagihan akan dikirim via email.
        </p>
      </div>

      {{-- Submit --}}
      @if(!$activeBooking && !$ktpMissing)
        <button type="submit" class="btn btn-primary btn-lg w-full">
          Ajukan Pemesanan
        </button>
      @elseif($ktpMissing)
        <button type="button" disabled
                class="btn btn-secondary btn-lg w-full opacity-50 cursor-not-allowed">
            Lengkapi Foto KTP Terlebih Dahulu
        </button>
      @else
        <button type="button" disabled
                class="btn btn-secondary btn-lg w-full opacity-50 cursor-not-allowed">
          Tidak Dapat Mengajukan Pemesanan Baru
        </button>
      @endif

      <a href="{{ route('rooms.show', $room) }}"
         class="block text-center text-sm text-gray-400 hover:text-gray-600">← Kembali ke detail kamar</a>

    </div>
  </form>
</div>

@push('scripts')
<script>
function bookingCalc(price) {
  return {
    price,
    duration: {{ old('duration_months', 1) }},
    checkIn: '{{ old('check_in_date', '') }}',

    get checkOutDate() {
      if (!this.checkIn) return '—';
      try {
        const d = new Date(this.checkIn);
        d.setMonth(d.getMonth() + parseInt(this.duration));
        d.setDate(d.getDate() - 1);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
      } catch { return '—'; }
    },

    formatRp(num) {
      return 'Rp ' + Number(num).toLocaleString('id-ID');
    },

    formatDate(str) {
      try {
        return new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
      } catch { return str; }
    },
  };
}
</script>
@endpush

@endsection
