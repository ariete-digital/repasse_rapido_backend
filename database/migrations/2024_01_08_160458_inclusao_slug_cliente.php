<?php

use App\Models\Cliente;
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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->index();
        });

        $clientes = Cliente::where('tipo', 'PJ')->get();
        foreach ($clientes as $key => $cliente) {
            if($cliente->nome_fantasia){
                $cliente->slug = Str::slug($cliente->nome_fantasia, '-');
                $cliente->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
