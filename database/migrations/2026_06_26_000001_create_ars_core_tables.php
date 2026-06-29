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
        // 1. Alter users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('Consulta'); // Administrador ARS, Supervisor Afiliación, Analista Afiliación, Auditor Médico, Autorizaciones Médicas, Usuario PSS, Consulta
            $table->unsignedBigInteger('pss_id')->nullable();
        });

        // 2. Create catalogos table
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('grupo'); // estado_lote, estado_solicitud, parentesco, etc.
            $table->string('codigo'); // VE, CED, NSS, etc.
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Create afiliados (Titulares) table
        Schema::create('afiliados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_identificacion_id');
            $table->string('cedula')->nullable()->unique();
            $table->string('nss')->nullable()->unique();
            $table->string('nui')->nullable()->unique();
            $table->string('nombres');
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('sexo', 1); // M, F
            $table->string('provincia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('numero_contrato')->nullable();
            $table->date('fecha_suscripcion')->nullable();
            $table->string('estado_afiliacion')->default('Pendiente'); // OK, PE, RE, Pendiente
            $table->string('motivo_estado')->nullable();
            $table->boolean('activo_nomina')->default(true);
            $table->boolean('tiene_aporte')->default(true);
            $table->string('regimen_actual')->nullable();
            $table->string('entidad_actual')->nullable();
            $table->string('tipo_afiliacion')->nullable();
            $table->date('fecha_afiliacion')->nullable();
            $table->string('ultimo_periodo_pagado')->nullable();
            $table->timestamps();

            $table->foreign('tipo_identificacion_id')->references('id')->on('catalogos');
        });

        // 4. Create dependientes table
        Schema::create('dependientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('titular_id');
            $table->unsignedBigInteger('tipo_identificacion_id');
            $table->string('cedula')->nullable();
            $table->string('nss')->nullable();
            $table->string('nui')->nullable();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->string('sexo', 1); // M, F
            $table->unsignedBigInteger('parentesco_id');
            $table->string('tipo_dependiente'); // Directo, Adicional
            $table->boolean('estudiante')->default(false);
            $table->boolean('discapacitado')->default(false);
            $table->string('nacionalidad')->default('Dominicana');
            $table->boolean('requiere_documento')->default(false);
            $table->string('estado_afiliacion')->default('Pendiente'); // OK, PE, RE, Pendiente
            $table->string('motivo_estado')->nullable();
            $table->timestamps();

            $table->foreign('titular_id')->references('id')->on('afiliados')->onDelete('cascade');
            $table->foreign('tipo_identificacion_id')->references('id')->on('catalogos');
            $table->foreign('parentesco_id')->references('id')->on('catalogos');
        });

        // 5. Create lotes table
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_lote')->unique();
            $table->string('tipo_lote'); // afiliacion_titulares, afiliacion_dependientes, novedades
            $table->string('estado_lote')->default('VE'); // VE, PC, PE, RE, EV
            $table->integer('total_registros')->default(0);
            $table->integer('registros_ok')->default(0);
            $table->integer('registros_re')->default(0);
            $table->unsignedBigInteger('creado_por');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_procesamiento')->nullable();
            $table->timestamps();

            $table->foreign('creado_por')->references('id')->on('users');
        });

        // 6. Create lote_detalles table
        Schema::create('lote_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lote_id');
            $table->string('entidad_type'); // titular, dependiente, novedad
            $table->unsignedBigInteger('entidad_id');
            $table->string('estado')->default('PE'); // OK, PE64, PE75, RE, etc.
            $table->string('motivo_rechazo')->nullable();
            $table->timestamps();

            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('cascade');
        });

        // 7. Create novedades table
        Schema::create('novedades', function (Blueprint $table) {
            $table->id();
            $table->string('afiliado_type'); // titular, dependiente
            $table->unsignedBigInteger('afiliado_id');
            $table->unsignedBigInteger('tipo_novedad_id');
            $table->text('campos_modificados'); // JSON representation
            $table->string('estado')->default('Pendiente'); // OK, PE, RE, AC, CA, DE
            $table->string('motivo_estado')->nullable();
            $table->unsignedBigInteger('lote_id')->nullable();
            $table->unsignedBigInteger('creado_por');
            $table->timestamp('fecha_novedad')->useCurrent();
            $table->timestamps();

            $table->foreign('tipo_novedad_id')->references('id')->on('catalogos');
            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
            $table->foreign('creado_por')->references('id')->on('users');
        });

        // 8. Create pss table
        Schema::create('pss', function (Blueprint $table) {
            $table->id();
            $table->string('rnc')->unique();
            $table->string('nombre');
            $table->string('tipo_entidad'); // Clínica, Hospital, Centro Médico
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->string('estado')->default('Activa'); // Activa, Inactiva
            $table->timestamps();
        });

        // 9. Create servicios_medicos table
        Schema::create('servicios_medicos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->decimal('cobertura_base', 5, 2)->default(80.00);
            $table->boolean('es_alto_costo')->default(false);
            $table->boolean('requiere_documento')->default(false);
            $table->timestamps();
        });

        // 10. Create contratos_pss table
        Schema::create('contratos_pss', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pss_id');
            $table->string('numero_contrato');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estado')->default('Activo'); // Activo, Vencido
            $table->timestamps();

            $table->foreign('pss_id')->references('id')->on('pss')->onDelete('cascade');
        });

        // 11. Create tarifas_pss table
        Schema::create('tarifas_pss', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_pss_id');
            $table->unsignedBigInteger('servicio_medico_id');
            $table->decimal('monto_tarifa', 12, 2);
            $table->timestamps();

            $table->foreign('contrato_pss_id')->references('id')->on('contratos_pss')->onDelete('cascade');
            $table->foreign('servicio_medico_id')->references('id')->on('servicios_medicos')->onDelete('cascade');
        });

        // 12. Create autorizaciones table
        Schema::create('autorizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_autorizacion')->unique();
            $table->string('afiliado_type'); // titular, dependiente
            $table->unsignedBigInteger('afiliado_id');
            $table->unsignedBigInteger('pss_id');
            $table->string('medico_solicitante');
            $table->string('diagnostico');
            $table->unsignedBigInteger('servicio_medico_id');
            $table->string('procedimiento')->nullable();
            $table->decimal('monto_solicitado', 12, 2);
            $table->decimal('monto_contratado', 12, 2)->default(0.00);
            $table->string('prioridad')->default('Media'); // Alta, Media, Baja
            $table->string('estado')->default('Pendiente'); // Pendiente, Aprobada, Rechazada, Auditoría, Pendiente Documento
            $table->string('motivo_estado')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->unsignedBigInteger('usuario_responsable_id')->nullable();
            $table->timestamps();

            $table->foreign('pss_id')->references('id')->on('pss');
            $table->foreign('servicio_medico_id')->references('id')->on('servicios_medicos');
            $table->foreign('usuario_responsable_id')->references('id')->on('users');
        });

        // 13. Create autorizacion_detalles table
        Schema::create('autorizacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autorizacion_id');
            $table->string('codigo');
            $table->string('descripcion');
            $table->integer('cantidad')->default(1);
            $table->decimal('monto', 12, 2);
            $table->string('estado')->default('Aprobado');
            $table->timestamps();

            $table->foreign('autorizacion_id')->references('id')->on('autorizaciones')->onDelete('cascade');
        });

        // 14. Create reglas_autorizacion table
        Schema::create('reglas_autorizacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->string('tipo_regla');
            $table->string('valor')->nullable();
            $table->string('estado')->default('Activa'); // Activa, Inactiva
            $table->timestamps();
        });

        // 15. Create documentos table
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('entidad_type'); // afiliado, dependiente, autorizacion, novedad
            $table->unsignedBigInteger('entidad_id');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->string('tipo_documento'); // Identificación, Soporte Médico, Acta de Nacimiento
            $table->timestamp('fecha_carga')->useCurrent();
            $table->timestamps();
        });

        // 16. Create bitacoras table
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('accion');
            $table->string('modulo');
            $table->text('detalles'); // JSON string
            $table->string('ip_address')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('reglas_autorizacion');
        Schema::dropIfExists('autorizacion_detalles');
        Schema::dropIfExists('autorizaciones');
        Schema::dropIfExists('tarifas_pss');
        Schema::dropIfExists('contratos_pss');
        Schema::dropIfExists('servicios_medicos');
        Schema::dropIfExists('pss');
        Schema::dropIfExists('novedades');
        Schema::dropIfExists('lote_detalles');
        Schema::dropIfExists('lotes');
        Schema::dropIfExists('dependientes');
        Schema::dropIfExists('afiliados');
        Schema::dropIfExists('catalogos');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'pss_id']);
        });
    }
};
