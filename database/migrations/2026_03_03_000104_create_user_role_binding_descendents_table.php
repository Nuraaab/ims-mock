<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_role_binding_descendents', function (Blueprint $table) {
            $table->foreignId('user_role_binding_id')->constrained()->cascadeOnDelete();
            $table->string('scope');
            $table->unsignedBigInteger('scope_id');
            $table->primary(['user_role_binding_id', 'scope', 'scope_id'], 'urb_descendents_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_role_binding_descendents');
    }
};

