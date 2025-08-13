<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banner_city_relationship', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained('banner');
            $table->foreignId('city_id')->nullable()->constrained('cidades')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('ufs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner_city_relationship');
    }
};
