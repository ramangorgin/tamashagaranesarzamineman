<?php

namespace App\Http\Controllers;

class GeoController extends Controller
{
    protected function load(string $file): array {
        $path = resource_path('data/'.$file);
        if (!is_file($path)) return [];
        return json_decode(file_get_contents($path), true) ?: [];
    }

    private function norm(array $rows, string $type): array
    {
        return array_map(function ($r) use ($type) {
            $id   = $r[$type.'Id'] ?? $r['id'] ?? $r['code'] ?? null;
            $name = $r[$type.'Name'] ?? $r['name'] ?? $r[$type.'Title'] ?? $r['title'] ?? $r[$type.'Fa'] ?? null;
            return [
                'id'        => (string) $id,
                'name'      => $name ?: '—',
                'provinceId'=> (string)($r['provinceId'] ?? ''),
                'cityId'    => (string)($r['cityId'] ?? ''),
                'countyId'  => (string)($r['countyId'] ?? ''),
            ];
        }, $rows);
    }

    public function provinces()
    {
        $rows = $this->load('provinces.json');
        return response()->json($this->norm($rows,'province'));
    }

    public function cities($provinceId)
    {
        $rows = array_values(array_filter(
            $this->load('provinces_cities.json'),
            fn($r) => (string)($r['provinceId'] ?? '') == (string)$provinceId
        ));
        return response()->json($this->norm($rows,'city'));
    }

    public function counties($provinceId, $cityId)
    {
        $rows = array_values(array_filter(
            $this->load('provinces_cities_counties.json'),
            fn($r) => (string)($r['provinceId'] ?? '') == (string)$provinceId
                 && (string)($r['cityId'] ?? '') == (string)$cityId
        ));
        return response()->json($this->norm($rows,'county'));
    }

    public function villages($provinceId, $cityId, $countyId)
    {
        $rows = array_values(array_filter(
            $this->load('provinces_cities_counties_villages.json'),
            fn($r) => (string)($r['provinceId'] ?? '') == (string)$provinceId
                 && (string)($r['cityId'] ?? '') == (string)$cityId
                 && (string)($r['countyId'] ?? '') == (string)$countyId
        ));
        return response()->json($this->norm($rows,'village'));
    }
}