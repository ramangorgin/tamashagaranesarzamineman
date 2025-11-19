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