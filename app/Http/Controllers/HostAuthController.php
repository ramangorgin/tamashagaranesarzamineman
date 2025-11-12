<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Host;
use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Melipayamak;

class HostAuthController extends Controller
{
    // فرم ورود / ثبت‌نام میزبان
    public function showLoginForm()
    {
        return view('host.auth.login');
    }

    // ارسال کد OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09\d{9}$/'
        ]);

        $code = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(3);

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        try {
            $sms = Melipayamak::sms();
            $sms->send($request->phone, '5000...', "کد ورود میزبان: {$code}");
        } catch (Exception $e) {
            return back()->with('error', 'خطا در ارسال پیامک');
        }

        return back()->with('success', 'کد با موفقیت ارسال شد.');
    }

    // بررسی کد OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'code' => 'required',
        ]);

        $otp = Otp::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return back()->with('error', 'کد وارد شده اشتباه یا منقضی شده است.');
        }

        $host = Host::where('phone', $request->phone)->first();

        if (!$host) {
            // اگر میزبان جدید بود، فقط شماره رو ذخیره می‌کنیم تا بعداً فرم تکمیل اطلاعات رو پر کنه
            $host = Host::create([
                'phone' => $request->phone,
                'status' => 'pending'
            ]);
        }

        Auth::guard('host')->login($host, true);
        session()->regenerate();

        // اگر میزبان اطلاعاتش ناقصه (مثلاً نام یا کدملی نداره)
        if (!$host->name || !$host->national_id) {
            return redirect()->route('host.completeProfile');
        }

        return redirect()->route('host.dashboard')->with('success', 'ورود با موفقیت انجام شد!');
    }

    // نمایش فرم تکمیل اطلاعات (ویزارد)
    public function showCompleteProfileForm()
    {
        return view('host.auth.complete-profile');
    }

    // ثبت اطلاعات میزبان
    public function completeProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => 'required|digits:10|unique:hosts,national_id,' . Auth::guard('host')->id(),
            'email' => 'nullable|email',
            'province' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string|max:500',
            'postal_code' => 'nullable|digits:10',
        ]);

        $host = Auth::guard('host')->user();
        $host->update($request->only([
            'name',
            'national_id',
            'email',
            'province',
            'city',
            'address',
            'postal_code',
        ]));

        return redirect()->route('host.dashboard')->with('success', 'پروفایل با موفقیت تکمیل شد!');
    }

    // داشبورد میزبان
    public function dashboard()
    {
        $host = Auth::guard('host')->user();
        return view('host.dashboard', compact('host'));
    }
}
