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
        'province_id','province_name','city_id','city_name',
        'county_id','county_name','village_name',
        'postal_code',
        'address',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function stays()
    {
        return $this->hasMany(Stay::class);
    }
}
