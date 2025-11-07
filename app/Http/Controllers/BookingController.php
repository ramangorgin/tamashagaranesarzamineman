<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stay;
use App\Models\Booking;
use App\Models\DiscountContractMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'phone'        => 'required|string|min:10',
            'national_id'  => 'required|string|min:8',
            'stay_id'      => 'required|integer|exists:stays,id',
        ]);

        DB::beginTransaction();

        try {
            $stay = Stay::findOrFail($request->stay_id);

            // نام کامل
            $full_name = trim($request->first_name . ' ' . $request->last_name);

            // 🟢 ایجاد یا بروزرسانی کاربر
            $user = User::updateOrCreate(
                ['phone' => $request->phone],
                [
                    'full_name'    => $full_name,
                    'national_id'  => $request->national_id,
                    'role'         => 'user',
                ]
            );

            // 🧩 بررسی تخفیف سازمانی
            $org_discount = DiscountContractMember::where(function ($q) use ($full_name, $request) {
                    $q->where('full_name', $full_name)
                      ->orWhere('phone', $request->phone)
                      ->orWhere('national_id', $request->national_id);
                })
                ->whereHas('contract', function ($q) {
                    $q->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
                })
                ->first()?->contract->discount_percent ?? 0;

            // 🧩 بررسی تخفیف اقامتگاه (عادی یا پیک)
            $stay_discount = $stay->is_peak ? $stay->peak_discount : $stay->normal_discount;

            // 🧮 مجموع تخفیف‌ها
            $total_discount = min($stay_discount + $org_discount, 100);

            // 🧮 قیمت نهایی
            $final_price = round($stay->price_per_night * (1 - $total_discount / 100), 0);

            // 📝 ذخیره رزرو
            $booking = Booking::create([
                'user_id'         => $user->id,
                'stay_id'         => $stay->id,
                'price_per_night' => $stay->price_per_night,
                'discount_percent'=> $total_discount,
                'final_price'     => $final_price,
                'status'          => 'pending',
            ]);

            DB::commit();

            // 🟢 پاسخ نهایی برای فرانت (AJAX)
            return response()->json([
                'success'       => true,
                'org_discount'  => $org_discount,
                'stay_discount' => $stay_discount,
                'total_discount'=> $total_discount,
                'final_price'   => number_format($final_price),
                'booking_id'    => $booking->id,
                'message'       => $org_discount > 0
                    ? "🎉 شما شامل تخفیف سازمانی {$org_discount}% شدید! مجموع تخفیف: {$total_discount}%"
                    : "مجموع تخفیف شما: {$total_discount}%",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '❌ خطایی در پردازش رزرو رخ داد: ' . $e->getMessage(),
            ], 500);
        }
    }
}
