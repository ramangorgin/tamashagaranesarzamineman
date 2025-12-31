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
use App\Models\DiscountPeriod;
use App\Models\Otp;
use Ipe\Sdk\Facades\SmsIr;

class BookingController extends Controller
{
    /**
     * ارسال کد تایید (OTP) برای رزرو – فقط نقش user
     */
    public function sendOtp(Request $request)
    {
        // $request->validate(['phone' => 'required|regex:/^09\d{9}$/']);
        $phone = $request->phone;
        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(3);
        Otp::updateOrCreate(['phone' => $phone], ['code' => $code, 'expires_at' => $expiresAt]);

        /* Sms.ir
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
        */
        // MeliPayamak
        
       // Melipayamak Console

        $url = 'https://console.melipayamak.com/api/send/shared/e9741f18ee7e494792c4b49f6c7572e9';
        $data = array('bodyId' => 386622, 'to' => $phone, 'args' => [(string)$code]);
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

        // Check if SMS was sent successfully (adjust based on your SMS provider response)
        $resultData = json_decode($result, true);
        if ($resultData && isset($resultData['status']) && $resultData['status'] === 'OK') {
            return response()->json(['success' => true, 'message' => 'کد تایید ارسال شد.']);
        }

        // For testing, always return success
        return response()->json(['success' => true, 'message' => 'کد تایید ارسال شد.']);
    }

