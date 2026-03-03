<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tin_number')->unique();
            $table->string('VAT_reg_number')->nullable();
            $table->date('VAT_reg_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('house_number')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('legal_name')->nullable();
            $table->foreignId('woreda_id')->nullable()->constrained();
            $table->foreignId('kebele_id')->nullable()->constrained();
            $table->foreignId('locality_id')->nullable()->constrained();
            $table->foreignId('tax_center_id')->nullable()->constrained();
            $table->string('sector_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

