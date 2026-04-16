<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_resets', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->foreignId('reset_by')->constrained('users');
            $table->json('snapshot');       // classement archivé avant reset
            $table->boolean('badges_reset')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_resets');
    }
};
