<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    protected $fillable = [
        'title', 'description', 'type', 'owner_id', 'city', 'province', 'address',
        'latitude', 'longitude', 'capacity', 'rooms', 'beds', 'bathrooms',
        'price_per_night', 'final_price', 'is_active' , 'normal_discount', 'peak_discount'
    ];

    public function images()
    {
        return $this->hasMany(StayImage::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getDynamicDiscountAttribute()
    {
        return $this->is_peak ? $this->peak_discount : $this->normal_discount;
    }

    public function getFinalPriceAttribute()
    {
        $discount = $this->dynamic_discount;
        return round($this->price_per_night * (1 - $discount / 100), 0);
    }

    public function getFinalPriceForUser($user)
    {
        // 1. Stay's discount
        $discount = $this->dynamic_discount;

        // 2. Contract's discount (if exists)
        if ($user) {
            $discount += $user->organizational_discount;
        }

        // should not be 100%
        $discount = min($discount, 100);

        // 3. calculating the Final Discount
        return round($this->price_per_night * (1 - $discount / 100), 0);
    }

}