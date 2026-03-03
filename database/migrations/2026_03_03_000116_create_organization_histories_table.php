<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->string('name');
            $table->string('tin_number');
            $table->string('VAT_reg_number')->nullable();
            $table->date('VAT_reg_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('house_number')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('legal_name')->nullable();
            $table->foreignId('woreda_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kebele_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('locality_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tax_center_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sector_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_histories');
    }
};
