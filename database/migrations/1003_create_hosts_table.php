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

            // اطلاعات پایه
            $table->string('name'); // نام و نام خانوادگی یا نام شرکت
            $table->string('national_id')->unique(); // کد ملی یا شناسه ملی
            $table->string('phone')->unique(); // شماره موبایل تایید شده
            $table->string('email')->nullable();

            // اطلاعات احراز هویت
            $table->string('id_card_image')->nullable(); // تصویر کارت ملی
            $table->string('selfie_image')->nullable(); // سلفی همراه کارت ملی
            $table->string('business_license')->nullable(); // مجوز کسب یا سند
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // اطلاعات بانکی
            $table->string('iban')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();

            // آدرس دقیق
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('address')->nullable(); // آدرس کامل محل سکونت

            // مشخصات نمایشی
            $table->string('avatar')->nullable();
            $table->string('bio')->nullable();

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
