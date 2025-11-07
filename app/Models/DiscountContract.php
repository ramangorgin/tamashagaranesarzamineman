<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiscountContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'discount_percent',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relaotions with Members
    public function members()
    {
        return $this->hasMany(DiscountContractMember::class, 'contract_id');
    }

    // checking if the contract is active
    public function getIsCurrentlyActiveAttribute(): bool
    {
        $today = Carbon::today();
        return $this->is_active && $today->between($this->start_date, $this->end_date);
    }
}
