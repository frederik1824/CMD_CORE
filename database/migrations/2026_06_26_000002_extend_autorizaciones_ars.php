<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────
        // Extender tabla autorizaciones
        // ─────────────────────────────────────────────────────────
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->string('canal_recepcion')->default('llamada')->after('motivo_estado');
            $table->string('persona_contacto')->nullable()->after('canal_recepcion');
            $table->string('telefono_contacto')->nullable()->after('persona_contacto');
            $table->string('codigo_diagnostico')->nullable()->after('telefono_contacto');
            $table->string('tipo_servicio')->default('consulta')->after('codigo_diagnostico');
            $table->string('especialidad')->nullable()->after('tipo_servicio');
            $table->unsignedBigInteger('auditor_id')->nullable()->after('usuario_responsable_id');
            $table->unsignedBigInteger('representante_id')->nullable()->after('auditor_id');
            $table->string('tipo_afiliado_display')->default('Titular')->after('representante_id');
            $table->string('codigo_respuesta')->nullable()->after('tipo_afiliado_display');
            $table->foreign('auditor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('representante_id')->references('id')->on('users')->nullOnDelete();
        });

        // ─────────────────────────────────────────────────────────
        // Crear tabla autorizacion_comentarios
        // ─────────────────────────────────────────────────────────
        Schema::create('autorizacion_comentarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autorizacion_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comentario');
            $table->boolean('es_interno')->default(true);
            $table->timestamps();
            $table->foreign('autorizacion_id')->references('id')->on('autorizaciones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ─────────────────────────────────────────────────────────
        // Crear tabla autorizacion_estado_logs
        // ─────────────────────────────────────────────────────────
        Schema::create('autorizacion_estado_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autorizacion_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo');
            $table->string('motivo')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->foreign('autorizacion_id')->references('id')->on('autorizaciones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Eliminar tablas nuevas
        Schema::dropIfExists('autorizacion_estado_logs');
        Schema::dropIfExists('autorizacion_comentarios');

        // Revertir columnas añadidas a autorizaciones
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->dropForeign(['auditor_id']);
            $table->dropForeign(['representante_id']);
            $table->dropColumn([
                'canal_recepcion',
                'persona_contacto',
                'telefono_contacto',
                'codigo_diagnostico',
                'tipo_servicio',
                'especialidad',
                'auditor_id',
                'representante_id',
                'tipo_afiliado_display',
                'codigo_respuesta',
            ]);
        });
    }
};
