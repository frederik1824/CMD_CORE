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
        Schema::table('pss', function (Blueprint $table) {
            $table->integer('nivel_atencion')->default(1)->after('estado'); // 1, 2 o 3
            $table->string('tipo_pss')->default('Clínica')->after('nivel_atencion'); // clínica, hospital, laboratorio, farmacia, etc.
            $table->boolean('red_contratada')->default(true)->after('tipo_pss');
            $table->boolean('contrato_vigente')->default(true)->after('red_contratada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pss', function (Blueprint $table) {
            $table->dropColumn([
                'nivel_atencion',
                'tipo_pss',
                'red_contratada',
                'contrato_vigente'
            ]);
        });
    }
};
