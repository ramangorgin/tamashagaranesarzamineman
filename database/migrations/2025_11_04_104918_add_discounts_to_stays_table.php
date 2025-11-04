<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stays', function (Blueprint $table) {
            $table->decimal('normal_discount', 5, 2)->default(0)->after('discount_percent'); // تخفیف عادی
            $table->decimal('peak_discount', 5, 2)->default(0)->after('normal_discount'); // تخفیف در حالت پیک
            $table->boolean('is_peak')->default(false)->after('normal_discount'); // آیا الان پیک است؟
        });
    }

    public function down(): void
    {
        Schema::table('stays', function (Blueprint $table) {
            $table->dropColumn(['normal_discount', 'peak_discount', 'is_peak']);
        });
    }
};
