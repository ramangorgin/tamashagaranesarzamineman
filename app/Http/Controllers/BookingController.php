<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Stay;
use App\Models\User;
use App\Models\DiscountContractMember;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
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
            'guests'       => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $stay = Stay::findOrFail($request->stay_id);
            $full_name = trim($request->first_name . ' ' . $request->last_name);

            // 🟢 1. ایجاد یا بروزرسانی کاربر
            $user = User::updateOrCreate(
                ['phone' => $request->phone],
                [
                    'name'        => $full_name,
                    'national_id' => $request->national_id,
                    'role'        => 'user',
                ]
            );

            // 🧮 2. محاسبه تعداد شب‌ها
            $start = Carbon::parse($request->start_date);
            $end   = Carbon::parse($request->end_date);
            $nights = $end->diffInDays($start);

            if ($nights < 1) {
                return response()->json(['success' => false, 'message' => 'حداقل باید یک شب انتخاب شود.']);
            }

            // 🏷️ 3. محاسبه قیمت پایه
            $base_price = $stay->price_per_night * $nights;

            // 👥 4. هزینه نفرات اضافه
            $extra_guests = max(0, $request->guests - $stay->capacity);
            $extra_cost = $extra_guests * ($stay->extra_person_price ?? 0) * $nights;

            // 💸 5. تخفیف اقامتگاه (عادی یا پیک)
            $stay_discount = $stay->is_peak ? $stay->peak_discount : $stay->normal_discount;
            $stay_discount_amount = ($base_price + $extra_cost) * ($stay_discount / 100);

            // 🏢 6. بررسی تخفیف سازمانی
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

            $org_discount_amount = ($base_price + $extra_cost - $stay_discount_amount) * ($org_discount / 100);

            // 🧾 7. محاسبه نهایی
            $total_discount = min($stay_discount + $org_discount, 100);
            $final_price = round(($base_price + $extra_cost) - ($stay_discount_amount + $org_discount_amount));

            // 📝 8. ذخیره رزرو
            $booking = Booking::create([
                'user_id'         => $user->id,
                'stay_id'         => $stay->id,
                'start_date'      => $start,
                'end_date'        => $end,
                'guests'          => $request->guests,
                'base_price'      => $base_price,
                'extra_cost'      => $extra_cost,
                'stay_discount'   => $stay_discount,
                'org_discount'    => $org_discount,
                'final_price'     => $final_price,
                'status'          => 'pending',
            ]);

            DB::commit();

            // ✅ 9. پیام نهایی برای نمایش در مدال
            $msg = match (true) {
                $org_discount > 0 && $stay_discount > 0 => "🎁 شامل {$org_discount}% تخفیف سازمانی و {$stay_discount}% تخفیف اقامت‌گاه شدید.",
                $org_discount > 0 => "🎉 شما شامل {$org_discount}% تخفیف سازمانی هستید.",
                $stay_discount > 0 => "🎉 شما شامل {$stay_discount}% تخفیف اقامت‌گاه شدید.",
                default => "هیچ تخفیفی برای شما اعمال نشده است."
            };

            return response()->json([
                'success'       => true,
                'message'       => $msg,
                'stay_discount' => $stay_discount,
                'org_discount'  => $org_discount,
                'total_discount'=> $total_discount,
                'final_price'   => number_format($final_price),
                'booking_id'    => $booking->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '❌ خطا در ثبت رزرو: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
