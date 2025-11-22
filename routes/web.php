<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StayController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\StayImageController;
use App\Http\Controllers\Admin\DiscountContractController;
use App\Http\Controllers\Admin\DiscountContractMemberController;
use App\Http\Controllers\Admin\HostController; // add
use App\Http\Controllers\GeoController;
use App\Http\Controllers\PeakPeriodController;

Route::get('/', fn() => view('home'))->name('home');

/*
| Authentication (shared)
*/
Route::prefix('login')->group(function () {
    Route::get('/{role}', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/{role}/send-otp', [AuthController::class, 'sendOtp'])->name('login.sendOtp');
    Route::post('/{role}/verify', [AuthController::class, 'verifyOtp'])->name('login.verify');
});
Route::post('/logout/{role}', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard/{role}', [AuthController::class, 'dashboard'])->name('dashboard');

/*
| Admin
*/
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'adminDashboard'])->name('dashboard');

    Route::resource('stays', StayController::class)->except(['show']);
    Route::patch('stays/{stay}/toggle-status', [StayController::class, 'toggleStatus'])->name('stays.toggleStatus');
    Route::post('stays/toggle-peak', [StayController::class, 'togglePeak'])->name('stays.togglePeak');
    Route::patch('stays/{stay}/approve', [StayController::class, 'approve'])->name('stays.approve');
    Route::patch('stays/{stay}/reject',  [StayController::class, 'reject'])->name('stays.reject');
    Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('stays.upload');
    Route::delete('images/{image}', [StayImageController::class, 'destroy'])->name('stays.image.destroy');

    Route::resource('discount_contracts', DiscountContractController::class);

    // Peak periods CRUD (only store & destroy needed for the inline form)
    Route::post('peak-periods', [PeakPeriodController::class,'store'])->name('peak_periods.store');
    Route::delete('peak-periods/{peakPeriod}', [PeakPeriodController::class,'destroy'])->name('peak_periods.destroy');
    // Nested member routes
    Route::prefix('discount_contracts/{discountContract}')->group(function () {
        Route::post('members', [DiscountContractMemberController::class, 'store'])->name('discount_contract_members.store');
        Route::delete('members/{discountContractMember}', [DiscountContractMemberController::class, 'destroy'])->name('discount_contract_members.destroy');
        Route::get('members/{discountContractMember}/edit', [DiscountContractMemberController::class, 'edit'])->name('discount_contract_members.edit');
        Route::put('members/{discountContractMember}', [DiscountContractMemberController::class, 'update'])->name('discount_contract_members.update');
    });

    Route::resource('hosts', HostController::class);
    Route::patch('hosts/{host}/approve', [HostController::class, 'approve'])->name('hosts.approve');
    Route::patch('hosts/{host}/reject',  [HostController::class, 'reject'])->name('hosts.reject');


});

/*
| Host
*/
Route::prefix('host')->name('host.')->middleware('auth:host')->group(function () {

    Route::get('/complete-profile', [AuthController::class,'showCompleteProfileForm'])->name('completeProfile.show');
    Route::post('/complete-profile', [AuthController::class,'storeCompleteProfile'])->name('completeProfile.store');

    Route::get('/dashboard', [AuthController::class, 'hostDashboard'])->name('dashboard');

    Route::resource('stays', StayController::class)->except(['show']);
    Route::post('stays/{stay}/upload-image', [StayImageController::class, 'upload'])->name('stays.upload');
    Route::delete('images/{image}', [StayImageController::class, 'destroy'])->name('stays.image.destroy');
});

/*
| User bookings
*/
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/payment/test/{booking}', function (\App\Models\Booking $booking) {
    return view('test-payment', compact('booking'));
})->name('payment.test');

/*
| Geo
*/
Route::prefix('geo')->group(function () {
    Route::get('/provinces', [GeoController::class, 'provinces'])->name('geo.provinces');
    Route::get('/provinces/{province}/cities', [GeoController::class, 'cities'])->name('geo.cities');
    Route::get('/provinces/{province}/cities/{city}/counties', [GeoController::class, 'counties'])->name('geo.counties');
    Route::get('/provinces/{province}/cities/{city}/counties/{county}/villages', [GeoController::class, 'villages'])->name('geo.villages');
});

/*
| Public stay show
*/
Route::get('stays/{stay}', [StayController::class, 'show'])->name('stays.show');

/*
| Static public pages
*/
Route::view('/support', 'pages.support')->name('support');
Route::post('/support', function(\Illuminate\Http\Request $request){
    $data = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email',
        'subject' => 'required|string|max:150',
        'message' => 'required|string|max:2000',
    ]);
    \Log::info('Support request', $data);
    return back()->with('status', 'درخواست شما ثبت شد. تیم پشتیبانی به زودی پاسخ خواهد داد.');
})->name('support.submit');
Route::view('/about', 'pages.about')->name('about');
Route::view('/terms', 'pages.terms')->name('terms');
