<?php

if (!function_exists('stayTypeToPersian')) {
    function stayTypeToPersian($type)
    {
        return match ($type) {
            'hotel' => 'هتل',
            'villa' => 'ویلا',
            'apartment' => 'آپارتمان',
            'ecolodge' => 'بوم‌گردی',
            'suite' => 'سوئیت',
            'motel' => 'مسافرخانه',
            'house' => 'خانه',
            default => 'نامشخص',
        };
    }
}

if (!function_exists('normalize_digits')) {
    /**
     * Convert Persian (۰-۹) & Arabic (٠-٩) digits to English 0-9 inside any string.
     */
    function normalize_digits($value)
    {
        if (is_array($value)) {
            return array_map('normalize_digits', $value);
        }
        if (!is_string($value)) {
            return $value;
        }
        static $map = [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
        ];
        return strtr($value, $map);
    }
}

if (!function_exists('displayStayPrice')) {
    /**
     * Display stay price with discount/peak styling
     * @param \App\Models\Stay $stay
     * @param string $priceType 'per_person' or 'per_night' or 'extra_person'
     * @return string HTML
     */
    function displayStayPrice($stay, $priceType = 'per_person')
    {
        $basePrice = 0;
        if ($priceType === 'per_night') {
            $basePrice = $stay->price_per_night ?? 0;
        } elseif ($priceType === 'extra_person') {
            $basePrice = $stay->extra_person_price ?? 0;
        } else {
            $basePrice = $stay->price_per_person ?? 0;
        }

        if ($basePrice <= 0) {
            return '<span class="text-muted">—</span>';
        }

        // Always calculate current adjusted price (don't rely on cached final_price for display)
        // This ensures prices are always up-to-date with current periods
        $priceInfo = $stay->getCurrentAdjustedPrice($basePrice);
        $finalPrice = $priceInfo['adjusted'];
        $type = $priceInfo['type'];
        $percent = $priceInfo['percent'];
        
        // If no adjustment, show simple price
        if ($type === null || $basePrice == $finalPrice) {
            return '<span class="fw-bold text-primary">' . number_format($finalPrice) . '</span>';
        }

        // Show with discount styling only (peak periods show adjusted price without styling)
        if ($type === 'discount') {
            $view = view('partials.price-with-discount', [
                'original' => $basePrice,
                'adjusted' => $finalPrice,
                'percent' => $percent
            ]);
            return $view->render();
        } else {
            // Peak period - show increased price WITHOUT special styling (just the adjusted price)
            return '<span class="fw-bold text-primary">' . number_format($finalPrice) . '</span>';
        }
    }
}