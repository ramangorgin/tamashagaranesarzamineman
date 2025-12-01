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
        Schema::table('stays', function (Blueprint $table) {
            $table->enum('pricing_mode', ['per_person', 'per_night'])->default('per_person')->after('extra_person_price');
            $table->unsignedBigInteger('price_per_night')->nullable()->after('pricing_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stays', function (Blueprint $table) {
            $table->dropColumn(['pricing_mode', 'price_per_night']);
        });
    }
};


