<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * relations with user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }

    /**
     * checking the expiration
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * checking the validation of the code
     */
    public static function verifyCode($phone, $code)
    {
        $otp = self::where('phone', $phone)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        return $otp ?: false;
    }
}
