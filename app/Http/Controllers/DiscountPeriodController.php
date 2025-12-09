<?php

namespace App\Http\Controllers;

use App\Models\DiscountPeriod;
use App\Models\PeakPeriod;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

class DiscountPeriodController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'start_date' => 'required|string',
            'end_date'   => 'required|string',
            'percentage' => 'required|numeric|min:0|max:100',
            'provinces'  => 'nullable|array',
            'provinces.*' => 'string',
        ]);

        // Convert Jalali dates to Gregorian
        $data['start_date'] = $this->normalizeDate($data['start_date']);
        $data['end_date'] = $this->normalizeDate($data['end_date']);
        
        // Handle provinces - if empty or null, set to null (applies to all)
        if (empty($data['provinces']) || !is_array($data['provinces'])) {
            $data['provinces'] = null;
        } else {
            // Filter out empty values and ensure they're strings
            $data['provinces'] = array_filter(array_map('trim', $data['provinces']));
            $data['provinces'] = !empty($data['provinces']) ? array_values($data['provinces']) : null;
        }

        // Validate date order after conversion
        if (Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))) {
            return back()->withErrors(['end_date' => 'تاریخ پایان نباید قبل از تاریخ شروع باشد.'])->withInput();
        }

        // Check for overlap with peak periods
        $overlap = PeakPeriod::where(function($q) use ($data) {
            $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
              ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
              ->orWhere(function($qq) use ($data) {
                  $qq->where('start_date', '<=', $data['start_date'])
                     ->where('end_date', '>=', $data['end_date']);
              });
        })->exists();

        if ($overlap) {
            return back()->withErrors(['overlap' => 'این بازه با بازه پیک فعال تداخل دارد.'])->withInput();
        }

        DiscountPeriod::create($data);
        
        // Update all stays' final prices
        \App\Models\Stay::updateAllFinalPrices();
        
        return back()->with('success','بازه تخفیف ثبت شد.');
    }

    public function destroy(DiscountPeriod $discountPeriod)
    {
        $discountPeriod->delete();
        
        // Update all stays' final prices
        \App\Models\Stay::updateAllFinalPrices();
        
        return back()->with('success','بازه حذف شد.');
    }

    /**
     * Convert a Jalali date string (like 1404/09/07 or 1404-09-07) to Gregorian YYYY-MM-DD.
     * If already Gregorian, returns as-is.
     */
    protected function normalizeDate(?string $value): string
    {
        $val = trim($value ?? '');
        if($val==='') return Carbon::today()->format('Y-m-d');
        
        // unify separators
        $valUnified = str_replace(['.',','], '-', str_replace('/', '-', $val));
        
        // Convert Persian/Arabic digits to English
        $map = [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ];
        $valEn = strtr($valUnified, $map);
        
        // Try to parse as Y-m-d format
        $parts = explode('-', $valEn);
        if(count($parts) !== 3) {
            // Invalid format, try Carbon parse as fallback
            try {
                return Carbon::parse($valEn)->format('Y-m-d');
            } catch (\Exception $e) {
                return Carbon::today()->format('Y-m-d');
            }
        }
        
        [$year, $month, $day] = array_map('intval', $parts);
        
        // If year >= 1300, it's likely Jalali
        if ($year >= 1300 && $year <= 1500) {
            try {
                // Parse as Jalali via Verta
                $v = Verta::createJalali($year, $month, $day, 0, 0, 0);
                $g = $v->datetime(); // Carbon instance (Gregorian)
                return $g->format('Y-m-d');
            } catch (\Exception $e) {
                // If conversion fails, try as Gregorian
            }
        }
        
        // Try as Gregorian date
        try {
            $c = Carbon::createFromDate($year, $month, $day);
            if($c && $c->isValid()) {
                // If year is reasonable for Gregorian (1900-2100), return it
                if($c->year >= 1900 && $c->year <= 2100) {
                    return $c->format('Y-m-d');
                }
            }
        } catch (\Exception $e) {}
        
        // Final fallback
        try {
            return Carbon::parse($valEn)->format('Y-m-d');
        } catch (\Exception $e) {
            return Carbon::today()->format('Y-m-d');
        }
    }
}

