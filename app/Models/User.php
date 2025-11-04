<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'role',
    ];


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
}
