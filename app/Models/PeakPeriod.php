<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeakPeriod extends Model
{
    use HasFactory;

    protected $fillable = ['start_date','end_date'];

    public static function isNowPeak(): bool
    {
        $today = now()->toDateString();
        return self::where('start_date','<=',$today)
                   ->where('end_date','>=',$today)
                   ->exists();
    }
}
