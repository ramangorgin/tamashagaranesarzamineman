<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'title',
        'capacity',
        'base_capacity',
        'extra_capacity',
        'area',
        'price_per_night',
        'total_rooms',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function beds()
    {
        return $this->belongsToMany(Bed::class, 'hotel_room_type_beds')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}

