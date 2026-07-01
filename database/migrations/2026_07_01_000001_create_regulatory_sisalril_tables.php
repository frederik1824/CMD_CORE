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
        // 1. Esquemas regulatorios (Definición de los 15 informes)
        Schema::create('regulatory_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('schema_code')->unique(); // Ej. '0031'
            $table->string('name'); // Ej. 'Cartera de Afiliados'
            $table->text('description')->nullable();
            $table->string('module_source'); // Ej. 'Afiliaciones'
            $table->string('report_type')->default('TXT');
            $table->integer('record_length')->default(0);
            $table->string('periodicity')->default('Mensual'); // Ej. 'Mensual', 'Trimestral'
            $table->boolean('simon_enabled')->default(true);
            $table->string('status')->default('Activo');
            $table->string('version')->default('1.0');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->string('documentation_file')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 2. Secciones del esquema (Header, Detail, Summary)
        Schema::create('regulatory_schema_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulatory_schema_id')->constrained('regulatory_schemas')->onDelete('cascade');
            $table->string('section_type'); // 'header', 'detail', 'summary'
            $table->string('name'); // Ej. 'Encabezado de Control'
            $table->string('record_type_constant'); // Ej. 'E', 'D', 'S'
            $table->integer('order')->default(1);
            $table->timestamps();
        });

        // 3. Campos de cada esquema/sección
        Schema::create('regulatory_schema_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulatory_schema_id')->constrained('regulatory_schemas')->onDelete('cascade');
            $table->string('section_type'); // 'header', 'detail', 'summary'
            $table->string('field_name'); // Ej. 'document_number'
            $table->string('field_label'); // Ej. 'Número de Documento'
            $table->string('data_type'); // 'AN', 'N', 'DATE', 'DECIMAL'
            $table->integer('length')->default(0);
            $table->boolean('required')->default(true);
            $table->integer('start_position')->default(1);
            $table->integer('end_position')->default(1);
            $table->string('default_value')->nullable();
            $table->string('constant_value')->nullable();
            $table->string('padding')->default('right'); // 'left', 'right'
            $table->string('padding_character')->default(' '); // ' ', '0'
            $table->string('format_mask')->nullable(); // Ej. 'DDMMYYYY', '0.00'
            $table->string('catalog_code')->nullable(); // Asoc con catálogos SIMON
            $table->string('validation_rule')->nullable();
            $table->string('source_model')->nullable();
            $table->string('source_field')->nullable();
            $table->string('transformation_rule')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Catálogos SISALRIL/SIMON
        Schema::create('regulatory_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('catalog_code')->unique(); // Ej. 'CAT-PARENTESCO'
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Activo');
            $table->timestamps();
        });

        // 5. Ítems de catálogos
        Schema::create('regulatory_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulatory_catalog_id')->constrained('regulatory_catalogs')->onDelete('cascade');
            $table->string('item_code'); // Ej. '0'
            $table->string('item_description'); // Ej. 'Titular'
            $table->text('extra_data')->nullable();
            $table->string('status')->default('Activo');
            $table->timestamps();
        });

        // 6. Períodos Regulatorios
        Schema::create('regulatory_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_code')->unique(); // Ej. '2026-06'
            $table->integer('month');
            $table->integer('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('abierto'); // 'abierto', 'cerrado', 'enviado', 'aprobado'
            $table->timestamps();
        });

        // 7. Corridas de Generación de Esquemas
        Schema::create('regulatory_schema_runs', function (Blueprint $table) {
            $table->id();
            $table->string('run_number')->unique(); // Ej. 'RUN-0031-202606-001'
            $table->foreignId('regulatory_schema_id')->constrained('regulatory_schemas');
            $table->foreignId('period_id')->constrained('regulatory_periods');
            $table->foreignId('generated_by')->nullable();
            $table->timestamp('generated_at');
            $table->string('status')->default('borrador'); // 'borrador', 'generado', 'con_errores', 'enviado_simon', 'aprobado'
            $table->integer('total_records')->default(0);
            $table->integer('valid_records')->default(0);
            $table->integer('invalid_records')->default(0);
            $table->string('file_name');
            $table->string('file_path')->nullable();
            $table->string('checksum')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 8. Detalles de corridas de esquemas (líneas del archivo plano)
        Schema::create('regulatory_schema_run_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulatory_schema_run_id')->constrained('regulatory_schema_runs')->onDelete('cascade');
            $table->string('source_model')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('record_type'); // 'E', 'D', 'S'
            $table->integer('line_number');
            $table->text('raw_line');
            $table->string('validation_status')->default('valido'); // 'valido', 'error', 'advertencia'
            $table->text('errors_json')->nullable();
            $table->text('warnings_json')->nullable();
            $table->timestamps();
        });

        // 9. Errores de validación regulatoria
        Schema::create('regulatory_schema_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulatory_schema_run_id')->constrained('regulatory_schema_runs')->onDelete('cascade');
            $table->unsignedBigInteger('detail_id')->nullable();
            $table->string('field_name')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message');
            $table->string('severity')->default('error'); // 'error', 'warning', 'info'
            $table->string('expected_value')->nullable();
            $table->string('current_value')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();
        });

        // 10. Envíos simulados a SIMON
        Schema::create('simon_mock_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('submission_number')->unique(); // Ej. 'SUB-SIMON-2026-0001'
            $table->foreignId('regulatory_schema_run_id')->constrained('regulatory_schema_runs')->onDelete('cascade');
            $table->foreignId('regulatory_schema_id')->constrained('regulatory_schemas');
            $table->foreignId('period_id')->constrained('regulatory_periods');
            $table->foreignId('submitted_by')->nullable();
            $table->timestamp('submitted_at');
            $table->string('status')->default('recibido'); // 'recibido', 'validando_estructura', 'estructura_rechazada', 'aprobado', 'rechazado'
            $table->text('response_summary')->nullable();
            $table->text('response_detail_json')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 11. Logs técnicos de envíos a SIMON
        Schema::create('simon_mock_submission_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simon_mock_submission_id')->constrained('simon_mock_submissions')->onDelete('cascade');
            $table->string('event_type');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('message');
            $table->text('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simon_mock_submission_logs');
        Schema::dropIfExists('simon_mock_submissions');
        Schema::dropIfExists('regulatory_schema_errors');
        Schema::dropIfExists('regulatory_schema_run_details');
        Schema::dropIfExists('regulatory_schema_runs');
        Schema::dropIfExists('regulatory_periods');
        Schema::dropIfExists('regulatory_catalog_items');
        Schema::dropIfExists('regulatory_catalogs');
        Schema::dropIfExists('regulatory_schema_fields');
        Schema::dropIfExists('regulatory_schema_sections');
        Schema::dropIfExists('regulatory_schemas');
    }
};
