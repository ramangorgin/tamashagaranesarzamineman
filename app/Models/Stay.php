<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'admin_id',
        'title',
        'category',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'county_id',
        'county_name',
        'village_name',
        'address',
        'latitude',
        'longitude',
        'capacity',
        'base_capacity',
        'extra_capacity',
        'area',
        'bedrooms',
        'double_beds',
        'single_beds',
        'floor_beds',
        'bathrooms',
        'iranian_toilets',
        'western_toilets',
        'price_per_person',
        'extra_person_price',
        'pricing_mode',
        'price_per_night',
        'final_price_per_person',
        'final_price_per_night',
        'final_extra_person_price',
        'site_commission',
        'min_price_adjustment',
        'max_price_adjustment',
        'checkin_time',
        'checkout_time',
        'is_active',
        'is_peak',
        'moderation_status',
        'approved_by_admin_id',
        'approved_at',
        'reject_reason',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
        'is_peak' => 'boolean',
        'checkin_time' => 'datetime:H:i',
        'checkout_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class); // FK: host_id
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class); // FK: admin_id
    }

    public function images()
    {
        return $this->hasMany(StayImage::class);
    }

    public function rules()
    {
        return $this->hasMany(StayRule::class);
    }

    // Approver admin
    public function approver() { return $this->belongsTo(Admin::class, 'approved_by_admin_id'); }

    /**
     * Get current adjusted price for a base price (uses final_price if available, otherwise calculates)
     * Returns array with: ['original' => base, 'adjusted' => adjusted, 'type' => 'peak'|'discount'|null, 'percent' => percentage]
     */
    public function getCurrentAdjustedPrice($basePrice)
    {
        $today = \Carbon\Carbon::today()->toDateString();
        return $this->calculateAdjustedPrice($basePrice, $today);
    }
    
    protected function calculateAdjustedPrice($basePrice, $date)
    {
        $adjustedPrice = $basePrice;
        $type = null;
        $percent = 0;

        // Check for peak period - use date comparison that works with date columns
        $peakPeriod = \App\Models\PeakPeriod::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get()
            ->filter(function($period) {
                // If no provinces specified, apply to all
                if (empty($period->provinces) || !is_array($period->provinces)) {
                    return true;
                }
                // Check if stay's province is in the period's provinces (normalize strings, strip leading zeros)
                $stayProvince = ltrim((string)$this->province_id, '0');
                $periodProvinces = array_map(function ($p) {
                    return ltrim((string)$p, '0');
                }, $period->provinces);
                return in_array($stayProvince, $periodProvinces, true);
            })
            ->first();

        if ($peakPeriod && $peakPeriod->percentage > 0) {
            $peakPercent = min((float)$peakPeriod->percentage, (float)($this->max_price_adjustment ?? 100));
            $adjustedPrice = $basePrice * (1 + ($peakPercent / 100));
            $type = 'peak';
            $percent = $peakPercent;
        } else {
            // Check for discount period
            $discountPeriod = \App\Models\DiscountPeriod::whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->get()
                ->filter(function($period) {
                    // If no provinces specified, apply to all
                    if (empty($period->provinces) || !is_array($period->provinces)) {
                        return true;
                    }
                    // Check if stay's province is in the period's provinces (normalize strings, strip leading zeros)
                    $stayProvince = ltrim((string)$this->province_id, '0');
                    $periodProvinces = array_map(function ($p) {
                        return ltrim((string)$p, '0');
                    }, $period->provinces);
                    return in_array($stayProvince, $periodProvinces, true);
                })
                ->first();

            if ($discountPeriod && $discountPeriod->percentage > 0) {
                $maxDiscountPercent = 100 - ((float)($this->min_price_adjustment ?? 0));
                $discountPercent = min((float)$discountPeriod->percentage, $maxDiscountPercent);
                $adjustedPrice = $basePrice * (1 - ($discountPercent / 100));
                $minPrice = $basePrice * (((float)($this->min_price_adjustment ?? 0)) / 100);
                $adjustedPrice = max($adjustedPrice, $minPrice);
                $type = 'discount';
                $percent = $discountPercent;
            }
        }

        return [
            'original' => (int)$basePrice,
            'adjusted' => (int)round($adjustedPrice),
            'type' => $type,
            'percent' => $percent
        ];
    }
    
    /**
     * Update final prices based on current peak/discount periods
     * This should be called when periods change or stay prices change
     */
    public function updateFinalPrices()
    {
        $today = \Carbon\Carbon::today()->toDateString();
        
        // Update price_per_person final price
        if ($this->price_per_person) {
            $priceInfo = $this->calculateAdjustedPrice($this->price_per_person, $today);
            $this->final_price_per_person = $priceInfo['adjusted'];
        } else {
            $this->final_price_per_person = null;
        }
        
        // Update price_per_night final price
        if ($this->price_per_night) {
            $priceInfo = $this->calculateAdjustedPrice($this->price_per_night, $today);
            $this->final_price_per_night = $priceInfo['adjusted'];
        } else {
            $this->final_price_per_night = null;
        }
        
        // Update extra_person_price final price
        if ($this->extra_person_price) {
            $priceInfo = $this->calculateAdjustedPrice($this->extra_person_price, $today);
            $this->final_extra_person_price = $priceInfo['adjusted'];
        } else {
            $this->final_extra_person_price = null;
        }
        
        $this->saveQuietly(); // Save without triggering events
    }
    
    /**
     * Update final prices for all stays
     * Call this when peak/discount periods are created/updated/deleted
     */
    public static function updateAllFinalPrices()
    {
        self::chunk(100, function ($stays) {
            foreach ($stays as $stay) {
                $stay->updateFinalPrices();
            }
        });
    }

    /**
     * Calculate adjusted price based on peak/discount periods for a date range
     * Returns the adjusted price per person/night
     */
    public function getAdjustedPriceForDateRange($startDate, $endDate, $basePrice)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $nights = $end->diffInDays($start);
        
        if ($nights < 1) {
            return $basePrice;
        }

        $totalAdjustedPrice = 0;
        $currentDate = $start->copy();

        for ($i = 0; $i < $nights; $i++) {
            $dateStr = $currentDate->toDateString();
            
            // Use the calculateAdjustedPrice method for consistency
            $priceInfo = $this->calculateAdjustedPrice($basePrice, $dateStr);
            $adjustedPrice = $priceInfo['adjusted'];

            $totalAdjustedPrice += $adjustedPrice;
            $currentDate->addDay();
        }

        // Return average price per night
        return round($totalAdjustedPrice / $nights);
    }
}
