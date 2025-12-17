<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bed;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beds = [
            ['code' => 'single', 'title_fa' => 'تخت تکنفره', 'title_en' => 'Single Bed'],
            ['code' => 'double', 'title_fa' => 'تخت دونفره', 'title_en' => 'Double Bed'],
            ['code' => 'queen', 'title_fa' => 'تخت کویین', 'title_en' => 'Queen Bed'],
            ['code' => 'king', 'title_fa' => 'تخت کینگ', 'title_en' => 'King Bed'],
            ['code' => 'extra', 'title_fa' => 'تخت اضافی', 'title_en' => 'Extra Bed'],
        ];

        foreach ($beds as $bed) {
            Bed::firstOrCreate(
                ['code' => $bed['code']],
                $bed
            );
        }
    }
}

