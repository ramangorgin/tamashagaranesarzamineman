<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'star_rating',
        'license_number',
        'has_lobby',
        'has_elevator',
        'has_restaurant',
        'has_parking',
        'has_breakfast',
        'has_24h_reception',
    ];

    protected $casts = [
        'has_lobby' => 'boolean',
        'has_elevator' => 'boolean',
        'has_restaurant' => 'boolean',
        'has_parking' => 'boolean',
        'has_breakfast' => 'boolean',
        'has_24h_reception' => 'boolean',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    public function roomTypes()
    {
        return $this->hasMany(HotelRoomType::class);
    }
}

