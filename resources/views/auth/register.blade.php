<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kost</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans">

<div class="min-h-screen flex">

    {{-- ── Left panel ───────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-gradient-to-br from-blue-700 to-blue-900 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-blue-600/30 rounded-full"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-blue-800/40 rounded-full"></div>

        <div class="relative">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                </div>
                <span class="text-white font-bold text-xl">{{ config('app.name') }}</span>
            </div>

            <h2 class="text-white text-3xl font-bold leading-snug mb-4">
                Mulai perjalanan<br>tinggal yang nyaman
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed">
                Daftar sekarang dan nikmati kemudahan mengelola sewa kost Anda — dari pemesanan hingga pembayaran, semua dalam satu platform.
            </p>

            <div class="mt-10 p-5 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20">
                <p class="text-white text-sm font-medium mb-3">Sudah bergabung dengan kami:</p>
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        @foreach (['bg-pink-400','bg-green-400','bg-yellow-400','bg-purple-400'] as $color)
                        <div class="w-8 h-8 rounded-full {{ $color }} border-2 border-blue-800 flex items-center justify-center text-white text-xs font-bold">{{ chr(rand(65,90)) }}</div>
                        @endforeach
                    </div>
                    <p class="text-blue-200 text-sm">dan banyak penghuni lainnya</p>
                </div>
            </div>
        </div>

        <p class="relative text-blue-300 text-xs">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>

    {{-- ── Right panel: form ────────────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 overflow-y-auto">
        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-2 mb-8">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" /></svg>
            </div>
            <span class="font-bold text-blue-600">{{ config('app.name') }}</span>
        </div>

        <div class="w-full max-w-sm">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Buat akun baru</h1>
            <p class="text-sm text-gray-500 mb-8">Isi data di bawah untuk mendaftar sebagai penyewa</p>

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

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name') }}"
                           placeholder="Masukkan nama lengkap"
                           autocomplete="name"
                           class="form-input {{ $errors->has('name') ? 'input-error' : '' }}"
                           required autofocus>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           autocomplete="email"
                           class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
                           required>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="form-label">Nomor HP / WhatsApp</label>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone') }}"
                           placeholder="08xxxxxxxxxx"
                           class="form-input {{ $errors->has('phone') ? 'input-error' : '' }}"
                           required>
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="form-label">Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'"
                               id="password" name="password"
                               placeholder="Minimal 8 karakter"
                               autocomplete="new-password"
                               class="form-input pr-10 {{ $errors->has('password') ? 'input-error' : '' }}"
                               required>
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            <svg x-show="show"  class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Ulangi password"
                           autocomplete="new-password"
                           class="form-input"
                           required>
                </div>

                <button type="submit" class="btn-primary btn-lg w-full">
                    Buat Akun
                </button>

                <p class="text-center text-xs text-gray-400">
                    Dengan mendaftar, Anda menyetujui syarat dan ketentuan penggunaan layanan kami.
                </p>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-700">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
