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
        Schema::create('hotel_room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('capacity');
            $table->unsignedSmallInteger('base_capacity');
            $table->unsignedSmallInteger('extra_capacity')->default(0);
            $table->unsignedSmallInteger('area')->nullable();
            $table->unsignedBigInteger('price_per_night');
            $table->unsignedSmallInteger('total_rooms');
            $table->timestamps();
            $table->index('hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_types');
    }
};

