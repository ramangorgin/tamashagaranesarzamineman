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
            $table->foreignId('host_id')->nullable()->constrained('hosts')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            
            $table->string('title');
            $table->enum('category', ['hotel','villa','apartment','ecolodge','suite','motel','house'])->default('villa');
            $table->string('province');
            $table->string('city');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->integer('area')->nullable(); // متراژ زیربنا
            $table->integer('capacity')->default(1); // ظرفیت کل
            $table->integer('base_capacity')->default(1); // ظرفیت پایه
            $table->integer('extra_capacity')->default(0); // نفر اضافه

            // سرویس‌های خواب و بهداشتی
            $table->integer('bedrooms')->default(1);
            $table->integer('double_beds')->default(0);
            $table->integer('single_beds')->default(0);
            $table->integer('floor_beds')->default(0);
            $table->integer('iranian_toilets')->default(0);
            $table->integer('western_toilets')->default(0);
            $table->integer('bathrooms')->default(0);

            // قیمت‌ها و درصدها
            $table->decimal('price_per_person', 12, 2)->default(0);
            $table->decimal('extra_person_price', 12, 2)->default(0);
            $table->decimal('site_commission', 5, 2)->default(10);
            $table->decimal('max_discount_normal', 5, 2)->default(20);
            $table->decimal('max_discount_peak', 5, 2)->default(10);

            // وضعیت‌ها
            $table->boolean('is_peak')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
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
