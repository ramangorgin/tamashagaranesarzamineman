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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('stay_id')->constrained()->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->integer('guests')->default(1);

            $table->decimal('base_price', 12, 0);
            $table->decimal('extra_cost', 12, 0)->default(0);
            $table->decimal('stay_discount', 5, 2)->default(0);
            $table->decimal('org_discount', 5, 2)->default(0);
            $table->decimal('final_price', 12, 0);

            $table->string('status')->default('pending'); // pending, paid, cancelled

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
