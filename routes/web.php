<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/verify', function () {
    return view('auth.verify');
})->name('verify');

Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');

Route::middleware('auth')->group(function () {
    Route::view('/user/dashboard', 'user.dashboard')->name('user.dashboard');
    Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('stays', \App\Http\Controllers\Admin\StayController::class);
    Route::delete('stay-images/{id}', [\App\Http\Controllers\Admin\StayImageController::class, 'destroy'])
    ->name('stay-images.destroy');
    Route::patch('stays/{stay}/toggle-status', [\App\Http\Controllers\Admin\StayController::class, 'toggleStatus'])
    ->name('stays.toggleStatus');

});
