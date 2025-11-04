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
            $table->unsignedBigInteger('stay_id');
            $table->string('image_path'); // مسیر فایل (مثلاً storage/stays/...)
            $table->boolean('is_main')->default(false); // تصویر اصلی یا خیر
            $table->timestamps();

            $table->foreign('stay_id')->references('id')->on('stays')->onDelete('cascade');
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
