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

            // Admin
            $adminKey  = $has('admins','phone') ? ['phone'=>'09014282751'] : ['id'=>1];
            $adminData = $onlyColumns('admins', [
                'name'  => 'ادمین سیستم',
                'role'  => 'admin',
                'phone' => '09014282751',
            ]);
            $admin = Admin::updateOrCreate($adminKey, $adminData);

            // User (optional)
            if (Schema::hasTable('users')) {
                $userKey  = $has('users','phone') ? ['phone'=>'09014282751'] : ['id'=>1];
                $userData = $onlyColumns('users', [
                    'full_name'   => 'کاربر تست',
                    'national_id' => '0150629737',
                    'phone'       => '09014282751',
                ]);
                User::updateOrCreate($userKey, $userData);
            }

            // Host
            $hostKey  = $has('hosts','phone') ? ['phone'=>'09014282752'] : ['id'=>1];
            $hostBase = [
                'name'          => 'میزبان نمونه',
                'national_id'   => '0250629737',
                'email'         => 'host@example.com',
                'phone'         => '09014282752',
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
                'account_holder'=> 'میزبان نمونه',
                'bio'           => 'میزبان با سابقه عالی',
                'id_card_image' => 'images/demo/id_card.jpg',
                'selfie_image' => 'images/demo/selfie.jpg',
                'business_license' => 'images/demo/license.jpg',
            ];
            $hostData = $onlyColumns('hosts', $hostBase);
            if (!isset($hostData['status']) && $has('hosts','status')) $hostData['status'] = 'approved';
            $host = Host::updateOrCreate($hostKey, $hostData);

            // Stays
            if (Schema::hasTable('stays') && $has('stays','title')) {

                $now = now();

                $stays = [
                    // Approved + Active
                    [
                        'title'              => 'ویلا جنگلی رامسر',
                        'category'           => 'villa',
                        'province_id'        => '02',
                        'province_name'      => 'مازندران',
                        'city_id'            => '0201',
                        'city_name'          => 'رامسر',
                        'county_id'          => '0202',
                        'county_name'        => 'رامسر',
                        'address'            => 'رامسر، بلوار معلم، کوچه گلستان ۵',
                        'latitude'           => 36.9040000,
                        'longitude'          => 50.6580000,
                        'area'               => 150,
                        'capacity'           => 8,
                        'base_capacity'      => 4,
                        'extra_capacity'     => 4,
                        'bedrooms'           => 3,
                        'double_beds'        => 2,
                        'single_beds'        => 2,
                        'floor_beds'         => 4,
                        'iranian_toilets'    => 1,
                        'western_toilets'    => 1,
                        'bathrooms'          => 2,
                        'price_per_person'   => 500000,
                        'extra_person_price' => 150000,
                        'site_commission'    => 12,
                        'max_discount_normal'=> 20,
                        'max_discount_peak'  => 10,
                        'is_peak'            => false,
                        'is_active'          => true,
                        'moderation_status'  => 'approved',
                        'approved_at'        => $now,
                        'host_id'            => $host->id,
                        'admin_id'           => $admin->id,
                    ],
                    // Pending (not visible publicly)
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
                        'latitude'           => 35.7448000,
                        'longitude'          => 51.3853000,
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
                        'host_id'            => $host->id,
                        'admin_id'           => $admin->id,
                    ],
                    // Rejected
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
                        'latitude'           => 35.3211000,
                        'longitude'          => 47.0185000,
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
                        'host_id'            => $host->id,
                        'admin_id'           => $admin->id,
                    ],
                ];

                foreach ($stays as $row) {
                    $payload = $onlyColumns('stays', $row);
                    if ($has('stays','host_id')) $payload['host_id'] = $host->id;
                    if ($has('stays','admin_id')) $payload['admin_id'] = $admin->id;
                    if ($row['moderation_status'] ?? false) {
                        if ($has('stays','moderation_status')) $payload['moderation_status'] = $row['moderation_status'];
                        if ($row['moderation_status'] === 'approved' && $has('stays','approved_at')) {
                            $payload['approved_at'] = $row['approved_at'] ?? $now;
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
                        $images = [
                            ['path' => "images/sample/{$stay->id}_1.jpg", 'is_main' => true],
                            ['path' => "images/sample/{$stay->id}_2.jpg", 'is_main' => false],
                            ['path' => "images/sample/{$stay->id}_3.jpg", 'is_main' => false],
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
                            ['rule_text' => 'برگزاری پارتی', 'is_allowed' => false],
                            ['rule_text' => 'سیگار کشیدن', 'is_allowed' => false],
                            ['rule_text' => 'پخش موسیقی', 'is_allowed' => true],
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
                $this->command?->warn('stays table missing essential columns; skipping stays seeding.');
            }

            $this->command?->info('Full demo seeding completed.');
        });
    }
}
