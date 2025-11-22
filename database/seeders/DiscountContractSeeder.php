<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountContract;
use App\Models\DiscountContractMember;

class DiscountContractSeeder extends Seeder
{
    public function run(): void
    {
        $c = DiscountContract::create([
            'title' => 'قرارداد تستی',
            'description' => 'قرارداد نمونه برای تست',
            'discount_percent' => 10,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        DiscountContractMember::create([
            'contract_id' => $c->id,
            'full_name' => 'کاربر تستی',
            'national_id' => '1234567890',
            'phone' => '09123456789',
        ]);
    }
}