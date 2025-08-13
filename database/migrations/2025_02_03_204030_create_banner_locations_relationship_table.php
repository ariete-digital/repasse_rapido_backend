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
        Schema::create('banner_locations_relationship', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained('banner');
            $table->foreignId('banner_location_id')->constrained('banner_locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner_locations_relationship');
    }
};
