<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\SmsIrService;

class OtpController extends Controller
{
    // ارسال کد
    public function send(Request $request)
    {
        $request->validate(['phone' => 'required|digits:11']);

        $code = rand(100000, 999999);

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['code' => $code, 'expires_at' => now()->addMinutes(2)]
        );

        try {
            /** @var SmsIrService $sms */
            $sms = app(SmsIrService::class);
            $result = $sms->ultraFastSend(['CODE' => (string)$code], 857262, $request->phone);
            if (!($result['IsSuccessful'] ?? false)) {
                return response()->json(['error' => 'ارسال پیامک ناموفق بود.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'ارسال پیامک با خطا مواجه شد.'], 500);
        }

        return response()->json(['success' => true]);
    }

    // بررسی کد و ورود یا ثبت‌نام
    public function verify(Request $request)
    {
        $request->validate(['phone' => 'required|digits:11', 'code' => 'required|digits:6']);

        $otp = Otp::where('phone', $request->phone)
                  ->where('code', $request->code)
                  ->where('expires_at', '>', now())
                  ->first();

        if (!$otp) {
            return response()->json(['error' => 'کد واردشده معتبر نیست یا منقضی شده است.'], 400);
        }

        // ثبت‌نام یا ورود خودکار
        $user = User::firstOrCreate(['phone' => $request->phone], [
            'role' => $request->get('role', 'user'),
            'phone_verified_at' => now(),
        ]);

        auth()->login($user);

        return response()->json(['success' => true, 'redirect' => $this->redirectByRole($user)]);
    }

    private function redirectByRole($user)
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'host' => route('host.dashboard'),
            'user' => route('user.dashboard'),
            default => route('home'),
        };
    }
}
