<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    /**
     * فیلدهایی که قابل پر شدن هستند
     */
    protected $fillable = [
        'host_id',
        'admin_id',
        'title',
        'category',
        'province',
        'city',
        'address',
        'latitude',
        'longitude',
        'area',
        'capacity',
        'base_capacity',
        'extra_capacity',
        'bedrooms',
        'double_beds',
        'single_beds',
        'floor_beds',
        'iranian_toilets',
        'western_toilets',
        'bathrooms',
        'price_per_person',
        'extra_person_price',
        'site_commission',
        'max_discount_normal',
        'max_discount_peak',
        'is_peak',
        'is_active',
    ];

    /**
     * نوع داده برای برخی فیلدها
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price_per_person' => 'decimal:2',
        'extra_person_price' => 'decimal:2',
        'site_commission' => 'decimal:2',
        'max_discount_normal' => 'decimal:2',
        'max_discount_peak' => 'decimal:2',
        'is_peak' => 'boolean',
        'is_active' => 'boolean',
    ];

    // -------------------------------
    // روابط (Relationships)
    // -------------------------------

    /**
     * میزبان اقامت‌گاه
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * ادمینی که اقامت‌گاه را ایجاد یا تایید کرده است
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * امکانات اقامت‌گاه
     */
    public function facilities()
    {
        return $this->hasMany(StayFacility::class);
    }

    /**
     * قوانین اقامت‌گاه
     */
    public function rules()
    {
        return $this->hasMany(StayRule::class);
    }

    /**
     * تصاویر اقامت‌گاه
     */
    public function images()
    {
        return $this->hasMany(StayImage::class);
    }

    /**
     * تصویر اصلی اقامت‌گاه
     */
    public function mainImage()
    {
        return $this->hasOne(StayImage::class)->where('is_main', true);
    }

    // -------------------------------
    // متدهای کمکی (Accessors / Helpers)
    // -------------------------------

        /**
     * تخفیف فعال فعلی اقامت‌گاه (وابسته به وضعیت پیک)
     */
    public function getDynamicDiscountAttribute()
    {
        // اگر پیک است => حداکثر تخفیف پیک
        // در غیر این صورت => حداکثر تخفیف عادی
        return $this->is_peak ? $this->max_discount_peak : $this->max_discount_normal;
    }

    /**
     * قیمت نهایی هر نفر با توجه به تخفیف فعال
     */
    public function getFinalPriceAttribute()
    {
        $discount = $this->dynamic_discount;
        return round($this->price_per_person * (1 - $discount / 100), 0);
    }

    /**
     * قیمت نهایی برای کاربر خاص (درصورت داشتن تخفیف سازمانی)
     */
    public function getFinalPriceForUser($user)
    {
        $discount = $this->dynamic_discount;

        // اگر کاربر دارای تخفیف سازمانی باشد
        if ($user && isset($user->organizational_discount)) {
            $discount += $user->organizational_discount;
        }

        // سقف تخفیف = 100٪
        $discount = min($discount, 100);

        return round($this->price_per_person * (1 - $discount / 100), 0);
    }


    /**
     * نمایش قیمت با فرمت زیبا
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price_per_person) . ' تومان';
    }

    /**
     * وضعیت فعال بودن
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'فعال' : 'غیرفعال';
    }

    /**
     * محاسبه تعداد کل تخت‌ها
     */
    public function getTotalBedsAttribute()
    {
        return $this->double_beds + $this->single_beds + $this->floor_beds;
    }
}
