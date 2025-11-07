<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

     protected $fillable = ['full_name', 'national_id', 'phone'];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
    * relations with OTPs table
    */
    public function otps()
    {
        return $this->hasMany(Otp::class, 'phone', 'phone');
    }

    /**
     * checking the role
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isHost()
    {
        return $this->role === 'host';
    }
    public function getOrganizationalDiscountAttribute()
    {
        $member = \App\Models\DiscountContractMember::where(function ($query) {
            $query->where('full_name', $this->name)
                ->orWhere('phone', $this->phone)
                ->orWhere('national_id', $this->national_id ?? null);
        })
        ->whereHas('contract', function ($query) {
            $query->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        })
        ->first();

        return $member ? $member->contract->discount_percent : 0;
    }

}
