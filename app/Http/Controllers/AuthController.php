<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Host;
use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Melipayamak;

class AuthController extends Controller
{
    /**
     * نمایش فرم ورود بر اساس نقش
     * مسیر: /login/{role}
     */
    public function showLoginForm($role)
    {
        if (!in_array($role, ['admin', 'host', 'user'])) {
            abort(404);
        }
        return view("auth.{$role}.login", compact('role'));
    }

    /**
     * ارسال کد OTP برای نقش مشخص
     */
    public function sendOtp(Request $request, $role)
    {
        $request->validate(['phone' => 'required|regex:/^09\d{9}$/']);

        if (!in_array($role, ['admin', 'host', 'user'])) {
            return back()->with('error', 'نقش نامعتبر است.');
        }

        $code = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(3);

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        try {
            $sms = Melipayamak::sms();
            $sms->send($request->phone, '5000...', "کد ورود {$role}: {$code}");
        } catch (Exception $e) {
            Log::error('SMS failed', ['role' => $role, 'error' => $e->getMessage()]);
            return back()->with('error', 'ارسال پیامک با خطا مواجه شد.');
        }

        return back()->with('success', 'کد با موفقیت ارسال شد.');
    }

    /**
     * بررسی و اعتبارسنجی OTP
     */
    public function verifyOtp(Request $request, $role)
    {
        $request->validate([
            'phone' => 'required',
            'code' => 'required',
        ]);

        if (!in_array($role, ['admin', 'host', 'user'])) {
            abort(404);
        }

        $otp = Otp::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return back()->with('error', 'کد وارد شده اشتباه یا منقضی شده است.');
        }

        switch ($role) {
            case 'admin':
                $user = Admin::where('phone', $request->phone)->first();
                if (!$user) return back()->with('error', 'شماره وارد شده در سیستم مدیر تعریف نشده است.');
                $guard = 'admin';
                break;

            case 'host':
                $user = Host::firstOrCreate(['phone' => $request->phone], ['status' => 'pending']);
                $guard = 'host';
                break;

            case 'user':
                $user = User::firstOrCreate(['phone' => $request->phone]);
                $guard = 'web';
                break;
        }

        Auth::guard($guard)->login($user, true);
        session()->regenerate();

        // Redirect to complete profile if host is pending OR missing required fields
        if ($role === 'host' && (
            ($user->status ?? 'pending') === 'pending' ||
            empty($user->name) || empty($user->national_id)
        )) {
            return redirect()->route('host.completeProfile.show');
        }

        $dashboardRoutes = [
            'admin' => 'admin.dashboard',
            'host'  => 'host.dashboard',
            'user'  => 'home'
        ];

        return redirect()->route($dashboardRoutes[$role])
            ->with('success', 'ورود با موفقیت انجام شد!');
    }

    /**
     * خروج از سیستم
     */
    public function logout(Request $request, $role)
    {
        if (!in_array($role, ['admin', 'host', 'user'])) {
            abort(404);
        }

        Auth::guard($role === 'user' ? 'web' : $role)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/login/{$role}")->with('success', 'با موفقیت خارج شدید.');
    }

    /**
     * داشبورد بر اساس نقش
     */
    public function dashboard($role)
    {
        switch ($role) {
            case 'admin':
                return view('admin.dashboard');
            case 'host':
                $host = Auth::guard('host')->user();
                return view('host.dashboard', compact('host'));
            case 'user':
                return view('home');
            default:
                abort(404);
        }
    }

    /**
     * داشبورد اختصاصی هر نقش
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function hostDashboard()
    {
        $host = Auth::guard('host')->user();
        return view('host.dashboard', compact('host'));
    }

    public function userDashboard()
    {
        return view('home');
    }

    public function showCompleteProfileForm()
    {
        $host = Auth::guard('host')->user();
        if ($host->status === 'pending_review') {
            $host->status = 'pending'; // normalize
        }
        // FIX: correct blade path
        return view('host.complete-profile', compact('host'));
    }

    public function storeCompleteProfile(Request $request)
    {
        $host = Auth::guard('host')->user();

        if (in_array($host->status, ['pending','approved'])) {
            // FIX: redirect to show route, not store
            return redirect()->route('host.completeProfile.show')
                ->with('info','اطلاعات شما قبلا ارسال شده و در حال بررسی است.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'national_id' => 'required|string|max:20',
            'email' => 'nullable|email|max:120',
            'id_card_image' => 'required|image|max:2048',
            'selfie_image' => 'required|image|max:3072',
            'business_license' => 'nullable|file|max:4096',
            'province_id' => 'required|string',
            'province_name' => 'required|string',
            'city_id' => 'required|string',
            'city_name' => 'required|string',
            'county_id' => 'required|string',
            'county_name' => 'required|string',
            'village_name' => 'nullable|string|max:120',
            'address' => 'required|string|max:400',
            'iban' => 'required|string|regex:/^[0-9]{24}$/',
            'bank_name' => 'required|string|max:80',
            'account_holder' => 'required|string|max:100',
        ]);

        $disk = 'public';
        foreach (['id_card_image','selfie_image','business_license'] as $f) {
            if ($request->hasFile($f)) {
                $validated[$f] = $request->file($f)->store("hosts/{$host->id}", $disk);
            }
        }

        $host->fill($validated);
        $host->status = 'pending';
        $host->save();

        // FIX: redirect to show route name
        return redirect()
            ->route('host.completeProfile.show')
            ->with('success','اطلاعات ارسال شد. لطفاً تا تأیید مدیریت منتظر بمانید.');
    }
}
