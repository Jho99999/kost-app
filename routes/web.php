<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CheckOutController as AdminCheckOutController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\ComplaintController as UserComplaintController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\PaymentController as UserPaymentController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\RoomController as UserRoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : app(HomeController::class)->index();
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('forgot-password', [AuthController::class, 'showForgotForm'])
        ->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('user.role')->group(function () {
        Route::get('rooms', [UserRoomController::class, 'index'])->name('rooms.index');
        Route::get('rooms/{room}', [UserRoomController::class, 'show'])->name('rooms.show');

        Route::get('bookings', [UserBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/create/{room}', [UserBookingController::class, 'create'])
            ->name('bookings.create');
        Route::post('bookings', [UserBookingController::class, 'store'])->name('bookings.store');
        Route::get('bookings/{booking}', [UserBookingController::class, 'show'])->name('bookings.show');

        Route::get('payments', [UserPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [UserPaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/method', [UserPaymentController::class, 'selectMethod'])
            ->name('payments.method');
        Route::post('payments/{payment}/upload', [UserPaymentController::class, 'upload'])
            ->name('payments.upload');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/ktp', [ProfileController::class, 'uploadKtp'])->name('profile.ktp.upload');
        Route::delete('profile/ktp', [ProfileController::class, 'deleteKtp'])->name('profile.ktp.delete');

        Route::resource('complaints', UserComplaintController::class)
            ->only(['index', 'create', 'store', 'show']);
    });

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', AdminUserController::class)->only(['index', 'show']);

        Route::resource('rooms', AdminRoomController::class);
        Route::delete('rooms/{room}/images', [AdminRoomController::class, 'destroyImage'])
            ->name('rooms.images.destroy');

        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');

        Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::put('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])
            ->name('payments.verify');

        Route::resource('payment-methods', PaymentMethodController::class)
            ->except(['show']);

        Route::get('complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
        Route::get('complaints/{complaint}', [AdminComplaintController::class, 'show'])->name('complaints.show');
        Route::put('complaints/{complaint}', [AdminComplaintController::class, 'update'])
            ->name('complaints.update');

        Route::get('checkouts', [AdminCheckOutController::class, 'index'])->name('checkouts.index');
        Route::get('checkouts/{booking}', [AdminCheckOutController::class, 'show'])->name('checkouts.show');
        Route::post('checkouts/{booking}/process', [AdminCheckOutController::class, 'process'])
            ->name('checkouts.process');
        Route::post('checkouts/{booking}/extend', [AdminCheckOutController::class, 'extend'])
            ->name('checkouts.extend');
        Route::get('checkouts/room/{room}/history', [AdminCheckOutController::class, 'roomHistory'])
            ->name('checkouts.history');
    });
});
