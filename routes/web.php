<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\BookingController  as AdminBooking;
use App\Http\Controllers\Admin\CheckOutController as AdminCheckOut;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaint;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PaymentController  as AdminPayment;
use App\Http\Controllers\Admin\RoomController     as AdminRoom;
use App\Http\Controllers\User\BookingController   as UserBooking;
use App\Http\Controllers\User\ComplaintController as UserComplaint;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\PaymentController   as UserPayment;
use App\Http\Controllers\User\ProfileController  as UserProfile;
use App\Http\Controllers\User\RoomController      as UserRoom;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


Route::get('/smtp-test', function () {

    Mail::raw('SMTP TEST', function ($mail) {
        $mail->to('emailanda@gmail.com')
             ->subject('SMTP TEST');
    });

    return 'SUCCESS';
});

Route::get('/mail-config', function () {
    return [
        'mailer' => config('mail.default'),
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'username' => config('mail.mailers.smtp.username'),
        'scheme' => config('mail.mailers.smtp.scheme'),
        'from' => config('mail.from.address'),
    ];
});
/* ── Guest only ─────────────────────────────────────────────── */
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Lupa Password
    Route::get('/forgot-password',        [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',       [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');
});

/* ── Authenticated ──────────────────────────────────────────── */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /* ── User area ─────────────────────────────────────────── */
    Route::middleware('user.role')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Fase 2: Kamar
        Route::get('/rooms',        [UserRoom::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{room}', [UserRoom::class, 'show'])->name('rooms.show');

        // Fase 3: Pemesanan
        Route::get('/bookings',               [UserBooking::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create/{room}', [UserBooking::class, 'create'])->name('bookings.create');
        Route::post('/bookings',              [UserBooking::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}',     [UserBooking::class, 'show'])->name('bookings.show');

        // Fase 4: Pembayaran
        Route::get('/payments',                        [UserPayment::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}',              [UserPayment::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/method',      [UserPayment::class, 'selectMethod'])->name('payments.method');
        Route::post('/payments/{payment}/upload',      [UserPayment::class, 'upload'])->name('payments.upload');

        // Profil
        Route::get('/profile',              [UserProfile::class, 'edit'])->name('profile.edit');
        Route::put('/profile',              [UserProfile::class, 'update'])->name('profile.update');
        Route::post('/profile/ktp',         [UserProfile::class, 'uploadKtp'])->name('profile.ktp.upload');
        Route::delete('/profile/ktp',       [UserProfile::class, 'deleteKtp'])->name('profile.ktp.delete');

        // Aduan
        Route::resource('complaints', UserComplaint::class)->only(['index', 'create', 'store', 'show']);
    });

    /* ── Admin area ────────────────────────────────────────── */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Fase 2: Manajemen Kamar
        Route::resource('rooms', AdminRoom::class);
        Route::delete('rooms/{room}/images', [AdminRoom::class, 'destroyImage'])
             ->name('rooms.images.destroy');

        // Fase 3: Manajemen Pemesanan
        Route::get('bookings',           [AdminBooking::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBooking::class, 'show'])->name('bookings.show');
        Route::put('bookings/{booking}', [AdminBooking::class, 'update'])->name('bookings.update');

        // Fase 4: Manajemen Pembayaran
        Route::get('payments',                      [AdminPayment::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}',            [AdminPayment::class, 'show'])->name('payments.show');
        Route::put('payments/{payment}/verify',     [AdminPayment::class, 'verify'])->name('payments.verify');

        // Aduan
        Route::get('complaints',                    [AdminComplaint::class, 'index'])->name('complaints.index');
        Route::get('complaints/{complaint}',        [AdminComplaint::class, 'show'])->name('complaints.show');
        Route::put('complaints/{complaint}',        [AdminComplaint::class, 'update'])->name('complaints.update');

        // Check-Out & Perpanjangan
        Route::get('checkouts',                         [AdminCheckOut::class, 'index'])->name('checkouts.index');
        Route::get('checkouts/{booking}',               [AdminCheckOut::class, 'show'])->name('checkouts.show');
        Route::post('checkouts/{booking}/process',      [AdminCheckOut::class, 'process'])->name('checkouts.process');
        Route::post('checkouts/{booking}/extend',       [AdminCheckOut::class, 'extend'])->name('checkouts.extend');
        Route::get('checkouts/room/{room}/history',     [AdminCheckOut::class, 'roomHistory'])->name('checkouts.history');
    });



});
