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

}
