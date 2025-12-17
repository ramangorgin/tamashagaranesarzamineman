<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title_fa',
        'title_en',
    ];

    public function roomTypes()
    {
        return $this->belongsToMany(HotelRoomType::class, 'hotel_room_type_beds')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}

