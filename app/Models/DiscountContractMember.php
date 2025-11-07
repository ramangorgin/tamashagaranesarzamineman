<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountContractMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'full_name',
        'national_id',
        'phone',
    ];

    public function contract()
    {
        return $this->belongsTo(DiscountContract::class, 'contract_id');
    }
}
