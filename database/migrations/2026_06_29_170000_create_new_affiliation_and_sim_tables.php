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
        // 1. Códigos de Respuesta de Unipago Configurables
        Schema::create('unipago_response_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // ok, pe, re, ev, er
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('recommended_action')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Catálogo de Servicios Simulados de Unipago
        Schema::create('unipago_mock_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_code')->unique();
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->string('endpoint_mock');
            $table->string('method')->default('POST');
            $table->string('protocol')->default('json'); // soap, json, xml
            $table->boolean('is_active')->default(true);
            $table->string('default_response_type')->default('OK');
            $table->integer('simulated_latency_ms')->default(0);
            $table->integer('error_probability')->default(0);
            $table->timestamps();
        });

        // 3. Escenarios Configurables de Respuestas
        Schema::create('unipago_mock_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('service_code');
            $table->string('scenario_name');
            $table->json('conditions')->nullable();
            $table->string('response_code');
            $table->json('response_payload_template')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Solicitudes de Afiliación de Titulares
        Schema::create('holder_affiliation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->unsignedBigInteger('contract_number_id')->nullable();
            $table->string('contract_number')->nullable();
            $table->unsignedBigInteger('promoter_id')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('employer_rnc')->nullable();
            $table->string('payroll_status')->nullable();
            $table->decimal('salary_amount', 12, 2)->nullable();
            $table->string('regime_type')->default('Contributivo');
            $table->string('channel')->nullable();
            $table->string('status')->default('borrador'); // borrador, prevalidado, pendiente_documento, listo_enviar, enviado_unipago, procesado_ok, pendiente_pe, rechazado_re, anulado, cerrado
            $table->unsignedBigInteger('unipago_batch_id')->nullable();
            $table->string('unipago_request_id')->nullable();
            $table->string('unipago_response_code')->nullable();
            $table->text('unipago_response_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 5. Solicitudes de Afiliación de Dependientes
        Schema::create('dependent_affiliation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->unsignedBigInteger('holder_affiliate_id');
            $table->unsignedBigInteger('dependent_affiliate_id')->nullable();
            $table->string('relationship');
            $table->string('document_type');
            $table->string('document_number');
            $table->string('status')->default('borrador'); // borrador, prevalidado, pendiente_documento, enviado_unipago, procesado_ok, pendiente_pe, rechazado_re, anulado, activo, cerrado
            $table->unsignedBigInteger('unipago_batch_id')->nullable();
            $table->string('unipago_request_id')->nullable();
            $table->string('unipago_response_code')->nullable();
            $table->text('unipago_response_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 6. Documentos de Soporte de Afiliados y Solicitudes
        Schema::create('affiliate_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->string('request_type')->nullable(); // titular / dependiente
            $table->string('document_type'); // formulario_firmado, copia_cedula, acta_nacimiento, etc.
            $table->string('file_path');
            $table->string('status')->default('activo');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });

        // 7. Grupos Familiares (Cabecera)
        Schema::create('family_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holder_affiliate_id')->unique();
            $table->string('status')->default('activo');
            $table->timestamps();
        });

        // 8. Integrantes del Grupo Familiar (Detalles)
        Schema::create('family_group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_group_id');
            $table->unsignedBigInteger('affiliate_id'); // Id en afiliados/dependientes (dependiente)
            $table->string('relationship'); // cónyuge, hijo, etc.
            $table->string('status')->default('activo');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_group_members');
        Schema::dropIfExists('family_groups');
        Schema::dropIfExists('affiliate_documents');
        Schema::dropIfExists('dependent_affiliation_requests');
        Schema::dropIfExists('holder_affiliation_requests');
        Schema::dropIfExists('unipago_mock_scenarios');
        Schema::dropIfExists('unipago_mock_services');
        Schema::dropIfExists('unipago_response_codes');
    }
};
