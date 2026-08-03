<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', 'Kost') }}</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

  {{-- ── Header ──────────────────────────────────── --}}
  <header class="border-b border-gray-100 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
      <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-blue-700">
        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
          </svg>
        </div>
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
    {{-- ── Hero ──────────────────────────────────── --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:py-20">
      <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">

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

        {{-- Blue gradient panel — same visual language as the login page's left panel --}}
        <div class="hidden lg:block bg-gradient-to-br from-blue-700 to-blue-900 rounded-3xl p-10 relative overflow-hidden shadow-xl">
          <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-600/30 rounded-full"></div>
          <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-blue-800/40 rounded-full"></div>

          <div class="relative">
            <div class="flex items-center gap-3 mb-8">
              <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
              </div>
              <span class="text-white font-bold text-xl">{{ config('app.name', 'Kost') }}</span>
            </div>

            <h2 class="text-white text-2xl font-bold leading-snug mb-3">
              Semua proses kost, dalam satu dashboard.
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
              Dari mencari kamar sampai memantau tagihan — tidak perlu chat bolak-balik dengan admin.
            </p>

            <div class="mt-8 space-y-4">
              @foreach ([
                'Pemesanan kamar online kapan saja',
                'Riwayat pembayaran yang tercatat rapi',
                'Notifikasi tagihan otomatis',
                'Aduan penghuni langsung ke admin',
              ] as $text)
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="text-blue-100 text-sm">{{ $text }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </div>

      </div>
    </section>

    {{-- ── Untuk siapa ─────────────────────────────── --}}
    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
      <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-blue-600/20 rounded-full"></div>
        <div class="absolute -bottom-20 -left-10 w-56 h-56 bg-blue-800/30 rounded-full"></div>

        <div class="relative grid gap-6 md:grid-cols-3">
          <div>
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-3">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>
            </div>
            <h2 class="text-xl font-bold">Untuk penghuni</h2>
            <p class="mt-2 text-sm leading-6 text-blue-100">Mencari kamar, mengajukan booking, mengelola tagihan, dan memantau status hunian dari satu tempat.</p>
          </div>
          <div>
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-3">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 21h14a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1Z" />
              </svg>
            </div>
            <h2 class="text-xl font-bold">Untuk admin</h2>
            <p class="mt-2 text-sm leading-6 text-blue-100">Mengelola kamar, booking, pembayaran, dan aduan dengan lebih cepat dan terdokumentasi dengan baik.</p>
          </div>
          <div>
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm mb-3">
              <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
            </div>
            <h2 class="text-xl font-bold">Hasilnya</h2>
            <p class="mt-2 text-sm leading-6 text-blue-100">Komunikasi lebih jelas, proses lebih tertata, dan pengalaman tinggal kost menjadi lebih profesional.</p>
          </div>
        </div>
      </div>
    </section>

    {{-- ── Fitur utama ─────────────────────────────── --}}
    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
      <div class="mb-6">
        <p class="text-sm font-semibold text-blue-600">FITUR UTAMA</p>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Semua kebutuhan kost dalam satu alur sederhana</h2>
      </div>

      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @php
          $features = [
            ['title' => 'Dashboard portal', 'desc' => 'Tampilan ringkas yang memudahkan penghuni melihat status aktif, tagihan, dan jadwal pembayaran.',
             'icon' => 'M3 13.5h4.5V21H3v-7.5Zm6.75-6H14.25V21H9.75V7.5ZM16.5 3H21v18h-4.5V3Z'],
            ['title' => 'Manajemen kamar', 'desc' => 'Admin dapat menampilkan daftar kamar, detail spesifikasi, dan status ketersediaan dengan cepat.',
             'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75'],
            ['title' => 'Pembayaran & bukti', 'desc' => 'Menyediakan alur pembayaran yang lebih rapi dengan status verifikasi yang tertata.',
             'icon' => 'M2.25 8.25h19.5M2.25 8.25v10.5A1.5 1.5 0 0 0 3.75 20.25h16.5a1.5 1.5 0 0 0 1.5-1.5V8.25M2.25 8.25l1.5-3A1.5 1.5 0 0 1 5.16 4.5h13.68a1.5 1.5 0 0 1 1.41 1.5l1.5 3'],
            ['title' => 'Aduan & komunikasi', 'desc' => 'Memungkinkan penghuni mengajukan kebutuhan atau masalah, sehingga proses follow-up jadi lebih mudah.',
             'icon' => 'M8.25 10.5h7.5m-7.5 3h4.5m6-9H5.25a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h3v3.75l4.5-3.75h5.25a2.25 2.25 0 0 0 2.25-2.25v-9A2.25 2.25 0 0 0 18.75 4.5Z'],
          ];
        @endphp

        @foreach($features as $f)
        <div class="card h-full">
          <div class="card-body">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-3">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
              </svg>
            </div>
            <p class="font-semibold text-gray-900">{{ $f['title'] }}</p>
            <p class="mt-2 text-sm text-gray-600">{{ $f['desc'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </section>

    {{-- ── Kenapa cocok ─────────────────────────────── --}}
    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
      <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
          <h2 class="text-2xl font-bold text-gray-900">Kenapa aplikasi ini cocok?</h2>
          <ul class="mt-5 space-y-4 text-sm text-gray-600">
            @foreach ([
              'Desain responsif, cocok untuk desktop maupun smartphone.',
              'Proses sewa dan pembayaran lebih transparan untuk penghuni.',
              'Admin bisa mengelola operasional kost dengan workflow yang lebih rapi.',
              'Mudah dikembangkan untuk kebutuhan bisnis kost yang terus berkembang.',
            ] as $point)
            <li class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
              {{ $point }}
            </li>
            @endforeach
          </ul>
        </div>

        <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-3xl p-8 text-white shadow-lg relative overflow-hidden">
          <div class="absolute -top-16 -right-16 w-56 h-56 bg-blue-600/25 rounded-full"></div>
          <div class="relative">
            <p class="text-sm font-semibold text-blue-200">Satu aplikasi, banyak manfaat</p>
            <h3 class="mt-2 text-2xl font-bold">Mulai kelola kost Anda dengan lebih profesional.</h3>
            <p class="mt-3 text-sm leading-6 text-blue-100">Dari pencarian kamar sampai proses pembayaran dan pengelolaan aduan, semua tersentralisasi dan mudah dipantau.</p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
              <a href="{{ route('register') }}" class="btn btn-primary">Daftar sekarang</a>
              <a href="{{ route('login') }}" class="rounded-lg border border-white/30 px-4 py-2 text-center font-medium text-white hover:bg-white/10">Masuk ke dashboard</a>
            </div>
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