<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Kost')</title>
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
<body class="h-full bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">

{{-- ── Sidebar overlay (mobile) ────────────────────────────────────────── --}}
<div x-show="sidebarOpen" x-cloak
     @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/40 lg:hidden"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
</div>

{{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
<aside class="fixed inset-y-0 left-0 z-30 w-60 bg-slate-800 flex flex-col overflow-x-hidden
              transform transition-transform duration-200 ease-in-out
              lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

  {{-- Logo --}}
  <div class="flex items-center gap-2.5 h-16 px-5 border-b border-slate-700 flex-shrink-0">
    <div class="w-7 h-7 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
      <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
      </svg>
    </div>
    <div>
      <p class="text-white text-sm font-semibold leading-tight">Kost</p>
      <p class="text-slate-400 text-xs">Admin Panel</p>
    </div>
  </div>

  {{-- Nav items --}}
  <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3 space-y-0.5">

    @php
    $navItems = [
      [
        'route'  => 'admin.dashboard',
        'match'  => 'admin.dashboard',
        'label'  => 'Dashboard',
        'icon'   => 'M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125V5.625A2.625 2.625 0 0 1 4.875 3h14.25A2.625 2.625 0 0 1 21.75 5.625v12.75A2.625 2.625 0 0 1 19.125 21H4.875A2.625 2.625 0 0 1 2.25 18.375V18.5m0 .875h1.5m-1.5-1.5V5.625M6 18.375V6.375A2.625 2.625 0 0 1 8.625 3.75h6.75A2.625 2.625 0 0 1 18 6.375v12m-12 0h12',
      ],
      [
        'route'  => 'admin.rooms.index',
        'match'  => 'admin.rooms.*',
        'label'  => 'Manajemen Kamar',
        'icon'   => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
      ],
      [
        'route'  => 'admin.bookings.index',
        'match'  => 'admin.bookings.*',
        'label'  => 'Pemesanan',
        'icon'   => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
      ],
      [
        'route'  => 'admin.payments.index',
        'match'  => 'admin.payments.*',
        'label'  => 'Pembayaran',
        'icon'   => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z',
      ],
      [
          'route' => 'admin.payment-methods.index',
          'match' => 'admin.payment-methods.*',
          'label' => 'Metode Pembayaran',
          'icon'  => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z',
      ],
      [
        'route'  => 'admin.complaints.index',
        'match'  => 'admin.complaints.*',
        'label'  => 'Aduan',
        'icon'   => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
      ],
      [
          'route' => 'admin.users.index',
          'match' => 'admin.users.*',
          'label' => 'Penyewa',
          'icon'  => 'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.964 0a9 9 0 1 0-11.964 0m11.964 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
      ],
    ];
    @endphp

    @foreach($navItems as $item)
  <a href="{{ route($item['route']) }}"
     class="sidebar-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
    </svg>
    <span class="truncate">{{ $item['label'] }}</span>
  </a>
    @endforeach

  </nav>

  {{-- Footer sidebar: info user + logout --}}
  <div class="border-t border-slate-700 p-3 flex-shrink-0">
    <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg mb-1">
      <div class="w-7 h-7 rounded-full bg-slate-600 flex items-center justify-center text-slate-200 text-xs font-semibold flex-shrink-0">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <div class="min-w-0">
        <p class="text-slate-200 text-xs font-medium truncate">{{ auth()->user()->name }}</p>
        <p class="text-slate-500 text-xs truncate">Administrator</p>
      </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              class="sidebar-link w-full text-slate-400 hover:text-red-400 hover:bg-red-500/10">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
        </svg>
        Keluar
      </button>
    </form>
  </div>

</aside>

{{-- ── Area utama (kanan sidebar) ─────────────────────────────────────── --}}
<div class="lg:pl-60 flex flex-col min-h-screen">

  {{-- Topbar --}}
  <header class="sticky top-0 z-10 h-16 bg-white border-b border-gray-100 shadow-sm flex items-center px-4 gap-3">
    {{-- Hamburger (mobile) --}}
    <button @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors flex-shrink-0">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>

    <h1 class="text-base font-semibold text-gray-800 flex-1">
      @yield('page-title', 'Dashboard')
    </h1>

    <span class="text-xs text-gray-400 hidden sm:block">
      {{ now()->isoFormat('dddd, D MMMM Y') }}
    </span>
    <button type="button" onclick="toggleTheme()" class="theme-toggle" aria-label="Ganti tema">
      <svg class="h-4 w-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
      </svg>
      <svg class="hidden h-4 w-4 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21m9-9h-2.25M5.25 12H3m12.95 6.95-1.6-1.6M9.65 9.65 8.05 8.05m7.9 0-1.6 1.6M9.65 14.35l-1.6 1.6M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5Z" />
      </svg>
    </button>
  </header>

  {{-- Flash messages --}}
  <div class="px-4 sm:px-6 pt-4 space-y-3">
    @foreach(['success' => 'alert-success', 'error' => 'alert-error', 'warning' => 'alert-warning', 'info' => 'alert-info'] as $type => $class)
      @if(session($type))
      <div x-data="{ show: true }" x-show="show" x-transition>
        <div class="alert {{ $class }}">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            @if($type === 'success')
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
            @else
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
            @endif
          </svg>
          <span>{{ session($type) }}</span>
          <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100 font-bold transition-opacity">×</button>
        </div>
      </div>
      @endif
    @endforeach
  </div>

  {{-- Konten halaman --}}
  <main class="flex-1 px-4 py-5 sm:px-6 sm:py-6">
    @yield('content')
  </main>

</div>

@stack('scripts')
</body>
</html>
