<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stays', function (Blueprint $table) {
            $table->bigInteger('final_price_per_person')->nullable()->after('price_per_person');
            $table->bigInteger('final_price_per_night')->nullable()->after('price_per_night');
            $table->bigInteger('final_extra_person_price')->nullable()->after('extra_person_price');
        });
    }

    public function down(): void
    {
        Schema::table('stays', function (Blueprint $table) {
            $table->dropColumn(['final_price_per_person', 'final_price_per_night', 'final_extra_person_price']);
        });
    }
};

