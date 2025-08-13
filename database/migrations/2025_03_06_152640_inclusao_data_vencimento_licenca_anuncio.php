<?php

use App\Models\LicencaAnuncio;
use Carbon\Carbon;
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
        Schema::table('licenca_anuncio', function (Blueprint $table) {
            $table->date('data_vencimento')->nullable();
        });

        $licencas = LicencaAnuncio::all();
        foreach ($licencas as $key => $licenca) {
            $licenca->data_vencimento = Carbon::now()->addDays(30);
            $licenca->save();
        }

        Schema::table('licenca_anuncio', function (Blueprint $table) {
            $table->date('data_vencimento')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenca_anuncio', function (Blueprint $table) {
            $table->dropColumn('data_vencimento');
        });
    }
};
