<?php

if (!function_exists('faNum')) {
    function faNum(?string $value): string {
        $value = (string) $value;
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return str_replace($en, $fa, $value);
    }
}

if (!function_exists('normalize_digits')) {
    // $to: 'en' or 'fa'
    function normalize_digits($value, string $to = 'en') {
        if (is_null($value)) return $value;
        $value = (string) $value;

        // Arabic-Indic and Persian digits
        $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $en = ['0','1','2','3','4','5','6','7','8','9','0','1','2','3','4','5','6','7','8','9'];

        if ($to === 'en') {
            $value = str_replace($fa, $en, $value);
        } else {
            $mapEn = ['0','1','2','3','4','5','6','7','8','9'];
            $mapFa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
            $value = str_replace($mapEn, $mapFa, $value);
        }

        // Optional: convert Arabic Kaf/Ya to Persian variants
        $value = str_replace(['ي','ك'], ['ی','ک'], $value);

        return $value;
    }
}

if (!function_exists('normalize_digits_recursive')) {
    function normalize_digits_recursive($payload, string $to = 'en') {
        if (is_array($payload)) {
            foreach ($payload as $k => $v) {
                $payload[$k] = normalize_digits_recursive($v, $to);
            }
            return $payload;
        }
        if (is_string($payload)) {
            return normalize_digits($payload, $to);
        }
        return $payload;
    }
}