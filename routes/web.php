<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StayController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\StayImageController;
use App\Http\Controllers\Admin\DiscountContractController;
use App\Http\Controllers\Admin\DiscountContractMemberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->group(function () {

    // صفحه اصلی
    Route::get('/', fn() => view('home'))->name('home');

    /*
    |--------------------------------------------------------------------------
    | 🔹 احراز هویت عمومی (ادمین، میزبان، کاربر)
    |--------------------------------------------------------------------------
    */
    Route::prefix('login')->group(function () {
        Route::get('/{role}', [AuthController::class, 'showLoginForm'])->name('login.form');
        Route::post('/{role}/send-otp', [AuthController::class, 'sendOtp'])->name('login.sendOtp');
        Route::post('/{role}/verify', [AuthController::class, 'verifyOtp'])->name('login.verify');
    });

    Route::post('/logout/{role}', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard/{role}', [AuthController::class, 'dashboard'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | 🔹 مسیرهای مربوط به ادمین
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('auth:admin')->group(function () {

        // داشبورد ادمین
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');

        // اقامتگاه‌ها
        Route::resource('stays', StayController::class)->names('admin.stays');
        Route::patch('stays/{stay}/toggle-status', [StayController::class, 'toggleStatus'])->name('admin.stays.toggleStatus');
        Route::post('stays/toggle-peak', [StayController::class, 'togglePeak'])->name('admin.stays.togglePeak');

        // مدیریت تصاویر اقامت‌گاه
        Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('admin.stays.upload');
        Route::delete('images/{image}', [StayImageController::class, 'destroy'])->name('admin.stays.image.destroy');

        // مدیریت قراردادها و تخفیف‌ها
        Route::resource('discount-contracts', DiscountContractController::class);
        Route::resource('discount-contract-members', DiscountContractMemberController::class)
            ->only(['store', 'destroy', 'edit', 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🔹 مسیرهای مربوط به میزبان
    |--------------------------------------------------------------------------
    */
    Route::prefix('host')->middleware('auth:host')->group(function () {

        // تکمیل پروفایل میزبان (درصورت ناقص بودن)
        Route::get('/complete-profile', [AuthController::class, 'showCompleteProfileForm'])->name('host.completeProfile');
        Route::post('/complete-profile', [AuthController::class, 'completeProfile'])->name('host.saveProfile');

        // داشبورد میزبان
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('host.dashboard');

        // اقامتگاه‌ها
        Route::resource('stays', StayController::class)->names('host.stays');
        Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('host.stays.upload');
        Route::delete('images/{image}', [StayImageController::class, 'destroy'])->name('host.stays.image.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 🔹 مسیرهای مربوط به کاربر عادی (رزرو و پرداخت)
    |--------------------------------------------------------------------------
    */
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // پرداخت آزمایشی
    Route::get('/payment/test/{booking}', function ($bookingId) {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        return view('test-payment', compact('booking'));
    })->name('payment.test');
});
