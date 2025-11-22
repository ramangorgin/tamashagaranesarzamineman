<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_contract_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')
                  ->constrained('discount_contracts')
                  ->cascadeOnDelete();

            $table->string('full_name');
            $table->string('national_id', 50)->nullable();
            $table->string('phone', 50)->nullable();

            $table->timestamps();

            $table->index(['contract_id', 'phone']);
            $table->index(['contract_id', 'national_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_contract_members');
    }
};