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
        Schema::create('stay_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained()->onDelete('cascade');
            $table->string('rule_text');
            $table->boolean('is_allowed')->default(true);
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stay_rules');
    }
};
