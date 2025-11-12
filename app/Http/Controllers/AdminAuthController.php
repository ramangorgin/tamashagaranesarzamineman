<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Melipayamak;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|regex:/^09\d{9}$/']);
        $code = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(2);

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        try {
            $sms = Melipayamak::sms();
            $sms->send($request->phone, '5000...', "کد ورود مدیر: {$code}");
        } catch (Exception $e) {
            return back()->with('error', 'ارسال پیامک با خطا مواجه شد.');
        }

        return back()->with('success', 'کد با موفقیت ارسال شد.');
    }

    public function verifyOtp(Request $request)
    {
        \Log::info('verifyOtp called', $request->all());

        $request->validate(['phone' => 'required', 'code' => 'required']);

        $otp = Otp::where('phone', $request->phone)
                ->where('code', $request->code)
                ->where('expires_at', '>', now())
                ->first();

        if (!$otp) {
            \Log::warning('OTP failed', ['phone' => $request->phone, 'code' => $request->code]);
            return back()->with('error', 'کد معتبر نیست یا منقضی شده است.');
        }

        $admin = Admin::where('phone', $request->phone)->first();
        if (!$admin) {
            \Log::warning('Admin not found', ['phone' => $request->phone]);
            return back()->with('error', 'این شماره برای مدیر تعریف نشده است.');
        }

        \Log::info('Admin found, logging in...', ['id' => $admin->id]);

        Auth::guard('admin')->login($admin, false);

        $request->session()->regenerate();

        \Log::info('Redirecting to dashboard');

        return redirect()->route('admin.dashboard')->with('success', 'ورود با موفقیت انجام شد!');
    }



    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
