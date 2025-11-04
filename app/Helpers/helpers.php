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
            default => 'نامشخص',
        };
    }
}
