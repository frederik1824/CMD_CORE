<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade');
            $table->string('tipo_solicitud');
            $table->text('descripcion');
            $table->string('soporte_path')->nullable();
            $table->string('estado')->default('Pendiente'); // Pendiente, En Revisión, Aprobada, Rechazada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_servicios');
    }
};
