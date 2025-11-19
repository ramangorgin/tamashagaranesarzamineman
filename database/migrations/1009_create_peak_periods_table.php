<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('peak_periods')) {
            Schema::create('peak_periods', function (Blueprint $table) {
                $table->id();
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('peak_periods');
    }
};
