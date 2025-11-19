<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StayImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }

    /**
     * مسیر کامل URL فایل
     */
    public function getUrlAttribute(): string
    {
        $p = $this->path ?? '';
        if (Str::startsWith($p, ['http://','https://'])) {
            return $p;
        }
        // If path already includes "storage/", serve as asset
        if (Str::startsWith($p, 'storage/')) {
            return asset($p);
        }
        // Otherwise use storage disk URL
        return Storage::url($p);
    }
}
