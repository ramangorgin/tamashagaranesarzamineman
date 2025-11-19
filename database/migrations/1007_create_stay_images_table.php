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
        Schema::create('stay_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stay_id')->constrained('stays')->cascadeOnDelete();
            $table->string('path'); 
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->index(['stay_id','is_main']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stay_images');
    }
};
