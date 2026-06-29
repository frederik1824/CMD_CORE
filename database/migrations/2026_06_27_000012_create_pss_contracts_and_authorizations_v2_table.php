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
        // 1. Contratos PSS
        Schema::create('pss_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
            $table->string('contract_number')->unique();
            $table->string('contract_name');
            $table->string('contract_type')->default('general'); // general, especialidad, capitado, evento, mixto
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('auto_renewal')->default(true);
            $table->string('status')->default('vigente'); // borrador, vigente, vencido, suspendido, terminado
            $table->dateTime('signed_at')->nullable();
            $table->unsignedBigInteger('signed_by')->nullable();
            $table->string('document_path')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 2. Versiones de Contrato PSS
        Schema::create('pss_contract_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pss_contract_id')->constrained('pss_contracts')->onDelete('cascade');
            $table->string('version_number');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status')->default('vigente'); // borrador, pendiente_aprobacion, vigente, reemplazada, anulada
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('change_reason')->nullable();
            $table->timestamps();
        });

        // 3. Esquemas Tarifarios
        Schema::create('pss_tariff_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pss_contract_id')->constrained('pss_contracts')->onDelete('cascade');
            $table->foreignId('pss_contract_version_id')->constrained('pss_contract_versions')->onDelete('cascade');
            $table->string('name');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status')->default('vigente'); // borrador, vigente, vencido, reemplazado
            $table->boolean('imported_from_file')->default(false);
            $table->unsignedBigInteger('imported_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        // 4. Ítems del Tarifario (Servicios Contratados con Reglas Específicas)
        Schema::create('pss_tariff_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pss_tariff_schedule_id')->constrained('pss_tariff_schedules')->onDelete('cascade');
            $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
            $table->unsignedBigInteger('pdss_service_id')->nullable(); // Mapeo opcional al catálogo PDSS
            $table->string('simon_code_snapshot');
            $table->string('cups_code_snapshot')->nullable();
            $table->string('service_description_snapshot');
            $table->string('service_group_snapshot')->nullable();
            $table->string('service_subgroup_snapshot')->nullable();
            $table->string('coverage_type_snapshot')->nullable();
            $table->decimal('contracted_amount', 14, 2);
            $table->string('currency')->default('DOP');
            $table->decimal('copay_percent', 5, 2)->default(20.00);
            $table->decimal('affiliate_copay_amount', 14, 2)->default(0.00);
            $table->decimal('ars_covered_percent', 5, 2)->default(80.00);
            $table->boolean('requires_authorization')->default(true);
            $table->boolean('requires_medical_audit')->default(false);
            $table->boolean('requires_document')->default(false);
            $table->integer('frequency_limit')->nullable();
            $table->string('frequency_period')->nullable(); // dia, mes, año, evento
            $table->decimal('max_amount_per_event', 14, 2)->nullable();
            $table->decimal('max_amount_per_year', 14, 2)->nullable();
            $table->boolean('level_1_allowed')->default(true);
            $table->boolean('level_2_allowed')->default(true);
            $table->boolean('level_3_allowed')->default(true);
            $table->boolean('is_high_cost')->default(false);
            $table->boolean('is_emergency')->default(false);
            $table->boolean('is_hospitalization')->default(false);
            $table->boolean('is_surgery')->default(false);
            $table->boolean('is_diagnostic_support')->default(false);
            $table->boolean('is_medicine')->default(false);
            $table->string('status')->default('activo'); // activo, inactivo, suspendido
            $table->timestamps();
        });

        // 5. Logs de Importaciones de Tarifario
        Schema::create('pss_tariff_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pss_id')->constrained('pss')->onDelete('cascade');
            $table->foreignId('pss_contract_id')->constrained('pss_contracts')->onDelete('cascade');
            $table->string('file_path');
            $table->integer('total_rows')->default(0);
            $table->integer('imported_rows')->default(0);
            $table->integer('rejected_rows')->default(0);
            $table->string('status')->default('completado'); // pendiente, procesando, completado, con_errores, fallido
            $table->text('errors')->nullable(); // Guardar JSON de errores
            $table->unsignedBigInteger('imported_by')->nullable();
            $table->dateTime('imported_at')->nullable();
            $table->timestamps();
        });

        // 6. Logs de Cambios Contractuales
        Schema::create('pss_contract_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pss_contract_id')->constrained('pss_contracts')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); // crear_contrato, crear_version, modificar_tarifa, etc.
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('observation')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 7. Overrides de Autorización
        Schema::create('authorization_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('authorization_id'); // Link a autorizaciones
            $table->string('override_type'); // monto_excedido, pss_sin_contrato, afiliado_inactivo, etc.
            $table->string('original_result');
            $table->string('new_result');
            $table->text('reason');
            $table->unsignedBigInteger('approved_by');
            $table->dateTime('approved_at');
            $table->boolean('requires_supervisor_approval')->default(false);
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->dateTime('supervisor_approved_at')->nullable();
            $table->timestamps();
        });

        // 8. Agregar columnas adicionales a la tabla actual de autorizaciones
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->string('origin')->default('portal_pss'); // portal_pss, core_ars
            $table->string('channel')->nullable(); // llamada, correo, whatsapp, portal, presencial, interno
            $table->unsignedBigInteger('pss_contract_id')->nullable();
            $table->unsignedBigInteger('pss_contract_version_id')->nullable();
            $table->unsignedBigInteger('pss_tariff_schedule_id')->nullable();
            $table->unsignedBigInteger('pss_tariff_item_id')->nullable();
            $table->decimal('contracted_amount_snapshot', 14, 2)->default(0.00);
            $table->decimal('affiliate_copay_amount', 14, 2)->default(0.00);
            $table->decimal('ars_amount', 14, 2)->default(0.00);
            $table->decimal('non_covered_amount', 14, 2)->default(0.00);
            $table->string('claim_status')->default('no_reclamada'); // no_reclamada, reclamada, en_reclamacion, pagada, cerrada
            $table->dateTime('claimed_at')->nullable();
            $table->unsignedBigInteger('claim_id')->nullable();
            $table->text('internal_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->dropColumn([
                'origin', 'channel', 'pss_contract_id', 'pss_contract_version_id',
                'pss_tariff_schedule_id', 'pss_tariff_item_id', 'contracted_amount_snapshot',
                'affiliate_copay_amount', 'ars_amount', 'non_covered_amount',
                'claim_status', 'claimed_at', 'claim_id', 'internal_notes'
            ]);
        });

        Schema::dropIfExists('authorization_overrides');
        Schema::dropIfExists('pss_contract_logs');
        Schema::dropIfExists('pss_tariff_imports');
        Schema::dropIfExists('pss_tariff_items');
        Schema::dropIfExists('pss_tariff_schedules');
        Schema::dropIfExists('pss_contract_versions');
        Schema::dropIfExists('pss_contracts');
    }
};
