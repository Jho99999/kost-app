<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Kost')</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans antialiased" x-data="{ navOpen: false }">

{{-- ── Navigasi ──────────────────────────────────────────────────────── --}}
<nav class="bg-white border-b border-gray-100 sticky top-0 z-30 shadow-sm">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between h-16">

      {{-- Logo --}}
      <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-blue-700 text-lg">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
        </svg>
        Kost
      </a>

      {{-- Desktop links --}}
      <div class="hidden md:flex items-center gap-1">
        @php
          $navLinks = [
            ['route' => 'home',            'label' => 'Beranda',   'match' => 'home'],
            ['route' => 'rooms.index',     'label' => 'Kamar',     'match' => 'rooms.*'],
            ['route' => 'bookings.index',  'label' => 'Pemesanan', 'match' => 'bookings.*'],
            ['route' => 'complaints.index','label' => 'Aduan',     'match' => 'complaints.*'],
            ['route' => 'payments.index',  'label' => 'Tagihan',   'match' => 'payments.*'],
          ];
        @endphp
        @foreach($navLinks as $link)
          <a href="{{ route($link['route']) }}"
             class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs($link['match'])
                        ? 'text-blue-700 bg-blue-50'
                        : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
            {{ $link['label'] }}
          </a>
        @endforeach
      </div>

      {{-- User dropdown (desktop) --}}
      <div class="hidden md:block relative" x-data="{ open: false }" @click.away="open = false">
        <button @click="open = !open"
                class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 rounded-lg px-2 py-1.5 hover:bg-gray-50 transition-colors">
          <span class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-xs select-none">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </span>
          <span class="max-w-28 truncate">{{ auth()->user()->name }}</span>
          <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-150"
               :class="open ? 'rotate-180' : ''"
               fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
          </svg>
        </button>

        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="absolute right-0 mt-1.5 w-44 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50">
          <div class="px-3 py-2 border-b border-gray-50">
            <p class="text-xs font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
          </div>
          <a href="{{ route('profile.edit') }}"
             class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
            Profil
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
              </svg>
              Keluar
            </button>
          </form>
        </div>
      </div>

      {{-- Hamburger (mobile) --}}
      <button @click="navOpen = !navOpen"
              class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
        <svg x-show="!navOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
        <svg x-show="navOpen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
        </svg>
      </button>

    </div>
  </div>

  {{-- Mobile menu --}}
  <div x-show="navOpen" x-cloak class="md:hidden border-t border-gray-100 bg-white">
    <div class="px-4 py-3 space-y-0.5">
      @foreach($navLinks as $link)
        <a href="{{ route($link['route']) }}"
           class="block px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs($link['match'])
                      ? 'text-blue-700 bg-blue-50'
                      : 'text-gray-600 hover:bg-gray-50' }}">
          {{ $link['label'] }}
        </a>
      @endforeach
      <div class="pt-2 mt-1 border-t border-gray-100">
        <a href="{{ route('profile.edit') }}"
           class="block px-3 py-2.5 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
          Profil
        </a>
        <p class="px-3 text-xs text-gray-400 mb-1 truncate">{{ auth()->user()->email }}</p>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="w-full text-left px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
            Keluar
          </button>
        </form>
      </div>
    </div>
  </div>
</nav>

{{-- ── Flash messages ──────────────────────────────────────────────────── --}}
@foreach(['success' => 'alert-success', 'error' => 'alert-error', 'warning' => 'alert-warning', 'info' => 'alert-info'] as $type => $class)
  @if(session($type))
  <div class="max-w-6xl mx-auto px-4 pt-4" x-data="{ show: true }" x-show="show" x-transition>
    <div class="alert {{ $class }}">
      <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        @if($type === 'success')
          <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
        @else
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        @endif
      </svg>
      <span>{{ session($type) }}</span>
      <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100 transition-opacity font-bold">×</button>
    </div>
  </div>
  @endif
@endforeach

{{-- ── Konten utama ─────────────────────────────────────────────────────── --}}
<main class="min-h-screen">
  @yield('content')
</main>

{{-- ── Footer ───────────────────────────────────────────────────────────── --}}
<footer class="mt-16 border-t border-gray-100 bg-white py-6">
  <div class="max-w-6xl mx-auto px-4 text-center text-xs text-gray-400">
    &copy; {{ date('Y') }} Kost &mdash; All rights reserved
  </div>
</footer>

@stack('scripts')
</body>
</html>
