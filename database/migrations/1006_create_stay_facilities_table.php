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
        Schema::create('stay_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained()->onDelete('cascade');
            $table->enum('category', ['base','kitchen','comfort','fun','outdoor','unavailable']);
            $table->string('name');
            $table->string('icon')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stay_facilities');
    }
};
