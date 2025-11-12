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

Route::middleware(['web'])->group(function () {

    Route::get('/', function () {
        return view('home');
    });

    // 🔹 User
    Route::post('/send-otp', [UserAuthController::class, 'sendOtp'])->name('user.sendOtp');
    Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp'])->name('user.verifyOtp');

    // 🔹 Admin
    Route::prefix('admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/send-otp', [AdminAuthController::class, 'sendOtp'])->name('admin.sendOtp');
        Route::post('/verify-otp', [AdminAuthController::class, 'verifyOtp'])->name('admin.verifyOtp');
        Route::post('/logout', function () {
            Auth::guard('admin')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('admin.login')->with('success', 'با موفقیت خارج شدید.');
        })->name('logout');

        Route::middleware(['auth:admin'])->group(function () {
            Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

            // مدیریت اقامتگاه‌ها
            Route::resource('stays', StayController::class);
            Route::patch('stays/{stay}/toggle-status', [StayController::class, 'toggleStatus'])->name('stays.toggleStatus');
            Route::delete('stay-images/{id}', [StayImageController::class, 'destroy'])->name('stay-images.destroy');
            Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('stay-images.upload');
            Route::post('stay-images/{id}/set-main', [StayImageController::class, 'setMain'])->name('stay-images.setMain');
            Route::post('stays/toggle-peak', [StayController::class, 'togglePeak'])->name('stays.togglePeak');

            // قراردادهای تخفیف
            Route::resource('discount-contracts', DiscountContractController::class);
            Route::resource('discount-contract-members', DiscountContractMemberController::class)
                ->only(['store', 'destroy', 'edit', 'update']);
        });
    });

    // 🔹 Host
    Route::prefix('host')->group(function () {
        Route::get('/login', [HostAuthController::class, 'showLoginForm'])->name('host.login');
        Route::post('/send-otp', [HostAuthController::class, 'sendOtp'])->name('host.sendOtp');
        Route::post('/verify-otp', [HostAuthController::class, 'verifyOtp'])->name('host.verifyOtp');

        Route::middleware('auth:host')->group(function () {
            Route::get('/complete-profile', [HostAuthController::class, 'showCompleteProfileForm'])->name('host.completeProfile');
            Route::post('/complete-profile', [HostAuthController::class, 'completeProfile'])->name('host.saveProfile');
            Route::get('/dashboard', [HostAuthController::class, 'dashboard'])->name('host.dashboard');
            Route::resource('stays', HostStayController::class)->names('host.stays');
        });
    });

    Route::prefix('host/stays')->middleware('auth:host')->group(function () {
        Route::post('{stay}/upload-image', [App\Http\Controllers\Host\StayImageController::class, 'upload'])->name('host.stays.upload');
        Route::delete('images/{image}', [App\Http\Controllers\Host\StayImageController::class, 'destroy'])->name('host.stays.image.destroy');

        Route::post('{stay}/update-facilities', [App\Http\Controllers\Host\StayFacilityController::class, 'update'])->name('host.stays.facilities.update');
        Route::post('{stay}/update-rules', [App\Http\Controllers\Host\StayRuleController::class, 'update'])->name('host.stays.rules.update');
    });

    Route::prefix('admin/stays')->middleware('auth:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StayController::class, 'index'])->name('admin.stays.index');
        Route::get('/create', [App\Http\Controllers\Admin\StayController::class, 'create'])->name('admin.stays.create');
        Route::post('/', [App\Http\Controllers\Admin\StayController::class, 'store'])->name('admin.stays.store');
        Route::get('/{stay}/edit', [App\Http\Controllers\Admin\StayController::class, 'edit'])->name('admin.stays.edit');
        Route::put('/{stay}', [App\Http\Controllers\Admin\StayController::class, 'update'])->name('admin.stays.update');
        Route::delete('/{stay}', [App\Http\Controllers\Admin\StayController::class, 'destroy'])->name('admin.stays.destroy');

        // AJAX
        Route::post('{stay}/upload-image', [App\Http\Controllers\Admin\StayImageController::class, 'upload'])->name('admin.stays.upload');
        Route::delete('images/{image}', [App\Http\Controllers\Admin\StayImageController::class, 'destroy'])->name('admin.stays.image.destroy');
        Route::post('{stay}/update-facilities', [App\Http\Controllers\Admin\StayFacilityController::class, 'update'])->name('admin.stays.facilities.update');
        Route::post('{stay}/update-rules', [App\Http\Controllers\Admin\StayRuleController::class, 'update'])->name('admin.stays.rules.update');
    });



    // 🔹 Payment test
    Route::get('/payment/test/{booking}', function ($bookingId) {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        return view('test-payment', compact('booking'));
    });

    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});
