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
        Schema::table('afiliados', function (Blueprint $table) {
            $table->string('sector')->nullable()->after('municipio');
            $table->string('direccion')->nullable()->after('sector');
            $table->boolean('esta_carnetizado')->default(false)->after('estado_afiliacion');
            $table->boolean('tiene_formulario')->default(false)->after('esta_carnetizado');
            $table->string('ubicacion_formulario')->nullable()->after('tiene_formulario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn([
                'sector',
                'direccion',
                'esta_carnetizado',
                'tiene_formulario',
                'ubicacion_formulario'
            ]);
        });
    }
};
