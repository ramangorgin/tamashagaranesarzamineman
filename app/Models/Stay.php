<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'admin_id',
        'title',
        'category',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'county_id',
        'county_name',
        'village_name',
        'address',
        'latitude',
        'longitude',
        'capacity',
        'base_capacity',
        'extra_capacity',
        'area',
        'bedrooms',
        'double_beds',
        'single_beds',
        'floor_beds',
        'bathrooms',
        'iranian_toilets',
        'western_toilets',
        'price_per_person',
        'extra_person_price',
        'pricing_mode',
        'price_per_night',
        'site_commission',
        'max_discount_normal',
        'max_discount_peak',
        'checkin_time',
        'checkout_time',
        'is_active',
        'is_peak',
        'moderation_status',
        'approved_by_admin_id',
        'approved_at',
        'reject_reason',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
        'is_peak' => 'boolean',
        'checkin_time' => 'datetime:H:i',
        'checkout_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class); // FK: host_id
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class); // FK: admin_id
    }

    public function images()
    {
        return $this->hasMany(StayImage::class);
    }

    public function rules()
    {
        return $this->hasMany(StayRule::class);
    }

    // Approver admin
    public function approver() { return $this->belongsTo(Admin::class, 'approved_by_admin_id'); }
}
