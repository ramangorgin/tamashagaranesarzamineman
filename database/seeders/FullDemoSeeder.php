<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin;
use App\Models\User;
use App\Models\Host;
use App\Models\Stay;
use App\Models\StayImage;
use App\Models\StayRule;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $onlyColumns = function (string $table, array $data): array {
                if (!Schema::hasTable($table)) return [];
                $cols = Schema::getColumnListing($table);
                return array_intersect_key($data, array_flip($cols));
            };
            $has = fn (string $table, string $col) => Schema::hasTable($table) && Schema::hasColumn($table, $col);

            // Admins
            $admins = [
                [
                    'name'  => 'رامان گرگین پاوه',
                    'role'  => 'admin',
                    'phone' => '09014282751',
                ],
                [
                    'name'  => 'محمدرضا کریمی',
                    'role'  => 'admin',
                    'phone' => '09126858394',
                ]
            ];

            $firstAdmin = null;
            foreach ($admins as $index => $adminData) {
                $admin = Admin::updateOrCreate(
                    ['phone' => $adminData['phone']],
                    $onlyColumns('admins', $adminData)
                );
                if ($index === 0) {
                    $firstAdmin = $admin;
                }
            }
            $admin = $firstAdmin; // Use Raman as the main admin for associations

            // User (Raman)
            if (Schema::hasTable('users')) {
                User::updateOrCreate(
                    ['phone' => '09014282751'],
                    $onlyColumns('users', [
                        'full_name'   => 'رامان گرگین پاوه',
                        'national_id' => '0150629737',
                        'phone'       => '09014282751',
                    ])
                );
            }

            // Host (Raman)
            $host = Host::updateOrCreate(
                ['phone' => '09014282751'],
                $onlyColumns('hosts', [
                    'name'          => 'رامان گرگین پاوه',
                    'national_id'   => '0150629737',
                    'email'         => 'host.raman@example.com',
                    'phone'         => '09014282751',
                    'status'        => 'approved',
                    'province_id'   => '02',
                    'province_name' => 'مازندران',
                    'city_id'       => '0201',
                    'city_name'     => 'رامسر',
                    'county_id'     => '0202',
                    'county_name'   => 'رامسر',
                    'address'       => 'رامسر، خیابان ساحلی، پلاک ۱۲',
                    'postal_code'   => '4678943210',
                    'iban'          => 'IR555555555555555555555555',
                    'bank_name'     => 'ملت',
                    'account_holder'=> 'رامان گرگین پاوه',
                    'bio'           => 'میزبان با سابقه عالی در رامسر و حومه',
                    'id_card_image' => 'images/demo/id_card.jpg',
                    'selfie_image'  => 'images/demo/selfie.jpg',
                    'business_license' => 'images/demo/license.jpg',
                ])
            );

            // Stays
            if (Schema::hasTable('stays') && $has('stays','title')) {

                $now = now();

                $stays = [
                    // 1. Approved + Active
                    [
                        'title'              => 'ویلا لوکس استخردار در رامسر',
                        'category'           => 'villa',
                        'province_id'        => '02',
                        'province_name'      => 'مازندران',
                        'city_id'            => '0201',
                        'city_name'          => 'رامسر',
                        'county_id'          => '0202',
                        'county_name'        => 'رامسر',
                        'address'            => 'رامسر، بلوار معلم، کوچه گلستان ۵',
                        'latitude'           => 36.9040,
                        'longitude'          => 50.6580,
                        'area'               => 250,
                        'capacity'           => 10,
                        'base_capacity'      => 6,
                        'extra_capacity'     => 4,
                        'bedrooms'           => 4,
                        'double_beds'        => 3,
                        'single_beds'        => 2,
                        'floor_beds'         => 2,
                        'iranian_toilets'    => 1,
                        'western_toilets'    => 2,
                        'bathrooms'          => 3,
                        'price_per_person'   => 800000,
                        'extra_person_price' => 200000,
                        'site_commission'    => 15,
                        'max_discount_normal'=> 25,
                        'max_discount_peak'  => 10,
                        'is_peak'            => false,
                        'is_active'          => true,
                        'moderation_status'  => 'approved',
                        'approved_at'        => $now,
                    ],
                    // 2. Approved + Active
                    [
                        'title'              => 'کلبه چوبی در دل جنگل‌های دوهزار',
                        'category'           => 'ecolodge',
                        'province_id'        => '02',
                        'province_name'      => 'مازندران',
                        'city_id'            => '0204', // Tonekabon
                        'city_name'          => 'تنکابن',
                        'county_id'          => '0205', // Khorramabad
                        'county_name'        => 'خرم آباد',
                        'address'            => 'جاده دوهزار، بعد از دوراهی خرم‌آباد',
                        'latitude'           => 36.7500,
                        'longitude'          => 50.8667,
                        'area'               => 80,
                        'capacity'           => 5,
                        'base_capacity'      => 2,
                        'extra_capacity'     => 3,
                        'bedrooms'           => 2,
                        'double_beds'        => 1,
                        'single_beds'        => 1,
                        'floor_beds'         => 3,
                        'iranian_toilets'    => 1,
                        'western_toilets'    => 0,
                        'bathrooms'          => 1,
                        'price_per_person'   => 450000,
                        'extra_person_price' => 100000,
                        'site_commission'    => 12,
                        'max_discount_normal'=> 15,
                        'max_discount_peak'  => 5,
                        'is_peak'            => false,
                        'is_active'          => true,
                        'moderation_status'  => 'approved',
                        'approved_at'        => $now,
                    ],
                    // 3. Approved + Active
                    [
                        'title'              => 'آپارتمان ساحلی با ویو دریا در متل قو',
                        'category'           => 'apartment',
                        'province_id'        => '02',
                        'province_name'      => 'مازندران',
                        'city_id'            => '0203', // Abbasabad
                        'city_name'          => 'عباس آباد',
                        'county_id'          => '0204', // Salman Shahr
                        'county_name'        => 'سلمان شهر',
                        'address'            => 'سلمان شهر (متل قو)، برج‌های ساحلی قو',
                        'latitude'           => 36.7154,
                        'longitude'          => 51.1680,
                        'area'               => 110,
                        'capacity'           => 6,
                        'base_capacity'      => 4,
                        'extra_capacity'     => 2,
                        'bedrooms'           => 2,
                        'double_beds'        => 2,
                        'single_beds'        => 2,
                        'floor_beds'         => 0,
                        'iranian_toilets'    => 1,
                        'western_toilets'    => 1,
                        'bathrooms'          => 2,
                        'price_per_person'   => 600000,
                        'extra_person_price' => 150000,
                        'site_commission'    => 14,
                        'max_discount_normal'=> 20,
                        'max_discount_peak'  => 10,
                        'is_peak'            => false,
                        'is_active'          => true,
                        'moderation_status'  => 'approved',
                        'approved_at'        => $now,
                    ],
                    // 4. Pending (not visible publicly)
                    [
                        'title'              => 'آپارتمان مرکز شهر تهران',
                        'category'           => 'apartment',
                        'province_id'        => '08',
                        'province_name'      => 'تهران',
                        'city_id'            => '0801',
                        'city_name'          => 'تهران',
                        'county_id'          => '0802',
                        'county_name'        => 'تهران',
                        'address'            => 'تهران، میدان ونک، خیابان گاندی',
                        'latitude'           => 35.7448,
                        'longitude'          => 51.3853,
                        'area'               => 90,
                        'capacity'           => 5,
                        'base_capacity'      => 3,
                        'extra_capacity'     => 2,
                        'bedrooms'           => 2,
                        'double_beds'        => 1,
                        'single_beds'        => 1,
                        'floor_beds'         => 2,
                        'iranian_toilets'    => 1,
                        'western_toilets'    => 1,
                        'bathrooms'          => 1,
                        'price_per_person'   => 400000,
                        'extra_person_price' => 120000,
                        'site_commission'    => 10,
                        'max_discount_normal'=> 15,
                        'max_discount_peak'  => 8,
                        'is_peak'            => false,
                        'is_active'          => false,
                        'moderation_status'  => 'pending',
                    ],
                    // 5. Rejected
                    [
                        'title'              => 'کلبه کوهستانی کردستان',
                        'category'           => 'ecolodge',
                        'province_id'        => '12',
                        'province_name'      => 'کردستان',
                        'city_id'            => '1201',
                        'city_name'          => 'سنندج',
                        'county_id'          => '1202',
                        'county_name'        => 'سنندج',
                        'address'            => 'سنندج، روستای دره‌دراز، پس از پل سنگی',
                        'latitude'           => 35.3211,
                        'longitude'          => 47.0185,
                        'area'               => 70,
                        'capacity'           => 4,
                        'base_capacity'      => 2,
                        'extra_capacity'     => 2,
                        'bedrooms'           => 1,
                        'double_beds'        => 1,
                        'single_beds'        => 0,
                        'floor_beds'         => 2,
                        'iranian_toilets'    => 1,
                        'western_toilets'    => 0,
                        'bathrooms'          => 1,
                        'price_per_person'   => 300000,
                        'extra_person_price' => 90000,
                        'site_commission'    => 10,
                        'max_discount_normal'=> 10,
                        'max_discount_peak'  => 5,
                        'is_peak'            => false,
                        'is_active'          => false,
                        'moderation_status'  => 'rejected',
                        'reject_reason'      => 'کیفیت تصاویر پایین',
                    ],
                ];

                foreach ($stays as $index => $row) {
                    $payload = $onlyColumns('stays', $row);
                    if ($has('stays','host_id')) $payload['host_id'] = $host->id;
                    
                    // Set admin approval details
                    if ($row['moderation_status'] ?? false) {
                        if ($has('stays','moderation_status')) $payload['moderation_status'] = $row['moderation_status'];
                        if ($row['moderation_status'] === 'approved') {
                            if ($has('stays','approved_at')) $payload['approved_at'] = $row['approved_at'] ?? $now;
                            if ($has('stays','approved_by_admin_id')) $payload['approved_by_admin_id'] = $admin->id;
                        }
                        if ($row['moderation_status'] === 'rejected' && $has('stays','reject_reason')) {
                            $payload['reject_reason'] = $row['reject_reason'] ?? null;
                        }
                    }

                    $stay = Stay::updateOrCreate(
                        ['title' => $row['title']],
                        $payload
                    );

                    // Images
                    if (Schema::hasTable('stay_images') && $has('stay_images','stay_id')) {
                        // Use a simple numeric index for image names to avoid issues with stay->id
                        $image_prefix = $index + 1;
                        $images = [
                            ['path' => "storage/images/sample/{$image_prefix}_1.jpg", 'is_main' => true],
                            ['path' => "storage/images/sample/{$image_prefix}_2.jpg", 'is_main' => false],
                            ['path' => "storage/images/sample/{$image_prefix}_3.jpg", 'is_main' => false],
                        ];
                        foreach ($images as $img) {
                            StayImage::updateOrCreate(
                                ['stay_id' => $stay->id, 'path' => $img['path']],
                                $onlyColumns('stay_images', ['is_main' => $img['is_main']])
                            );
                        }
                    }

                    // Rules
                    if (Schema::hasTable('stay_rules') && $has('stay_rules','rule_text')) {
                        $rules = [
                            ['rule_text' => 'برگزاری جشن و مهمانی ممنوع', 'is_allowed' => false],
                            ['rule_text' => 'استعمال دخانیات (سیگار، قلیان) در فضای داخلی ممنوع', 'is_allowed' => false],
                            ['rule_text' => 'ورود حیوانات خانگی ممنوع', 'is_allowed' => false],
                            ['rule_text' => 'رعایت نظافت الزامی است', 'is_allowed' => true],
                        ];
                        foreach ($rules as $r) {
                            StayRule::updateOrCreate(
                                ['stay_id' => $stay->id, 'rule_text' => $r['rule_text']],
                                $onlyColumns('stay_rules', ['is_allowed' => $r['is_allowed']])
                            );
                        }
                    }
                }
            } else {
                $this->command?->warn('`stays` table missing essential columns; skipping stays seeding.');
            }

            $this->command?->info('Full demo seeding completed successfully.');
        });
    }
}
