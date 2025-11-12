<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\User;
use App\Models\Host;
use App\Models\Stay;
use App\Models\StayImage;
use App\Models\StayFacility;
use App\Models\StayRule;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1️⃣ ایجاد ادمین
            $admin = Admin::updateOrCreate(
                ['phone' => '09014282751'],
                [
                    'name' => 'رامان گرگین پاوه',
                    'role' => 'admin',
                ]
            );

            // 2️⃣ ایجاد کاربر تستی
            $user = User::updateOrCreate(
                ['phone' => '09014282751'],
                [
                    'full_name'   => 'رامان گرگین پاوه',
                    'national_id' => '0150629737',
                ]
            );

            // 3️⃣ ایجاد میزبان تایید شده
            $host = Host::updateOrCreate(
                ['phone' => '09014282751'],
                [
                    'name' => 'رامان گرگین پاوه',
                    'national_id' => '0150629737',
                    'email' => 'raman@example.com',
                    'province' => 'تهران',
                    'city' => 'تهران',
                    'postal_code' => '1134567890',
                    'address' => 'تهران، خیابان آزادی، پلاک ۱۲۳',
                    'iban' => 'IR123456789012345678901234',
                    'bank_name' => 'بانک ملت',
                    'account_holder' => 'رامان گرگین پاوه',
                    'status' => 'approved',
                    'bio' => 'میزبان فعال با تجربه در اقامتگاه‌های شمال و مرکز کشور',
                    'avatar' => 'avatars/raman.jpg',
                ]
            );

            // 4️⃣ ایجاد چند اقامت‌گاه تستی منطبق با فیلدهای جدید جدول stays
            $stays = [
                [
                    'title' => 'ویلا جنگلی رامسر',
                    'category' => 'villa',
                    'province' => 'مازندران',
                    'city' => 'رامسر',
                    'address' => 'رامسر، بلوار معلم، کوچه گلستان ۵',
                    'latitude' => 36.904,
                    'longitude' => 50.658,
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
                [
                    'title' => 'سوئیت ساحلی کیش',
                    'category' => 'suite',
                    'province' => 'هرمزگان',
                    'city' => 'کیش',
                    'address' => 'کیش، بلوار ساحل، کوچه نیلوفر',
                    'latitude' => 26.533,
                    'longitude' => 53.987,
                    'area' => 80,
                    'capacity' => 4,
                    'base_capacity' => 2,
                    'extra_capacity' => 2,
                    'bedrooms' => 1,
                    'double_beds' => 1,
                    'single_beds' => 1,
                    'floor_beds' => 2,
                    'iranian_toilets' => 1,
                    'western_toilets' => 1,
                    'bathrooms' => 1,
                    'price_per_person' => 600000,
                    'extra_person_price' => 200000,
                    'site_commission' => 10,
                    'max_discount_normal' => 15,
                    'max_discount_peak' => 10,
                    'is_peak' => true,
                    'is_active' => true,
                    'host_id' => $host->id,
                    'admin_id' => $admin->id,
                ],
                [
                    'title' => 'بوم‌گردی کویری ورزنه',
                    'category' => 'ecolodge',
                    'province' => 'اصفهان',
                    'city' => 'ورزنه',
                    'address' => 'ورزنه، خیابان امام، کوچه ماه‌نو',
                    'latitude' => 32.422,
                    'longitude' => 52.654,
                    'area' => 120,
                    'capacity' => 6,
                    'base_capacity' => 3,
                    'extra_capacity' => 3,
                    'bedrooms' => 2,
                    'double_beds' => 1,
                    'single_beds' => 2,
                    'floor_beds' => 3,
                    'iranian_toilets' => 1,
                    'western_toilets' => 0,
                    'bathrooms' => 1,
                    'price_per_person' => 400000,
                    'extra_person_price' => 100000,
                    'site_commission' => 10,
                    'max_discount_normal' => 10,
                    'max_discount_peak' => 5,
                    'is_peak' => false,
                    'is_active' => true,
                    'host_id' => $host->id,
                    'admin_id' => $admin->id,
                ],
                [
                    'title' => 'آپارتمان شهری تهران',
                    'category' => 'apartment',
                    'province' => 'تهران',
                    'city' => 'تهران',
                    'address' => 'خیابان آزادی، میدان انقلاب',
                    'latitude' => 35.699,
                    'longitude' => 51.337,
                    'area' => 90,
                    'capacity' => 3,
                    'base_capacity' => 2,
                    'extra_capacity' => 1,
                    'bedrooms' => 2,
                    'double_beds' => 1,
                    'single_beds' => 1,
                    'floor_beds' => 0,
                    'iranian_toilets' => 1,
                    'western_toilets' => 1,
                    'bathrooms' => 1,
                    'price_per_person' => 550000,
                    'extra_person_price' => 150000,
                    'site_commission' => 10,
                    'max_discount_normal' => 0,
                    'max_discount_peak' => 0,
                    'is_peak' => false,
                    'is_active' => true,
                    'host_id' => $host->id,
                    'admin_id' => $admin->id,
                ],
            ];

            foreach ($stays as $stayData) {
                $stay = Stay::updateOrCreate(['title' => $stayData['title']], $stayData);

                // 5️⃣ تصاویر فرضی
                StayImage::updateOrCreate(
                    ['stay_id' => $stay->id, 'path' => "images/sample/{$stay->id}_1.jpg"],
                    ['is_main' => true]
                );
                StayImage::updateOrCreate(
                    ['stay_id' => $stay->id, 'path' => "images/sample/{$stay->id}_2.jpg"],
                    ['is_main' => false]
                );

                // 6️⃣ امکانات
                $facilities = [
                    ['icon' => 'bi-wifi', 'name' => 'اینترنت رایگان'],
                    ['icon' => 'bi-tv', 'name' => 'تلویزیون'],
                    ['icon' => 'bi-snow', 'name' => 'سیستم سرمایشی'],
                    ['icon' => 'bi-car-front', 'name' => 'پارکینگ'],
                    ['icon' => 'bi-cup-hot', 'name' => 'آشپزخانه مجهز'],
                ];

                foreach ($facilities as $f) {
                    StayFacility::updateOrCreate([
                        'stay_id' => $stay->id,
                        'name' => $f['name']
                    ], [
                        'icon' => $f['icon']
                    ]);
                }

                // 7️⃣ قوانین
                $rules = [
                    'پذیرش ۲۴ ساعته مهمان',
                    'ورود حیوانات خانگی مجاز نیست',
                    'برگزاری مراسم مجاز نیست',
                    'همراه داشتن کارت ملی الزامی است',
                ];

                foreach ($rules as $rule) {
                    StayRule::updateOrCreate([
                        'stay_id' => $stay->id,
                        'rule_text' => $rule
                    ]);
                }
            }

            $this->command->info('Seeding The Database Completed Successfully!');
        });
    }
}
