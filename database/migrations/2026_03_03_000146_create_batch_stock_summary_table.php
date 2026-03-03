<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_stock_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_stock_summary');
    }
};

