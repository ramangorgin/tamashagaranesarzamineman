<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('discount_contract_members')) {
            Schema::create('discount_contract_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contract_id')
                    ->constrained('discount_contracts')
                    ->onDelete('cascade');
                $table->string('full_name');
                $table->string('national_id')->nullable();
                $table->string('phone')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_contract_members');
    }
};
