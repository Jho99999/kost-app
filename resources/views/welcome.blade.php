<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', 'Kost') }}</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
  <script>
    (() => {
      const theme = localStorage.getItem('kost-theme') || 'light';
      document.documentElement.dataset.theme = theme;
      document.documentElement.classList.toggle('dark', theme === 'dark');
    })();
  </script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans">
  <header class="border-b border-gray-100 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
      <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-blue-700">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white">K</span>
        {{ config('app.name', 'Kost') }}
      </a>
      <div class="flex items-center gap-2">
        <button type="button" onclick="toggleTheme()" class="theme-toggle" aria-label="Ganti tema">
          <svg class="h-4 w-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
          </svg>
          <svg class="hidden h-4 w-4 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21m9-9h-2.25M5.25 12H3m12.95 6.95-1.6-1.6M9.65 9.65 8.05 8.05m7.9 0-1.6 1.6M9.65 14.35l-1.6 1.6M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5Z" />
          </svg>
        </button>
        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
      </div>
    </div>
  </header>

  <main>
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:py-20">
      <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
          <p class="mb-4 text-sm font-semibold tracking-[0.2em] text-blue-600">MANAJEMEN KOST TERPADU</p>
          <h1 class="text-4xl font-black tracking-tight text-gray-900 sm:text-5xl">
            Platform kost modern untuk <span class="text-blue-700">penghuni dan admin</span>.
          </h1>
          <p class="mt-5 max-w-2xl text-base leading-7 text-gray-600 sm:text-lg">
            Aplikasi ini memudahkan proses sewa kamar, pemesanan, pembayaran, hingga pengelolaan aduan dalam satu dashboard yang rapi dan mudah dipahami.
          </p>

          <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar sebagai penghuni</a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">Saya sudah punya akun</a>
          </div>

          <div class="mt-8 grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4">
              <p class="text-2xl font-bold text-blue-700">1</p>
              <p class="text-sm text-gray-700">Pilih kamar yang sesuai</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4">
              <p class="text-2xl font-bold text-blue-700">2</p>
              <p class="text-sm text-gray-700">Ajukan booking dan bayar</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4">
              <p class="text-2xl font-bold text-blue-700">3</p>
              <p class="text-sm text-gray-700">Pantau status secara real-time</p>
            </div>
          </div>
        </div>

        <div class="card shadow-xl shadow-blue-100/50">
          <div class="card-body grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 p-5">
              <p class="font-semibold text-gray-900">Pemesanan kamar</p>
              <p class="mt-2 text-sm leading-6 text-gray-600">Lihat detail kamar, spesifikasi, dan daftar kamar yang tersedia dengan cepat.</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-5">
              <p class="font-semibold text-gray-900">Pembayaran jelas</p>
              <p class="mt-2 text-sm leading-6 text-gray-600">Pilih metode pembayaran yang tersedia dan unggah bukti transaksi dengan mudah.</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-5">
              <p class="font-semibold text-gray-900">Status terkini</p>
              <p class="mt-2 text-sm leading-6 text-gray-600">Pantau status pemesanan, tagihan, serta verifikasi pembayaran tanpa harus bertanya berulang kali.</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-5">
              <p class="font-semibold text-gray-900">Aduan terkelola</p>
              <p class="mt-2 text-sm leading-6 text-gray-600">Penghuni bisa melaporkan kebutuhan atau masalah, dan admin bisa menindaklanjuti dengan lebih rapi.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
      <div class="rounded-3xl bg-gradient-to-r from-blue-700 to-sky-600 p-8 text-white shadow-xl shadow-blue-200">
        <div class="grid gap-6 md:grid-cols-3">
          <div>
            <h2 class="text-xl font-bold">Untuk penghuni</h2>
            <p class="mt-2 text-sm leading-6 text-blue-50">Mencari kamar, mengajukan booking, mengelola tagihan, dan memantau status hunian dari satu tempat.</p>
          </div>
          <div>
            <h2 class="text-xl font-bold">Untuk admin</h2>
            <p class="mt-2 text-sm leading-6 text-blue-50">Mengelola kamar, booking, pembayaran, dan aduan dengan lebih cepat dan terdokumentasi dengan baik.</p>
          </div>
          <div>
            <h2 class="text-xl font-bold">Hasilnya</h2>
            <p class="mt-2 text-sm leading-6 text-blue-50">Komunikasi lebih jelas, proses lebih tertata, dan pengalaman tinggal kost menjadi lebih profesional.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
      <div class="mb-6">
        <p class="text-sm font-semibold text-blue-600">FITUR UTAMA</p>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Semua kebutuhan kost dalam satu alur sederhana</h2>
      </div>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="card h-full">
          <div class="card-body">
            <p class="font-semibold text-gray-900">Dashboard portal</p>
            <p class="mt-2 text-sm text-gray-600">Tampilan ringkas yang memudahkan penghuni melihat status aktif, tagihan, dan jadwal pembayaran.</p>
          </div>
        </div>

        <div class="card h-full">
          <div class="card-body">
            <p class="font-semibold text-gray-900">Manajemen kamar</p>
            <p class="mt-2 text-sm text-gray-600">Admin dapat menampilkan daftar kamar, detail spesifikasi, dan status ketersediaan dengan cepat.</p>
          </div>
        </div>

        <div class="card h-full">
          <div class="card-body">
            <p class="font-semibold text-gray-900">Pembayaran & bukti</p>
            <p class="mt-2 text-sm text-gray-600">Menyediakan alur pembayaran yang lebih rapi dengan status verifikasi yang tertata.</p>
          </div>
        </div>

        <div class="card h-full">
          <div class="card-body">
            <p class="font-semibold text-gray-900">Aduan & komunikasi</p>
            <p class="mt-2 text-sm text-gray-600">Memungkinkan penghuni mengajukan kebutuhan atau masalah, sehingga proses follow-up jadi lebih mudah.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
      <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="text-2xl font-bold text-gray-900">Kenapa aplikasi ini cocok?</h2>
          <ul class="mt-5 space-y-3 text-sm text-gray-600">
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>Desain responsif, cocok untuk desktop maupun smartphone.</li>
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>Proses sewa dan pembayaran lebih transparan untuk penghuni.</li>
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>Admin bisa mengelola operasional kost dengan workflow yang lebih rapi.</li>
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span>Mudah dikembangkan untuk kebutuhan bisnis kost yang terus berkembang.</li>
          </ul>
        </div>

        <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-lg shadow-slate-300">
          <p class="text-sm font-semibold text-sky-300">Satu aplikasi, banyak manfaat</p>
          <h3 class="mt-2 text-2xl font-bold">Mulai kelola kost Anda dengan lebih profesional.</h3>
          <p class="mt-3 text-sm leading-6 text-slate-300">Dari pencarian kamar sampai proses pembayaran dan pengelolaan aduan, semua tersentralisasi dan mudah dipantau.</p>
          <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('register') }}" class="btn btn-primary">Daftar sekarang</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">Masuk ke dashboard</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="border-t border-gray-100 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-6 text-center text-sm text-gray-500 sm:px-6">
      © {{ date('Y') }} {{ config('app.name', 'Kost') }}
    </div>
  </footer>
</body>
</html>
