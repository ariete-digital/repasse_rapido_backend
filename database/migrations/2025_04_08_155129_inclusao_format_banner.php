<?php

use App\Models\Banner;
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
        Schema::table('banner', function (Blueprint $table) {
            $table->string('format')->nullable();
        });
        $banners = Banner::all();
        foreach ($banners as $key => $b) {
            $b->format = "D";
            $b->save();
        }
        Schema::table('banner', function (Blueprint $table) {
            $table->string('format')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->dropColumn('format');
        });
    }
};
