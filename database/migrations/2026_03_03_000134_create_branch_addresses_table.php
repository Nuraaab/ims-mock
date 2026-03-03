<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_pivot_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kebele_history_id')->nullable()->constrained('kebeles')->nullOnDelete();
            $table->foreignId('locality_history_id')->nullable()->constrained('localities')->nullOnDelete();
            $table->foreignId('tax_center_history_id')->nullable()->constrained('tax_center_histories')->nullOnDelete();
            $table->foreignId('branch_history_id')->constrained('branch_histories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_addresses');
    }
};

