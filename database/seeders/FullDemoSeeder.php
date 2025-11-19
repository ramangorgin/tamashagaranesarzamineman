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

            // Helpers
            $onlyColumns = function (string $table, array $data): array {
                $cols = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];
                return array_intersect_key($data, array_flip($cols));
            };
            $has = fn (string $table, string $col) => Schema::hasTable($table) && Schema::hasColumn($table, $col);

            // Admin (keep simple; adapt to your admin schema)
            $adminKey = $has('admins', 'phone') ? ['phone' => '09014282751'] : ['id' => 1];
            $adminData = $onlyColumns('admins', [
                'name' => 'رامان گرگین پاوه',
                'role' => 'admin',
                'phone' => '09014282751',
            ]);
            $admin = Admin::updateOrCreate($adminKey, $adminData);

            // User (optional)
            if (Schema::hasTable('users')) {
                $userKey = $has('users', 'phone') ? ['phone' => '09014282751'] : ['id' => 1];
                $userData = $onlyColumns('users', [
                    'full_name' => 'رامان گرگین پاوه',
                    'national_id' => '0150629737',
                    'phone' => '09014282751',
                ]);
                User::updateOrCreate($userKey, $userData);
            }

            // Host (matches current minimal hosts migration: id, status, timestamps)
            $hostKey = $has('hosts', 'phone') ? ['phone' => '09014282751'] : ['id' => 1];
            $hostBase = [
                'name' => 'رامان گرگین پاوه',
                'national_id' => '0150629737',
                'email' => 'raman.gorginpaveh@gmail.com',
                'phone' => '09014282751',
                'status' => 'approved',
                // other fields only if they exist
                'province_id' => '01',
                'province_name' => 'البرز',
                'city_id' => '0101',
                'city_name' => 'کرج',
                'county_id' => '0102',
                'county_name' => 'کرج',
                'village_name' => null,
                'address' => 'جهانشهر، بلوار بهارستان، خیابان ترانه شرقی پلاک ۱۶ واحد ۱۹',
                'postal_code' => '3143843531',
                'id_card_image' => 'hosts/id_cards/sample.jpg',
                'selfie_image' => 'hosts/selfies/sample.jpg',
                'business_license' => null,
                'iban' => 'IR123456789012345678901234',
                'bank_name' => 'بلوبانک',
                'account_holder' => 'رامان گرگین پاوه',
                'bio' => 'میزبان فعال با تجربه',
                'avatar' => 'avatars/raman.jpg',
            ];
            $hostData = $onlyColumns('hosts', $hostBase);
            if (!isset($hostData['status']) && $has('hosts','status')) {
                $hostData['status'] = 'approved';
            }
            $host = Host::updateOrCreate($hostKey, $hostData);

            // Seed stays only if table has at least title/category columns
            if (Schema::hasTable('stays') && $has('stays','title')) {
                $stays = [
                    [
                        'title' => 'ویلا جنگلی رامسر',
                        'category' => 'villa',
                        'province_id' => '02',
                        'province_name' => 'مازندران',
                        'city_id' => '0201',
                        'city_name' => 'رامسر',
                        'county_id' => '0202',
                        'county_name' => 'رامسر',
                        'address' => 'رامسر، بلوار معلم، کوچه گلستان ۵',
                        'latitude' => 36.9040000,
                        'longitude' => 50.6580000,
                        'area' => 150,
                        'capacity' => 8,
                        'base_capacity' => 4,
                        'extra_capacity' => 4,
                        'bedrooms' => 3,
                        'double_beds' => 2,
                        'single_beds' => 2,
                        'floor_beds' => 4,
                        'iranian_toilets' => 1,
                        'western_toilets' => 1,
                        'bathrooms' => 2,
                        'price_per_person' => 500000,
                        'extra_person_price' => 150000,
                        'site_commission' => 12,
                        'max_discount_normal' => 20,
                        'max_discount_peak' => 10,
                        'is_peak' => false,
                        'is_active' => true,
                        'host_id' => $host->id,
                        'admin_id' => $admin->id,
                    ],
                ];

                foreach ($stays as $row) {
                    $payload = $onlyColumns('stays', $row);
                    // Ensure foreign keys only if present
                    if ($has('stays','host_id')) $payload['host_id'] = $host->id;
                    if ($has('stays','admin_id')) $payload['admin_id'] = $admin->id;

                    $stay = Stay::updateOrCreate(
                        ['title' => $row['title']],
                        $payload
                    );

                    // Images if table exists
                    if (Schema::hasTable('stay_images') && $has('stay_images','stay_id')) {
                        StayImage::updateOrCreate(
                            ['stay_id' => $stay->id, 'path' => "images/sample/{$stay->id}_1.jpg"],
                            $onlyColumns('stay_images', ['is_main' => true])
                        );
                        StayImage::updateOrCreate(
                            ['stay_id' => $stay->id, 'path' => "images/sample/{$stay->id}_2.jpg"],
                            $onlyColumns('stay_images', ['is_main' => false])
                        );
                    }

                    // Rules if table exists
                    if (Schema::hasTable('stay_rules') && $has('stay_rules','rule_text')) {
                        $rules = [
                            ['rule_text' => 'برگزاری پارتی', 'is_allowed' => false],
                            ['rule_text' => 'پخش آهنگ', 'is_allowed' => true],
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

            $this->command?->info('Seeding completed.');
        });
    }
}
