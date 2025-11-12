<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Host extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'national_id',
        'phone',
        'email',
        'id_card_image',
        'selfie_image',
        'business_license',
        'status',
        'rejection_reason',
        'iban',
        'bank_name',
        'account_holder',
        'province',
        'city',
        'postal_code',
        'address',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }
}
