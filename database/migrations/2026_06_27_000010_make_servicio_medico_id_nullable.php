<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make servicio_medico_id nullable in autorizaciones and autorizacion_detalles tables.
     * This is required because authorizations sourced from the PDSS catalogue use
     * pdss_service_id instead of servicio_medico_id.
     */
    public function up(): void
    {
        // autorizaciones table
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('servicio_medico_id')->nullable()->change();
        });

        // autorizacion_detalles table (if it exists and has the column)
        if (Schema::hasColumn('autorizacion_detalles', 'servicio_medico_id')) {
            Schema::table('autorizacion_detalles', function (Blueprint $table) {
                $table->unsignedBigInteger('servicio_medico_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('servicio_medico_id')->nullable(false)->change();
        });

        if (Schema::hasColumn('autorizacion_detalles', 'servicio_medico_id')) {
            Schema::table('autorizacion_detalles', function (Blueprint $table) {
                $table->unsignedBigInteger('servicio_medico_id')->nullable(false)->change();
            });
        }
    }
};
