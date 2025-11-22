<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Host extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'phone','status','name','national_id','email',
        'id_card_image','selfie_image','business_license',
        'province_id','province_name','city_id','city_name',
        'county_id','county_name','village_name','address',
        'iban','bank_name','account_holder',
    ];

    protected $hidden = ['remember_token'];
}
