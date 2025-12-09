<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountPeriod extends Model
{
    use HasFactory;

    protected $fillable = ['start_date','end_date','percentage','provinces'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'percentage' => 'decimal:2',
        'provinces' => 'array',
    ];

    public static function isNowDiscount(): bool
    {
        $today = now()->toDateString();
        return self::whereDate('start_date','<=',$today)
                   ->whereDate('end_date','>=',$today)
                   ->exists();
    }

    public static function getActiveDiscount(): ?self
    {
        $today = now()->toDateString();
        return self::whereDate('start_date','<=',$today)
                   ->whereDate('end_date','>=',$today)
                   ->first();
    }
}

