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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->unique()->constrained('stays')->cascadeOnDelete();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->string('license_number')->nullable();
            $table->boolean('has_lobby')->default(false);
            $table->boolean('has_elevator')->default(false);
            $table->boolean('has_restaurant')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_breakfast')->default(false);
            $table->boolean('has_24h_reception')->default(false);
            $table->timestamps();
            $table->index('stay_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};

