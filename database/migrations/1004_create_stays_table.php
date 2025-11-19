<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            // مالک
            $table->foreignId('host_id')->nullable()->constrained('hosts')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();

            // مشخصات پایه
            $table->string('title');
            $table->string('category', 40);

            // موقعیت
            $table->string('province_id', 10)->nullable();
            $table->string('province_name', 80)->nullable();
            $table->string('city_id', 10)->nullable();
            $table->string('city_name', 80)->nullable();
            $table->string('county_id', 10)->nullable();
            $table->string('county_name', 80)->nullable();
            $table->string('village_name', 120)->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // ظرفیت و امکانات خواب
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('base_capacity')->default(1);
            $table->unsignedSmallInteger('extra_capacity')->default(0);
            $table->unsignedSmallInteger('area')->nullable();
            $table->unsignedSmallInteger('bedrooms')->default(0);
            $table->unsignedSmallInteger('double_beds')->default(0);
            $table->unsignedSmallInteger('single_beds')->default(0);
            $table->unsignedSmallInteger('floor_beds')->default(0);
            $table->unsignedSmallInteger('bathrooms')->default(0);
            $table->unsignedSmallInteger('iranian_toilets')->default(0);
            $table->unsignedSmallInteger('western_toilets')->default(0);

            // قیمت
            $table->unsignedBigInteger('price_per_person')->default(0);
            $table->unsignedBigInteger('extra_person_price')->nullable();
            $table->decimal('site_commission', 5, 2)->default(0);
            $table->decimal('max_discount_normal', 5, 2)->default(0);
            $table->decimal('max_discount_peak', 5, 2)->default(0);

            // قوانین اصلی
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();

            // وضعیت
            $table->boolean('is_active')->default(false);
            $table->boolean('is_peak')->default(false);

            $table->timestamps();
            $table->index(['host_id','is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stays');
    }
};
