@extends('layouts.app')
@section('title', 'Tagihan ' . $payment->payment_code)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="{
    showMethodModal: false,
    selectedMethod: null
  }">

  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('payments.index') }}" class="hover:text-gray-600">Tagihan</a>
    <span>/</span>
    <span class="font-mono text-gray-700">{{ $payment->payment_code }}</span>
  </nav>

  <div class="mb-4">
    <label class="text-xs text-gray-500">Nomor Referensi</label>
    <div class="mt-1">
      <div class="px-3 py-2 bg-gray-100 border border-gray-200 rounded font-mono text-sm text-gray-800 select-all">{{ $payment->payment_code }}</div>
    </div>
  </div>

  @php
    $isPaid    = $payment->status === 'paid';
    $isOverdue = $payment->status === 'overdue';
    $hasProof  = (bool) $payment->proof_image;
    $sc = match($payment->status) {
      'paid'    => 'badge badge-green',
      'overdue' => 'badge badge-red',
      default   => 'badge badge-yellow',
    };
    $sl = match($payment->status) {
      'paid'    => 'Lunas',
      'overdue' => 'Jatuh Tempo',
      default   => 'Belum Bayar',
    };
  @endphp

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Detail Tagihan</h1>
    <span class="{{ $sc }} text-sm px-3 py-1">{{ $sl }}</span>
  </div>

  @if(!$isPaid)
  <div class="mb-6 p-4 border-2 border-orange-200 rounded-lg bg-orange-50">
    <div class="flex items-center justify-between gap-4">
      <div class="flex-1">
        <p class="text-sm font-semibold text-orange-900 mb-2">⚠️ Tagihan Belum Dibayar</p>
        @if($payment->payment_method)
          <p class="text-sm text-gray-700">Metode yang dipilih: <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold text-xs">{{ ucfirst($payment->payment_method) }} ✓</span></p>
        @else
          <p class="text-sm text-gray-600">Silakan pilih metode pembayaran untuk melanjutkan.</p>
        @endif
      </div>
      <button type="button" @click="showMethodModal = true"
              class="px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg flex items-center gap-2 whitespace-nowrap shadow-md transition">
        {{ $payment->payment_method ? 'Ubah Metode' : 'Pilih Metode Bayar' }}
      </button>
    </div>
  </div>
  @endif

  {{-- Banner bukti ditolak --}}
  @if($payment->notes && !$isPaid)
  <div class="alert alert-error mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
    </svg>
    <div>
      <p class="font-medium">Bukti pembayaran Anda ditolak oleh admin.</p>
      <p class="text-sm mt-0.5">Alasan: <em>{{ $payment->notes }}</em></p>
      <p class="text-sm mt-0.5">Silakan unggah bukti pembayaran yang valid di bawah.</p>
    </div>
  </div>
  @endif

  {{-- Menunggu verifikasi --}}
  @if($hasProof && !$isPaid)
  <div class="alert alert-info mb-5">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
    Bukti pembayaran sudah diunggah dan sedang menunggu verifikasi admin.
  </div>
  @endif

  {{-- Detail tagihan --}}
  <div class="card mb-5">
    <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Informasi Tagihan</h3></div>
    <div class="card-body space-y-2 text-sm">
      <div class="flex justify-between"><span class="text-gray-500">Kode Pembayaran</span><span class="font-mono font-medium">{{ $payment->payment_code }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Kamar</span><span class="font-medium text-gray-800">{{ $payment->booking->room->name }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Tagihan Bulan ke-</span><span class="font-medium text-gray-800">{{ $payment->month_number }} dari {{ $payment->booking->duration_months }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Jatuh Tempo</span>
        <span class="font-medium {{ $isOverdue && !$isPaid ? 'text-red-600' : 'text-gray-800' }}">
          {{ \Carbon\Carbon::parse($payment->due_date)->locale('id')->isoFormat('D MMMM YYYY') }}
        </span>
      </div>
      <hr class="border-gray-100">
      <div class="flex justify-between text-base">
        <span class="font-semibold text-gray-700">Jumlah Tagihan</span>
        <span class="font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
      </div>
      @if($isPaid)
      <div class="flex justify-between"><span class="text-gray-500">Tanggal Lunas</span><span class="font-medium text-green-700">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}</span></div>
      @if($payment->verifiedBy)
      <div class="flex justify-between"><span class="text-gray-500">Diverifikasi oleh</span><span class="text-gray-700">{{ $payment->verifiedBy->name }}</span></div>
      @endif
      @endif
    </div>
  </div>

  {{-- Upload bukti / tampilkan bukti --}}
  @if(!$isPaid)
  <div class="card" x-data="{ preview: null }">
    <div class="card-header">
      <h3 class="text-sm font-semibold text-gray-700">
        {{ $hasProof ? 'Ganti Bukti Pembayaran' : 'Upload Bukti Pembayaran' }}
      </h3>
    </div>
    <div class="card-body space-y-4">

      {{-- Tampilkan bukti yang sudah ada --}}
      @if($hasProof)
      <div>
        <p class="text-xs text-gray-500 mb-2">Bukti saat ini:</p>
        <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
           class="block rounded-xl overflow-hidden border border-gray-100 hover:opacity-90 transition">
          <img src="{{ asset('storage/' . $payment->proof_image) }}"
               alt="Bukti pembayaran"
               class="w-full max-h-64 object-contain bg-gray-50">
        </a>
        <p class="text-xs text-gray-400 mt-1 text-center">Klik untuk lihat penuh</p>
      </div>
      @endif

      {{-- Form upload --}}
      <form method="POST" action="{{ route('payments.upload', $payment) }}"
            enctype="multipart/form-data">
        @csrf

        <div class="space-y-3">
          <div>
            <label class="form-label">
            {{ $hasProof ? 'Ganti dengan bukti baru' : 'Upload Bukti Pembayaran' }}
              <span class="text-red-500">*</span>
            </label>
            <input type="file" name="proof_image" accept="image/*,.pdf" required
                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                   class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                          file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100 @error('proof_image') ring-1 ring-red-400 @enderror">
            @error('proof_image')<p class="form-error">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, atau PDF · Maks. 5 MB</p>
          </div>

          {{-- Preview file baru --}}
          <template x-if="preview">
            <div class="rounded-xl overflow-hidden border border-blue-100">
              <img :src="preview" class="w-full max-h-48 object-contain bg-gray-50">
            </div>
          </template>

          <button type="submit" class="btn btn-primary w-full">
            {{ $hasProof ? 'Ganti Bukti Pembayaran' : 'Upload Bukti Pembayaran' }}
          </button>
        </div>
      </form>

    </div>
  </div>
  @else
  {{-- Tampilkan bukti untuk tagihan yang sudah lunas --}}
  @if($payment->proof_image)
  <div class="card">
    <div class="card-header"><h3 class="text-sm font-semibold text-gray-700">Bukti Pembayaran</h3></div>
    <div class="card-body">
      <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank"
         class="block rounded-xl overflow-hidden border border-gray-100 hover:opacity-90 transition">
        <img src="{{ asset('storage/' . $payment->proof_image) }}"
             alt="Bukti pembayaran" class="w-full max-h-64 object-contain bg-gray-50">
      </a>
    </div>
  </div>
  @endif
  @endif

  <div class="mt-5">
    <a href="{{ route('payments.index') }}"
       class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1.5">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
      </svg>
      Kembali ke daftar tagihan
    </a>
  </div>

  {{-- Modal pilih metode pembayaran --}}
  <div x-show="showMethodModal" x-cloak class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4" @click.self="showMethodModal = false">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md z-10 p-6 max-h-[90vh] overflow-y-auto" @click.stop>
      {{-- Header --}}
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="text-lg font-bold text-gray-900">Pilih Metode Pembayaran</h3>
          <p class="text-xs text-gray-500 mt-1">Nominal tagihan: <span class="font-semibold text-gray-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></p>
        </div>
        <button type="button" @click="showMethodModal = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      {{-- Pilihan metode --}}
      <div class="space-y-3 mb-5">
        <button type="button" @click="selectedMethod = 'transfer'"
                :class="selectedMethod === 'transfer' ? 'ring-3 ring-blue-500 bg-blue-50' : 'bg-gray-50 hover:bg-gray-100'"
                class="w-full p-4 border-2 rounded-lg text-left transition duration-200"
                :style="selectedMethod === 'transfer' ? 'border-color: rgb(59, 130, 246)' : 'border-color: rgb(229, 231, 235)'">
          <div class="flex gap-3">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6" :class="selectedMethod === 'transfer' ? 'text-blue-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM0 20.25v-.004c0-.717.564-1.306 1.253-1.671A3.75 3.75 0 015.25 18h13.5a3.75 3.75 0 013.997 3.579c.69.365 1.253.954 1.253 1.671v.004"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold" :class="selectedMethod === 'transfer' ? 'text-blue-900' : 'text-gray-900'">Transfer Bank</p>
              <p class="text-xs mt-1" :class="selectedMethod === 'transfer' ? 'text-blue-700' : 'text-gray-600'">Scan QR atau transfer manual ke rekening kami</p>
            </div>
            <div class="flex-shrink-0">
              <svg class="w-5 h-5" x-show="selectedMethod === 'transfer'" x-cloak class="text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </button>

        <button type="button" @click="selectedMethod = 'cash'"
                :class="selectedMethod === 'cash' ? 'ring-3 ring-green-500 bg-green-50' : 'bg-gray-50 hover:bg-gray-100'"
                class="w-full p-4 border-2 rounded-lg text-left transition duration-200"
                :style="selectedMethod === 'cash' ? 'border-color: rgb(34, 197, 94)' : 'border-color: rgb(229, 231, 235)'">
          <div class="flex gap-3">
            <div class="flex-shrink-0">
              <svg class="w-6 h-6" :class="selectedMethod === 'cash' ? 'text-green-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8a3.5 3.5 0 01-3.5-3.5h7A3.5 3.5 0 0112 8zm0 0l3-3m-3 3l-3-3m4 4v7a2 2 0 11-4 0v-7m-4 0h14a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2v-7a2 2 0 012-2z"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold" :class="selectedMethod === 'cash' ? 'text-green-900' : 'text-gray-900'">Cash (Tunai)</p>
              <p class="text-xs mt-1" :class="selectedMethod === 'cash' ? 'text-green-700' : 'text-gray-600'">Bayar di loket dengan menyimpan kwitansi</p>
            </div>
            <div class="flex-shrink-0">
              <svg class="w-5 h-5" x-show="selectedMethod === 'cash'" x-cloak class="text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </button>
      </div>

      {{-- Detail metode yang dipilih --}}
      <div x-show="selectedMethod" x-cloak class="mb-5 p-4 rounded-lg" :class="selectedMethod === 'transfer' ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200'">
        <template x-if="selectedMethod === 'transfer'">
            <div class="space-y-3">
              <div class="bg-white p-3 rounded border border-blue-100 space-y-2">
                <div>
                  <p class="text-xs text-gray-600 mb-1">Kode Referensi</p>
                  <div class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded text-sm font-mono font-bold text-gray-900 select-all">
                    {{ $payment->payment_code }}
                  </div>
                </div>
                <div>
                  <p class="text-xs text-gray-600 mb-1">Rekening Tujuan</p>
                  <div class="flex gap-2">
                    <input type="text" id="bankAccount" readonly value="{{ config('payment.bank.account') }}"
                           class="flex-1 px-2 py-2 bg-white border border-blue-200 rounded text-sm font-mono font-bold text-gray-900">
                    <button type="button" onclick="copyToClipboard('bankAccount')"
                            class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold transition">Salin</button>
                  </div>
                  <p class="text-xs text-gray-500 mt-1.5">{{ config('payment.bank.name') }} a.n. {{ config('payment.bank.account_name') }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-600 mb-1">Nominal</p>
                  <div class="flex gap-2">
                    <input type="text" id="bankAmount" readonly value="Rp {{ number_format($payment->amount, 0, ',', '.') }}"
                           class="flex-1 px-2 py-2 bg-white border border-blue-200 rounded text-sm font-mono font-bold text-gray-900">
                    <button type="button" onclick="copyToClipboard('bankAmount')"
                            class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold transition">Salin</button>
                  </div>
                </div>
              </div>
              <p class="text-xs text-blue-700 bg-blue-50 border border-blue-100 p-2 rounded">Scan QR atau transfer ke rekening di atas</p>
            </div>
          </template>
          <template x-if="selectedMethod === 'cash'">
            <div class="space-y-2">
              <p class="font-semibold text-green-900">Instruksi Pembayaran Tunai</p>
              <ul class="text-sm text-green-800 space-y-1 list-disc list-inside">
                <li>Kunjungi loket pembayaran</li>
                <li>Bayar Nominal: <span class="font-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></li>
                <li>Simpan kwitansi sebagai bukti</li>
                <li>Upload foto kwitansi</li>
              </ul>
            </div>
          </template>
        </div>

        {{-- Button action --}}
        <div class="flex justify-end gap-3">
          <button type="button" @click="showMethodModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition">Batal</button>

          <form method="POST" :action="'{{ route('payments.method', $payment) }}'" x-show="selectedMethod" x-cloak class="inline">
            @csrf
            <input type="hidden" name="method" :value="selectedMethod">
            <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition">Simpan & Lanjut</button>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
function copyToClipboard(elementId) {
  const el = document.getElementById(elementId);
  const text = el.value;
  navigator.clipboard.writeText(text).then(() => {
    const btn = event.target;
    const oldText = btn.textContent;
    btn.textContent = 'Tersalin!';
    btn.classList.add('bg-green-100', 'text-green-700');
    btn.classList.remove('bg-blue-100', 'text-blue-700');
    setTimeout(() => {
      btn.textContent = oldText;
      btn.classList.remove('bg-green-100', 'text-green-700');
      btn.classList.add('bg-blue-100', 'text-blue-700');
    }, 1500);
  });
}

// No Alpine init needed - using inline x-data object
</script>

@endsection
