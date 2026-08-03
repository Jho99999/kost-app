<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Kost')</title>
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
@php
  $navLinks = [
    ['route' => 'home', 'label' => 'Beranda', 'match' => 'home'],
    ['route' => 'rooms.index', 'label' => 'Kamar', 'match' => 'rooms.*'],
    ['route' => 'bookings.index', 'label' => 'Pemesanan', 'match' => 'bookings.*'],
    ['route' => 'payments.index', 'label' => 'Tagihan', 'match' => 'payments.*'],
    ['route' => 'complaints.index', 'label' => 'Aduan', 'match' => 'complaints.*'],
  ];
@endphp

<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/40 lg:hidden"></div>

<aside class="fixed inset-y-0 left-0 z-30 flex w-60 flex-col border-r border-gray-100 bg-white
              transition-transform duration-200 lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
  <div class="flex h-16 items-center gap-2.5 border-b border-gray-100 px-5">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 font-bold text-white">K</span>
    <div>
      <p class="text-sm font-semibold text-gray-900">Kost</p>
      <p class="text-xs text-gray-400">Portal Penghuni</p>
    </div>
  </div>

  <nav class="flex-1 space-y-1 overflow-y-auto p-3">
    @foreach($navLinks as $link)
      <a href="{{ route($link['route']) }}"
         class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs($link['match']) ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
        {{ $link['label'] }}
      </a>
    @endforeach
  </nav>

  <div class="border-t border-gray-100 p-3">
    <a href="{{ route('profile.edit') }}" class="mb-1 flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">
      <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </span>
      <span class="min-w-0 truncate">{{ auth()->user()->name }}</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">
        Keluar
      </button>
    </form>
  </div>
</aside>

<div class="flex min-h-screen flex-col lg:pl-60">
  <header class="sticky top-0 z-10 flex h-16 items-center gap-3 border-b border-gray-100 bg-white px-4 shadow-sm sm:px-6">
    <button type="button" @click="sidebarOpen = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" aria-label="Buka menu">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
    <p class="flex-1 text-sm font-semibold text-gray-800">@yield('page-title', 'Portal Penghuni')</p>
    <button type="button" onclick="toggleTheme()" class="theme-toggle hidden sm:inline-flex" aria-label="Ganti tema">
      <svg class="h-4 w-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
      </svg>
      <svg class="hidden h-4 w-4 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21m9-9h-2.25M5.25 12H3m12.95 6.95-1.6-1.6M9.65 9.65 8.05 8.05m7.9 0-1.6 1.6M9.65 14.35l-1.6 1.6M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5Z" />
      </svg>
    </button>
  </header>

  @foreach(['success' => 'alert-success', 'error' => 'alert-error', 'warning' => 'alert-warning', 'info' => 'alert-info'] as $type => $class)
    @if(session($type))
      <div class="px-4 pt-4 sm:px-6" x-data="{ show: true }" x-show="show">
        <div class="alert {{ $class }}">
          <span>{{ session($type) }}</span>
          <button @click="show = false" class="ml-auto font-bold opacity-60 hover:opacity-100">×</button>
        </div>
      </div>
    @endif
  @endforeach

  <main class="flex-1">
    @yield('content')
  </main>

  <footer class="border-t border-gray-100 bg-white px-4 py-5 text-center text-xs text-gray-400 sm:px-6">
    © {{ date('Y') }} Kost
  </footer>
</div>

@stack('scripts')
</body>
</html>
