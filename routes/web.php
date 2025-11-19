<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StayController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\StayImageController;
use App\Http\Controllers\Admin\DiscountContractController;
use App\Http\Controllers\Admin\DiscountContractMemberController;
use App\Http\Controllers\GeoController;

Route::middleware(['web'])->group(function () {

    // Home page
    Route::get('/', fn() => view('home'))->name('home');

    /*
    |--------------------------------------------------------------------------
    | General Authentication (Admin, Host, User)
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
    | Routes for Admin
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {

        // Admin's dashboard
        Route::get('/dashboard', [AuthController::class, 'adminDashboard'])->name('dashboard');

        // Stays
        Route::resource('stays', StayController::class)->except(['show']);
        Route::patch('stays/{stay}/toggle-status', [StayController::class, 'toggleStatus'])->name('stays.toggleStatus');
        Route::post('stays/toggle-peak', [StayController::class, 'togglePeak'])->name('stays.togglePeak');

        // Managing stay images
        Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('stays.upload');
        Route::delete('images/{image}', [StayImageController::class, 'destroy'])->name('stays.image.destroy');

        // Managing contracts and discounts
        Route::resource('discount-contracts', DiscountContractController::class);
        Route::resource('discount-contract-members', DiscountContractMemberController::class)
            ->only(['store', 'destroy', 'edit', 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Routes for Host
    |--------------------------------------------------------------------------
    */
    Route::prefix('host')->name('host.')->middleware('auth:host')->group(function () {

        // completing the profile of host (if not completed)
        Route::get('/complete-profile', [AuthController::class, 'showCompleteProfileForm'])->name('completeProfile');
        Route::post('/complete-profile', [AuthController::class, 'storeCompleteProfile'])->name('completeProfile.store');

        // Host's dashboard
        Route::get('/dashboard', [AuthController::class, 'hostDashboard'])->name('dashboard');

        // Stays
        Route::resource('stays', StayController::class)->except(['show']);
        Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('stays.upload');
        Route::delete('images/{image}', [StayImageController::class, 'destroy'])->name('stays.image.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes for Regular User (Booking and Payment)
    |--------------------------------------------------------------------------
    */
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // Test Payment
    Route::get('/payment/test/{booking}', function ($bookingId) {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        return view('test-payment', compact('booking'));
    })->name('payment.test');

    Route::prefix('geo')->group(function () {
        Route::get('/provinces', [GeoController::class, 'provinces'])->name('geo.provinces');
        Route::get('/provinces/{province}/cities', [GeoController::class, 'cities'])->name('geo.cities');
        Route::get('/provinces/{province}/cities/{city}/counties', [GeoController::class, 'counties'])->name('geo.counties');
        Route::get('/provinces/{province}/cities/{city}/counties/{county}/villages', [GeoController::class, 'villages'])->name('geo.villages');
    });

    // Public (or shared) Stay show page
    Route::get('stays/{stay}', [StayController::class, 'show'])->name('stays.show');
});
