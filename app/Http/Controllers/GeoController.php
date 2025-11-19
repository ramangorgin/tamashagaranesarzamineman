<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class GeoController extends Controller
{
    protected function load(string $file): array {
        $path = resource_path('data/'.$file);
        if (!is_file($path)) return [];
        return json_decode(file_get_contents($path), true) ?: [];
    }

    public function provinces() {
        return response()->json($this->load('provinces.json'));
    }

    public function cities($provinceId) {
        $all = $this->load('provinces_cities.json');
        $rows = array_values(array_filter($all, fn($r) => ($r['provinceId'] ?? null) === $provinceId));
        return response()->json($rows);
    }

    public function counties($provinceId, $cityId) {
        $all = $this->load('provinces_cities_counties.json');
        $rows = array_values(array_filter($all, fn($r) =>
            ($r['provinceId'] ?? null) === $provinceId && ($r['cityId'] ?? null) === $cityId
        ));
        return response()->json($rows);
    }

    public function villages($provinceId, $cityId, $countyId) {
        $all = $this->load('provinces_cities_counties_villages.json');
        $rows = array_values(array_filter($all, fn($r) =>
            ($r['provinceId'] ?? null) === $provinceId &&
            ($r['cityId'] ?? null) === $cityId &&
            ($r['countyId'] ?? null) === $countyId
        ));
        return response()->json($rows);
    }
}