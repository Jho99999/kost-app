@extends('layouts.app')
@section('title', 'Tagihan ' . $payment->payment_code)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
  <nav class="text-sm text-gray-400 mb-5 flex items-center gap-1.5">
    <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
    <span>/</span>
    <a href="{{ route('payments.index') }}" class="hover:text-gray-600">Tagihan</a>
    <span>/</span>
    <span class="font-mono text-gray-700">{{ $payment->payment_code }}</span>
  </nav>

  @php
    $isPaid = $payment->status === 'paid';
    $hasProof = (bool) $payment->proof_image;
    $statusClass = match($payment->status) {
      'paid' => 'badge badge-green',
      'overdue' => 'badge badge-red',
      default => 'badge badge-yellow',
    };
  @endphp

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-xl font-bold text-gray-900">Detail Tagihan</h1>
    <span class="{{ $statusClass }} text-sm px-3 py-1">{{ $payment->status_label }}</span>
  </div>

  <div class="card mb-5">
    <div class="card-header">
      <h2 class="text-sm font-semibold text-gray-700">Informasi Tagihan</h2>
    </div>
    <div class="card-body space-y-2 text-sm">
      <div class="flex justify-between gap-4">
        <span class="text-gray-500">Kamar</span>
        <span class="font-medium text-right text-gray-800">{{ $payment->booking->room->name }}</span>
      </div>
      <div class="flex justify-between gap-4">
        <span class="text-gray-500">Periode</span>
        <span class="font-medium text-right text-gray-800">Bulan ke-{{ $payment->month_number }} dari {{ $payment->booking->duration_months }}</span>
      </div>
      <div class="flex justify-between gap-4">
        <span class="text-gray-500">Jatuh tempo</span>
        <span class="font-medium text-right {{ $payment->is_overdue && ! $isPaid ? 'text-red-600' : 'text-gray-800' }}">
          {{ $payment->due_date->locale('id')->isoFormat('D MMMM YYYY') }}
        </span>
      </div>
      <hr class="border-gray-100">
      <div class="flex justify-between text-base">
        <span class="font-semibold text-gray-700">Total pembayaran</span>
        <span class="font-bold text-gray-900">{{ $payment->formatted_amount }}</span>
      </div>
    </div>
  </div>

  @if($isPaid)
    <div class="alert alert-success mb-5">Tagihan ini sudah lunas.</div>
  @else
    <div class="card mb-5">
      <div class="card-header">
        <h2 class="text-sm font-semibold text-gray-700">Pilih Cara Pembayaran</h2>
      </div>
      <div class="card-body">
        @if($paymentMethods->isEmpty())
          <p class="text-sm text-gray-500">Belum ada metode pembayaran yang tersedia. Silakan hubungi admin.</p>
        @else
          <div class="space-y-3">
            @foreach($paymentMethods as $method)
              <form method="POST" action="{{ route('payments.method', $payment) }}">
                @csrf
                <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                <button type="submit"
                        class="w-full text-left rounded-xl border p-4 transition {{ $payment->payment_method_id === $method->id ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50' }}">
                  <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                      <p class="font-semibold text-gray-900">{{ $method->name }}</p>
                      <p class="mt-1 text-xs text-gray-500">
                        {{ match($method->type) { 'bank' => 'Transfer bank', 'qris' => 'Bayar dengan QRIS', default => 'E-wallet' } }}
                      </p>
                    </div>
                    @if($payment->payment_method_id === $method->id)
                      <span class="badge badge-blue">Dipilih</span>
                    @else
                      <span class="text-sm font-medium text-blue-600">Pilih</span>
                    @endif
                  </div>
                </button>
              </form>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    @if($payment->paymentMethod)
      @php $method = $payment->paymentMethod; @endphp
      <div class="card mb-5" id="payment-instructions">
        <div class="card-header">
          <h2 class="text-sm font-semibold text-gray-700">Bayar melalui {{ $method->name }}</h2>
        </div>
        <div class="card-body space-y-4">
          <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 text-sm text-blue-900">
            Bayar tepat sebesar <strong>{{ $payment->formatted_amount }}</strong>, lalu gunakan kode
            <strong class="font-mono">{{ $payment->payment_code }}</strong> sebagai referensi pembayaran.
          </div>

          @if($method->type === 'qris' && $method->image)
            <div class="text-center">
              <img src="{{ asset('storage/' . $method->image) }}"
                   alt="QRIS {{ $method->name }}"
                   class="mx-auto max-w-xs rounded-xl border border-gray-200">
              <p class="mt-3 text-sm text-gray-600">Scan QRIS dengan aplikasi pembayaran Anda.</p>
            </div>
          @else
            <div class="space-y-3 text-sm">
              @if($method->account_number)
                <div>
                  <p class="mb-1 text-gray-500">Nomor rekening / akun</p>
                  <div class="flex gap-2">
                    <input id="account-number" readonly value="{{ $method->account_number }}"
                           class="form-input flex-1 font-mono font-semibold">
                    <button type="button" data-copy="account-number" class="btn btn-secondary">Salin</button>
                  </div>
                </div>
              @endif
              @if($method->account_name)
                <div class="flex justify-between gap-4">
                  <span class="text-gray-500">Atas nama</span>
                  <span class="font-medium text-right text-gray-800">{{ $method->account_name }}</span>
                </div>
              @endif
              @if($method->notes)
                <div class="rounded-lg bg-gray-50 p-3 text-gray-600">{{ $method->notes }}</div>
              @endif
            </div>
          @endif

          <div>
            <p class="mb-1 text-sm text-gray-500">Nominal pembayaran</p>
            <div class="flex gap-2">
              <input id="payment-amount" readonly value="{{ $payment->formatted_amount }}"
                     class="form-input flex-1 font-semibold">
              <button type="button" data-copy="payment-amount" class="btn btn-secondary">Salin</button>
            </div>
          </div>

          <a href="#payment-proof" class="btn btn-primary w-full text-center block">
            Saya Sudah Membayar
          </a>
        </div>
      </div>
    @endif

    @if($payment->notes)
      <div class="alert alert-error mb-5">
        Bukti pembayaran ditolak: {{ $payment->notes }}. Silakan periksa kembali dan kirim bukti baru.
      </div>
    @endif

    <div class="card" id="payment-proof" x-data="{ preview: null }">
      <div class="card-header">
        <h2 class="text-sm font-semibold text-gray-700">Konfirmasi Pembayaran</h2>
      </div>
      <div class="card-body space-y-4">
        @if($hasProof)
          <div class="alert alert-info">Bukti pembayaran sudah dikirim dan sedang menunggu verifikasi admin.</div>
          <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank" class="block">
            <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti pembayaran"
                 class="max-h-64 w-full rounded-xl border object-contain">
          </a>
        @endif

        <form method="POST" action="{{ route('payments.upload', $payment) }}" enctype="multipart/form-data">
          @csrf
          <label class="form-label">{{ $hasProof ? 'Ganti bukti pembayaran' : 'Unggah bukti setelah pembayaran dilakukan' }}</label>
          <input type="file" name="proof_image" accept="image/jpeg,image/png,image/webp,application/pdf" required
                 @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                 class="form-input">
          @error('proof_image')<p class="form-error">{{ $message }}</p>@enderror
          <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP, atau PDF. Maksimal 5 MB.</p>
          <template x-if="preview">
            <img :src="preview" alt="Pratinjau bukti" class="mt-3 max-h-48 w-full rounded-xl border object-contain">
          </template>
          <button type="submit" class="btn btn-primary mt-4 w-full">
            {{ $hasProof ? 'Kirim Bukti Baru' : 'Kirim Bukti Pembayaran' }}
          </button>
        </form>
      </div>
    </div>
  @endif

</div>

<script>
document.querySelectorAll('[data-copy]').forEach((button) => {
  button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.copy);

    navigator.clipboard.writeText(input.value).then(() => {
      const label = button.textContent;
      button.textContent = 'Tersalin';
      setTimeout(() => button.textContent = label, 1500);
    });
  });
});
</script>
@endsection
