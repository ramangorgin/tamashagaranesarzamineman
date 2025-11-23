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
 
// use Ipe\Sdk\Facades\SmsIr; // Temporarily disabled for Melipayamak testing


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

        $mobile = $request->phone;
        $templateId = 857262; 
        $parameters = [
            [
                "name" => "Code",
                "value" => (string)$code
            ]
        ];

        // لاگ قبل از ارسال
        Log::info('OTP generating', [
            'phone' => $mobile,
            'role' => $role,
            'templateId' => $templateId,
            'code' => $code,
            'expires_at' => $expiresAt->toDateTimeString()
        ]);

        /* Original Sms.ir sending block kept for future use
        try {
            $response = SmsIr::verifySend($mobile, $templateId, $parameters);
            Log::info('OTP sms.ir response', [
                'phone' => $mobile,
                'status' => $response->status ?? null,
                'message' => $response->message ?? null,
                'data' => $response->data ?? null,
            ]);
            if (empty($response->status) || !in_array($response->status, [true, 1, 'Success', 'OK'])) {
                return back()->with('error', 'ارسال کد تایید ناموفق بود. لطفاً دوباره تلاش کنید.');
            }
            return back()->with('success', 'کد تایید ارسال شد.');
        } catch (\Ipe\Sdk\Exceptions\SmsException $e) {
            Log::error('sms.ir SmsException while sending OTP', [
                'phone' => $mobile,
                'code' => $code,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
            return back()->with('error', 'خطای سرویس پیامک: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Unexpected exception sending OTP', [
                'phone' => $mobile,
                'code' => $code,
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'خطای غیرمنتظره در ارسال پیامک.');
        }
        */

       // Melipayamak Console

        $url = 'https://console.melipayamak.com/api/send/shared/e9741f18ee7e494792c4b49f6c7572e9';
        $data = array('bodyId' => 386622, 'to' => $mobile, 'args' => [(string)$code]);
        $data_string = json_encode($data);
        $ch = curl_init($url);                          
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                      
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        curl_close($ch);

        return back()->with('error', 'خطا در سرویس پیامک. لطفا با پشتیبانی تماس بگیرید.');
    
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

    // اختصاصی برای سادگی استفاده در لینک‌ها بدون پارامتر
    public function logoutAdmin(Request $request){
        return $this->performGuardLogout($request,'admin');
    }
    public function logoutHost(Request $request){
        return $this->performGuardLogout($request,'host');
    }
    public function logoutUser(Request $request){
        return $this->performGuardLogout($request,'user');
    }
    protected function performGuardLogout(Request $request,string $role){
        Auth::guard($role==='user'?'web':$role)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.form',$role)->with('success','خروج انجام شد');
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
