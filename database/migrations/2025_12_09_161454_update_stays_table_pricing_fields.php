<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stays', function (Blueprint $table) {
            // remove max discount fields
            $table->dropColumn(['max_discount_normal', 'max_discount_peak']);

            // add admin price adjustment fields
            $table->decimal('min_price_adjustment', 5, 2)->default(0);  
            $table->decimal('max_price_adjustment', 5, 2)->default(0);  
        });
    }

    public function down()
    {
        Schema::table('stays', function (Blueprint $table) {
            // restore max discount fields
            $table->decimal('max_discount_normal', 5, 2)->default(0);
            $table->decimal('max_discount_peak', 5, 2)->default(0);

            // remove admin price adjustment fields
            $table->dropColumn(['min_price_adjustment', 'max_price_adjustment']);
        });
    }
};