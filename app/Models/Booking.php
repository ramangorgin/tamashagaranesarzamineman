<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stay_id',
        'start_date',
        'end_date',
        'base_guests',
        'extra_guests',
        'base_price',
        'extra_cost',
        'stay_discount',
        'org_discount',
        'discount_contract_id',
        'discount_amount',
        'final_price',
        'status',
        'cancelled_by',
        'cancellation_reason',
        'refund_amount',
        'refunded_at',
        'payment_reference',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'refunded_at'  => 'datetime',
    ];

    // 🔗 ارتباط با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 ارتباط با اقامت‌گاه
    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    // 🧮 متد کمکی: تعداد شب‌ها
    public function getNightsAttribute()
    {
        return $this->end_date->diffInDays($this->start_date);
    }

    // 💵 متد کمکی: نمایش قیمت نهایی با فرمت
    public function getFormattedPriceAttribute()
    {
        return number_format($this->final_price) . ' تومان';
    }

    // 🎯 وضعیت رزرو با رنگ
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending'   => '<span class="badge bg-warning text-dark">در انتظار پرداخت</span>',
            'paid'      => '<span class="badge bg-success">پرداخت‌شده</span>',
            'cancelled' => '<span class="badge bg-danger">لغو شده</span>',
            'refunded'  => '<span class="badge bg-info text-dark">بازگشت وجه</span>',
            'failed'    => '<span class="badge bg-danger">خطای پرداخت</span>',
            'expired'   => '<span class="badge bg-secondary">منقضی شده</span>',
            default     => '<span class="badge bg-secondary">نامشخص</span>',
        };
    }

    public function discountContract()
    {
        return $this->belongsTo(\App\Models\DiscountContract::class, 'discount_contract_id');
    }
}
