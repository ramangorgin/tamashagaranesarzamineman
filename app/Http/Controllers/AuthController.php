<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    /**
     * STEP 1: sending the OTP to user's phone
     */
    public function sendOtp(Request $request)
    {
        // validating the phone number
        $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/']
        ], [
            'phone.required' => 'شماره تلفن الزامی است.',
            'phone.regex' => 'شماره تلفن معتبر نیست.',
        ]);

        // generating 6-digits code
        $code = rand(100000, 999999);

        // saving OTP in table (update if exists)
        Otp::updateOrCreate(
            ['phone' => $request->phone],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        Log::info("OTP for {$request->phone} is {$code}");

        /* 
        *   MeliPayamak (Shared Template API)

        //-------------------------------------------------------------------------
        try {
            $url = 'https://console.melipayamak.com/api/send/shared/e9741f18ee7e494792c4b49f6c7572e9';

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'bodyId' => 386622,              
                    'to' => $request->phone,      
                    'args' => [$code],        
                ]);

            Log::info('پاسخ ملی پیامک', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error('خطا در ارسال پیامک: ' . $e->getMessage());
        }
        //-------------------------------------------------------------------------

        */
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'کد تأیید ارسال شد.',
                'phone' => $request->phone
            ]);
        } else {
            return redirect()->route('verify')->with('phone', $request->phone);
        }

    }

    /**
     * STEP 2: check & validate code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/',
            'code' => 'required|digits:6'
        ], [
            'phone.required' => 'شماره تلفن الزامی است.',
            'code.required' => 'کد تأیید الزامی است.',
        ]);


        // validating
        $otp = Otp::verifyCode($request->phone, $request->code);


        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'کد تأیید نادرست یا منقضی شده است.'
            ], 400);
        }

        // if user does not exist, create it!
        $user = User::firstOrCreate(['phone' => $request->phone]);

        // Loging-in the User
        Auth::login($user);

        // Deleting OTP (for safty)
        $otp->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ورود با موفقیت انجام شد.',
                'user' => $user,
            ]);
        } else {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }
        
    }

    /**
     * STEP 3: Loging-out
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('message', 'با موفقیت خارج شدید.');
    }
}
