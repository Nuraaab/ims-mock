<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_pivots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('woreda_history_id')->nullable()->constrained('woredas', 'id');
            $table->foreignId('zone_history_id')->nullable()->constrained('zones', 'id');
            $table->foreignId('region_history_id')->nullable()->constrained('regions', 'id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_pivots');
    }
};
