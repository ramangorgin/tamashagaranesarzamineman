<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // اطلاعات پایه
            $table->string('name')->nullable(); // نام و نام خانوادگی یا نام شرکت
            $table->string('national_id')->nullable()->unique(); // کد ملی یا شناسه ملی
            $table->string('phone')->unique(); // شماره موبایل تایید شده
            $table->string('email')->nullable();

            // اطلاعات احراز هویت
            $table->string('id_card_image')->nullable(); // تصویر کارت ملی
            $table->string('selfie_image')->nullable(); // سلفی همراه کارت ملی
            $table->string('business_license')->nullable(); // مجوز کسب یا سند

            // اطلاعات بانکی
            $table->string('iban')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();

            // آدرس دقیق
            $table->string('province_id', 4)->nullable();
            $table->string('province_name')->nullable();
            $table->string('city_id', 4)->nullable();
            $table->string('city_name')->nullable();
            $table->string('county_id', 4)->nullable();
            $table->string('county_name')->nullable();
            $table->string('village_name')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 10)->nullable();
            
            // امنیت و زمان‌بندی
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
