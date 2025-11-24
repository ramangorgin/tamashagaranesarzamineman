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

            $table->unsignedSmallInteger('base_guests')->default(1);
            $table->unsignedSmallInteger('extra_guests')->default(0);

            $table->decimal('base_price', 12, 0);          // base_guests * price_per_person * nights
            $table->decimal('extra_cost', 12, 0)->default(0); // extra_guests * extra_person_price * nights
            $table->decimal('stay_discount', 5, 2)->default(0); // % applied (normal/peak)
            $table->decimal('org_discount', 5, 2)->default(0);  // % from contract

            $table->foreignId('discount_contract_id')->nullable()
                  ->constrained('discount_contracts')->nullOnDelete();

            $table->decimal('discount_amount', 12, 0)->default(0);
            $table->decimal('final_price', 12, 0);

            // Lifecycle statuses: pending, paid, cancelled, refunded, failed, expired
            $table->string('status')->default('pending');
            // Cancellation / refund tracking
            $table->string('cancelled_by')->nullable(); // user|host|system
            $table->text('cancellation_reason')->nullable();
            $table->unsignedDecimal('refund_amount', 12, 0)->default(0); // amount refunded (<= final_price)
            $table->timestamp('refunded_at')->nullable();
            // Payment gateway tracking (optional external reference)
            $table->string('payment_reference')->nullable();

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
