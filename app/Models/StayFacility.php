<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StayFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'category',
        'name',
        'icon',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }
}
