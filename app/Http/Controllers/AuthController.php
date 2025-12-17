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
        try {
            $request->validate(['phone' => 'required|regex:/^09\d{9}$/']);

            if (!in_array($role, ['admin', 'host', 'user'])) {
                $errorMsg = 'نقش نامعتبر است.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => $errorMsg], 400);
                }
                return back()->with('error', $errorMsg);
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

            $env = app()->environment();
            // Expanded local detection to ensure it works on local machines
            $isLocal = in_array($env, ['local', 'development']) 
                || config('app.debug', false) 
                || $request->ip() === '127.0.0.1' 
                || $request->ip() === '::1'
                || str_contains($request->getHost(), 'localhost');

            // لاگ قبل از ارسال
            Log::info('OTP generating', [
                'phone' => $mobile,
                'role' => $role,
                'templateId' => $templateId,
                'code' => $code,
                'expires_at' => $expiresAt->toDateTimeString(),
                'environment' => $env,
                'is_local' => $isLocal,
                'host' => $request->getHost()
            ]);

            // در محیط local: فقط لاگ می‌کنیم و پیامک ارسال نمی‌شود
            // یا اگر ارسال پیامک با خطا مواجه شد ولی محیط دیباگ بود
            if ($isLocal) {
                Log::info('OTP Code (Local Environment - SMS not sent)', [
                    'phone' => $mobile,
                    'role' => $role,
                    'code' => $code,
                    'message' => 'کد OTP در محیط local فقط در لاگ ثبت می‌شود و پیامک ارسال نمی‌شود.'
                ]);
                
                $successMsg = 'کد تایید با موفقیت تولید شد. کد: ' . $code . ' (حالت Local)';
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $successMsg,
                        'code' => $code
                    ]);
                }
                return back()->with('success', $successMsg);
            }

            // در محیط production: ارسال پیامک از طریق Melipayamak
            $url = 'https://console.melipayamak.com/api/send/shared/e9741f18ee7e494792c4b49f6c7572e9';
            $data = array('bodyId' => 386622, 'to' => $mobile, 'args' => [(string)$code]);
            $data_string = json_encode($data);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ]);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // بررسی خطای curl
            if ($curlError) {
                Log::error('Melipayamak curl error', [
                    'phone' => $mobile,
                    'code' => $code,
                    'error' => $curlError
                ]);
                $errorMsg = 'خطا در ارتباط با سرویس پیامک. لطفاً دوباره تلاش کنید.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => $errorMsg], 500);
                }
                return back()->with('error', $errorMsg);
            }

            // بررسی پاسخ HTTP
            if ($httpCode !== 200) {
                Log::error('Melipayamak HTTP error', [
                    'phone' => $mobile,
                    'code' => $code,
                    'http_code' => $httpCode,
                    'response' => $result
                ]);
                $errorMsg = 'خطا در ارسال پیامک. لطفاً دوباره تلاش کنید.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => $errorMsg], $httpCode);
                }
                return back()->with('error', $errorMsg);
            }

            // بررسی پاسخ JSON
            $response = json_decode($result, true);
            Log::info('Melipayamak response', [
                'phone' => $mobile,
                'code' => $code,
                'http_code' => $httpCode,
                'response' => $response
            ]);

            // بررسی موفقیت ارسال (بسته به فرمت پاسخ Melipayamak ممکن است نیاز به تنظیم باشد)
            $successMsg = 'کد تایید ارسال شد.';
            if (isset($response['status']) && $response['status'] === 'OK') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $successMsg]);
                }
                return back()->with('success', $successMsg);
            } elseif (isset($response['strRetStatus']) && $response['strRetStatus'] === 'Ok') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $successMsg]);
                }
                return back()->with('success', $successMsg);
            } elseif ($httpCode === 200 && !isset($response['error'])) {
                // اگر HTTP 200 است و خطایی در پاسخ نیست، احتمالاً موفق بوده
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'message' => $successMsg]);
                }
                return back()->with('success', $successMsg);
            } else {
                Log::warning('Melipayamak unexpected response', [
                    'phone' => $mobile,
                    'code' => $code,
                    'response' => $response
                ]);
                $errorMsg = 'خطا در ارسال پیامک. لطفاً دوباره تلاش کنید.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => $errorMsg], 500);
                }
                return back()->with('error', $errorMsg);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'شماره تلفن وارد شده معتبر نیست.',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Unexpected exception in sendOtp', [
                'phone' => $request->phone ?? 'unknown',
                'role' => $role,
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 600)
            ]);
            $errorMsg = 'خطای غیرمنتظره در ارسال کد.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $errorMsg], 500);
            }
            return back()->with('error', $errorMsg);
        }
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
                // Create host record if not exists. New host has status 'pending' but without required fields -> treated as incomplete.
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

        // Redirect host to complete profile ONLY if status pending AND essential fields are missing
        if ($role === 'host' && $user->status === 'pending' && (empty($user->name) || empty($user->national_id))) {
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

        // Block re-submission only if already submitted (status pending or approved) AND has essential fields
        if (in_array($host->status, ['pending','approved']) && $host->name && $host->national_id) {
            return redirect()->route('host.completeProfile.show')
                ->with('info','اطلاعات شما قبلا ارسال شده و در حال بررسی است.');
        }

        Log::info('Host profile submission started', ['host_id' => $host?->id, 'status' => $host?->status]);

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

        Log::info('Host profile validated', ['host_id' => $host->id, 'fields' => array_keys($validated)]);

        try {
            $disk = 'public';
            foreach (['id_card_image','selfie_image','business_license'] as $f) {
                if ($request->hasFile($f)) {
                    $stored = $request->file($f)->store("hosts/{$host->id}", $disk);
                    $validated[$f] = $stored;
                    Log::info('Stored file', ['field'=>$f,'path'=>$stored]);
                }
            }

            // Explicit assignment to avoid silent ignoring
            $host->name = $validated['name'];
            $host->national_id = $validated['national_id'];
            $host->email = $validated['email'] ?? null;
            $host->id_card_image = $validated['id_card_image'] ?? $host->id_card_image;
            $host->selfie_image = $validated['selfie_image'] ?? $host->selfie_image;
            $host->business_license = $validated['business_license'] ?? $host->business_license;
            $host->province_id = $validated['province_id'];
            $host->province_name = $validated['province_name'];
            $host->city_id = $validated['city_id'];
            $host->city_name = $validated['city_name'];
            $host->county_id = $validated['county_id'];
            $host->county_name = $validated['county_name'];
            $host->village_name = $validated['village_name'] ?? null;
            $host->address = $validated['address'];
            $host->iban = $validated['iban'];
            $host->bank_name = $validated['bank_name'];
            $host->account_holder = $validated['account_holder'];
            $host->status = 'pending';

            $host->save();
            Log::info('Host profile saved', ['host_id'=>$host->id]);
        } catch (\Throwable $e) {
            Log::error('Host profile save failed', [
                'host_id' => $host->id,
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(),0,600)
            ]);
            return back()->with('error','خطای ذخیره اطلاعات. لطفاً دوباره تلاش کنید.');
        }

        return redirect()->route('host.completeProfile.show')
            ->with('success','اطلاعات ارسال شد. لطفاً تا تأیید مدیریت منتظر بمانید.');
    }
}
