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
        'description',
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
        $tz = config('app.timezone', 'Asia/Tehran');
        $today = now($tz)->startOfDay();      // local day
        $start = $this->start_date?->copy()->startOfDay();
        $end   = $this->end_date?->copy()->endOfDay();

        return $this->is_active
            && $start
            && $end
            && $today->greaterThanOrEqualTo($start)
            && $today->lessThanOrEqualTo($end);
    }

    // Find best discount for given identifiers (ONLY phone OR national_id, NOT full_name)
    public static function findForMember(array $data): ?self
    {
        $today = Carbon::today();
        $query = static::query()->active()->with('members')
            ->whereHas('members', function ($q) use ($data) {
                $q->where(function ($w) use ($data) {
                    if (!empty($data['phone'])) {
                        $w->where('phone', $data['phone']);
                    }
                    if (!empty($data['national_id'])) {
                        $w->orWhere('national_id', $data['national_id']);
                    }
                });
            })
            ->orderByDesc('discount_percent')
            ->orderBy('end_date');

        return $query->first();
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)
                 ->whereDate('start_date', '<=', Carbon::today())
                 ->whereDate('end_date', '>=', Carbon::today());
    }

    public function scopeOnDate($q, $date)
    {
        return $q->whereDate('start_date', '<=', $date)
                 ->whereDate('end_date', '>=', $date);
    }
}
