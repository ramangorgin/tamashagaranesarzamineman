<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Stay;
use App\Models\User;
use App\Models\DiscountContractMember;
use App\Models\PeakPeriod;
use App\Models\Otp;
use Ipe\Sdk\Facades\SmsIr;

class BookingController extends Controller
{
    /**
     * ارسال کد تایید (OTP) برای رزرو – فقط نقش user
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|regex:/^09\d{9}$/']);
        $phone = $request->phone;
        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(3);
        Otp::updateOrCreate(['phone' => $phone], ['code' => $code, 'expires_at' => $expiresAt]);

        try {
            $templateId = 857262; // نمونه
            $parameters = [["name" => "Code", "value" => (string)$code]];
            $resp = SmsIr::verifySend($phone, $templateId, $parameters);
            if (empty($resp->status) || !in_array($resp->status, [true, 1, 'Success', 'OK'])) {
                return response()->json(['success' => false, 'message' => 'ارسال کد ناموفق بود.'], 500);
            }
            return response()->json(['success' => true, 'message' => 'کد ارسال شد.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'خطا در ارسال کد.'], 500);
        }
    }

    /**
     * تایید کد OTP وارد شده
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09\d{9}$/',
            'code'  => 'required|digits:6',
        ]);
        $otp = Otp::where('phone', $request->phone)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();
        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'کد اشتباه یا منقضی شده است.'], 422);
        }
        return response()->json(['success' => true, 'message' => 'شماره تایید شد.']);
    }

    /**
     * محاسبه قیمت و تخفیف‌ها قبل از ثبت رزرو (Preview)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'national_id'  => 'required|string|min:8',
            'phone'        => 'required|string|regex:/^09\d{9}$/',
            'stay_id'      => 'required|integer|exists:stays,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'base_guests'  => 'required|integer|min:1',
            'extra_guests' => 'required|integer|min:0',
        ]);

        $stay = Stay::findOrFail($request->stay_id);
        if ($request->base_guests > (int)$stay->base_capacity) {
            return response()->json(['success' => false, 'message' => 'مهمان پایه بیش از ظرفیت است.'], 422);
        }
        if ($request->extra_guests > (int)$stay->extra_capacity) {
            return response()->json(['success' => false, 'message' => 'مهمان اضافه بیش از ظرفیت است.'], 422);
        }
        $start  = Carbon::parse($request->start_date);
        $end    = Carbon::parse($request->end_date);
        $nights = $end->diffInDays($start);
        if ($nights < 1) {
            return response()->json(['success' => false, 'message' => 'حداقل یک شب لازم است.'], 422);
        }
        $base_price_per_night  = (int)$stay->price_per_person;
        $extra_price_per_night = (int)($stay->extra_person_price ?? 0);
        $base_price = $base_price_per_night * (int)$request->base_guests * $nights;
        $extra_cost = $extra_price_per_night * (int)$request->extra_guests * $nights;
        $isPeakNow = method_exists(PeakPeriod::class, 'isNowPeak') ? PeakPeriod::isNowPeak() : (bool)$stay->is_peak;
        $stay_discount_percent = (float)($isPeakNow ? $stay->max_discount_peak : $stay->max_discount_normal);
        $subtotal = $base_price + $extra_cost;
        $stay_discount_amount = $subtotal * ($stay_discount_percent / 100);

        $full_name = trim($request->first_name . ' ' . $request->last_name);
        $contract = DiscountContractMember::with('contract')
            ->where(function ($q) use ($full_name, $request) {
                $q->where('full_name', $full_name)
                  ->orWhere('phone', $request->phone)
                  ->orWhere('national_id', $request->national_id);
            })
            ->whereHas('contract', function ($q) {
                $q->where('is_active', true)
                  ->whereDate('start_date', '<=', now(config('app.timezone')))
                  ->whereDate('end_date', '>=', now(config('app.timezone')));
            })
            ->first()
            ?->contract;
        $org_discount_percent = (float)($contract->discount_percent ?? 0);
        $org_discount_amount = max(0, $subtotal - $stay_discount_amount) * ($org_discount_percent / 100);
        $final_price = (int)round($subtotal - ($stay_discount_amount + $org_discount_amount));

        return response()->json([
            'success' => true,
            'nights' => $nights,
            'base_price' => $base_price,
            'extra_cost' => $extra_cost,
            'stay_discount_percent' => $stay_discount_percent,
            'stay_discount_amount' => (int)round($stay_discount_amount),
            'org_discount_percent' => $org_discount_percent,
            'org_discount_amount' => (int)round($org_discount_amount),
            'final_price' => (int)$final_price,
            'has_contract' => (bool)$contract,
            'contract_id' => $contract?->id,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'phone'        => 'required|string|regex:/^09\d{9}$/',
            'national_id'  => 'required|string|min:8',
            'stay_id'      => 'required|integer|exists:stays,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'base_guests'  => 'required|integer|min:1',
            'extra_guests' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $stay = Stay::findOrFail($request->stay_id);

            // Capacity checks against stay
            if ($request->base_guests > (int)$stay->base_capacity) {
                return response()->json(['success' => false, 'message' => 'تعداد مهمان پایه بیشتر از ظرفیت مجاز است.'], 422);
            }
            if ($request->extra_guests > (int)$stay->extra_capacity) {
                return response()->json(['success' => false, 'message' => 'تعداد مهمان اضافه بیشتر از ظرفیت مجاز است.'], 422);
            }

            $full_name = trim($request->first_name . ' ' . $request->last_name);
            $user = User::updateOrCreate(
                ['phone' => $request->phone],
                ['name' => $full_name, 'national_id' => $request->national_id, 'role' => 'user']
            );

            $start  = Carbon::parse($request->start_date);
            $end    = Carbon::parse($request->end_date);
            $nights = $end->diffInDays($start);
            if ($nights < 1) {
                return response()->json(['success' => false, 'message' => 'حداقل باید یک شب انتخاب شود.'], 422);
            }

            // Pricing per night
            $base_price_per_night = (int) $stay->price_per_person;
            $extra_price_per_night = (int) ($stay->extra_person_price ?? 0);

            $base_price = $base_price_per_night * (int)$request->base_guests * $nights;
            $extra_cost = $extra_price_per_night * (int)$request->extra_guests * $nights;

            // Stay discount percent based on peak/non-peak (use max_discount_* as active percent)
            $isPeakNow = method_exists(PeakPeriod::class, 'isNowPeak') ? PeakPeriod::isNowPeak() : (bool)$stay->is_peak;
            $stay_discount = (float) ($isPeakNow ? $stay->max_discount_peak : $stay->max_discount_normal);
            $subtotal = $base_price + $extra_cost;
            $stay_discount_amount = $subtotal * ($stay_discount / 100);

            // Organizational contract
            $contract = DiscountContractMember::with('contract')
                ->where(function ($q) use ($full_name, $request) {
                    $q->where('full_name', $full_name)
                      ->orWhere('phone', $request->phone)
                      ->orWhere('national_id', $request->national_id);
                })
                ->whereHas('contract', function ($q) {
                    $q->where('is_active', true)
                      ->whereDate('start_date', '<=', now(config('app.timezone')))
                      ->whereDate('end_date', '>=', now(config('app.timezone')));
                })
                ->first()
                ?->contract;

            $org_discount = (float) ($contract->discount_percent ?? 0);
            $org_discount_amount = max(0, $subtotal - $stay_discount_amount) * ($org_discount / 100);

            $final_price = (int) round($subtotal - ($stay_discount_amount + $org_discount_amount));
            $discount_amount = (int) round($stay_discount_amount + $org_discount_amount);

            $booking = Booking::create([
                'user_id'      => $user->id,
                'stay_id'      => $stay->id,
                'start_date'   => $start,
                'end_date'     => $end,
                'base_guests'  => (int)$request->base_guests,
                'extra_guests' => (int)$request->extra_guests,
                'base_price'   => $base_price,
                'extra_cost'   => $extra_cost,
                'stay_discount'=> $stay_discount,
                'org_discount' => $org_discount,
                'discount_contract_id' => $contract?->id,
                'discount_amount' => $discount_amount,
                'final_price'   => $final_price,
                'status'        => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'رزرو ثبت شد.',
                'booking_id'   => $booking->id,
                'final_price'  => number_format($final_price),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'خطای سرور'], 500);
        }
    }
}
