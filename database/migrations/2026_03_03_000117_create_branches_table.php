<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->string('name');
            $table->string('sub_tin')->nullable();
            $table->foreignId('woreda_id')->nullable()->constrained();
            $table->foreignId('kebele_id')->nullable()->constrained();
            $table->foreignId('locality_id')->nullable()->constrained();
            $table->foreignId('tax_center_id')->nullable()->constrained();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};

