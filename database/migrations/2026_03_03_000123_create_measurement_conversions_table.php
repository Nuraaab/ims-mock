<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('measurement_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_measurement_id')->constrained('measurements')->cascadeOnDelete();
            $table->foreignId('to_measurement_id')->constrained('measurements')->cascadeOnDelete();
            $table->decimal('factor', 15, 6);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('measurement_conversions');
    }
};

