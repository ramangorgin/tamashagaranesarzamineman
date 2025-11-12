<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StayRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'rule_text',
        'is_allowed',
        'checkin_time',
        'checkout_time',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
        'checkin_time' => 'datetime:H:i',
        'checkout_time' => 'datetime:H:i',
    ];

    public function stay()
    {
        return $this->belongsTo(Stay::class);
    }
}
