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
        Schema::table('planos', function (Blueprint $table) {
            $table->text('descricao')->change();
            $table->text('descricao_curta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->dropColumn('descricao');
            $table->dropColumn('descricao_curta');
        });
        Schema::table('planos', function (Blueprint $table) {
            $table->string('descricao');;
        });
    }
};
