<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Jadwal Cron Aplikasi (Laravel 11 — tanpa Kernel.php)
|--------------------------------------------------------------------------
|
| Pastikan server (Railway/VPS) menjalankan perintah berikut setiap menit:
|   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
|
| Di Railway: Settings → Cron Jobs → tambah job dengan perintah di atas.
|
*/

// Tandai tagihan overdue setiap hari tengah malam lewat 5 menit
// (sedikit offset agar tidak bentrok dengan proses midnight lain)
Schedule::command('payments:mark-overdue')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();

// Kirim reminder H-7 setiap hari pukul 08.00 pagi
Schedule::command('payments:send-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();

// Hapus KTP pengguna yang masa sewanya sudah habis (tengah malam)
Schedule::command('ktp:clean-expired')
    ->dailyAt('00:10')
    ->withoutOverlapping()
    ->runInBackground();
