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
        Schema::table('escritorios_regionais', function (Blueprint $table) {
            $table->string('endereco');
            $table->string('email');
            $table->string('telefone');
        });

        Schema::table('subregioes', function (Blueprint $table) {
            $table->string('endereco');
            $table->string('email');
            $table->string('telefone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escritorios_regionais', function (Blueprint $table) {
            $table->dropColumn('endereco');
            $table->dropColumn('email');
            $table->dropColumn('telefone');
        });

        Schema::table('subregioes', function (Blueprint $table) {
            $table->dropColumn('endereco');
            $table->dropColumn('email');
            $table->dropColumn('telefone');
        });
    }
};
