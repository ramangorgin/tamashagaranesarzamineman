<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StayImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'image_path',
        'is_main',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    // Full Path for showing the image in Blade pages
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
