<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('default_measurement_id')->nullable()->constrained('measurements')->nullOnDelete();
            $table->boolean('track_stock')->default(true);
            $table->boolean('track_batch')->default(false);
            $table->boolean('track_expiry')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

