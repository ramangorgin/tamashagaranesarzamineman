<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stay;

class StaySeeder extends Seeder
{
    public function run(): void
    {
        Stay::create([
            'title' => 'اقامتگاه نمونه',
            'price_per_night' => 500000,
            'capacity' => 4,
            'extra_person_price' => 80000,
            'is_peak' => false,
            'normal_discount' => 0,
            'peak_discount' => 0,
            // add required fields...
        ]);
    }
}