<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_center_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_center_id')->constrained();
            $table->json('history_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_center_histories');
    }
};
