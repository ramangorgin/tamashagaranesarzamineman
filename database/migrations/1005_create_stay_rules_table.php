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
            $table->foreignId('stay_id')->constrained('stays')->cascadeOnDelete();
            $table->string('rule_text', 255);
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();
            $table->index('stay_id');
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
