<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HostAuthController;
use App\Http\Controllers\OtpController;

use App\Http\Controllers\Admin\StayController;
use App\Http\Controllers\Admin\StayImageController;
use App\Http\Controllers\Admin\DiscountContractController;
use App\Http\Controllers\Admin\DiscountContractMemberController;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAuthController;


Route::get('/', function () {
    return view('home');
});

// Users
Route::post('/send-otp', [UserAuthController::class, 'sendOtp'])->name('user.sendOtp');
Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp'])->name('user.verifyOtp');

// Admins
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/send-otp', [AdminAuthController::class, 'sendOtp'])->name('admin.sendOtp');
    Route::post('/verify-otp', [AdminAuthController::class, 'verifyOtp'])->name('admin.verifyOtp');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
    });
});

// Hosts
Route::prefix('host')->group(function () {
    Route::get('/login', [HostAuthController::class, 'showLoginForm'])->name('host.login');
    Route::post('/send-otp', [HostAuthController::class, 'sendOtp'])->name('host.sendOtp');
    Route::post('/verify-otp', [HostAuthController::class, 'verifyOtp'])->name('host.verifyOtp');
    Route::post('/register', [HostAuthController::class, 'register'])->name('host.register');
    Route::get('/dashboard', [HostAuthController::class, 'dashboard'])->middleware('auth')->name('host.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::view('/user/dashboard', 'user.dashboard')->name('user.dashboard');
    Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('stays', StayController::class);
    Route::patch('stays/{stay}/toggle-status', [StayController::class, 'toggleStatus'])
    ->name('stays.toggleStatus');

    Route::delete('stay-images/{id}', [StayImageController::class, 'destroy'])
    ->name('stay-images.destroy');

    Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])
    ->name('stay-images.upload');

    Route::post('stay-images/{id}/set-main', [StayImageController::class, 'setMain'])
    ->name('stay-images.setMain');

    Route::post('stays/toggle-peak', [StayController::class, 'togglePeak'])
    ->name('stays.togglePeak');

    // مدیریت قراردادهای تخفیف
    Route::resource('discount-contracts', DiscountContractController::class);

    // مدیریت اعضای قرارداد
    Route::resource('discount-contract-members', DiscountContractMemberController::class)
        ->only(['store', 'destroy', 'edit', 'update']);

});
Route::get('/payment/test/{booking}', function ($bookingId) {
    $booking = \App\Models\Booking::findOrFail($bookingId);
    return view('test-payment', compact('booking'));
});


Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

