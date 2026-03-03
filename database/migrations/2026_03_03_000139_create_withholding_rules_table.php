<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withholding_rules', function (Blueprint $table) {
            $table->id();
            $table->string('item_type')->nullable();
            $table->decimal('minimum_holdable', 15, 2)->default(0);
            $table->string('state')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withholding_rules');
    }
};

