<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measurement_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('min_quantity', 15, 2)->nullable();
            $table->decimal('max_quantity', 15, 2)->nullable();
            $table->string('pricing_type')->default('fixed');
            $table->decimal('value', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
    }
};