    /**
     * تایید کد OTP وارد شده
     */
    public function verifyOtp(Request $request)
    {
        // $request->validate([
        //     'phone' => 'required|regex:/^09\d{9}$/',
        //     'code'  => 'required|digits:6',
        // ]);
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
        // $request->validate([
        //     'first_name'   => 'required|string|max:100',
        //     'last_name'    => 'required|string|max:100',
        //     'national_id'  => 'required|string|min:8',
        //     'phone'        => 'required|string|regex:/^09\d{9}$/',
        //     'stay_id'      => 'required|integer|exists:stays,id',
        //     'start_date'   => 'required|date',
        //     'end_date'     => 'required|date|after:start_date',
        //     'base_guests'  => 'required|integer|min:1',
        //     'extra_guests' => 'required|integer|min:0',
        // ]);

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
        // Get base prices (already adjusted in views, but we need to recalculate for the booking date range)
        $base_price_per_night  = (int)$stay->price_per_person;
        $extra_price_per_night = (int)($stay->extra_person_price ?? 0);
        
        // Apply peak/discount period adjustments for the booking date range
        $adjusted_base_price = $stay->getAdjustedPriceForDateRange($request->start_date, $request->end_date, $base_price_per_night);
        $adjusted_extra_price = $stay->getAdjustedPriceForDateRange($request->start_date, $request->end_date, $extra_price_per_night);
        
        $base_price = $adjusted_base_price * (int)$request->base_guests * $nights;
        $extra_cost = $adjusted_extra_price * (int)$request->extra_guests * $nights;
        
        $subtotal = $base_price + $extra_cost;
        $stay_discount_amount = 0; // Peak/discount adjustments are already in the price

        // Organizational discount: ONLY phone OR national_id (NOT full_name)
        $contract = DiscountContractMember::with('contract')
            ->where(function ($q) use ($request) {
                if (!empty($request->phone)) {
                    $q->where('phone', $request->phone);
                }
                if (!empty($request->national_id)) {
                    $q->orWhere('national_id', $request->national_id);
                }
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
            'base_price' => $base_price, // Already includes peak/discount period adjustments
            'extra_cost' => $extra_cost, // Already includes peak/discount period adjustments
            'stay_discount_percent' => 0, // Peak/discount adjustments already applied to base prices
            'stay_discount_amount' => 0, // Peak/discount adjustments already applied to base prices
            'org_discount_percent' => $org_discount_percent, // DiscountContract discount (extra discount for specific people)
            'org_discount_amount' => (int)round($org_discount_amount), // DiscountContract discount amount
            'final_price' => (int)$final_price,
            'has_contract' => (bool)$contract,
            'contract_id' => $contract?->id,
        ]);
    }
    public function store(Request $request)
    {
        // $request->validate([
        //     'first_name'   => 'required|string|max:100',
        //     'last_name'    => 'required|string|max:100',
        //     'phone'        => 'required|string|regex:/^09\d{9}$/',
        //     'national_id'  => 'required|string|min:8',
        //     'stay_id'      => 'required|integer|exists:stays,id',
        //     'start_date'   => 'required|date',
        //     'end_date'     => 'required|date|after:start_date',
        //     'base_guests'  => 'required|integer|min:1',
        //     'extra_guests' => 'required|integer|min:0',
        // ]);

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
                ['full_name' => $full_name, 'national_id' => $request->national_id]
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

            // Apply peak/discount period adjustments for the booking date range
            $adjusted_base_price = $stay->getAdjustedPriceForDateRange($request->start_date, $request->end_date, $base_price_per_night);
            $adjusted_extra_price = $stay->getAdjustedPriceForDateRange($request->start_date, $request->end_date, $extra_price_per_night);

            $base_price = $adjusted_base_price * (int)$request->base_guests * $nights;
            $extra_cost = $adjusted_extra_price * (int)$request->extra_guests * $nights;

            $subtotal = $base_price + $extra_cost;
            $stay_discount = 0; // Peak/discount adjustments are already in the price
            $stay_discount_amount = 0;

            // Organizational discount: ONLY phone OR national_id (NOT full_name)
            $contract = DiscountContractMember::with('contract')
                ->where(function ($q) use ($request) {
                    if (!empty($request->phone)) {
                        $q->where('phone', $request->phone);
                    }
                    if (!empty($request->national_id)) {
                        $q->orWhere('national_id', $request->national_id);
                    }
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

            // Times are set by host in stay settings, not by user
            $startTime = $stay->checkin_time ? \Carbon\Carbon::parse($stay->checkin_time)->format('H:i:s') : '14:00:00';
            $endTime = $stay->checkout_time ? \Carbon\Carbon::parse($stay->checkout_time)->format('H:i:s') : '12:00:00';

            $booking = Booking::create([
                'user_id'      => $user->id,
                'stay_id'      => $stay->id,
                'start_date'   => $start,
                'start_time'   => $startTime,
                'end_date'     => $end,
                'end_time'     => $endTime,
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

    /**
     * لیست رزروهای مربوط به اقامت‌گاه‌های میزبان (پنل میزبان)
     */
    public function hostIndex(Request $request)
    {
        $host = auth('host')->user();
        if (!$host) {
            abort(403);
        }
        // فقط میزبان تایید شده اجازه مشاهده دارد (لینک در حالت pending/rejected غیرفعال است اما محافظ دوبل)
        if (!in_array($host->status, ['approved'])) {
            return redirect()->route('host.dashboard');
        }

        $stayIds = $host->stays()->pluck('id');

        $bookings = Booking::with(['user','stay'])
            ->whereIn('stay_id', $stayIds)
            ->when($request->filled('q'), function($q) use ($request){
                $term = trim($request->q);
                $q->where(function($qq) use ($term){
                    $qq->whereHas('user', function($uq) use ($term){
                        $uq->where('full_name','like',"%$term%")
                           ->orWhere('phone','like',"%$term%");
                    })
                    ->orWhereHas('stay', function($sq) use ($term){
                        $sq->where('title','like',"%$term%");
                    });
                });
            })
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('from'), fn($q) => $q->whereDate('start_date','>=',$request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('end_date','<=',$request->to))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $filters = [
            'q' => $request->q,
            'status' => $request->status,
            'from' => $request->from,
            'to' => $request->to,
        ];

        return view('host.bookings', compact('bookings','filters'));
    }

    /**
     * لیست همه رزروها برای پنل مدیریت با فیلترها
     */
    public function adminIndex(Request $request)
    {
        $admin = auth('admin')->user();
        if(!$admin){ abort(403); }

        $bookings = Booking::with(['user','stay','stay.host'])
            ->when($request->filled('q'), function($q) use ($request){
                $term = trim($request->q);
                $q->where(function($qq) use ($term){
                    $qq->whereHas('user', function($uq) use ($term){
                        $uq->where('full_name','like',"%$term%")
                           ->orWhere('phone','like',"%$term%");
                    })
                    ->orWhereHas('stay', function($sq) use ($term){
                        $sq->where('title','like',"%$term%")
                           ->orWhere('address','like',"%$term%");
                    });
                });
            })
            ->when($request->filled('status'), fn($q) => $q->where('status',$request->status))
            ->when($request->filled('from'), fn($q) => $q->whereDate('start_date','>=',$request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('end_date','<=',$request->to))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $filters = [
            'q' => $request->q,
            'status' => $request->status,
            'from' => $request->from,
            'to' => $request->to,
        ];

        return view('admin.bookings', compact('bookings','filters'));
    }

}
