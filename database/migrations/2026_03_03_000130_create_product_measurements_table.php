<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measurement_id')->constrained()->cascadeOnDelete();
            $table->decimal('conversion_rate', 15, 4)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->boolean('is_base')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_measurements');
    }
};

