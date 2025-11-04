<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\StayController;
use App\Http\Controllers\StayImageController;


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

});


