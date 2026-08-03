<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kost</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (() => {
            const theme = localStorage.getItem('kost-theme') || 'light';
            document.documentElement.dataset.theme = theme;
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-slate-50 font-sans">

<div class="absolute right-4 top-4 z-10">
    <button type="button" onclick="toggleTheme()" class="theme-toggle" aria-label="Ganti tema">
        <svg class="h-4 w-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
        </svg>
        <svg class="hidden h-4 w-4 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21m9-9h-2.25M5.25 12H3m12.95 6.95-1.6-1.6M9.65 9.65 8.05 8.05m7.9 0-1.6 1.6M9.65 14.35l-1.6 1.6M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5Z" />
        </svg>
    </button>
</div>

<div class="min-h-screen flex">

    {{-- ── Left panel (hidden mobile) ─────────────── --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-gradient-to-br from-blue-700 to-blue-900 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-600/30 rounded-full"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-blue-800/40 rounded-full"></div>

        <div class="relative">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                </div>
                <span class="text-white font-bold text-xl">{{ config('app.name') }}</span>
            </div>

            <h2 class="text-white text-3xl font-bold leading-snug mb-4">
                Atur ulang password<br>Anda dengan mudah
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
                Masukkan email yang terdaftar, dan kami akan kirimkan tautan untuk mereset password Anda.
            </p>
        </div>

        <p class="relative text-blue-300 text-xs">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>

    {{-- ── Right panel: form ────────────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10">
        <div class="lg:hidden flex items-center gap-2 mb-8">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" /></svg>
            </div>
            <span class="font-bold text-blue-600">{{ config('app.name') }}</span>
        </div>

        <div class="w-full max-w-sm">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Lupa password?</h1>
            <p class="text-sm text-gray-500 mb-8">Jangan khawatir, masukkan email Anda dan kami akan kirim tautan reset.</p>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert-error mb-5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success --}}
            @if (session('success'))
                <div class="alert-success mb-5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           autocomplete="email"
                           class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                           required autofocus>
                </div>

                <button type="submit" class="btn-primary btn-lg w-full">
                    Kirim Tautan Reset
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-700">
                    &larr; Kembali ke halaman login
                </a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
